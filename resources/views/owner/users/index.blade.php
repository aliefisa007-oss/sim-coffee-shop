@extends('layouts.app')
@section('title', 'Users')
@section('page-title', 'Manajemen User')

@section('content')
<div class="chart-card">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="chart-title mb-0">👥 Daftar User</div>
        <a href="{{ route('owner.users.create') }}" class="btn-gold" style="padding:8px 16px; border-radius:8px; text-decoration:none; font-size:12px;">+ Tambah User</a>
    </div>

    <!-- Search -->
    <form method="GET" class="d-flex gap-2 mb-3">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama / email..."
               style="background:#0f1117; border:1px solid #2a2d38; color:#e8e6e0; border-radius:8px; padding:8px 12px; font-size:12px; flex:1;">
        <select name="role" style="background:#0f1117; border:1px solid #2a2d38; color:#e8e6e0; border-radius:8px; padding:8px 12px; font-size:12px;">
            <option value="">Semua Role</option>
            <option value="owner" {{ request('role') === 'owner' ? 'selected' : '' }}>Owner</option>
            <option value="kasir" {{ request('role') === 'kasir' ? 'selected' : '' }}>Kasir</option>
        </select>
        <button type="submit" style="padding:8px 16px; background:#1a1d27; border:1px solid #2a2d38; color:#888; border-radius:8px; font-size:12px; cursor:pointer;">Cari</button>
    </form>

    <table class="table-dark-custom">
        <thead>
            <tr>
                <th>Nama</th>
                <th>Email</th>
                <th>Role</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
            <tr>
                <td style="font-weight:500;">{{ $user->name }}</td>
                <td style="color:#888;">{{ $user->email }}</td>
                <td>
                    <span style="padding:3px 8px; border-radius:6px; font-size:10px; font-weight:600;
                        background:{{ $user->role === 'owner' ? 'rgba(200,169,126,0.12)' : 'rgba(91,141,238,0.12)' }};
                        color:{{ $user->role === 'owner' ? '#c8a97e' : '#5b8dee' }};">
                        {{ ucfirst($user->role) }}
                    </span>
                </td>
                <td>
                    @if($user->is_active)
                        <span class="badge-aktif">Aktif</span>
                    @else
                        <span class="badge-nonaktif">Nonaktif</span>
                    @endif
                </td>
                <td>
                    <div class="d-flex gap-2">
                        <a href="{{ route('owner.users.edit', $user->id) }}"
                           style="padding:4px 10px; border-radius:6px; border:1px solid #2a2d38; color:#5b8dee; font-size:11px; text-decoration:none;">
                            Edit
                        </a>
                        <form method="POST" action="{{ route('owner.users.toggle-active', $user->id) }}">
                            @csrf @method('PATCH')
                            <button type="submit"
                                    style="padding:4px 10px; border-radius:6px; border:1px solid #2a2d38; background:transparent; color:#888; font-size:11px; cursor:pointer;">
                                {{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                            </button>
                        </form>
                        @if(auth()->user()->id !== $user->id)
                        <form method="POST" action="{{ route('owner.users.destroy', $user->id) }}"
                              onsubmit="return confirm('Hapus user ini?')">
                            @csrf @method('DELETE')
                            <button type="submit"
                                    style="padding:4px 10px; border-radius:6px; border:1px solid #2a2d38; background:transparent; color:#e07c7c; font-size:11px; cursor:pointer;">
                                Hapus
                            </button>
                        </form>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align:center; color:#555; padding:30px;">Belum ada user.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="mt-3">{{ $users->links() }}</div>
</div>
@endsection