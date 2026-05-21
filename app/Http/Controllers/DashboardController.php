<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Borrowing;
use App\Models\Member;
use App\Models\ReturnBook;

class DashboardController extends Controller
{
    public function index()
    {
        $totalBooks     = Book::count();
        $totalMembers   = Member::count();
        $totalBorrowing = Borrowing::where('status', 'borrowed')->count();
        $totalOverdue   = Borrowing::where('status', 'overdue')->count();

        $recentBorrowings = Borrowing::with(['member.user', 'book'])
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard.index', compact(
            'totalBooks',
            'totalMembers',
            'totalBorrowing',
            'totalOverdue',
            'recentBorrowings'
        ));
    }
}
