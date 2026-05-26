@extends('layouts.app')

@section('title', 'Kelola Buku')
@section('page-title', 'Kelola Buku')

@section('content')
<div class="card">
    <div class="card-header">
        <div class="row align-items-center">
            <div class="col">
                <h6 class="mb-0 fw-bold"><i class="bi bi-book me-2 text-primary"></i>Daftar Buku</h6>
            </div>
            <div class="col-auto">
                @if(auth()->user()->isAdmin())
                <a href="{{ route('books.create') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-lg me-1"></i>Tambah Buku
                </a>
                @endif
            </div>
        </div>
        <!-- Filter & Search -->
        <form method="GET" class="mt-3">
            <div class="row g-2">
                <div class="col-md-5">
                    <input type="text" name="search" class="form-control form-control-sm"
                        placeholder="Cari judul, pengarang, ISBN..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="category_id" class="form-select form-select-sm">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-search me-1"></i>Cari
                    </button>
                    <a href="{{ route('books.index') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
                </div>
            </div>
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Judul Buku</th>
                        <th>Pengarang</th>
                        <th>Kategori</th>
                        <th>Stok</th>
                        <th>Tahun</th>
                        @if(auth()->user()->isAdmin())
                        <th class="text-center">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse($books as $book)
                    <tr>
                        <td class="text-muted small">{{ $loop->iteration }}</td>
                        <td>
                            <div class="fw-semibold">{{ $book->title }}</div>
                            @if($book->isbn)<div class="text-muted small">ISBN: {{ $book->isbn }}</div>@endif
                        </td>
                        <td class="small">{{ $book->author }}</td>
                        <td><span class="badge bg-light text-dark border">{{ $book->category->name }}</span></td>
                        <td>
                            <span class="badge {{ $book->stock > 0 ? 'bg-success' : 'bg-danger' }}">
                                {{ $book->stock }} tersedia
                            </span>
                        </td>
                        <td class="small">{{ $book->year ?? '-' }}</td>
                        @if(auth()->user()->isAdmin())
                        <td class="text-center">
                            <a href="{{ route('books.show', $book) }}" class="btn btn-sm btn-outline-info" title="Detail">
                                <i class="bi bi-eye"></i>
                            </a>
                            @if(auth()->user()->isMember() && $book->stock > 0)
                            <a href="{{ route('member.borrowings.create.book', $book) }}" class="btn btn-sm btn-outline-success" title="Pinjam Buku">
                                <i class="bi bi-plus-circle"></i> Pinjam
                            </a>
                            @endif
                            <a href="{{ route('books.edit', $book) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('books.destroy', $book) }}" method="POST" class="d-inline"
                                onsubmit="return confirm('Yakin ingin menghapus buku ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                        @endif
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            <i class="bi bi-inbox fs-2 d-block mb-2"></i>Belum ada data buku
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($books->hasPages())
    <div class="card-footer bg-white">
        {{ $books->withQueryString()->links() }}
    </div>
    @endif
</div>
@endsection
