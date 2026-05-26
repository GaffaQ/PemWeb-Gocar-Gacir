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
