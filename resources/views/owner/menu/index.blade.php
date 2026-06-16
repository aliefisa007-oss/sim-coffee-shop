@extends('layouts.app')
@section('title', 'Menu')
@section('page-title', 'Manajemen Menu')

@section('content')
<div class="chart-card mb-3">
    <form method="GET" class="d-flex gap-3 align-items-center flex-wrap">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="🔍 Cari nama / kode menu..."
               style="background:#0f1117; border:1px solid #2a2d38; color:#e8e6e0; border-radius:8px; padding:8px 12px; font-size:12px; flex:1; min-width:200px;">
        <select name="kategori_id" style="background:#0f1117; border:1px solid #2a2d38; color:#e8e6e0; border-radius:8px; padding:8px 12px; font-size:12px;">
            <option value="">Semua Kategori</option>
            @foreach($kategori as $kat)
                <option value="{{ $kat->id }}" {{ request('kategori_id') == $kat->id ? 'selected' : '' }}>
                    {{ $kat->nama_kategori }}
                </option>
            @endforeach
        </select>
        <select name="status" style="background:#0f1117; border:1px solid #2a2d38; color:#e8e6e0; border-radius:8px; padding:8px 12px; font-size:12px;">
            <option value="">Semua Status</option>
            <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Aktif</option>
            <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Nonaktif</option>
        </select>
        <button type="submit" style="padding:8px 20px; background:#1a1d27; border:1px solid #2a2d38; color:#888; border-radius:8px; font-size:12px; cursor:pointer;">Filter</button>
        <a href="{{ route('owner.menu.create') }}" class="btn-gold" style="padding:8px 20px; border-radius:8px; text-decoration:none; font-size:12px;">+ Tambah Menu</a>
    </form>
</div>

<div class="row g-3">
    @forelse($menus as $menu)
    <div class="col-md-3">
        <div class="chart-card h-100" style="position:relative;">
            @if($menu->foto_menu)
                <img src="{{ $menu->foto_url }}" alt="{{ $menu->nama_menu }}"
                     style="width:100%; height:120px; object-fit:cover; border-radius:8px; margin-bottom:12px;">
            @else
                <div style="width:100%; height:120px; background:#0f1117; border-radius:8px; display:flex; align-items:center; justify-content:center; margin-bottom:12px; font-size:36px;">☕</div>
            @endif

            <span style="position:absolute; top:28px; right:28px; padding:3px 8px; border-radius:10px; font-size:10px; font-weight:600;
                background:{{ $menu->status_aktif ? 'rgba(62,207,142,0.12)' : 'rgba(224,124,58,0.12)' }};
                color:{{ $menu->status_aktif ? '#3ecf8e' : '#e07c3a' }};">
                {{ $menu->status_aktif ? 'Aktif' : 'Nonaktif' }}
            </span>

            <div style="font-size:10px; color:#555; margin-bottom:4px;">{{ $menu->kode_menu }}</div>
            <div style="font-size:14px; font-weight:600; margin-bottom:2px;">{{ $menu->nama_menu }}</div>
            <div style="font-size:11px; color:#888; margin-bottom:8px;">{{ $menu->kategori->nama_kategori }}</div>
            <div style="font-size:16px; font-weight:700; color:#c8a97e; margin-bottom:12px;">
                Rp {{ number_format($menu->harga_jual, 0, ',', '.') }}
            </div>

            <div class="d-flex gap-2">
                <a href="{{ route('owner.menu.edit', $menu->id) }}"
                   style="flex:1; text-align:center; padding:6px; border-radius:6px; border:1px solid #2a2d38; color:#5b8dee; font-size:11px; text-decoration:none;">
                    Edit
                </a>
                <a href="{{ route('owner.resep.by-menu', $menu->id) }}"
                   style="flex:1; text-align:center; padding:6px; border-radius:6px; border:1px solid #2a2d38; color:#c8a97e; font-size:11px; text-decoration:none;">
                    Resep
                </a>
                <form method="POST" action="{{ route('owner.menu.destroy', $menu->id) }}"
                      onsubmit="return confirm('Hapus menu ini?')">
                    @csrf @method('DELETE')
                    <button type="submit"
                            style="padding:6px 10px; border-radius:6px; border:1px solid #2a2d38; background:transparent; color:#e07c7c; font-size:11px; cursor:pointer;">
                        ✕
                    </button>
                </form>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12 text-center" style="color:#555; padding:60px;">
        Belum ada menu. <a href="{{ route('owner.menu.create') }}" style="color:#c8a97e;">Tambah sekarang</a>
    </div>
    @endforelse
</div>
<div class="mt-4">{{ $menus->links() }}</div>
@endsection