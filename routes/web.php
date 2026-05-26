<?php

use App\Http\Controllers\AdminMemberController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\BorrowingController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MemberBorrowingController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => redirect()->route('login'));

// Auth routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
});

// Authenticated routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ============================================================
    // ADMIN ROUTES
    // ============================================================
    Route::middleware('role:admin')->group(function () {
        // Buku & Kategori
        Route::resource('books', BookController::class);
        Route::resource('categories', CategoryController::class);

        // Peminjaman (admin)
        Route::resource('borrowings', BorrowingController::class);
        Route::get('/borrowings/{borrowing}/return', fn($borrowing) => view('borrowings.return', [
            'borrowing' => \App\Models\Borrowing::with(['member.user', 'book'])->findOrFail($borrowing)
        ]))->name('borrowings.return.form');
        Route::post('/borrowings/{borrowing}/return', [BorrowingController::class, 'returnBook'])->name('borrowings.return');

        // Manajemen Member (admin)
        Route::get('/admin/members', [AdminMemberController::class, 'index'])->name('admin.members.index');
        Route::get('/admin/members/{member}', [AdminMemberController::class, 'show'])->name('admin.members.show');
        Route::post('/admin/members/{member}/toggle-status', [AdminMemberController::class, 'toggleStatus'])->name('admin.members.toggle');
        Route::post('/admin/borrowings/{borrowing}/approve', [AdminMemberController::class, 'approveBorrowing'])->name('admin.borrowings.approve');
        Route::post('/admin/borrowings/{borrowing}/reject', [AdminMemberController::class, 'rejectBorrowing'])->name('admin.borrowings.reject');
    });

    // ============================================================
    // MEMBER ROUTES
    // ============================================================
    // Katalog (semua user)
    Route::get('/catalog', [BookController::class, 'index'])->name('catalog');
    Route::get('/catalog/{book}', [BookController::class, 'show'])->name('catalog.show');

    // Peminjaman member
    Route::middleware('role:member')->group(function () {
        Route::get('/my-borrowings', [MemberBorrowingController::class, 'index'])->name('member.borrowings.index');
        Route::get('/my-borrowings/request', [MemberBorrowingController::class, 'create'])->name('member.borrowings.create');
        Route::get('/my-borrowings/request/{book}', [MemberBorrowingController::class, 'create'])->name('member.borrowings.create.book');
        Route::post('/my-borrowings', [MemberBorrowingController::class, 'store'])->name('member.borrowings.store');
        Route::get('/my-borrowings/{id}', [MemberBorrowingController::class, 'show'])->name('member.borrowings.show');
    });
});
