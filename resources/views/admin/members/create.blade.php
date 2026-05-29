@extends('layouts.app')
@section('title', 'Tambah Anggota')
@section('page-title', 'Tambah Anggota Baru')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold"><i class="bi bi-person-plus-fill me-2 text-primary"></i>Form Registrasi Anggota</h6>
                <a href="{{ route('admin.members.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i>Kembali
                </a>
            </div>
            <div class="card-body p-4">
                <div class="alert alert-info mb-4">
                    <i class="bi bi-info-circle-fill me-2"></i>
                    Anggota yang didaftarkan akan otomatis memiliki status <strong>Aktif</strong>. Kode anggota (member code) unik akan digenerate otomatis setelah formulir disimpan.
                </div>

                @if($errors->any())
                <div class="alert alert-danger mb-4">
                    <ul class="mb-0">
                        @foreach($errors->all() as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <form action="{{ route('admin.members.store') }}" method="POST">
                    @csrf

                    <div class="row">
                        <!-- Nama Lengkap -->
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="bi bi-person"></i></span>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                    value="{{ old('name') }}" placeholder="Contoh: Budi Santoso" required>
                            </div>
                            @error('name')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </div>

                        <!-- Alamat Email -->
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-semibold">Alamat Email <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="bi bi-envelope"></i></span>
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                    value="{{ old('email') }}" placeholder="budi@contoh.com" required>
                            </div>
                            @error('email')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="row">
                        <!-- Nomor HP / Telepon -->
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-semibold">Nomor Telepon / HP</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="bi bi-telephone"></i></span>
                                <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror"
                                    value="{{ old('phone') }}" placeholder="Contoh: 08123456789">
                            </div>
                            @error('phone')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </div>

                        <!-- Tanggal Lahir -->
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-semibold">Tanggal Lahir</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="bi bi-calendar-event"></i></span>
                                <input type="date" name="birth_date" class="form-control @error('birth_date') is-invalid @enderror"
                                    value="{{ old('birth_date') }}">
                            </div>
                            @error('birth_date')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <!-- Alamat Lengkap -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Alamat Lengkap</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-geo-alt"></i></span>
                            <textarea name="address" rows="3" class="form-control @error('address') is-invalid @enderror"
                                placeholder="Tulis alamat rumah lengkap anggota di sini...">{{ old('address') }}</textarea>
                        </div>
                        @error('address')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-2">
                        <a href="{{ route('admin.members.index') }}" class="btn btn-outline-secondary">
                            Batal
                        </a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-save me-1"></i> Simpan Anggota
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
