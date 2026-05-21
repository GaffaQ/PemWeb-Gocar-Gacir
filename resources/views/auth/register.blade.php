<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - Perpustakaan Digital</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #1a3a5c 0%, #2d6a9f 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            font-family: 'Segoe UI', sans-serif;
        }
        .auth-card { background: #fff; border-radius: 20px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); padding: 3rem; }
        .form-control:focus { border-color: #2d6a9f; box-shadow: 0 0 0 0.2rem rgba(45,106,159,0.15); }
        .btn-register { background: linear-gradient(135deg, #1a3a5c, #2d6a9f); border: none; padding: 0.75rem; font-weight: 600; }
        .input-group-text { background: #f4f7fc; border-color: #dee2e6; color: #6c757d; }
    </style>
</head>
<body>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-6">
            <div class="text-center mb-4">
                <i class="bi bi-journal-bookmark-fill text-warning" style="font-size: 2.5rem;"></i>
                <h4 class="text-white fw-bold mt-2">Perpustakaan Digital</h4>
            </div>
            <div class="auth-card">
                <h5 class="fw-bold mb-1">Buat Akun Baru</h5>
                <p class="text-muted mb-4">Daftarkan diri Anda sebagai anggota perpustakaan</p>

                @if ($errors->any())
                    <div class="alert alert-danger">
                        @foreach ($errors->all() as $error)
                            <div><i class="bi bi-exclamation-circle me-1"></i>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <form action="{{ route('register.post') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Lengkap</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-person"></i></span>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                placeholder="Nama lengkap Anda" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Email</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                placeholder="email@contoh.com" value="{{ old('email') }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock"></i></span>
                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                                placeholder="Minimal 6 karakter" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Konfirmasi Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                            <input type="password" name="password_confirmation" class="form-control"
                                placeholder="Ulangi password" required>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-register btn-primary w-100 text-white">
                        <i class="bi bi-person-plus me-2"></i>Daftar Sekarang
                    </button>
                </form>

                <hr class="my-4">
                <p class="text-center text-muted mb-0">
                    Sudah punya akun?
                    <a href="{{ route('login') }}" class="text-decoration-none fw-semibold" style="color: #2d6a9f;">Masuk di sini</a>
                </p>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
