@extends('layouts.app')
@section('title', 'Detail Peminjaman')
@section('page-title', 'Detail Peminjaman')
@section('content')
<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold"><i class="bi bi-info-circle me-2 text-primary"></i>Detail Peminjaman</h6>
                <a href="{{ route('borrowings.index') }}" class="btn btn-sm btn-outline-secondary">Kembali</a>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr><td class="fw-semibold text-muted" width="40%">Anggota</td><td>{{ $borrowing->member->user->name }}</td></tr>
                    <tr><td class="fw-semibold text-muted">Kode Anggota</td><td>{{ $borrowing->member->member_code }}</td></tr>
                    <tr><td class="fw-semibold text-muted">Buku</td><td>{{ $borrowing->book->title }}</td></tr>
                    <tr><td class="fw-semibold text-muted">Pengarang</td><td>{{ $borrowing->book->author }}</td></tr>
                    <tr><td class="fw-semibold text-muted">Tanggal Pinjam</td><td>{{ $borrowing->borrow_date->format('d F Y') }}</td></tr>
                    <tr><td class="fw-semibold text-muted">Batas Kembali</td><td>{{ $borrowing->due_date->format('d F Y') }}</td></tr>
                    <tr>
                        <td class="fw-semibold text-muted">Status</td>
                        <td>
                            @if($borrowing->status === 'borrowed')
                                <span class="badge bg-primary fs-6">Dipinjam</span>
                            @elseif($borrowing->status === 'returned')
                                <span class="badge bg-success fs-6">Dikembalikan</span>
                            @else
                                <span class="badge bg-danger fs-6">Terlambat</span>
                            @endif
                        </td>
                    </tr>
                </table>

                @if($borrowing->return)
                <hr>
                <h6 class="fw-bold mb-3">Informasi Pengembalian</h6>
                <table class="table table-borderless">
                    <tr><td class="fw-semibold text-muted" width="40%">Tanggal Kembali</td><td>{{ $borrowing->return->return_date->format('d F Y') }}</td></tr>
                    <tr>
                        <td class="fw-semibold text-muted">Denda</td>
                        <td>
                            @if($borrowing->return->fine > 0)
                                <span class="text-danger fw-bold">Rp {{ number_format($borrowing->return->fine, 0, ',', '.') }}</span>
                            @else
                                <span class="text-success">Tidak ada denda</span>
                            @endif
                        </td>
                    </tr>
                    <tr><td class="fw-semibold text-muted">Catatan</td><td>{{ $borrowing->return->notes ?? '-' }}</td></tr>
                </table>
                @endif

                @if($borrowing->status !== 'returned')
                <div class="d-flex gap-2 mt-3">
                    <a href="{{ route('borrowings.return.form', $borrowing) }}" class="btn btn-success">
                        <i class="bi bi-arrow-return-left me-1"></i>Proses Pengembalian
                    </a>
                    <a href="{{ route('borrowings.edit', $borrowing) }}" class="btn btn-warning">
                        <i class="bi bi-pencil me-1"></i>Edit
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
