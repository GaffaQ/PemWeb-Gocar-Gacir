@extends('layouts.app')

@section('title', 'Kategori Buku')
@section('page-title', 'Kategori Buku')

@section('content')
<div class="card">
    <div class="card-header">
        <div class="row align-items-center">
            <div class="col">
                <h6 class="mb-0 fw-bold"><i class="bi bi-tags me-2 text-primary"></i>Daftar Kategori</h6>
            </div>
            <div class="col-auto">
                <a href="{{ route('categories.create') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-lg me-1"></i>Tambah Kategori
                </a>
            </div>
        </div>
        <form method="GET" class="mt-3">
            <div class="row g-2">
                <div class="col-md-5">
                    <input type="text" name="search" class="form-control form-control-sm"
                        placeholder="Cari kategori..." value="{{ request('search') }}">
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-outline-primary btn-sm"><i class="bi bi-search"></i> Cari</button>
                    <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
                </div>
            </div>
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr><th>#</th><th>Nama Kategori</th><th>Deskripsi</th><th>Jumlah Buku</th><th class="text-center">Aksi</th></tr>
                </thead>
                <tbody>
                    @forelse($categories as $cat)
                    <tr>
                        <td class="text-muted small">{{ $loop->iteration }}</td>
                        <td class="fw-semibold">{{ $cat->name }}</td>
                        <td class="small text-muted">{{ Str::limit($cat->description, 60) ?? '-' }}</td>
                        <td><span class="badge bg-primary">{{ $cat->books_count }} buku</span></td>
                        <td class="text-center">
                            <a href="{{ route('categories.show', $cat) }}" class="btn btn-sm btn-outline-info" title="Detail">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('categories.edit', $cat) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('categories.destroy', $cat) }}" method="POST" class="d-inline"
                                onsubmit="return confirm('Yakin ingin menghapus kategori ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center text-muted py-4"><i class="bi bi-inbox fs-2 d-block mb-2"></i>Belum ada kategori</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($categories->hasPages())
    <div class="card-footer bg-white">{{ $categories->withQueryString()->links() }}</div>
    @endif
</div>
@endsection
