@extends('layouts.app')
@section('title', 'Laporan')
@section('page-title', 'Laporan Penjualan')

@section('content')
<div class="chart-card mb-3">
    <form method="GET" class="d-flex gap-3 align-items-center flex-wrap">
        <select name="filter" class="form-select" style="width:auto;">
            <option value="harian"   {{ $filter === 'harian'   ? 'selected' : '' }}>Hari Ini</option>
            <option value="mingguan" {{ $filter === 'mingguan' ? 'selected' : '' }}>Minggu Ini</option>
            <option value="bulanan"  {{ $filter === 'bulanan'  ? 'selected' : '' }}>Bulan Ini</option>
            <option value="custom"   {{ $filter === 'custom'   ? 'selected' : '' }}>Rentang Tanggal</option>
        </select>
        <input type="date" name="dari" value="{{ $dari }}" class="form-control" style="width:auto;">
        <input type="date" name="sampai" value="{{ $sampai }}" class="form-control" style="width:auto;">
        <button type="submit" class="btn-gold" style="padding:8px 20px; border-radius:8px;">Tampilkan</button>
        <a href="{{ route('owner.laporan.export-pdf', ['dari' => $dari, 'sampai' => $sampai]) }}"
           style="padding:8px 16px; background:#e07c3a; border:none; border-radius:8px; color:white; font-size:12px; font-weight:700; text-decoration:none;">
           📄 Export PDF
        </a>
        <a href="{{ route('owner.laporan.export-excel', ['dari' => $dari, 'sampai' => $sampai]) }}"
           style="padding:8px 16px; background:#3ecf8e; border:none; border-radius:8px; color:#1a2e1a; font-size:12px; font-weight:700; text-decoration:none;">
           📊 Export Excel
        </a>
    </form>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="accent-bar" style="background:#c8a97e;"></div>
            <div class="stat-label">Total Transaksi</div>
            <div class="stat-value">{{ $data['total_transaksi'] }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="accent-bar" style="background:#5b8dee;"></div>
            <div class="stat-label">Total Omzet</div>
            <div class="stat-value" style="font-size:16px;">Rp {{ number_format($data['omzet'], 0, ',', '.') }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="accent-bar" style="background:#3ecf8e;"></div>
            <div class="stat-label">Estimasi Laba (35%)</div>
            <div class="stat-value" style="font-size:16px; color:#3ecf8e;">Rp {{ number_format($data['estimasi_laba'], 0, ',', '.') }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="accent-bar" style="background:#e07c3a;"></div>
            <div class="stat-label">Periode</div>
            <div class="stat-value" style="font-size:13px;">{{ $data['dari'] }}</div>
            <div class="stat-sub">s/d {{ $data['sampai'] }}</div>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-5">
        <div class="chart-card">
            <div class="chart-title">🏆 Top Menu</div>
            <table class="table-dark-custom">
                <thead>
                    <tr>
                        <th>Menu</th>
                        <th>Qty</th>
                        <th>Omzet</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data['top_menu'] as $menu)
                    <tr>
                        <td>{{ $menu->nama_menu }}</td>
                        <td style="color:#c8a97e; font-weight:600;">{{ $menu->total_qty }}x</td>
                        <td style="color:#3ecf8e;">Rp {{ number_format($menu->total_omzet, 0, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" style="text-align:center; color:#555; padding:20px;">Belum ada data.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="col-md-7">
        <div class="chart-card">
            <div class="chart-title">🧾 Daftar Transaksi</div>
            <table class="table-dark-custom">
                <thead>
                    <tr>
                        <th>No. Transaksi</th>
                        <th>Kasir</th>
                        <th>Metode</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data['transaksi']->take(15) as $trx)
                    <tr>
                        <td style="font-size:11px;">{{ $trx->nomor_transaksi }}</td>
                        <td style="color:#888; font-size:11px;">{{ $trx->user->name }}</td>
                        <td style="font-size:11px;">{{ strtoupper($trx->metode_bayar) }}</td>
                        <td style="color:#3ecf8e; font-weight:600;">Rp {{ number_format($trx->total, 0, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" style="text-align:center; color:#555; padding:20px;">Belum ada transaksi.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection