@extends('layouts.app')
@section('title', 'Pinjam Buku')
@section('page-title', 'Request Peminjaman Buku')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0 fw-bold"><i class="bi bi-plus-circle me-2 text-primary"></i>Form Peminjaman Buku</h6>
            </div>
            <div class="card-body p-4">
                <div class="alert alert-info mb-4">
                    <i class="bi bi-info-circle-fill me-2"></i>
                    Permintaan Anda akan diproses oleh admin. Buku akan tersedia setelah disetujui.
                    <strong>Denda keterlambatan: Rp 1.000/hari.</strong>
                </div>

                @if($errors->any())
                <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
                @endif

                <form action="{{ route('member.borrowings.store') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Pilih Buku <span class="text-danger">*</span></label>
                        <select name="book_id" class="form-select @error('book_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Buku --</option>
                            @foreach($books as $book)
                            <option value="{{ $book->id }}" {{ (old('book_id', $selectedBook?->id) == $book->id) ? 'selected' : '' }}>
                                {{ $book->title }} — {{ $book->author }} (Stok: {{ $book->stock }})
                            </option>
                            @endforeach
                        </select>
                        @error('book_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Tanggal Pengembalian <span class="text-danger">*</span></label>
                        <input type="date" name="due_date" class="form-control @error('due_date') is-invalid @enderror"
                            value="{{ old('due_date', now()->addDays(7)->format('Y-m-d')) }}"
                            min="{{ now()->addDay()->format('Y-m-d') }}"
                            max="{{ now()->addDays(30)->format('Y-m-d') }}" required>
                        <div class="form-text">Maksimal peminjaman 30 hari dari hari ini.</div>
                        @error('due_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-send me-1"></i> Kirim Permintaan
                        </button>
                        <a href="{{ route('catalog') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i> Kembali ke Katalog
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
