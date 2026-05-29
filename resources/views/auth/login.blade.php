<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Perpustakaan Digital</title>
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
        .auth-card {
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
        }
        .auth-left {
            background: linear-gradient(180deg, #1a3a5c, #0d2035);
            padding: 3rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
        }
        .auth-left .icon-wrap {
            width: 80px; height: 80px;
            background: rgba(232,160,32,0.2);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 1.5rem;
        }
        .auth-left h4 { color: #e8a020; font-weight: 700; }
        .auth-left p { color: rgba(255,255,255,0.6); font-size: 0.9rem; }
        .auth-right { padding: 3rem; }
        .form-control:focus { border-color: #2d6a9f; box-shadow: 0 0 0 0.2rem rgba(45,106,159,0.15); }
        .btn-login { background: linear-gradient(135deg, #1a3a5c, #2d6a9f); border: none; padding: 0.75rem; font-weight: 600; }
        .btn-login:hover { opacity: 0.9; }
        .input-group-text { background: #f4f7fc; border-color: #dee2e6; color: #6c757d; }
    </style>
</head>
<body>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-9 col-lg-8">
            <div class="auth-card">
                <div class="row g-0">
                    <div class="col-md-4 auth-left d-none d-md-flex">
                        <div>
                            <div class="icon-wrap mx-auto">
                                <i class="bi bi-journal-bookmark-fill text-warning" style="font-size: 2rem;"></i>
                            </div>
                            <h4>Perpustakaan<br>Digital</h4>
                            <p>Sistem Manajemen Perpustakaan Modern</p>
                        </div>
                    </div>
                    <div class="col-md-8 auth-right">
                        <h5 class="fw-bold mb-1">Selamat Datang!</h5>
                        <p class="text-muted mb-4">Masuk ke akun Anda untuk melanjutkan</p>

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                @foreach ($errors->all() as $error)
                                    <div><i class="bi bi-exclamation-circle me-1"></i>{{ $error }}</div>
                                @endforeach
                            </div>
                        @endif

                        <form action="{{ route('login.post') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Email</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                        placeholder="email@contoh.com" value="{{ old('email') }}" required autofocus>
                                </div>
                            </div>
                            <div class="mb-4">
                                <label class="form-label fw-semibold">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                    <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-login btn-primary w-100 text-white">
                                <i class="bi bi-box-arrow-in-right me-2"></i>Masuk
                            </button>
                        </form>

                        <hr class="my-4">
                        <p class="text-center text-muted mb-0 small">
                            Portal Internal Khusus Staf & Admin Perpustakaan
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
