@extends('layouts.app')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="accent-bar" style="background:#c8a97e;"></div>
            <div class="stat-label">Omzet Hari Ini</div>
            <div class="stat-value">Rp {{ number_format($ringkasan['omzet_hari_ini'], 0, ',', '.') }}</div>
            <div class="stat-sub">{{ $ringkasan['total_transaksi_hari'] }} transaksi</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="accent-bar" style="background:#5b8dee;"></div>
            <div class="stat-label">Omzet Bulan Ini</div>
            <div class="stat-value">Rp {{ number_format($ringkasan['omzet_bulan_ini'], 0, ',', '.') }}</div>
            <div class="stat-sub">{{ $ringkasan['total_transaksi_bulan'] }} transaksi</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="accent-bar" style="background:#3ecf8e;"></div>
            <div class="stat-label">Transaksi Hari Ini</div>
            <div class="stat-value">{{ $ringkasan['total_transaksi_hari'] }}</div>
            <div class="stat-sub">transaksi selesai</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="accent-bar" style="background:#e07c3a;"></div>
            <div class="stat-label">Stok Menipis</div>
            <div class="stat-value" style="color:#e07c3a;">{{ $ringkasan['stok_menipis']->count() }}</div>
            <div class="stat-sub">bahan baku</div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-8">
        <div class="chart-card">
            <div class="chart-title">📊 Omzet 7 Hari Terakhir</div>
            <canvas id="chartHarian" height="100"></canvas>
        </div>
    </div>
    <div class="col-md-4">
        <div class="chart-card h-100">
            <div class="chart-title">🏆 Top 5 Menu</div>
            @foreach($ringkasan['top_menu']->take(5) as $menu)
                @php $max = $ringkasan['top_menu']->first()->total_terjual ?? 1; $pct = ($menu->total_terjual / $max) * 100; @endphp
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span style="font-size:12px;">{{ $menu->nama_menu }}</span>
                        <span style="font-size:11px; color:#c8a97e; font-weight:600;">{{ $menu->total_terjual }}x</span>
                    </div>
                    <div style="height:5px; background:#23262f; border-radius:3px;">
                        <div style="height:100%; width:{{ $pct }}%; background:#c8a97e; border-radius:3px;"></div>
                    </div>
                </div>
            @endforeach
            @if($ringkasan['top_menu']->isEmpty())
                <div style="text-align:center; color:#555; font-size:12px; padding:20px;">Belum ada transaksi.</div>
            @endif
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-6">
        <div class="chart-card">
            <div class="chart-title d-flex justify-content-between">
                <span>⚠️ Stok Menipis</span>
                <span style="color:#e07c3a; font-size:10px;">{{ $ringkasan['stok_menipis']->count() }} item</span>
            </div>
            @forelse($ringkasan['stok_menipis'] as $bahan)
                <div class="alert-row mb-2">
                    <div>
                        <div style="font-size:12px; font-weight:500;">{{ $bahan->nama_bahan }}</div>
                        <div style="font-size:10px; color:#555; margin-top:2px;">Min: {{ $bahan->stok_minimum }} {{ $bahan->satuan }}</div>
                    </div>
                    <div style="color:#e07c3a; font-weight:600; font-size:12px;">{{ $bahan->stok }} {{ $bahan->satuan }}</div>
                </div>
            @empty
                <div style="text-align:center; color:#555; font-size:12px; padding:20px;">✅ Semua stok aman</div>
            @endforelse
        </div>
    </div>
    <div class="col-md-6">
        <div class="chart-card">
            <div class="chart-title">📈 Grafik Bulanan</div>
            <canvas id="chartBulanan" height="130"></canvas>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const chartConfig = {
    plugins: { legend: { display: false } },
    scales: {
        x: { grid: { color: '#1e2130' }, ticks: { color: '#555', font: { size: 11 } } },
        y: { grid: { color: '#1e2130' }, ticks: { color: '#555', font: { size: 11 } } },
    }
};
const harian = @json($grafik7Hari);
new Chart(document.getElementById('chartHarian'), {
    type: 'bar',
    data: {
        labels: harian.map(d => d.tanggal),
        datasets: [{ data: harian.map(d => d.omzet), backgroundColor: 'rgba(200,169,126,0.7)', borderRadius: 6 }]
    },
    options: chartConfig,
});
const bulanan = @json($grafikBulanan);
new Chart(document.getElementById('chartBulanan'), {
    type: 'line',
    data: {
        labels: bulanan.map(d => d.bulan),
        datasets: [{ data: bulanan.map(d => d.omzet), borderColor: '#5b8dee', backgroundColor: 'rgba(91,141,238,0.08)', tension: 0.4, fill: true, pointBackgroundColor: '#5b8dee' }]
    },
    options: chartConfig,
});
</script>
@endpush