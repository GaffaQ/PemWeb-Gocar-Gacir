@extends('layouts.app')
@section('title', 'Peminjaman Saya')
@section('page-title', 'Peminjaman Saya')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <p class="text-muted mb-0">Daftar semua peminjaman buku Anda</p>
    </div>
    <a href="{{ route('member.borrowings.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Pinjam Buku Baru
    </a>
</div>

{{-- Status Summary --}}
<div class="row g-3 mb-4">
    @php
        $statusGroups = $borrowings->groupBy('status');
        $statuses = ['pending' => ['label'=>'Menunggu','color'=>'#6f42c1'], 'borrowed' => ['label'=>'Dipinjam','color'=>'#2d6a9f'], 'overdue' => ['label'=>'Terlambat','color'=>'#dc3545'], 'returned' => ['label'=>'Dikembalikan','color'=>'#28a745']];
    @endphp
    @foreach($statuses as $key => $s)
    <div class="col-6 col-md-3">
        <div class="card text-center py-3" style="border-top: 3px solid {{ $s['color'] }}">
            <div class="fs-4 fw-bold" style="color:{{ $s['color'] }}">{{ $borrowings->where('status', $key)->count() }}</div>
            <div class="small text-muted">{{ $s['label'] }}</div>
        </div>
    </div>
    @endforeach
</div>

<div class="card">
    <div class="card-header"><h6 class="mb-0 fw-bold"><i class="bi bi-card-list me-2 text-primary"></i>Riwayat Peminjaman</h6></div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr><th>Buku</th><th>Tanggal Pinjam</th><th>Batas Kembali</th><th>Status</th><th>Denda</th><th>Aksi</th></tr>
                </thead>
                <tbody>
                    @forelse($borrowings as $b)
                    <tr class="{{ $b->status === 'overdue' ? 'table-danger' : '' }}">
                        <td>
                            <div class="fw-semibold small">{{ $b->book->title }}</div>
                            <div class="text-muted" style="font-size:0.75rem">{{ $b->book->category->name ?? '-' }}</div>
                        </td>
                        <td><span class="small">{{ $b->borrow_date->format('d M Y') }}</span></td>
                        <td>
                            <span class="small {{ $b->status === 'overdue' ? 'text-danger fw-bold' : '' }}">
                                {{ $b->due_date->format('d M Y') }}
                            </span>
                            @if($b->status === 'overdue')
                                <div class="text-danger" style="font-size:0.7rem">
                                    {{ $b->due_date->diffInDays(now()) }} hari terlambat
                                </div>
                            @endif
                        </td>
                        <td>
                            @if($b->status === 'borrowed') <span class="badge bg-primary badge-status">Dipinjam</span>
                            @elseif($b->status === 'returned') <span class="badge bg-success badge-status">Dikembalikan</span>
                            @elseif($b->status === 'pending') <span class="badge badge-status" style="background:#6f42c1">Menunggu Admin</span>
                            @else <span class="badge bg-danger badge-status">Terlambat</span> @endif
                        </td>
                        <td>
                            @if($b->status === 'returned' && $b->return)
                                @if($b->return->fine > 0)
                                    <span class="text-danger small fw-bold">Rp {{ number_format($b->return->fine, 0, ',', '.') }}</span>
                                @else
                                    <span class="text-success small">-</span>
                                @endif
                            @elseif(in_array($b->status, ['overdue', 'borrowed']))
                                @php $est = $b->status === 'overdue' ? $b->due_date->diffInDays(now()) * 1000 : 0; @endphp
                                @if($est > 0)
                                    <span class="text-warning small">Est. Rp {{ number_format($est, 0, ',', '.') }}</span>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            @else
                                <span class="text-muted small">-</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('member.borrowings.show', $b->id) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center text-muted py-5">
                        <i class="bi bi-inbox fs-2 d-block mb-2"></i>Belum ada peminjaman
                        <div class="mt-2"><a href="{{ route('member.borrowings.create') }}" class="btn btn-primary btn-sm">Pinjam Sekarang</a></div>
                    </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($borrowings->hasPages())
    <div class="card-footer">{{ $borrowings->links() }}</div>
    @endif
</div>
@endsection
