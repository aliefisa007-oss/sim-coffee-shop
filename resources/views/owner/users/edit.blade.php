@extends('layouts.app')
@section('title', 'Edit User')
@section('page-title', 'Edit User')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="chart-card">
            <div class="chart-title">✏️ Edit User — {{ $user->name }}</div>
            <form method="POST" action="{{ route('owner.users.update', $user->id) }}">
                @csrf @method('PUT')
                <div class="mb-3">
                    <label style="font-size:11px; color:#888; text-transform:uppercase;">Nama Lengkap</label>
                    <input type="text" name="name" class="form-control mt-1" value="{{ $user->name }}" required>
                </div>
                <div class="mb-3">
                    <label style="font-size:11px; color:#888; text-transform:uppercase;">Email</label>
                    <input type="email" class="form-control mt-1" value="{{ $user->email }}" disabled>
                    <div style="font-size:10px; color:#555; margin-top:4px;">Email tidak bisa diubah</div>
                </div>
                <div class="mb-3">
                    <label style="font-size:11px; color:#888; text-transform:uppercase;">Password Baru</label>
                    <input type="password" name="password" class="form-control mt-1" placeholder="Kosongkan jika tidak ingin mengubah" minlength="8">
                </div>
                <div class="mb-3">
                    <label style="font-size:11px; color:#888; text-transform:uppercase;">Role</label>
                    <select name="role" class="form-select mt-1" required>
                        <option value="kasir" {{ $user->role === 'kasir' ? 'selected' : '' }}>Kasir</option>
                        <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="owner" {{ $user->role === 'owner' ? 'selected' : '' }}>Owner</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label style="font-size:11px; color:#888; text-transform:uppercase;">Status</label>
                    <select name="is_active" class="form-select mt-1">
                        <option value="1" {{ $user->is_active ? 'selected' : '' }}>Aktif</option>
                        <option value="0" {{ !$user->is_active ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('owner.users.index') }}"
                       style="flex:1; text-align:center; padding:10px; border-radius:8px; border:1px solid #2a2d38; color:#888; text-decoration:none;">
                        Batal
                    </a>
                    <button type="submit" class="btn-gold" style="flex:1; padding:10px; border-radius:8px;">
                        Update User
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection