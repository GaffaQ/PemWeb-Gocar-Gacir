<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BookController extends Controller
{
    // ============================================================
    // ANGGOTA 1 - Bagian C + R
    // ============================================================

    /**
     * R - Menampilkan daftar semua buku
     */
    public function index(Request $request)
    {
        $query = Book::with('category');

        // Fitur pencarian
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('author', 'like', '%' . $request->search . '%')
                  ->orWhere('isbn', 'like', '%' . $request->search . '%');
        }

        // Filter berdasarkan kategori
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $books      = $query->latest()->paginate(10);
        $categories = Category::all();

        return view('books.index', compact('books', 'categories'));
    }

    /**
     * C - Menampilkan form tambah buku
     */
    public function create()
    {
        $categories = Category::all();
        return view('books.create', compact('categories'));
    }

    /**
     * C - Menyimpan data buku baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'title'       => 'required|min:3|max:255',
            'author'      => 'required|min:3|max:255',
            'isbn'        => 'nullable|unique:books,isbn|max:20',
            'category_id' => 'required|exists:categories,id',
            'stock'       => 'required|integer|min:0',
            'year'        => 'nullable|integer|min:1900|max:' . date('Y'),
            'publisher'   => 'nullable|max:255',
            'description' => 'nullable',
            'cover'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $data = $request->except('cover');

        if ($request->hasFile('cover')) {
            $data['cover'] = $request->file('cover')->store('covers', 'public');
        }

        Book::create($data);

        return redirect()->route('books.index')->with('success', 'Buku berhasil ditambahkan!');
    }

    /**
     * R - Menampilkan detail buku
     */
    public function show(Book $book)
    {
        $book->load('category', 'borrowings.member.user');
        return view('books.show', compact('book'));
    }

    // ============================================================
    // ANGGOTA 2 - Bagian U + D
    // ============================================================

    /**
     * U - Menampilkan form edit buku
     */
    public function edit(Book $book)
    {
        $categories = Category::all();
        return view('books.edit', compact('book', 'categories'));
    }

    /**
     * U - Memperbarui data buku
     */
    public function update(Request $request, Book $book)
    {
        $request->validate([
            'title'       => 'required|min:3|max:255',
            'author'      => 'required|min:3|max:255',
            'isbn'        => 'nullable|unique:books,isbn,' . $book->id . '|max:20',
            'category_id' => 'required|exists:categories,id',
            'stock'       => 'required|integer|min:0',
            'year'        => 'nullable|integer|min:1900|max:' . date('Y'),
            'publisher'   => 'nullable|max:255',
            'description' => 'nullable',
            'cover'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $data = $request->except('cover');

        if ($request->hasFile('cover')) {
            // Hapus cover lama jika ada
            if ($book->cover) {
                Storage::disk('public')->delete($book->cover);
            }
            $data['cover'] = $request->file('cover')->store('covers', 'public');
        }

        $book->update($data);

        return redirect()->route('books.index')->with('success', 'Data buku berhasil diperbarui!');
    }

    /**
     * D - Menghapus data buku
     */
    public function destroy(Book $book)
    {
        if ($book->cover) {
            Storage::disk('public')->delete($book->cover);
        }

        $book->delete();

        return redirect()->route('books.index')->with('success', 'Buku berhasil dihapus!');
    }
}
