<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Borrowing;
use App\Models\Member;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MemberBorrowingController extends Controller
{
    /**
     * Daftar peminjaman milik member yang sedang login
     */
    public function index()
    {
        $member = auth()->user()->member;

        if (!$member) {
            return redirect()->route('dashboard')->with('error', 'Profil member tidak ditemukan.');
        }

        // Update status overdue otomatis
        Borrowing::where('member_id', $member->id)
            ->where('status', 'borrowed')
            ->where('due_date', '<', now())
            ->update(['status' => 'overdue']);

        $borrowings = Borrowing::with(['book.category', 'return'])
            ->where('member_id', $member->id)
            ->latest()
            ->paginate(10);

        return view('member.borrowings.index', compact('borrowings'));
    }

    /**
     * Form request peminjaman
     */
    public function create($bookId = null)
    {
        $books = Book::where('stock', '>', 0)->orderBy('title')->get();
        $selectedBook = $bookId ? Book::find($bookId) : null;
        return view('member.borrowings.create', compact('books', 'selectedBook'));
    }

    /**
     * Simpan request peminjaman (status: pending)
     */
    public function store(Request $request)
    {
        $request->validate([
            'book_id'  => 'required|exists:books,id',
            'due_date' => 'required|date|after:today',
        ]);

        $member = auth()->user()->member;
        if (!$member) {
            return back()->with('error', 'Profil member tidak ditemukan.');
        }

        $book = Book::findOrFail($request->book_id);
        if ($book->stock < 1) {
            return back()->with('error', 'Stok buku tidak tersedia!')->withInput();
        }

        // Cek apakah member sudah punya request pending untuk buku ini
        $existing = Borrowing::where('member_id', $member->id)
            ->where('book_id', $book->id)
            ->whereIn('status', ['pending', 'borrowed', 'overdue'])
            ->first();

        if ($existing) {
            return back()->with('error', 'Anda sudah memiliki peminjaman aktif atau request untuk buku ini!')->withInput();
        }

        Borrowing::create([
            'member_id'   => $member->id,
            'book_id'     => $request->book_id,
            'borrow_date' => now()->toDateString(),
            'due_date'    => $request->due_date,
            'status'      => 'pending',
        ]);

        return redirect()->route('member.borrowings.index')
            ->with('success', 'Permintaan peminjaman berhasil dikirim! Tunggu persetujuan admin.');
    }

    /**
     * Detail peminjaman member
     */
    public function show($id)
    {
        $member = auth()->user()->member;
        $borrowing = Borrowing::with(['book.category', 'return'])
            ->where('member_id', $member->id)
            ->findOrFail($id);

        // Hitung estimasi denda jika masih dipinjam/overdue
        $estimatedFine = 0;
        if (in_array($borrowing->status, ['borrowed', 'overdue'])) {
            $today   = Carbon::now();
            $dueDate = Carbon::parse($borrowing->due_date);
            if ($today->gt($dueDate)) {
                $estimatedFine = $today->diffInDays($dueDate) * 1000;
            }
        }

        return view('member.borrowings.show', compact('borrowing', 'estimatedFine'));
    }
}
