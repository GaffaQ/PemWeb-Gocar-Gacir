@extends('layouts.app')
@section('title', 'Detail Anggota')
@section('page-title', 'Detail Anggota')

@section('content')
<div class="row g-4">
    {{-- Profile Card --}}
    <div class="col-lg-4">
        <div class="card text-center p-4">
            <div class="avatar mx-auto mb-3" style="width:70px;height:70px;font-size:1.8rem">
                {{ strtoupper(substr($member->user->name, 0, 1)) }}
            </div>
            <h5 class="fw-bold mb-1">{{ $member->user->name }}</h5>
            <div class="text-muted small mb-2">{{ $member->user->email }}</div>
            <span class="badge {{ $member->status === 'active' ? 'bg-success' : 'bg-secondary' }} mb-3">
                {{ $member->status === 'active' ? 'Aktif' : 'Nonaktif' }}
            </span>
            <div class="border rounded p-3 text-start mb-3">
                <div class="d-flex justify-content-between mb-1">
                    <span class="text-muted small">Kode Member</span>
                    <span class="fw-semibold small">{{ $member->member_code }}</span>
                </div>
                <div class="d-flex justify-content-between mb-1">
                    <span class="text-muted small">No. HP</span>
                    <span class="small">{{ $member->phone ?? '-' }}</span>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="text-muted small">Bergabung</span>
                    <span class="small">{{ $member->created_at->format('d M Y') }}</span>
                </div>
            </div>
            <form action="{{ route('admin.members.toggle', $member->id) }}" method="POST">
                @csrf
                <button type="submit" class="btn w-100 {{ $member->status === 'active' ? 'btn-outline-danger' : 'btn-outline-success' }}"
                    onclick="return confirm('Ubah status member ini?')">
                    <i class="bi {{ $member->status === 'active' ? 'bi-person-x' : 'bi-person-check' }} me-1"></i>
                    {{ $member->status === 'active' ? 'Nonaktifkan' : 'Aktifkan' }} Member
                </button>
            </form>
        </div>

        {{-- Stats --}}
        <div class="card mt-3">
            <div class="card-header"><h6 class="mb-0 fw-bold small">Statistik Peminjaman</h6></div>
            <div class="card-body p-0">
                @foreach([['label'=>'Total Peminjaman','val'=>$stats['total'],'color'=>'#2d6a9f'],['label'=>'Aktif','val'=>$stats['active'],'color'=>'#ffc107'],['label'=>'Terlambat','val'=>$stats['overdue'],'color'=>'#dc3545'],['label'=>'Dikembalikan','val'=>$stats['returned'],'color'=>'#28a745'],['label'=>'Menunggu','val'=>$stats['pending'],'color'=>'#6f42c1']] as $s)
                <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom">
                    <span class="small text-muted">{{ $s['label'] }}</span>
                    <span class="fw-bold" style="color:{{ $s['color'] }}">{{ $s['val'] }}</span>
                </div>
                @endforeach
                <div class="d-flex justify-content-between align-items-center px-3 py-2">
                    <span class="small text-muted">Total Denda</span>
                    <span class="fw-bold text-danger">Rp {{ number_format($stats['fine'], 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Borrowing History --}}
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold"><i class="bi bi-clock-history me-2 text-primary"></i>Riwayat Peminjaman</h6>
                <a href="{{ route('admin.members.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Kembali
                </a>
            </div>
            <div class="card-body p-0">
                <table class="table mb-0">
                    <thead><tr><th>Buku</th><th>Pinjam</th><th>Batas</th><th>Status</th><th>Denda</th><th>Aksi</th></tr></thead>
                    <tbody>
                        @forelse($borrowings as $b)
                        <tr class="{{ $b->status === 'overdue' ? 'table-danger' : '' }}">
                            <td><span class="small fw-semibold">{{ Str::limit($b->book->title, 30) }}</span></td>
                            <td><span class="small">{{ $b->borrow_date->format('d M Y') }}</span></td>
                            <td><span class="small">{{ $b->due_date->format('d M Y') }}</span></td>
                            <td>
                                @if($b->status === 'borrowed') <span class="badge bg-primary badge-status">Dipinjam</span>
                                @elseif($b->status === 'returned') <span class="badge bg-success badge-status">Dikembalikan</span>
                                @elseif($b->status === 'pending')
                                    <span class="badge badge-status" style="background:#6f42c1">Pending</span>
                                @else <span class="badge bg-danger badge-status">Terlambat</span> @endif
                            </td>
                            <td>
                                @if($b->return && $b->return->fine > 0)
                                    <span class="text-danger small">Rp {{ number_format($b->return->fine, 0, ',', '.') }}</span>
                                @else <span class="text-muted small">-</span> @endif
                            </td>
                            <td>
                                @if($b->status === 'pending')
                                <form action="{{ route('admin.borrowings.approve', $b->id) }}" method="POST" class="d-inline">@csrf
                                    <button class="btn btn-xs btn-success" style="font-size:0.7rem;padding:2px 8px" title="Setujui">✓</button>
                                </form>
                                <form action="{{ route('admin.borrowings.reject', $b->id) }}" method="POST" class="d-inline">@csrf
                                    <button class="btn btn-xs btn-danger" style="font-size:0.7rem;padding:2px 8px" onclick="return confirm('Tolak?')" title="Tolak">✗</button>
                                </form>
                                @elseif(in_array($b->status, ['borrowed','overdue']))
                                <a href="{{ route('borrowings.return.form', $b->id) }}" class="btn btn-xs btn-outline-warning" style="font-size:0.7rem;padding:2px 8px">Kembalikan</a>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">Belum ada riwayat peminjaman</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
