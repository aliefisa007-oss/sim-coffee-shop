@extends('layouts.app')
@section('title', 'Dashboard Stok')
@section('page-title', 'Dashboard Stok')

@section('content')
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="stat-card">
            <div class="accent-bar" style="background:#e07c3a;"></div>
            <div class="stat-label">Stok Menipis</div>
            <div class="stat-value" style="color:#e07c3a;">{{ $stok['jumlah_stok_menipis'] }}</div>
            <div class="stat-sub">bahan baku perlu restock</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <div class="accent-bar" style="background:#c8a97e;"></div>
            <div class="stat-label">Nilai Total Stok</div>
            <div class="stat-value">Rp {{ number_format($stok['nilai_total_stok'], 0, ',', '.') }}</div>
            <div class="stat-sub">estimasi nilai inventori</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <div class="accent-bar" style="background:#5b8dee;"></div>
            <div class="stat-label">Bahan Fast Moving</div>
            <div class="stat-value">{{ $stok['top_fast_moving']->where('total_keluar', '>', 0)->count() }}</div>
            <div class="stat-sub">bahan aktif terpakai (30 hari)</div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="chart-card">
            <div class="chart-title d-flex justify-content-between">
                <span>⚠️ Stok Menipis</span>
                <span style="color:#e07c3a; font-size:10px;">{{ $stok['jumlah_stok_menipis'] }} item</span>
            </div>
            @forelse($stok['stok_menipis'] as $bahan)
                <div class="alert-row mb-2">
                    <div>
                        <div style="font-size:12px; font-weight:500;">{{ $bahan->nama_bahan }}</div>
                        <div style="font-size:10px; color:#555; margin-top:2px;">Min: {{ $bahan->stok_minimum }} {{ $bahan->satuan }}</div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <div style="color:#e07c3a; font-weight:600; font-size:12px;">{{ $bahan->stok }} {{ $bahan->satuan }}</div>
                        <a href="{{ route('owner.stok.masuk', $bahan->id) }}"
                           style="padding:3px 8px; border-radius:6px; border:1px solid #2a2d38; color:#3ecf8e; font-size:11px; text-decoration:none;">
                           + Masuk
                        </a>
                    </div>
                </div>
            @empty
                <div style="text-align:center; color:#555; font-size:12px; padding:20px;">✅ Semua stok aman</div>
            @endforelse
        </div>
    </div>

    <div class="col-md-6">
        <div class="chart-card h-100">
            <div class="chart-title">🔥 Top 10 Bahan Baku Fast Moving (30 Hari)</div>
            @forelse($stok['top_fast_moving'] as $bahan)
                @php
                    $maxKeluar = $stok['top_fast_moving']->max('total_keluar') ?: 1;
                    $pct = $bahan->total_keluar > 0 ? ($bahan->total_keluar / $maxKeluar) * 100 : 0;
                @endphp
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span style="font-size:12px;">{{ $bahan->nama_bahan }}</span>
                        <span style="font-size:11px; color:#5b8dee; font-weight:600;">
                            {{ number_format($bahan->total_keluar, 1) }} {{ $bahan->satuan }}
                        </span>
                    </div>
                    <div style="height:5px; background:#23262f; border-radius:3px;">
                        <div style="height:100%; width:{{ $pct }}%; background:#5b8dee; border-radius:3px;"></div>
                    </div>
                </div>
            @empty
                <div style="text-align:center; color:#555; font-size:12px; padding:20px;">Belum ada data pemakaian.</div>
            @endforelse
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-12">
        <div class="chart-card">
            <div class="chart-title">🏆 Top 10 Menu Terlaris</div>
            <div class="row g-3">
                @forelse($stok['top_menu'] as $i => $menu)
                    @php $max = $stok['top_menu']->first()->total_terjual ?? 1; $pct = ($menu->total_terjual / $max) * 100; @endphp
                    <div class="col-md-6">
                        <div class="d-flex justify-content-between mb-1">
                            <span style="font-size:12px;">{{ $i + 1 }}. {{ $menu->nama_menu }}</span>
                            <span style="font-size:11px; color:#c8a97e; font-weight:600;">{{ $menu->total_terjual }}x</span>
                        </div>
                        <div style="height:5px; background:#23262f; border-radius:3px; margin-bottom:10px;">
                            <div style="height:100%; width:{{ $pct }}%; background:#c8a97e; border-radius:3px;"></div>
                        </div>
                    </div>
                @empty
                    <div class="col-12" style="text-align:center; color:#555; font-size:12px; padding:20px;">Belum ada transaksi.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mt-1">
    <div class="col-12 d-flex justify-content-end gap-2">
        <a href="{{ route('owner.bahan-baku.index') }}"
           style="padding:6px 14px; border-radius:8px; border:1px solid #2a2d38; color:#c8a97e; font-size:12px; text-decoration:none;">
           Kelola Bahan Baku
        </a>
        <a href="{{ route('owner.riwayat-stok.index') }}"
           style="padding:6px 14px; border-radius:8px; border:1px solid #2a2d38; color:#5b8dee; font-size:12px; text-decoration:none;">
           Lihat Riwayat Stok
        </a>
    </div>
</div>
@endsection
