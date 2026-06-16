@extends('layouts.app')
@section('title', 'Riwayat Stok')
@section('page-title', 'Riwayat Stok')

@section('content')
<div class="chart-card">
    <form method="GET" class="d-flex gap-2 mb-4 flex-wrap">
        <select name="bahan_baku_id" style="background:#0f1117; border:1px solid #2a2d38; color:#e8e6e0; border-radius:8px; padding:8px 12px; font-size:12px;">
            <option value="">Semua Bahan</option>
            @foreach($bahanBaku as $b)
                <option value="{{ $b->id }}" {{ request('bahan_baku_id') == $b->id ? 'selected' : '' }}>
                    {{ $b->nama_bahan }}
                </option>
            @endforeach
        </select>
        <select name="tipe" style="background:#0f1117; border:1px solid #2a2d38; color:#e8e6e0; border-radius:8px; padding:8px 12px; font-size:12px;">
            <option value="">Semua Tipe</option>
            <option value="masuk" {{ request('tipe') === 'masuk' ? 'selected' : '' }}>Masuk</option>
            <option value="keluar" {{ request('tipe') === 'keluar' ? 'selected' : '' }}>Keluar</option>
            <option value="penyesuaian" {{ request('tipe') === 'penyesuaian' ? 'selected' : '' }}>Penyesuaian</option>
        </select>
        <input type="date" name="dari" value="{{ request('dari') }}"
               style="background:#0f1117; border:1px solid #2a2d38; color:#e8e6e0; border-radius:8px; padding:8px 12px; font-size:12px;">
        <input type="date" name="sampai" value="{{ request('sampai') }}"
               style="background:#0f1117; border:1px solid #2a2d38; color:#e8e6e0; border-radius:8px; padding:8px 12px; font-size:12px;">
        <button type="submit" style="padding:8px 20px; background:#1a1d27; border:1px solid #2a2d38; color:#888; border-radius:8px; font-size:12px; cursor:pointer;">
            Filter
        </button>
    </form>

    <table class="table-dark-custom">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Bahan Baku</th>
                <th>Tipe</th>
                <th>Jumlah</th>
                <th>Sebelum</th>
                <th>Sesudah</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($riwayat as $log)
            <tr>
                <td style="color:#888; font-size:11px;">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                <td style="font-weight:500;">{{ $log->bahanBaku->nama_bahan }}</td>
                <td>
                    @php
                        $warna = match($log->tipe) {
                            'masuk'       => ['bg' => 'rgba(62,207,142,0.12)',  'text' => '#3ecf8e'],
                            'keluar'      => ['bg' => 'rgba(224,124,122,0.12)', 'text' => '#e07c7c'],
                            default       => ['bg' => 'rgba(91,141,238,0.12)',  'text' => '#5b8dee'],
                        };
                    @endphp
                    <span style="padding:3px 8px; border-radius:6px; font-size:10px; font-weight:600;
                          background:{{ $warna['bg'] }}; color:{{ $warna['text'] }};">
                        {{ ucfirst($log->tipe) }}
                    </span>
                </td>
                <td style="font-weight:600; color:{{ $log->tipe === 'masuk' ? '#3ecf8e' : '#e07c7c' }};">
                    {{ $log->tipe === 'masuk' ? '+' : '-' }}{{ $log->jumlah }} {{ $log->bahanBaku->satuan }}
                </td>
                <td style="color:#888;">{{ $log->stok_sebelum }}</td>
                <td style="color:#c8a97e; font-weight:500;">{{ $log->stok_sesudah }}</td>
                <td style="color:#666; font-size:11px;">{{ $log->keterangan ?? '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align:center; color:#555; padding:40px;">Belum ada riwayat stok.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="mt-3">{{ $riwayat->links() }}</div>
</div>
@endsection