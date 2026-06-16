@extends('layouts.app')
@section('title', 'Resep Produk')
@section('page-title', 'Resep Produk (BOM)')

@section('content')
<div class="chart-card">
    <div class="chart-title">📜 Daftar Menu & Resep</div>
    <table class="table-dark-custom">
        <thead>
            <tr>
                <th>Menu</th>
                <th>Kategori</th>
                <th>Jumlah Bahan</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($menus as $menu)
            <tr>
                <td style="font-weight:500;">{{ $menu->nama_menu }}</td>
                <td style="color:#888;">{{ $menu->kategori->nama_kategori }}</td>
                <td>
                    @if($menu->resepProduk->count() > 0)
                        <span class="badge-aktif">{{ $menu->resepProduk->count() }} bahan</span>
                    @else
                        <span class="badge-nonaktif">Belum ada resep</span>
                    @endif
                </td>
                <td>
                    <a href="{{ route('owner.resep.by-menu', $menu->id) }}"
                       style="padding:4px 12px; border-radius:6px; border:1px solid #2a2d38; color:#c8a97e; font-size:11px; text-decoration:none;">
                        Kelola Resep
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" style="text-align:center; color:#555; padding:30px;">Belum ada menu.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection