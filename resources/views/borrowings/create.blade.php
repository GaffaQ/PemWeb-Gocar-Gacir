@extends('layouts.app')
@section('title', 'Tambah Peminjaman')
@section('page-title', 'Tambah Peminjaman')
@section('content')
<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0 fw-bold"><i class="bi bi-plus-circle me-2 text-primary"></i>Form Peminjaman Buku</h6>
            </div>
            <div class="card-body">
                @if($errors->any())
                    <div class="alert alert-danger">
                        @foreach($errors->all() as $e)<div><i class="bi bi-exclamation-circle me-1"></i>{{ $e }}</div>@endforeach
                    </div>
                @endif
                <form action="{{ route('borrowings.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Anggota <span class="text-danger">*</span></label>
                        <select name="member_id" class="form-select @error('member_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Anggota --</option>
                            @foreach($members as $member)
                                <option value="{{ $member->id }}" {{ old('member_id') == $member->id ? 'selected' : '' }}>
                                    {{ $member->user->name }} ({{ $member->member_code }})
                                </option>
                            @endforeach
                        </select>
                        @error('member_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Buku <span class="text-danger">*</span></label>
                        <select name="book_id" class="form-select @error('book_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Buku --</option>
                            @foreach($books as $book)
                                <option value="{{ $book->id }}" {{ old('book_id') == $book->id ? 'selected' : '' }}>
                                    {{ $book->title }} - {{ $book->author }} (Stok: {{ $book->stock }})
                                </option>
                            @endforeach
                        </select>
                        @error('book_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tanggal Pinjam <span class="text-danger">*</span></label>
                            <input type="date" name="borrow_date" class="form-control @error('borrow_date') is-invalid @enderror"
                                value="{{ old('borrow_date', date('Y-m-d')) }}" required>
                            @error('borrow_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Batas Pengembalian <span class="text-danger">*</span></label>
                            <input type="date" name="due_date" class="form-control @error('due_date') is-invalid @enderror"
                                value="{{ old('due_date', date('Y-m-d', strtotime('+7 days'))) }}" required>
                            @error('due_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="alert alert-info mt-3 small">
                        <i class="bi bi-info-circle me-1"></i>
                        Denda keterlambatan: <strong>Rp 1.000 per hari</strong>
                    </div>
                    <div class="d-flex gap-2 mt-3">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Simpan Peminjaman</button>
                        <a href="{{ route('borrowings.index') }}" class="btn btn-outline-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
