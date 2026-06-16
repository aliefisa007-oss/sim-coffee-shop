@extends('layouts.app')
@section('title', 'Edit Menu')
@section('page-title', 'Edit Menu')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="chart-card">
            <div class="chart-title">✏️ Edit Menu — {{ $menu->nama_menu }}</div>
            <form method="POST" action="{{ route('owner.menu.update', $menu->id) }}" enctype="multipart/form-data">
                @csrf @method('PUT')
                <div class="mb-3">
                    <label style="font-size:11px; color:#888; text-transform:uppercase;">Kode Menu</label>
                    <input type="text" name="kode_menu" class="form-control mt-1"
                           value="{{ $menu->kode_menu }}" required>
                </div>
                <div class="mb-3">
                    <label style="font-size:11px; color:#888; text-transform:uppercase;">Nama Menu</label>
                    <input type="text" name="nama_menu" class="form-control mt-1"
                           value="{{ $menu->nama_menu }}" required>
                </div>
                <div class="mb-3">
                    <label style="font-size:11px; color:#888; text-transform:uppercase;">Kategori</label>
                    <select name="kategori_id" class="form-select mt-1" required>
                        @foreach($kategori as $kat)
                            <option value="{{ $kat->id }}" {{ $menu->kategori_id == $kat->id ? 'selected' : '' }}>
                                {{ $kat->nama_kategori }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label style="font-size:11px; color:#888; text-transform:uppercase;">Harga Jual</label>
                    <input type="number" name="harga_jual" class="form-control mt-1"
                           value="{{ $menu->harga_jual }}" required min="0">
                </div>
                <div class="mb-3">
                    <label style="font-size:11px; color:#888; text-transform:uppercase;">Status</label>
                    <select name="status_aktif" class="form-select mt-1">
                        <option value="1" {{ $menu->status_aktif ? 'selected' : '' }}>Aktif</option>
                        <option value="0" {{ !$menu->status_aktif ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label style="font-size:11px; color:#888; text-transform:uppercase;">Foto Menu</label>
                    @if($menu->foto_menu)
                        <div class="mb-2">
                            <img src="{{ $menu->foto_url }}"
                                 style="width:80px; height:80px; object-fit:cover; border-radius:8px;">
                            <div style="font-size:10px; color:#555; margin-top:4px;">Foto saat ini</div>
                        </div>
                    @endif
                    <input type="file" name="foto_menu" class="form-control mt-1" accept="image/*">
                    <div style="font-size:10px; color:#555; margin-top:4px;">Kosongkan jika tidak ingin mengubah foto</div>
                </div>
                <div class="mb-4">
                    <label style="font-size:11px; color:#888; text-transform:uppercase;">Deskripsi</label>
                    <textarea name="deskripsi" rows="3" class="form-control mt-1">{{ $menu->deskripsi }}</textarea>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('owner.menu.index') }}"
                       style="flex:1; text-align:center; padding:10px; border-radius:8px; border:1px solid #2a2d38; color:#888; text-decoration:none;">
                        Batal
                    </a>
                    <button type="submit" class="btn-gold" style="flex:1; padding:10px; border-radius:8px;">
                        Update Menu
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection