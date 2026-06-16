@extends('layouts.app')
@section('title', 'Laporan Kasir')
@section('page-title', 'Laporan Kasir')

@section('content')

{{-- Filter Tanggal --}}
<div class="chart-card mb-3">
    <form method="GET" class="d-flex gap-3 align-items-center flex-wrap">
        <div>
            <label style="font-size:11px; color:#888; text-transform:uppercase; display:block; margin-bottom:4px;">
                Dari Tanggal
            </label>
            <input type="date" name="dari" value="{{ $dari }}" class="form-control" style="width:auto;">
        </div>
        <div>
            <label style="font-size:11px; color:#888; text-transform:uppercase; display:block; margin-bottom:4px;">
                Sampai Tanggal
            </label>
            <input type="date" name="sampai" value="{{ $sampai }}" class="form-control" style="width:auto;">
        </div>
        <div style="padding-top:20px;">
            <button type="submit" class="btn-gold" style="padding:8px 20px; border-radius:8px;">
                Tampilkan
            </button>
        </div>
        <div style="padding-top:20px;">
            <button type="button" onclick="window.print()"
                    style="padding:8px 16px; background:#5b8dee; border:none; border-radius:8px; color:white; font-size:12px; font-weight:700; cursor:pointer;">
                🖨️ Print
            </button>
        </div>
    </form>
</div>

{{-- Ringkasan Total --}}
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="accent-bar" style="background:#c8a97e;"></div>
            <div class="stat-label">Total Pembayaran</div>
            <div class="stat-value" style="font-size:16px;">
                Rp {{ number_format($transaksi->sum('total'), 0, ',', '.') }}
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="accent-bar" style="background:#5b8dee;"></div>
            <div class="stat-label">Total Transaksi</div>
            <div class="stat-value">{{ $transaksi->count() }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="accent-bar" style="background:#3ecf8e;"></div>
            <div class="stat-label">Periode</div>
            <div class="stat-value" style="font-size:13px;">
                {{ \Carbon\Carbon::parse($dari)->format('d M Y') }}
            </div>
            <div class="stat-sub">s/d {{ \Carbon\Carbon::parse($sampai)->format('d M Y') }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="accent-bar" style="background:#9b59b6;"></div>
            <div class="stat-label">Total Item Terjual</div>
            <div class="stat-value">
                {{ $transaksi->sum(fn($t) => $t->detailTransaksi->sum('qty')) }}
            </div>
        </div>
    </div>
</div>

{{-- Data Per Tanggal --}}
@forelse($perTanggal as $tanggal => $trxHari)

@php
    $totalHari   = $trxHari->sum('total');
    $tglFormatted = \Carbon\Carbon::parse($tanggal)->translatedFormat('l, d F Y');
@endphp

<div class="chart-card mb-3 no-break">

    {{-- Header Tanggal --}}
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px; padding-bottom:10px; border-bottom:1px solid #23262f;">
        <div>
            <div style="font-size:14px; font-weight:700; color:#c8a97e;">
                📅 {{ $tglFormatted }}
            </div>
            <div style="font-size:11px; color:#888; margin-top:2px;">
                {{ $trxHari->count() }} transaksi
            </div>
        </div>
        <div style="text-align:right;">
            <div style="font-size:11px; color:#888;">Total Hari Ini</div>
            <div style="font-size:18px; font-weight:700; color:#3ecf8e;">
                Rp {{ number_format($totalHari, 0, ',', '.') }}
            </div>
        </div>
    </div>

    {{-- Tabel Transaksi Hari Ini --}}
    <table class="table-dark-custom">
        <thead>
            <tr>
                <th>No. Transaksi</th>
                <th>Waktu</th>
                <th>Kasir</th>
                <th>Yang Dipesan</th>
                <th>Metode</th>
                <th class="text-end" style="text-align:right;">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($trxHari as $trx)
            <tr>
                <td style="font-size:11px; color:#c8a97e; font-weight:600;">
                    {{ $trx->nomor_transaksi }}
                </td>
                <td style="color:#888; font-size:11px;">
                    {{ $trx->tanggal->format('H:i') }}
                </td>
                <td style="font-size:12px;">
                    {{ $trx->user->name }}
                </td>
                <td>
                    {{-- Detail pesanan --}}
                    @foreach($trx->detailTransaksi as $detail)
                        <div style="font-size:11px; margin-bottom:2px;">
                            <span style="color:#e8e6e0;">{{ $detail->nama_menu }}</span>
                            <span style="color:#888;"> × {{ $detail->qty }}</span>
                            <span style="color:#c8a97e; float:right;">
                                Rp {{ number_format($detail->subtotal, 0, ',', '.') }}
                            </span>
                        </div>
                    @endforeach
                </td>
                <td>
                    <span style="
                        padding:3px 8px; border-radius:6px; font-size:10px; font-weight:600;
                        background:{{ $trx->metode_bayar === 'cash' ? 'rgba(62,207,142,0.12)' : ($trx->metode_bayar === 'qris' ? 'rgba(91,141,238,0.12)' : 'rgba(200,169,126,0.12)') }};
                        color:{{ $trx->metode_bayar === 'cash' ? '#3ecf8e' : ($trx->metode_bayar === 'qris' ? '#5b8dee' : '#c8a97e') }};">
                        {{ strtoupper($trx->metode_bayar) }}
                    </span>
                </td>
                <td style="text-align:right; font-weight:700; color:#e8e6e0;">
                    Rp {{ number_format($trx->total, 0, ',', '.') }}
                </td>
            </tr>
            @endforeach
        </tbody>

        {{-- Total Per Tanggal --}}
        <tfoot>
            <tr style="border-top:2px solid #c8a97e44;">
                <td colspan="3" style="padding:10px; font-weight:700; color:#888; font-size:12px;">
                    Total {{ \Carbon\Carbon::parse($tanggal)->format('d/m/Y') }}
                </td>
                <td style="padding:10px;">
                    <div style="font-size:11px; color:#666;">
                        {{ $trxHari->sum(fn($t) => $t->detailTransaksi->sum('qty')) }} item terjual
                    </div>
                </td>
                <td style="padding:10px; color:#888; font-size:11px;">
                    {{ $trxHari->count() }} transaksi
                </td>
                <td style="text-align:right; padding:10px; font-size:16px; font-weight:700; color:#3ecf8e;">
                    Rp {{ number_format($totalHari, 0, ',', '.') }}
                </td>
            </tr>
        </tfoot>
    </table>
</div>

@empty
<div class="chart-card" style="text-align:center; padding:60px 20px;">
    <div style="font-size:40px; margin-bottom:12px;">📋</div>
    <div style="font-size:14px; color:#888;">Tidak ada transaksi pada periode ini.</div>
    <div style="font-size:12px; color:#555; margin-top:6px;">Silakan pilih rentang tanggal yang berbeda.</div>
</div>
@endforelse

@endsection

@push('styles')
<style>
    @media print {
        .sidebar, .topbar, .no-print { display: none !important; }
        .main-content { margin-left: 0 !important; }
        .content-body { padding: 10px !important; }
        .chart-card {
            border: 1px solid #ddd !important;
            background: white !important;
            color: black !important;
            margin-bottom: 16px !important;
        }
        .no-break { page-break-inside: avoid; }
        table { font-size: 10px !important; }
        th, td { color: black !important; border-color: #ddd !important; }
        .stat-card { display: none; }
    }
</style>
@endpush