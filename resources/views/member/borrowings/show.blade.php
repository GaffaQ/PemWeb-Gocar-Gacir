@extends('layouts.app')
@section('title', 'Detail Peminjaman')
@section('page-title', 'Detail Peminjaman')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        {{-- Status Banner --}}
        @if($borrowing->status === 'pending')
        <div class="alert alert-purple d-flex align-items-center mb-4" style="background:#f0ebff;border:1px solid #6f42c1;color:#6f42c1">
            <i class="bi bi-hourglass-split me-3 fs-4"></i>
            <div><strong>Menunggu Persetujuan Admin</strong><br><small>Permintaan Anda sedang diproses. Anda akan mendapat konfirmasi segera.</small></div>
        </div>
        @elseif($borrowing->status === 'overdue')
        <div class="alert alert-danger d-flex align-items-center mb-4">
            <i class="bi bi-exclamation-triangle-fill me-3 fs-4"></i>
            <div><strong>Buku Terlambat Dikembalikan!</strong><br>
            <small>Terlambat {{ $borrowing->due_date->diffInDays(now()) }} hari. Estimasi denda: <strong>Rp {{ number_format($estimatedFine, 0, ',', '.') }}</strong></small></div>
        </div>
        @elseif($borrowing->status === 'returned')
        <div class="alert alert-success d-flex align-items-center mb-4">
            <i class="bi bi-check-circle-fill me-3 fs-4"></i>
            <div><strong>Buku Sudah Dikembalikan</strong><br><small>Terima kasih telah mengembalikan buku tepat waktu.</small></div>
        </div>
        @endif

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold"><i class="bi bi-file-text me-2 text-primary"></i>Detail Peminjaman #{{ $borrowing->id }}</h6>
                <a href="{{ route('member.borrowings.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Kembali
                </a>
            </div>
            <div class="card-body">
                <div class="row g-4">
                    <div class="col-md-6">
                        <h6 class="text-muted text-uppercase small fw-semibold mb-3">Informasi Buku</h6>
                        <table class="table table-sm table-borderless">
                            <tr><td class="text-muted" style="width:40%">Judul</td><td class="fw-semibold">{{ $borrowing->book->title }}</td></tr>
                            <tr><td class="text-muted">Penulis</td><td>{{ $borrowing->book->author }}</td></tr>
                            <tr><td class="text-muted">Kategori</td><td>{{ $borrowing->book->category->name ?? '-' }}</td></tr>
                            <tr><td class="text-muted">ISBN</td><td>{{ $borrowing->book->isbn ?? '-' }}</td></tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted text-uppercase small fw-semibold mb-3">Informasi Peminjaman</h6>
                        <table class="table table-sm table-borderless">
                            <tr><td class="text-muted" style="width:45%">Tanggal Pinjam</td><td>{{ $borrowing->borrow_date->format('d M Y') }}</td></tr>
                            <tr><td class="text-muted">Batas Kembali</td>
                                <td class="{{ $borrowing->status === 'overdue' ? 'text-danger fw-bold' : '' }}">
                                    {{ $borrowing->due_date->format('d M Y') }}
                                </td>
                            </tr>
                            <tr><td class="text-muted">Status</td><td>
                                @if($borrowing->status === 'borrowed') <span class="badge bg-primary">Dipinjam</span>
                                @elseif($borrowing->status === 'returned') <span class="badge bg-success">Dikembalikan</span>
                                @elseif($borrowing->status === 'pending') <span class="badge" style="background:#6f42c1">Menunggu</span>
                                @else <span class="badge bg-danger">Terlambat</span> @endif
                            </td></tr>
                        </table>
                    </div>
                </div>

                @if($borrowing->status === 'returned' && $borrowing->return)
                <hr>
                <div class="row g-4">
                    <div class="col-md-6">
                        <h6 class="text-muted text-uppercase small fw-semibold mb-3">Informasi Pengembalian</h6>
                        <table class="table table-sm table-borderless">
                            <tr><td class="text-muted" style="width:45%">Tanggal Kembali</td><td>{{ \Carbon\Carbon::parse($borrowing->return->return_date)->format('d M Y') }}</td></tr>
                            <tr><td class="text-muted">Denda</td>
                                <td class="{{ $borrowing->return->fine > 0 ? 'text-danger fw-bold' : 'text-success' }}">
                                    {{ $borrowing->return->fine > 0 ? 'Rp ' . number_format($borrowing->return->fine, 0, ',', '.') : 'Tidak ada denda' }}
                                </td>
                            </tr>
                            @if($borrowing->return->notes)
                            <tr><td class="text-muted">Catatan</td><td>{{ $borrowing->return->notes }}</td></tr>
                            @endif
                        </table>
                    </div>
                </div>
                @endif

                @if(in_array($borrowing->status, ['borrowed', 'overdue']))
                <hr>
                <div class="bg-light rounded p-3">
                    <h6 class="fw-semibold mb-2"><i class="bi bi-info-circle me-2 text-primary"></i>Cara Mengembalikan Buku</h6>
                    <p class="text-muted small mb-0">Hubungi petugas perpustakaan untuk mengembalikan buku. Jangan lupa bawa kartu anggota Anda. Denda keterlambatan akan dihitung otomatis oleh sistem.</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
