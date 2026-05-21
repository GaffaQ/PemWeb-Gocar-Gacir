@extends('layouts.app')
@section('title', 'Peminjaman Buku')
@section('page-title', 'Peminjaman Buku')
@section('content')
<div class="card">
    <div class="card-header">
        <div class="row align-items-center">
            <div class="col">
                <h6 class="mb-0 fw-bold"><i class="bi bi-arrow-left-right me-2 text-primary"></i>Daftar Peminjaman</h6>
            </div>
            <div class="col-auto">
                <a href="{{ route('borrowings.create') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-lg me-1"></i>Tambah Peminjaman
                </a>
            </div>
        </div>
        <form method="GET" class="mt-3">
            <div class="row g-2">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control form-control-sm"
                        placeholder="Cari anggota atau buku..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select form-select-sm">
                        <option value="">Semua Status</option>
                        <option value="borrowed" {{ request('status') == 'borrowed' ? 'selected' : '' }}>Dipinjam</option>
                        <option value="returned" {{ request('status') == 'returned' ? 'selected' : '' }}>Dikembalikan</option>
                        <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>Terlambat</option>
                    </select>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-outline-primary btn-sm"><i class="bi bi-search"></i> Cari</button>
                    <a href="{{ route('borrowings.index') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
                </div>
            </div>
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr><th>#</th><th>Anggota</th><th>Buku</th><th>Tgl Pinjam</th><th>Batas Kembali</th><th>Status</th><th class="text-center">Aksi</th></tr>
                </thead>
                <tbody>
                    @forelse($borrowings as $b)
                    <tr>
                        <td class="text-muted small">{{ $loop->iteration }}</td>
                        <td>
                            <div class="fw-semibold small">{{ $b->member->user->name }}</div>
                            <div class="text-muted" style="font-size:0.75rem;">{{ $b->member->member_code }}</div>
                        </td>
                        <td class="small fw-semibold">{{ Str::limit($b->book->title, 30) }}</td>
                        <td class="small">{{ $b->borrow_date->format('d M Y') }}</td>
                        <td class="small {{ $b->status === 'overdue' ? 'text-danger fw-bold' : '' }}">
                            {{ $b->due_date->format('d M Y') }}
                        </td>
                        <td>
                            @if($b->status === 'borrowed')
                                <span class="badge bg-primary">Dipinjam</span>
                            @elseif($b->status === 'returned')
                                <span class="badge bg-success">Dikembalikan</span>
                            @else
                                <span class="badge bg-danger">Terlambat</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <a href="{{ route('borrowings.show', $b) }}" class="btn btn-sm btn-outline-info" title="Detail">
                                <i class="bi bi-eye"></i>
                            </a>
                            @if($b->status !== 'returned')
                                <a href="{{ route('borrowings.return.form', $b) }}" class="btn btn-sm btn-outline-success" title="Kembalikan">
                                    <i class="bi bi-arrow-return-left"></i>
                                </a>
                                <a href="{{ route('borrowings.edit', $b) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                            @endif
                            <form action="{{ route('borrowings.destroy', $b) }}" method="POST" class="d-inline"
                                onsubmit="return confirm('Yakin ingin menghapus data peminjaman ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center text-muted py-4"><i class="bi bi-inbox fs-2 d-block mb-2"></i>Belum ada data peminjaman</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($borrowings->hasPages())
    <div class="card-footer bg-white">{{ $borrowings->withQueryString()->links() }}</div>
    @endif
</div>
@endsection
