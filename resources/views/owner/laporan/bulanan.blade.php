@extends('layouts.app')
@section('title', 'Laporan Bulanan')
@section('page-title', 'Laporan Bulanan')

@section('content')

{{-- Filter Bulan --}}
<div class="chart-card mb-3">
    <form method="GET" class="d-flex gap-3 align-items-center flex-wrap">
        <div>
            <label style="font-size:11px; color:#888; text-transform:uppercase; display:block; margin-bottom:4px;">Pilih Bulan</label>
            <input type="month" name="bulan" value="{{ $bulan }}" class="form-control" style="width:auto;">
        </div>
        <div style="padding-top:20px;">
            <button type="submit" class="btn-gold" style="padding:8px 20px; border-radius:8px;">
                Tampilkan
            </button>
        </div>
        <div style="padding-top:20px;">
    <a href="{{ route('owner.laporan.bulanan.export-excel', ['bulan' => $bulan]) }}"
       style="padding:8px 16px; background:#3ecf8e; border:none; border-radius:8px; color:#1a2e1a; font-size:12px; font-weight:700; text-decoration:none;">
        📊 Export Excel
    </a>
</div>
        <div style="padding-top:20px;">
            <a href="{{ route('owner.laporan.index') }}"
               style="padding:8px 16px; background:#1a1d27; border:1px solid #2a2d38; border-radius:8px; color:#888; text-decoration:none; font-size:12px;">
                ← Laporan Transaksi
            </a>
        </div>
    </form>
</div>

{{-- Judul Bulan --}}
<div style="font-size:18px; font-weight:700; color:#c8a97e; margin-bottom:16px;">
    📅 {{ $awalBulan->translatedFormat('F Y') }}
</div>

{{-- Ringkasan Bulan --}}
<div class="row g-3 mb-4">
    <div class="col-md-2">
        <div class="stat-card">
            <div class="accent-bar" style="background:#c8a97e;"></div>
            <div class="stat-label">Total Penjualan</div>
            <div class="stat-value" style="font-size:14px;">
                Rp {{ number_format($ringkasan['total_penjualan'], 0, ',', '.') }}
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="stat-card">
            <div class="accent-bar" style="background:#5b8dee;"></div>
            <div class="stat-label">Total Transaksi</div>
            <div class="stat-value">{{ $ringkasan['total_transaksi'] }}</div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="stat-card">
            <div class="accent-bar" style="background:#9b59b6;"></div>
            <div class="stat-label">Total Item Terjual</div>
            <div class="stat-value">{{ $ringkasan['total_item'] }}</div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="stat-card">
            <div class="accent-bar" style="background:#e07c3a;"></div>
            <div class="stat-label">Total Modal</div>
            <div class="stat-value" style="font-size:14px;">
                Rp {{ number_format($ringkasan['total_modal'], 0, ',', '.') }}
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="stat-card">
            <div class="accent-bar" style="background:#3ecf8e;"></div>
            <div class="stat-label">Total Laba</div>
            <div class="stat-value" style="font-size:14px; color:#3ecf8e;">
                Rp {{ number_format($ringkasan['total_laba'], 0, ',', '.') }}
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="stat-card">
            <div class="accent-bar" style="background:#f39c12;"></div>
            <div class="stat-label">Rata-rata/Hari</div>
            <div class="stat-value" style="font-size:14px;">
                Rp {{ number_format($ringkasan['rata_per_hari'], 0, ',', '.') }}
            </div>
        </div>
    </div>
</div>

{{-- Hari Terbaik --}}
@if($ringkasan['hari_terbaik'] && $ringkasan['hari_terbaik']['total_penjualan'] > 0)
<div class="chart-card mb-4" style="border:1px solid #c8a97e44; background:linear-gradient(135deg, #1a1d27, #1f2035);">
    <div style="display:flex; align-items:center; gap:16px;">
        <div style="font-size:32px;">🏆</div>
        <div>
            <div style="font-size:11px; color:#888; text-transform:uppercase; letter-spacing:0.06em;">Hari Terbaik Bulan Ini</div>
            <div style="font-size:16px; font-weight:700; color:#c8a97e; margin-top:2px;">
                {{ $ringkasan['hari_terbaik']['hari'] }},
                {{ $ringkasan['hari_terbaik']['tanggal_lengkap'] }}
            </div>
        </div>
        <div style="margin-left:auto; text-align:right;">
            <div style="font-size:11px; color:#888;">Penjualan</div>
            <div style="font-size:18px; font-weight:700; color:#3ecf8e;">
                Rp {{ number_format($ringkasan['hari_terbaik']['total_penjualan'], 0, ',', '.') }}
            </div>
        </div>
        <div style="text-align:right;">
            <div style="font-size:11px; color:#888;">Transaksi</div>
            <div style="font-size:18px; font-weight:700; color:#5b8dee;">
                {{ $ringkasan['hari_terbaik']['jumlah_transaksi'] }}x
            </div>
        </div>
        <div style="text-align:right;">
            <div style="font-size:11px; color:#888;">Laba</div>
            <div style="font-size:18px; font-weight:700; color:#c8a97e;">
                Rp {{ number_format($ringkasan['hari_terbaik']['laba'], 0, ',', '.') }}
            </div>
        </div>
    </div>
</div>
@endif

{{-- Tabel Harian --}}
<div class="chart-card">
    <div class="chart-title mb-3">📋 Rincian Per Hari</div>
    <table class="table-dark-custom">
        <thead>
            <tr>
                <th>Tgl</th>
                <th>Hari</th>
                <th class="text-end">Total Penjualan</th>
                <th class="text-center">Transaksi</th>
                <th class="text-center">Item Terjual</th>
                <th class="text-end">Modal</th>
                <th class="text-end">Laba</th>
                <th class="text-center">Margin</th>
                <th class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($dataHarian as $hari)
            <tr style="
                {{ $hari['is_today'] ? 'background:rgba(200,169,126,0.08); border-left:3px solid #c8a97e;' : '' }}
                {{ $hari['is_weekend'] && !$hari['is_today'] ? 'background:rgba(91,141,238,0.04);' : '' }}
            ">
                <td style="font-weight:700; color:{{ $hari['is_today'] ? '#c8a97e' : '#e8e6e0' }};">
                    {{ $hari['tanggal'] }}
                    @if($hari['is_today'])
                        <span style="font-size:9px; background:#c8a97e; color:#1a1208; padding:1px 4px; border-radius:4px; margin-left:4px;">Hari Ini</span>
                    @endif
                </td>
                <td style="color:{{ $hari['is_weekend'] ? '#5b8dee' : '#888' }}; font-size:11px;">
                    {{ $hari['hari'] }}
                </td>
                <td style="text-align:right; font-weight:600; color:{{ $hari['total_penjualan'] > 0 ? '#e8e6e0' : '#333' }};">
                    @if($hari['total_penjualan'] > 0)
                        Rp {{ number_format($hari['total_penjualan'], 0, ',', '.') }}
                    @else
                        <span style="color:#333;">—</span>
                    @endif
                </td>
                <td style="text-align:center; color:{{ $hari['jumlah_transaksi'] > 0 ? '#5b8dee' : '#333' }}; font-weight:600;">
                    {{ $hari['jumlah_transaksi'] > 0 ? $hari['jumlah_transaksi'] . 'x' : '—' }}
                </td>
                <td style="text-align:center; color:{{ $hari['jumlah_item'] > 0 ? '#9b59b6' : '#333' }}; font-weight:600;">
                    {{ $hari['jumlah_item'] > 0 ? $hari['jumlah_item'] . ' item' : '—' }}
                </td>
                <td style="text-align:right; color:#e07c3a; font-size:11px;">
                    @if($hari['modal'] > 0)
                        Rp {{ number_format($hari['modal'], 0, ',', '.') }}
                    @else
                        <span style="color:#333;">—</span>
                    @endif
                </td>
                <td style="text-align:right; font-weight:600; color:{{ $hari['laba'] > 0 ? '#3ecf8e' : ($hari['laba'] < 0 ? '#e07c7c' : '#333') }};">
                    @if($hari['total_penjualan'] > 0)
                        Rp {{ number_format($hari['laba'], 0, ',', '.') }}
                    @else
                        <span style="color:#333;">—</span>
                    @endif
                </td>
                <td style="text-align:center;">
                    @if($hari['total_penjualan'] > 0)
                        @php
                            $margin = $hari['total_penjualan'] > 0
                                ? ($hari['laba'] / $hari['total_penjualan']) * 100
                                : 0;
                        @endphp
                        <span style="
                            padding:2px 8px; border-radius:6px; font-size:10px; font-weight:600;
                            background:{{ $margin >= 30 ? 'rgba(62,207,142,0.12)' : ($margin >= 15 ? 'rgba(200,169,126,0.12)' : 'rgba(224,124,58,0.12)') }};
                            color:{{ $margin >= 30 ? '#3ecf8e' : ($margin >= 15 ? '#c8a97e' : '#e07c3a') }};">
                            {{ number_format($margin, 1) }}%
                        </span>
                    @else
                        <span style="color:#333;">—</span>
                    @endif
                </td>
                <td style="text-align:center;">
                    @if($hari['total_penjualan'] > 0)
                        <span class="badge-aktif">Ada Transaksi</span>
                    @else
                        <span style="color:#333; font-size:11px;">Tutup</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
        {{-- Total Row --}}
        <tfoot>
            <tr style="border-top:2px solid #c8a97e;">
                <td colspan="2" style="font-weight:700; color:#c8a97e; padding:10px;">TOTAL BULAN INI</td>
                <td style="text-align:right; font-weight:700; color:#c8a97e;">
                    Rp {{ number_format($ringkasan['total_penjualan'], 0, ',', '.') }}
                </td>
                <td style="text-align:center; font-weight:700; color:#5b8dee;">
                    {{ $ringkasan['total_transaksi'] }}x
                </td>
                <td style="text-align:center; font-weight:700; color:#9b59b6;">
                    {{ $ringkasan['total_item'] }} item
                </td>
                <td style="text-align:right; font-weight:700; color:#e07c3a;">
                    Rp {{ number_format($ringkasan['total_modal'], 0, ',', '.') }}
                </td>
                <td style="text-align:right; font-weight:700; color:#3ecf8e;">
                    Rp {{ number_format($ringkasan['total_laba'], 0, ',', '.') }}
                </td>
                <td style="text-align:center; font-weight:700; color:#c8a97e;">
                    @php
                        $marginTotal = $ringkasan['total_penjualan'] > 0
                            ? ($ringkasan['total_laba'] / $ringkasan['total_penjualan']) * 100
                            : 0;
                    @endphp
                    {{ number_format($marginTotal, 1) }}%
                </td>
                <td></td>
            </tr>
        </tfoot>
    </table>
</div>

@endsection