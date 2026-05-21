@extends('layouts.app')
@section('title', 'Detail Kategori')
@section('page-title', 'Detail Kategori')
@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold"><i class="bi bi-tag me-2 text-primary"></i>{{ $category->name }}</h6>
                <div class="d-flex gap-2">
                    <a href="{{ route('categories.edit', $category) }}" class="btn btn-sm btn-warning">
                        <i class="bi bi-pencil me-1"></i>Edit
                    </a>
                    <a href="{{ route('categories.index') }}" class="btn btn-sm btn-outline-secondary">Kembali</a>
                </div>
            </div>
            <div class="card-body">
                <p class="text-muted mb-0">{{ $category->description ?? 'Tidak ada deskripsi.' }}</p>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h6 class="mb-0 fw-bold">Buku dalam Kategori Ini</h6>
            </div>
            <div class="card-body p-0">
                <table class="table mb-0">
                    <thead>
                        <tr><th>Judul</th><th>Pengarang</th><th>Stok</th></tr>
                    </thead>
                    <tbody>
                        @forelse($books as $book)
                        <tr>
                            <td><a href="{{ route('books.show', $book) }}" class="text-decoration-none fw-semibold">{{ $book->title }}</a></td>
                            <td class="small">{{ $book->author }}</td>
                            <td><span class="badge {{ $book->stock > 0 ? 'bg-success' : 'bg-danger' }}">{{ $book->stock }}</span></td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="text-center text-muted py-3">Belum ada buku di kategori ini</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($books->hasPages())
            <div class="card-footer bg-white">{{ $books->links() }}</div>
            @endif
        </div>
    </div>
</div>
@endsection
