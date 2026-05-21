<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\BorrowingController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

// Redirect root ke login
Route::get('/', fn() => redirect()->route('login'));

// Auth routes (guest only)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
});

// Authenticated routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Admin only routes
    Route::middleware('role:admin')->group(function () {
        // Manajemen Buku (Anggota 1 & 2)
        Route::resource('books', BookController::class);

        // Manajemen Kategori (Anggota 3 & 4)
        Route::resource('categories', CategoryController::class);

        // Manajemen Peminjaman (Anggota 5 & 6)
        Route::resource('borrowings', BorrowingController::class);
        Route::get('/borrowings/{borrowing}/return', fn($borrowing) => view('borrowings.return', [
            'borrowing' => \App\Models\Borrowing::with(['member.user', 'book'])->findOrFail($borrowing)
        ]))->name('borrowings.return.form');
        Route::post('/borrowings/{borrowing}/return', [BorrowingController::class, 'returnBook'])->name('borrowings.return');
    });

    // Member dapat melihat daftar buku
    Route::get('/catalog', [BookController::class, 'index'])->name('catalog');
    Route::get('/catalog/{book}', [BookController::class, 'show'])->name('catalog.show');
});
