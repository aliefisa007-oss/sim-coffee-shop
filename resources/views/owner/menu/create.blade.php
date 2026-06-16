@extends('layouts.app')
@section('title', 'Tambah Menu')
@section('page-title', 'Tambah Menu Baru')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="chart-card">
            <div class="chart-title">➕ Form Tambah Menu</div>
            <form method="POST" action="{{ route('owner.menu.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label style="font-size:11px; color:#888; text-transform:uppercase;">Kode Menu</label>
                    <input type="text" name="kode_menu" class="form-control mt-1"
                           placeholder="MNU-006" required value="{{ old('kode_menu') }}">
                    @error('kode_menu')
                        <div style="color:#e07c7c; font-size:11px; margin-top:4px;">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label style="font-size:11px; color:#888; text-transform:uppercase;">Nama Menu</label>
                    <input type="text" name="nama_menu" class="form-control mt-1"
                           placeholder="Contoh: Espresso" required value="{{ old('nama_menu') }}">
                    @error('nama_menu')
                        <div style="color:#e07c7c; font-size:11px; margin-top:4px;">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label style="font-size:11px; color:#888; text-transform:uppercase;">Kategori</label>
                    <select name="kategori_id" class="form-select mt-1" required>
                        <option value="">Pilih kategori...</option>
                        @foreach($kategori as $kat)
                            <option value="{{ $kat->id }}" {{ old('kategori_id') == $kat->id ? 'selected' : '' }}>
                                {{ $kat->nama_kategori }}
                            </option>
                        @endforeach
                    </select>
                    @error('kategori_id')
                        <div style="color:#e07c7c; font-size:11px; margin-top:4px;">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label style="font-size:11px; color:#888; text-transform:uppercase;">Harga Jual</label>
                    <input type="number" name="harga_jual" class="form-control mt-1"
                           placeholder="25000" required value="{{ old('harga_jual') }}" min="0">
                    @error('harga_jual')
                        <div style="color:#e07c7c; font-size:11px; margin-top:4px;">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label style="font-size:11px; color:#888; text-transform:uppercase;">Status</label>
                    <select name="status_aktif" class="form-select mt-1" required>
                        <option value="1" selected>Aktif</option>
                        <option value="0">Nonaktif</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label style="font-size:11px; color:#888; text-transform:uppercase;">Foto Menu</label>
                    <input type="file" name="foto_menu" class="form-control mt-1" accept="image/*">
                    <div style="font-size:10px; color:#555; margin-top:4px;">Format: JPG, PNG, WEBP. Maks 2MB.</div>
                </div>
                <div class="mb-4">
                    <label style="font-size:11px; color:#888; text-transform:uppercase;">Deskripsi</label>
                    <textarea name="deskripsi" rows="3" class="form-control mt-1"
                              placeholder="Deskripsi menu (opsional)">{{ old('deskripsi') }}</textarea>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('owner.menu.index') }}"
                       style="flex:1; text-align:center; padding:10px; border-radius:8px; border:1px solid #2a2d38; color:#888; text-decoration:none;">
                        Batal
                    </a>
                    <button type="submit" class="btn-gold" style="flex:1; padding:10px; border-radius:8px;">
                        Simpan Menu
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection