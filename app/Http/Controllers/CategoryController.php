<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    // ============================================================
    // ANGGOTA 3 - Bagian C + R
    // ============================================================

    /**
     * R - Menampilkan daftar kategori
     */
    public function index(Request $request)
    {
        $query = Category::withCount('books');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $categories = $query->latest()->paginate(10);

        return view('categories.index', compact('categories'));
    }

    /**
     * C - Menampilkan form tambah kategori
     */
    public function create()
    {
        return view('categories.create');
    }

    /**
     * C - Menyimpan kategori baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|min:2|max:100|unique:categories,name',
            'description' => 'nullable|max:500',
        ]);

        Category::create($request->only('name', 'description'));

        return redirect()->route('categories.index')->with('success', 'Kategori berhasil ditambahkan!');
    }

    /**
     * R - Menampilkan detail kategori beserta buku-bukunya
     */
    public function show(Category $category)
    {
        $books = $category->books()->paginate(10);
        return view('categories.show', compact('category', 'books'));
    }

    // ============================================================
    // ANGGOTA 4 - Bagian U + D
    // ============================================================

    /**
     * U - Menampilkan form edit kategori
     */
    public function edit(Category $category)
    {
        return view('categories.edit', compact('category'));
    }

    /**
     * U - Memperbarui data kategori
     */
    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name'        => 'required|min:2|max:100|unique:categories,name,' . $category->id,
            'description' => 'nullable|max:500',
        ]);

        $category->update($request->only('name', 'description'));

        return redirect()->route('categories.index')->with('success', 'Kategori berhasil diperbarui!');
    }

    /**
     * D - Menghapus kategori
     */
    public function destroy(Category $category)
    {
        if ($category->books()->count() > 0) {
            return redirect()->route('categories.index')
                ->with('error', 'Kategori tidak dapat dihapus karena masih memiliki buku!');
        }

        $category->delete();

        return redirect()->route('categories.index')->with('success', 'Kategori berhasil dihapus!');
    }
}
