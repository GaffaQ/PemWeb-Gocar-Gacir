@extends('layouts.app')

@section('title', 'Dashboard - Perpustakaan')
@section('page-title', 'Dashboard')

@section('content')
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="stat-card" style="background: linear-gradient(135deg, #1a3a5c, #2d6a9f);">
            <div class="mb-1 text-white-50 small">Total Buku</div>
            <div class="fs-2 fw-bold text-white">{{ $totalBooks }}</div>
            <i class="bi bi-book-fill icon"></i>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card" style="background: linear-gradient(135deg, #1e7e34, #28a745);">
            <div class="mb-1 text-white-50 small">Total Anggota</div>
            <div class="fs-2 fw-bold text-white">{{ $totalMembers }}</div>
            <i class="bi bi-people-fill icon"></i>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card" style="background: linear-gradient(135deg, #e08600, #ffc107);">
            <div class="mb-1 text-white-50 small">Sedang Dipinjam</div>
            <div class="fs-2 fw-bold text-white">{{ $totalBorrowing }}</div>
            <i class="bi bi-arrow-left-right icon"></i>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card" style="background: linear-gradient(135deg, #b02a37, #dc3545);">
            <div class="mb-1 text-white-50 small">Terlambat</div>
            <div class="fs-2 fw-bold text-white">{{ $totalOverdue }}</div>
            <i class="bi bi-exclamation-triangle-fill icon"></i>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-bold"><i class="bi bi-clock-history me-2 text-primary"></i>Peminjaman Terbaru</h6>
        @if(auth()->user()->isAdmin())
            <a href="{{ route('borrowings.index') }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
        @endif
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>Anggota</th>
                        <th>Buku</th>
                        <th>Tanggal Pinjam</th>
                        <th>Batas Kembali</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentBorrowings as $b)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="avatar">{{ strtoupper(substr($b->member->user->name, 0, 1)) }}</div>
                                <div>
                                    <div class="fw-semibold small">{{ $b->member->user->name }}</div>
                                    <div class="text-muted" style="font-size:0.75rem;">{{ $b->member->member_code }}</div>
                                </div>
                            </div>
                        </td>
                        <td><span class="fw-semibold small">{{ Str::limit($b->book->title, 35) }}</span></td>
                        <td><span class="small">{{ $b->borrow_date->format('d M Y') }}</span></td>
                        <td><span class="small">{{ $b->due_date->format('d M Y') }}</span></td>
                        <td>
                            @if($b->status === 'borrowed')
                                <span class="badge bg-primary badge-status">Dipinjam</span>
                            @elseif($b->status === 'returned')
                                <span class="badge bg-success badge-status">Dikembalikan</span>
                            @else
                                <span class="badge bg-danger badge-status">Terlambat</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">
                            <i class="bi bi-inbox fs-2 d-block mb-2"></i>Belum ada data peminjaman
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
