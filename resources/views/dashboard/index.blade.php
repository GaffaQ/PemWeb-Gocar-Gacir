@extends('layouts.app')
@section('title', 'Dashboard - Perpustakaan')
@section('page-title', 'Dashboard')

@section('content')
@if(auth()->user()->isAdmin())
{{-- ============ ADMIN DASHBOARD ============ --}}
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

@if($totalPending > 0)
<div class="card mb-4 border-0" style="border-left: 4px solid #6f42c1 !important; background: #f8f5ff;">
    <div class="card-body py-3">
        <div class="d-flex align-items-center justify-content-between">
            <div><i class="bi bi-bell-fill text-purple me-2" style="color:#6f42c1"></i>
                <strong>{{ $totalPending }} permintaan peminjaman</strong> menunggu persetujuan Anda.
            </div>
            <a href="{{ route('borrowings.index') }}?status=pending" class="btn btn-sm" style="background:#6f42c1;color:#fff;">Lihat Semua</a>
        </div>
    </div>
</div>
@endif

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold"><i class="bi bi-clock-history me-2 text-primary"></i>Peminjaman Terbaru</h6>
                <a href="{{ route('borrowings.index') }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
            </div>
            <div class="card-body p-0">
                <table class="table mb-0">
                    <thead><tr><th>Anggota</th><th>Buku</th><th>Batas Kembali</th><th>Status</th></tr></thead>
                    <tbody>
                        @forelse($recentBorrowings as $b)
                        <tr>
                            <td><div class="d-flex align-items-center gap-2">
                                <div class="avatar">{{ strtoupper(substr($b->member->user->name, 0, 1)) }}</div>
                                <span class="small fw-semibold">{{ $b->member->user->name }}</span>
                            </div></td>
                            <td><span class="small">{{ Str::limit($b->book->title, 30) }}</span></td>
                            <td><span class="small">{{ $b->due_date->format('d M Y') }}</span></td>
                            <td>
                                @if($b->status === 'borrowed') <span class="badge bg-primary badge-status">Dipinjam</span>
                                @elseif($b->status === 'returned') <span class="badge bg-success badge-status">Dikembalikan</span>
                                @elseif($b->status === 'pending') <span class="badge badge-status badge-pending">Pending</span>
                                @else <span class="badge bg-danger badge-status">Terlambat</span> @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center text-muted py-4"><i class="bi bi-inbox fs-2 d-block mb-2"></i>Belum ada data</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold"><i class="bi bi-hourglass-split me-2" style="color:#6f42c1"></i>Request Pending</h6>
                <a href="{{ route('admin.members.index') }}" class="btn btn-sm btn-outline-secondary">Kelola Anggota</a>
            </div>
            <div class="card-body p-0">
                <table class="table mb-0">
                    <thead><tr><th>Anggota</th><th>Buku</th><th>Aksi</th></tr></thead>
                    <tbody>
                        @forelse($pendingRequests as $p)
                        <tr>
                            <td><span class="small fw-semibold">{{ $p->member->user->name }}</span></td>
                            <td><span class="small">{{ Str::limit($p->book->title, 20) }}</span></td>
                            <td>
                                <form action="{{ route('admin.borrowings.approve', $p->id) }}" method="POST" class="d-inline">@csrf
                                    <button class="btn btn-xs btn-success" style="font-size:0.7rem;padding:2px 8px">✓</button>
                                </form>
                                <form action="{{ route('admin.borrowings.reject', $p->id) }}" method="POST" class="d-inline">@csrf
                                    <button class="btn btn-xs btn-danger" style="font-size:0.7rem;padding:2px 8px" onclick="return confirm('Tolak request ini?')">✗</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="text-center text-muted py-3 small">Tidak ada request pending</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@else
{{-- ============ MEMBER DASHBOARD ============ --}}
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="stat-card" style="background: linear-gradient(135deg, #1a3a5c, #2d6a9f);">
            <div class="mb-1 text-white-50 small">Sedang Dipinjam</div>
            <div class="fs-2 fw-bold text-white">{{ $activeBorrowings }}</div>
            <i class="bi bi-book-fill icon"></i>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card" style="background: linear-gradient(135deg, #b02a37, #dc3545);">
            <div class="mb-1 text-white-50 small">Terlambat</div>
            <div class="fs-2 fw-bold text-white">{{ $overdueBorrowings }}</div>
            <i class="bi bi-exclamation-triangle-fill icon"></i>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card" style="background: linear-gradient(135deg, #5a0a8f, #6f42c1);">
            <div class="mb-1 text-white-50 small">Menunggu Approval</div>
            <div class="fs-2 fw-bold text-white">{{ $pendingCount }}</div>
            <i class="bi bi-hourglass-split icon"></i>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card" style="background: linear-gradient(135deg, #9e1b1b, #e74c3c);">
            <div class="mb-1 text-white-50 small">Total Denda</div>
            <div class="fs-2 fw-bold text-white">Rp {{ number_format($totalFine, 0, ',', '.') }}</div>
            <i class="bi bi-cash-stack icon"></i>
        </div>
    </div>
</div>

@if($overdueBorrowings > 0)
<div class="alert alert-danger d-flex align-items-center mb-4">
    <i class="bi bi-exclamation-triangle-fill me-3 fs-5"></i>
    <div>Anda memiliki <strong>{{ $overdueBorrowings }} buku terlambat dikembalikan</strong>. Denda berjalan Rp 1.000/hari.
    <a href="{{ route('member.borrowings.index') }}" class="alert-link ms-1">Lihat detail →</a></div>
</div>
@endif

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold"><i class="bi bi-clock-history me-2 text-primary"></i>Peminjaman Terbaru Saya</h6>
                <a href="{{ route('member.borrowings.index') }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
            </div>
            <div class="card-body p-0">
                <table class="table mb-0">
                    <thead><tr><th>Buku</th><th>Batas Kembali</th><th>Status</th><th></th></tr></thead>
                    <tbody>
                        @forelse($memberBorrowings as $b)
                        <tr>
                            <td><span class="fw-semibold small">{{ Str::limit($b->book->title, 35) }}</span></td>
                            <td><span class="small {{ $b->status === 'overdue' ? 'text-danger fw-bold' : '' }}">{{ $b->due_date->format('d M Y') }}</span></td>
                            <td>
                                @if($b->status === 'borrowed') <span class="badge bg-primary badge-status">Dipinjam</span>
                                @elseif($b->status === 'returned') <span class="badge bg-success badge-status">Dikembalikan</span>
                                @elseif($b->status === 'pending') <span class="badge badge-status badge-pending">Menunggu</span>
                                @else <span class="badge bg-danger badge-status">Terlambat</span> @endif
                            </td>
                            <td><a href="{{ route('member.borrowings.show', $b->id) }}" class="btn btn-xs btn-outline-secondary" style="font-size:0.75rem">Detail</a></td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center text-muted py-4"><i class="bi bi-inbox fs-2 d-block mb-2"></i>Belum ada peminjaman</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card text-center p-4">
            <i class="bi bi-plus-circle-fill mb-3" style="font-size:3rem;color:var(--primary)"></i>
            <h6 class="fw-bold">Pinjam Buku Baru</h6>
            <p class="text-muted small mb-3">Cari buku yang ingin kamu pinjam dari katalog kami</p>
            <a href="{{ route('member.borrowings.create') }}" class="btn btn-primary">Pinjam Sekarang</a>
            <a href="{{ route('catalog') }}" class="btn btn-outline-secondary mt-2">Lihat Katalog</a>
        </div>
    </div>
</div>
@endif
@endsection
