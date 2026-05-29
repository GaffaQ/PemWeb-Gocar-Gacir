<?php

namespace App\Http\Controllers;

use App\Models\Borrowing;
use App\Models\Member;
use App\Models\User;
use Illuminate\Http\Request;

class AdminMemberController extends Controller
{
    /**
     * Daftar semua member
     */
    public function index(Request $request)
    {
        $query = Member::with('user');

        if ($request->filled('search')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            })->orWhere('member_code', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $members = $query->latest()->paginate(10);

        return view('admin.members.index', compact('members'));
    }

    /**
     * Menampilkan form tambah anggota baru oleh Admin
     */
    public function create()
    {
        return view('admin.members.create');
    }

    /**
     * Menyimpan data anggota baru yang didaftarkan oleh Admin
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'       => 'required|min:3|max:100',
            'email'      => 'required|email|unique:users,email',
            'phone'      => 'nullable|max:20',
            'birth_date' => 'nullable|date',
            'address'    => 'nullable|max:500',
        ]);

        // 1. Buat User record dengan role member dan password acak
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => bcrypt(\Illuminate\Support\Str::random(16)),
            'role'     => 'member',
        ]);

        // 2. Generate kode member unik berurutan MBR-XXXXX
        // Cari id member terakhir
        $lastMemberId = Member::max('id') ?? 0;
        $nextId = $lastMemberId + 1;
        $memberCode = 'MBR-' . str_pad($nextId, 5, '0', STR_PAD_LEFT);

        // 3. Buat Member record
        Member::create([
            'user_id'     => $user->id,
            'member_code' => $memberCode,
            'phone'       => $request->phone,
            'birth_date'  => $request->birth_date,
            'address'     => $request->address,
            'status'      => 'active',
        ]);

        return redirect()->route('admin.members.index')->with('success', 'Anggota baru berhasil didaftarkan dengan kode ' . $memberCode . '!');
    }

    /**
     * Detail member beserta riwayat peminjaman
     */
    public function show(Member $member)
    {
        $member->load('user');

        $borrowings = Borrowing::with(['book', 'return'])
            ->where('member_id', $member->id)
            ->latest()
            ->get();

        $totalFine = $borrowings->sum(function ($b) {
            return $b->return ? $b->return->fine : 0;
        });

        $stats = [
            'total'    => $borrowings->count(),
            'active'   => $borrowings->whereIn('status', ['borrowed', 'overdue'])->count(),
            'returned' => $borrowings->where('status', 'returned')->count(),
            'overdue'  => $borrowings->where('status', 'overdue')->count(),
            'pending'  => $borrowings->where('status', 'pending')->count(),
            'fine'     => $totalFine,
        ];

        return view('admin.members.show', compact('member', 'borrowings', 'stats'));
    }

    /**
     * Toggle status member (active/inactive)
     */
    public function toggleStatus(Member $member)
    {
        $newStatus = $member->status === 'active' ? 'inactive' : 'active';
        $member->update(['status' => $newStatus]);

        return back()->with('success', 'Status member berhasil diubah menjadi ' . $newStatus . '.');
    }

    /**
     * Approve request peminjaman dari member
     */
    public function approveBorrowing(Borrowing $borrowing)
    {
        if ($borrowing->status !== 'pending') {
            return back()->with('error', 'Request ini sudah diproses.');
        }

        $book = $borrowing->book;
        if ($book->stock < 1) {
            return back()->with('error', 'Stok buku habis, tidak bisa disetujui.');
        }

        $book->decrement('stock');
        $borrowing->update([
            'status'      => 'borrowed',
            'borrow_date' => now()->toDateString(),
        ]);

        return back()->with('success', 'Peminjaman berhasil disetujui!');
    }

    /**
     * Reject request peminjaman dari member
     */
    public function rejectBorrowing(Borrowing $borrowing)
    {
        if ($borrowing->status !== 'pending') {
            return back()->with('error', 'Request ini sudah diproses.');
        }

        $borrowing->delete();

        return back()->with('success', 'Request peminjaman berhasil ditolak.');
    }
}
