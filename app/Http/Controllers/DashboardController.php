<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Borrowing;
use App\Models\Member;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->isAdmin()) {
            return $this->adminDashboard();
        }

        return $this->memberDashboard($user);
    }

    private function adminDashboard()
    {
        // Update overdue otomatis
        Borrowing::where('status', 'borrowed')
            ->where('due_date', '<', now())
            ->update(['status' => 'overdue']);

        $totalBooks     = Book::count();
        $totalMembers   = Member::count();
        $totalBorrowing = Borrowing::where('status', 'borrowed')->count();
        $totalOverdue   = Borrowing::where('status', 'overdue')->count();
        $totalPending   = Borrowing::where('status', 'pending')->count();

        $recentBorrowings = Borrowing::with(['member.user', 'book'])
            ->latest()->take(5)->get();

        $pendingRequests = Borrowing::with(['member.user', 'book'])
            ->where('status', 'pending')
            ->latest()->take(5)->get();

        return view('dashboard.index', compact(
            'totalBooks', 'totalMembers', 'totalBorrowing',
            'totalOverdue', 'totalPending', 'recentBorrowings', 'pendingRequests'
        ));
    }

    private function memberDashboard($user)
    {
        $member = $user->member;

        if (!$member) {
            return view('dashboard.index', [
                'memberBorrowings' => collect(),
                'activeBorrowings' => 0,
                'overdueBorrowings' => 0,
                'totalFine' => 0,
                'pendingCount' => 0,
            ]);
        }

        // Update overdue untuk member ini
        Borrowing::where('member_id', $member->id)
            ->where('status', 'borrowed')
            ->where('due_date', '<', now())
            ->update(['status' => 'overdue']);

        $memberBorrowings = Borrowing::with(['book', 'return'])
            ->where('member_id', $member->id)
            ->latest()->take(5)->get();

        $activeBorrowings  = Borrowing::where('member_id', $member->id)->whereIn('status', ['borrowed', 'overdue'])->count();
        $overdueBorrowings = Borrowing::where('member_id', $member->id)->where('status', 'overdue')->count();
        $pendingCount      = Borrowing::where('member_id', $member->id)->where('status', 'pending')->count();

        $totalFine = Borrowing::with('return')
            ->where('member_id', $member->id)
            ->get()
            ->sum(fn($b) => $b->return ? $b->return->fine : 0);

        return view('dashboard.index', compact(
            'memberBorrowings', 'activeBorrowings', 'overdueBorrowings', 'totalFine', 'pendingCount'
        ));
    }
}
