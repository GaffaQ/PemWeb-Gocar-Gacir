<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Borrowing;
use App\Models\Member;
use App\Models\ReturnBook;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BorrowingController extends Controller
{
    // ============================================================
    // ANGGOTA 5 - Bagian C + R
    // ============================================================

    /**
     * R - Menampilkan daftar peminjaman
     */
    public function index(Request $request)
    {
        $query = Borrowing::with(['member.user', 'book']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->whereHas('member.user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            })->orWhereHas('book', function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%');
            });
        }

        // Update status overdue otomatis
        Borrowing::where('status', 'borrowed')
            ->where('due_date', '<', now())
            ->update(['status' => 'overdue']);

        $borrowings = $query->latest()->paginate(10);

        return view('borrowings.index', compact('borrowings'));
    }

    /**
     * C - Menampilkan form tambah peminjaman
     */
    public function create()
    {
        $members = Member::with('user')->where('status', 'active')->get();
        $books   = Book::where('stock', '>', 0)->get();
        return view('borrowings.create', compact('members', 'books'));
    }

    /**
     * C - Menyimpan data peminjaman baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'member_id'   => 'required|exists:members,id',
            'book_id'     => 'required|exists:books,id',
            'borrow_date' => 'required|date',
            'due_date'    => 'required|date|after:borrow_date',
        ]);

        $book = Book::findOrFail($request->book_id);

        // Validasi stok
        if ($book->stock < 1) {
            return back()->with('error', 'Stok buku tidak tersedia!')->withInput();
        }

        // Kurangi stok
        $book->decrement('stock');

        Borrowing::create([
            'member_id'   => $request->member_id,
            'book_id'     => $request->book_id,
            'borrow_date' => $request->borrow_date,
            'due_date'    => $request->due_date,
            'status'      => 'borrowed',
        ]);

        return redirect()->route('borrowings.index')->with('success', 'Peminjaman berhasil dicatat!');
    }

    /**
     * R - Menampilkan detail peminjaman
     */
    public function show(Borrowing $borrowing)
    {
        $borrowing->load('member.user', 'book.category', 'return');
        return view('borrowings.show', compact('borrowing'));
    }

    // ============================================================
    // ANGGOTA 6 - Bagian U + D
    // ============================================================

    /**
     * U - Menampilkan form edit peminjaman
     */
    public function edit(Borrowing $borrowing)
    {
        if ($borrowing->status === 'returned') {
            return redirect()->route('borrowings.index')
                ->with('error', 'Peminjaman yang sudah dikembalikan tidak dapat diubah!');
        }
        $members = Member::with('user')->where('status', 'active')->get();
        $books   = Book::all();
        return view('borrowings.edit', compact('borrowing', 'members', 'books'));
    }

    /**
     * U - Memperbarui data peminjaman
     */
    public function update(Request $request, Borrowing $borrowing)
    {
        $request->validate([
            'due_date' => 'required|date|after:borrow_date',
            'status'   => 'required|in:borrowed,overdue',
        ]);

        $borrowing->update([
            'due_date' => $request->due_date,
            'status'   => $request->status,
        ]);

        return redirect()->route('borrowings.index')->with('success', 'Data peminjaman berhasil diperbarui!');
    }

    /**
     * D - Menghapus data peminjaman
     */
    public function destroy(Borrowing $borrowing)
    {
        if ($borrowing->status === 'borrowed' || $borrowing->status === 'overdue') {
            // Kembalikan stok jika buku belum dikembalikan
            $borrowing->book->increment('stock');
        }

        $borrowing->delete();

        return redirect()->route('borrowings.index')->with('success', 'Data peminjaman berhasil dihapus!');
    }

    /**
     * U - Proses pengembalian buku
     */
    public function returnBook(Request $request, Borrowing $borrowing)
    {
        $request->validate([
            'return_date' => 'required|date|after_or_equal:' . $borrowing->borrow_date,
            'notes'       => 'nullable|max:500',
        ]);

        $returnDate = Carbon::parse($request->return_date);
        $dueDate    = Carbon::parse($borrowing->due_date);

        // Hitung denda: Rp 1.000 per hari keterlambatan
        $fine = 0;
        if ($returnDate->gt($dueDate)) {
            $daysLate = $returnDate->diffInDays($dueDate);
            $fine     = $daysLate * 1000;
        }

        ReturnBook::create([
            'borrowing_id' => $borrowing->id,
            'return_date'  => $request->return_date,
            'fine'         => $fine,
            'notes'        => $request->notes,
        ]);

        // Update status peminjaman
        $borrowing->update(['status' => 'returned']);

        // Kembalikan stok buku
        $borrowing->book->increment('stock');

        return redirect()->route('borrowings.index')
            ->with('success', 'Buku berhasil dikembalikan!' . ($fine > 0 ? ' Denda: Rp ' . number_format($fine, 0, ',', '.') : ''));
    }
}
