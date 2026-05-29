<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Perpustakaan Digital')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root { --primary: #1a3a5c; --secondary: #2d6a9f; --accent: #e8a020; --light-bg: #f4f7fc; }
        body { background-color: var(--light-bg); font-family: 'Segoe UI', sans-serif; }
        .sidebar { background: linear-gradient(180deg, var(--primary) 0%, var(--secondary) 100%); min-height: 100vh; width: 250px; position: fixed; top: 0; left: 0; z-index: 100; transition: all 0.3s; }
        .sidebar-brand { padding: 1.5rem; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .sidebar-brand h5 { color: var(--accent); font-weight: 700; margin: 0; }
        .sidebar-brand small { color: rgba(255,255,255,0.6); font-size: 0.75rem; }
        .sidebar .nav-link { color: rgba(255,255,255,0.8); padding: 0.75rem 1.5rem; border-radius: 0; display: flex; align-items: center; gap: 0.75rem; transition: all 0.2s; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { color: #fff; background: rgba(255,255,255,0.15); border-left: 3px solid var(--accent); }
        .sidebar .nav-link i { font-size: 1.1rem; width: 20px; }
        .sidebar .nav-section { color: rgba(255,255,255,0.4); font-size: 0.7rem; font-weight: 600; letter-spacing: 1px; text-transform: uppercase; padding: 1rem 1.5rem 0.25rem; }
        .sidebar-user { position: absolute; bottom: 0; width: 100%; padding: 1rem; }
        .main-content { margin-left: 250px; min-height: 100vh; }
        .topbar { background: #fff; padding: 0.875rem 1.5rem; border-bottom: 1px solid #e0e7ef; display: flex; align-items: center; justify-content: space-between; position: sticky; top: 0; z-index: 99; }
        .page-content { padding: 1.75rem; }
        .card { border: none; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,0.06); }
        .card-header { background: #fff; border-bottom: 1px solid #eef2f7; border-radius: 12px 12px 0 0 !important; padding: 1.25rem 1.5rem; }
        .stat-card { border-radius: 12px; padding: 1.5rem; color: #fff; position: relative; overflow: hidden; }
        .stat-card .icon { font-size: 2.5rem; opacity: 0.3; position: absolute; right: 1rem; top: 50%; transform: translateY(-50%); }
        .btn-primary { background-color: var(--primary); border-color: var(--primary); }
        .btn-primary:hover { background-color: var(--secondary); border-color: var(--secondary); }
        .badge-status { font-size: 0.75rem; padding: 0.4em 0.75em; border-radius: 20px; }
        .table thead th { background: var(--light-bg); color: #6b7a8d; font-size: 0.8rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; border: none; }
        .table tbody td { vertical-align: middle; border-color: #f0f4f8; }
        .avatar { width: 36px; height: 36px; border-radius: 50%; background: var(--secondary); color: #fff; display: inline-flex; align-items: center; justify-content: center; font-weight: 600; font-size: 0.85rem; }
        .badge-pending { background: #6f42c1 !important; }

        /* ── Animations ── */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(18px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to   { opacity: 1; }
        }
        .page-content { animation: fadeIn 0.35s ease; }
        .card { animation: fadeInUp 0.4s ease both; }
        .card:nth-child(2) { animation-delay: 0.05s; }
        .card:nth-child(3) { animation-delay: 0.10s; }
        .card:nth-child(4) { animation-delay: 0.15s; }
        .stat-card { animation: fadeInUp 0.45s ease both; transition: transform 0.2s ease, box-shadow 0.2s ease; }
        .stat-card:hover { transform: translateY(-4px); box-shadow: 0 8px 24px rgba(0,0,0,0.15); }
        .col-md-3:nth-child(1) .stat-card { animation-delay: 0.05s; }
        .col-md-3:nth-child(2) .stat-card { animation-delay: 0.10s; }
        .col-md-3:nth-child(3) .stat-card { animation-delay: 0.15s; }
        .col-md-3:nth-child(4) .stat-card { animation-delay: 0.20s; }
        .table tbody tr { transition: background 0.15s ease; }
        .table tbody tr:hover { background: #f0f5ff; }
        .btn { transition: all 0.18s ease; }
        .btn:hover { transform: translateY(-1px); }
        .sidebar .nav-link { transition: all 0.2s ease; }
        .alert { animation: fadeInUp 0.3s ease both; }
        .page-link { transition: all 0.18s ease; }
        .badge { transition: all 0.15s ease; }
    </style>
</head>
<body>
<nav class="sidebar">
    <div class="sidebar-brand">
        <h5><i class="bi bi-journal-bookmark-fill"></i> Perpustakaan</h5>
        <small>Sistem Digital</small>
    </div>

    <div class="nav-section">Menu Utama</div>
    <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
        <i class="bi bi-grid-1x2-fill"></i> Dashboard
    </a>

    <div class="nav-section">Pengelolaan</div>
    <a href="{{ route('books.index') }}" class="nav-link {{ request()->routeIs('books.*') ? 'active' : '' }}">
        <i class="bi bi-book-fill"></i> Kelola Buku
    </a>
    <a href="{{ route('categories.index') }}" class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}">
        <i class="bi bi-tags-fill"></i> Kategori
    </a>
    <a href="{{ route('borrowings.index') }}" class="nav-link {{ request()->routeIs('borrowings.*') ? 'active' : '' }}">
        <i class="bi bi-arrow-left-right"></i> Peminjaman
    </a>
    <a href="{{ route('admin.members.index') }}" class="nav-link {{ request()->routeIs('admin.members.*') ? 'active' : '' }}">
        <i class="bi bi-people-fill"></i> Kelola Anggota
    </a>

    <div class="sidebar-user">
        <div style="padding: 0.75rem 1rem; background: rgba(255,255,255,0.08); border-radius: 8px; margin-bottom: 0.5rem;">
            <div style="color: #fff; font-size: 0.85rem; font-weight: 600;">{{ auth()->user()->name }}</div>
            <div style="color: rgba(255,255,255,0.5); font-size: 0.75rem;">{{ ucfirst(auth()->user()->role) }}</div>
        </div>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="nav-link w-100 border-0 text-start" style="background: rgba(255,0,0,0.15);">
                <i class="bi bi-box-arrow-right"></i> Logout
            </button>
        </form>
    </div>
</nav>

<div class="main-content">
    <div class="topbar">
        <h6 class="mb-0 fw-semibold">@yield('page-title', 'Dashboard')</h6>
        <span class="text-muted small"><i class="bi bi-calendar3"></i> {{ now()->format('d F Y') }}</span>
    </div>
    <div class="page-content">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show"><i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        @endif
        @yield('content')
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
