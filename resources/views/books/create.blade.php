@extends('layouts.app')

@section('title', 'Tambah Buku')
@section('page-title', 'Tambah Buku')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0 fw-bold"><i class="bi bi-plus-circle me-2 text-primary"></i>Form Tambah Buku</h6>
            </div>
            <div class="card-body">
                @if($errors->any())
                    <div class="alert alert-danger">
                        @foreach($errors->all() as $e)
                            <div><i class="bi bi-exclamation-circle me-1"></i>{{ $e }}</div>
                        @endforeach
                    </div>
                @endif

                <form action="{{ route('books.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Judul Buku <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                                value="{{ old('title') }}" placeholder="Judul buku" required>
                            @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Tahun Terbit</label>
                            <input type="number" name="year" class="form-control @error('year') is-invalid @enderror"
                                value="{{ old('year') }}" placeholder="{{ date('Y') }}" min="1900" max="{{ date('Y') }}">
                            @error('year')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Pengarang <span class="text-danger">*</span></label>
                            <input type="text" name="author" class="form-control @error('author') is-invalid @enderror"
                                value="{{ old('author') }}" placeholder="Nama pengarang" required>
                            @error('author')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Penerbit</label>
                            <input type="text" name="publisher" class="form-control" value="{{ old('publisher') }}" placeholder="Nama penerbit">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">ISBN</label>
                            <input type="text" name="isbn" class="form-control @error('isbn') is-invalid @enderror"
                                value="{{ old('isbn') }}" placeholder="978-xxx-xxx">
                            @error('isbn')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Kategori <span class="text-danger">*</span></label>
                            <select name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Kategori --</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Stok <span class="text-danger">*</span></label>
                            <input type="number" name="stock" class="form-control @error('stock') is-invalid @enderror"
                                value="{{ old('stock', 1) }}" min="0" required>
                            @error('stock')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Deskripsi</label>
                            <textarea name="description" class="form-control" rows="3" placeholder="Sinopsis atau deskripsi buku">{{ old('description') }}</textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Cover Buku</label>
                            <input type="file" name="cover" class="form-control @error('cover') is-invalid @enderror"
                                accept="image/*">
                            <div class="form-text">Format: JPG, PNG, WebP. Maks 2MB</div>
                            @error('cover')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i>Simpan Buku
                        </button>
                        <a href="{{ route('books.index') }}" class="btn btn-outline-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
