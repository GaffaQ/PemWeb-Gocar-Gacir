@extends('layouts.app')
@section('title', 'Kelola Anggota')
@section('page-title', 'Kelola Anggota')

@section('content')
<div class="card">
    <div class="card-header">
        <div class="row align-items-center g-2">
            <div class="col"><h6 class="mb-0 fw-bold"><i class="bi bi-people-fill me-2 text-primary"></i>Daftar Anggota</h6></div>
            <div class="col-auto">
                <form class="d-flex gap-2" method="GET">
                    <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari nama / email / kode..." value="{{ request('search') }}" style="width:250px">
                    <select name="status" class="form-select form-select-sm" style="width:130px">
                        <option value="">Semua Status</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                    <button class="btn btn-sm btn-primary">Cari</button>
                    @if(request('search') || request('status'))
                    <a href="{{ route('admin.members.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
                    @endif
                </form>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr><th>Anggota</th><th>Kode Member</th><th>Email</th><th>No. HP</th><th>Status</th><th>Aksi</th></tr>
                </thead>
                <tbody>
                    @forelse($members as $member)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="avatar">{{ strtoupper(substr($member->user->name, 0, 1)) }}</div>
                                <div>
                                    <div class="fw-semibold small">{{ $member->user->name }}</div>
                                    <div class="text-muted" style="font-size:0.7rem">Bergabung {{ $member->created_at->format('d M Y') }}</div>
                                </div>
                            </div>
                        </td>
                        <td><span class="badge bg-light text-dark border">{{ $member->member_code }}</span></td>
                        <td><span class="small">{{ $member->user->email }}</span></td>
                        <td><span class="small">{{ $member->phone ?? '-' }}</span></td>
                        <td>
                            @if($member->status === 'active')
                                <span class="badge bg-success badge-status">Aktif</span>
                            @else
                                <span class="badge bg-secondary badge-status">Nonaktif</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('admin.members.show', $member->id) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <form action="{{ route('admin.members.toggle', $member->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-sm {{ $member->status === 'active' ? 'btn-outline-danger' : 'btn-outline-success' }}"
                                        onclick="return confirm('Ubah status member ini?')">
                                        <i class="bi {{ $member->status === 'active' ? 'bi-person-x' : 'bi-person-check' }}"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center text-muted py-5">
                        <i class="bi bi-people fs-2 d-block mb-2"></i>Tidak ada anggota ditemukan
                    </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($members->hasPages())
    <div class="card-footer">{{ $members->links() }}</div>
    @endif
</div>
@endsection
