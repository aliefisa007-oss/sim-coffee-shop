@extends('layouts.app')
@section('title', 'Riwayat Transaksi')
@section('page-title', 'Riwayat Transaksi')

@section('content')
<div class="chart-card">
    <table class="table-dark-custom">
        <thead>
            <tr>
                <th>No. Transaksi</th>
                <th>Kasir</th>
                <th>Metode</th>
                <th>Total</th>
                <th>Waktu</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transaksi as $trx)
            <tr>
                <td style="color:#e8e6e0; font-weight:500;">{{ $trx->nomor_transaksi }}</td>
                <td style="color:#888;">{{ $trx->user->name }}</td>
                <td>
                    <span style="padding:3px 8px; border-radius:6px; font-size:10px; font-weight:600;
                        background:{{ $trx->metode_bayar === 'cash' ? 'rgba(62,207,142,0.12)' : ($trx->metode_bayar === 'qris' ? 'rgba(91,141,238,0.12)' : 'rgba(200,169,126,0.12)') }};
                        color:{{ $trx->metode_bayar === 'cash' ? '#3ecf8e' : ($trx->metode_bayar === 'qris' ? '#5b8dee' : '#c8a97e') }};">
                        {{ strtoupper($trx->metode_bayar) }}
                    </span>
                </td>
                <td style="color:#3ecf8e; font-weight:600;">Rp {{ number_format($trx->total, 0, ',', '.') }}</td>
                <td style="color:#666;">{{ $trx->tanggal->format('d/m/Y H:i') }}</td>
            </tr>
            @empty
            <tr><td colspan="5" style="text-align:center; color:#555; padding:40px;">Belum ada transaksi.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="mt-3">{{ $transaksi->links() }}</div>
</div>
@endsection