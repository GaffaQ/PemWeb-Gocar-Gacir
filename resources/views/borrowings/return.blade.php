@extends('layouts.app')
@section('title', 'Proses Pengembalian')
@section('page-title', 'Proses Pengembalian Buku')
@section('content')
<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0 fw-bold"><i class="bi bi-arrow-return-left me-2 text-success"></i>Form Pengembalian Buku</h6>
            </div>
            <div class="card-body">
                @if($errors->any())
                    <div class="alert alert-danger">
                        @foreach($errors->all() as $e)<div><i class="bi bi-exclamation-circle me-1"></i>{{ $e }}</div>@endforeach
                    </div>
                @endif

                <div class="card bg-light border-0 mb-4">
                    <div class="card-body">
                        <div class="row g-2">
                            <div class="col-md-6">
                                <div class="small text-muted">Anggota</div>
                                <div class="fw-semibold">{{ $borrowing->member->user->name }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="small text-muted">Buku</div>
                                <div class="fw-semibold">{{ $borrowing->book->title }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="small text-muted">Tanggal Pinjam</div>
                                <div>{{ $borrowing->borrow_date->format('d M Y') }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="small text-muted">Batas Kembali</div>
                                <div class="{{ $borrowing->status === 'overdue' ? 'text-danger fw-bold' : '' }}">
                                    {{ $borrowing->due_date->format('d M Y') }}
                                    @if($borrowing->status === 'overdue')
                                        <span class="badge bg-danger ms-1">TERLAMBAT</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="alert alert-warning small">
                    <i class="bi bi-info-circle me-1"></i>
                    Denda keterlambatan <strong>Rp 1.000 per hari</strong>. Dihitung otomatis dari tanggal kembali.
                </div>

                <form action="{{ route('borrowings.return', $borrowing) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tanggal Pengembalian <span class="text-danger">*</span></label>
                        <input type="date" name="return_date" class="form-control @error('return_date') is-invalid @enderror"
                            value="{{ old('return_date', date('Y-m-d')) }}" required>
                        @error('return_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Catatan</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="Kondisi buku, catatan tambahan...">{{ old('notes') }}</textarea>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-check-circle me-1"></i>Proses Pengembalian
                        </button>
                        <a href="{{ route('borrowings.index') }}" class="btn btn-outline-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
