@extends('layouts.app')

@section('title', 'Edit Buku')
@section('page-title', 'Edit Buku')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0 fw-bold"><i class="bi bi-pencil-square me-2 text-warning"></i>Form Edit Buku</h6>
            </div>
            <div class="card-body">
                @if($errors->any())
                    <div class="alert alert-danger">
                        @foreach($errors->all() as $e)
                            <div><i class="bi bi-exclamation-circle me-1"></i>{{ $e }}</div>
                        @endforeach
                    </div>
                @endif

                <form action="{{ route('books.update', $book) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Judul Buku <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                                value="{{ old('title', $book->title) }}" required>
                            @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Tahun Terbit</label>
                            <input type="number" name="year" class="form-control"
                                value="{{ old('year', $book->year) }}" min="1900" max="{{ date('Y') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Pengarang <span class="text-danger">*</span></label>
                            <input type="text" name="author" class="form-control @error('author') is-invalid @enderror"
                                value="{{ old('author', $book->author) }}" required>
                            @error('author')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Penerbit</label>
                            <input type="text" name="publisher" class="form-control" value="{{ old('publisher', $book->publisher) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">ISBN</label>
                            <input type="text" name="isbn" class="form-control @error('isbn') is-invalid @enderror"
                                value="{{ old('isbn', $book->isbn) }}">
                            @error('isbn')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Kategori <span class="text-danger">*</span></label>
                            <select name="category_id" class="form-select" required>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ old('category_id', $book->category_id) == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Stok <span class="text-danger">*</span></label>
                            <input type="number" name="stock" class="form-control @error('stock') is-invalid @enderror"
                                value="{{ old('stock', $book->stock) }}" min="0" required>
                            @error('stock')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Deskripsi</label>
                            <textarea name="description" class="form-control" rows="3">{{ old('description', $book->description) }}</textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Cover Buku</label>
                            @if($book->cover)
                                <div class="mb-2">
                                    <img src="{{ asset('storage/' . $book->cover) }}" alt="Cover" style="height:80px; border-radius:6px;">
                                    <div class="form-text">Cover saat ini. Upload baru untuk mengganti.</div>
                                </div>
                            @endif
                            <input type="file" name="cover" class="form-control" accept="image/*">
                        </div>
                    </div>
                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-warning">
                            <i class="bi bi-save me-1"></i>Perbarui Buku
                        </button>
                        <a href="{{ route('books.index') }}" class="btn btn-outline-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
