@extends('layouts.app')
@section('title', 'Tambah User')
@section('page-title', 'Tambah User')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="chart-card">
            <div class="chart-title">➕ Form Tambah User</div>

            <form method="POST" action="{{ route('owner.users.store') }}">
                @csrf

                <div class="mb-3">
                    <label style="font-size:11px; color:#888; text-transform:uppercase;">Nama Lengkap</label>
                    <input type="text" name="name" class="form-control mt-1"
                           placeholder="Contoh: Budi Santoso"
                           value="{{ old('name') }}" required>
                    @error('name')
                        <div style="color:#e07c7c; font-size:11px; margin-top:4px;">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label style="font-size:11px; color:#888; text-transform:uppercase;">Email</label>
                    <input type="email" name="email" class="form-control mt-1"
                           placeholder="contoh@email.com"
                           value="{{ old('email') }}" required>
                    @error('email')
                        <div style="color:#e07c7c; font-size:11px; margin-top:4px;">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label style="font-size:11px; color:#888; text-transform:uppercase;">Password</label>
                    <input type="password" name="password" class="form-control mt-1"
                           placeholder="Minimal 8 karakter" required minlength="8">
                    @error('password')
                        <div style="color:#e07c7c; font-size:11px; margin-top:4px;">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label style="font-size:11px; color:#888; text-transform:uppercase;">Role</label>
                    <select name="role" class="form-select mt-1" required>
                        <option value="kasir" {{ old('role') === 'kasir' ? 'selected' : '' }}>Kasir</option>
                        <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="owner" {{ old('role') === 'owner' ? 'selected' : '' }}>Owner</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label style="font-size:11px; color:#888; text-transform:uppercase;">Status</label>
                    <select name="is_active" class="form-select mt-1" required>
                        <option value="1">Aktif</option>
                        <option value="0">Nonaktif</option>
                    </select>
                </div>

                <div class="d-flex gap-2">
                    <a href="{{ route('owner.users.index') }}"
                       style="flex:1; text-align:center; padding:10px; border-radius:8px; border:1px solid #2a2d38; color:#888; text-decoration:none; font-size:13px;">
                        Batal
                    </a>
                    <button type="submit" class="btn-gold" style="flex:1; padding:10px; border-radius:8px; font-size:13px;">
                        Simpan User
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection