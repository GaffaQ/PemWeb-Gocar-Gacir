@extends('layouts.app')

@section('title', 'Detail Buku')
@section('page-title', 'Detail Buku')

@section('content')
<div class="row g-4">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-body text-center p-4">
                @if($book->cover)
                    <img src="{{ asset('storage/' . $book->cover) }}" alt="{{ $book->title }}"
                        class="img-fluid rounded mb-3" style="max-height: 250px;">
                @else
                    <div class="d-flex align-items-center justify-content-center rounded mb-3"
                        style="height:200px; background:#f4f7fc;">
                        <i class="bi bi-book text-muted" style="font-size: 4rem;"></i>
                    </div>
                @endif
                <h5 class="fw-bold">{{ $book->title }}</h5>
                <p class="text-muted">{{ $book->author }}</p>
                <span class="badge bg-primary">{{ $book->category->name }}</span>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0 fw-bold">Informasi Buku</h6>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr><td class="text-muted fw-semibold" width="35%">ISBN</td><td>{{ $book->isbn ?? '-' }}</td></tr>
                    <tr><td class="text-muted fw-semibold">Penerbit</td><td>{{ $book->publisher ?? '-' }}</td></tr>
                    <tr><td class="text-muted fw-semibold">Tahun Terbit</td><td>{{ $book->year ?? '-' }}</td></tr>
                    <tr>
                        <td class="text-muted fw-semibold">Stok</td>
                        <td>
                            <span class="badge {{ $book->stock > 0 ? 'bg-success' : 'bg-danger' }} fs-6">
                                {{ $book->stock }} buku tersedia
                            </span>
                        </td>
                    </tr>
                    <tr><td class="text-muted fw-semibold">Deskripsi</td><td>{{ $book->description ?? '-' }}</td></tr>
                </table>
                <div class="d-flex gap-2 mt-2">
                    <a href="{{ route('books.edit', $book) }}" class="btn btn-warning btn-sm">
                        <i class="bi bi-pencil me-1"></i>Edit
                    </a>
                    <a href="{{ route('books.index') }}" class="btn btn-outline-secondary btn-sm">Kembali</a>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h6 class="mb-0 fw-bold"><i class="bi bi-clock-history me-2"></i>Riwayat Peminjaman</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr><th>Anggota</th><th>Tanggal Pinjam</th><th>Batas Kembali</th><th>Status</th></tr>
                        </thead>
                        <tbody>
                            @forelse($book->borrowings->take(5) as $b)
                            <tr>
                                <td class="small">{{ $b->member->user->name }}</td>
                                <td class="small">{{ $b->borrow_date->format('d M Y') }}</td>
                                <td class="small">{{ $b->due_date->format('d M Y') }}</td>
                                <td>
                                    @if($b->status === 'borrowed')
                                        <span class="badge bg-primary">Dipinjam</span>
                                    @elseif($b->status === 'returned')
                                        <span class="badge bg-success">Dikembalikan</span>
                                    @else
                                        <span class="badge bg-danger">Terlambat</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="text-center text-muted py-3">Belum ada riwayat peminjaman</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
