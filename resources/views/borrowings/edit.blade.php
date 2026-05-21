@extends('layouts.app')
@section('title', 'Edit Peminjaman')
@section('page-title', 'Edit Peminjaman')
@section('content')
<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0 fw-bold"><i class="bi bi-pencil-square me-2 text-warning"></i>Edit Data Peminjaman</h6>
            </div>
            <div class="card-body">
                @if($errors->any())
                    <div class="alert alert-danger">
                        @foreach($errors->all() as $e)<div><i class="bi bi-exclamation-circle me-1"></i>{{ $e }}</div>@endforeach
                    </div>
                @endif

                <div class="alert alert-light border mb-4">
                    <strong>Anggota:</strong> {{ $borrowing->member->user->name }}<br>
                    <strong>Buku:</strong> {{ $borrowing->book->title }}<br>
                    <strong>Tanggal Pinjam:</strong> {{ $borrowing->borrow_date->format('d M Y') }}
                </div>

                <form action="{{ route('borrowings.update', $borrowing) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Batas Pengembalian <span class="text-danger">*</span></label>
                        <input type="date" name="due_date" class="form-control @error('due_date') is-invalid @enderror"
                            value="{{ old('due_date', $borrowing->due_date->format('Y-m-d')) }}" required>
                        @error('due_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Status</label>
                        <select name="status" class="form-select">
                            <option value="borrowed" {{ $borrowing->status === 'borrowed' ? 'selected' : '' }}>Dipinjam</option>
                            <option value="overdue" {{ $borrowing->status === 'overdue' ? 'selected' : '' }}>Terlambat</option>
                        </select>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-warning"><i class="bi bi-save me-1"></i>Perbarui</button>
                        <a href="{{ route('borrowings.index') }}" class="btn btn-outline-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
