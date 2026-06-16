<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Penjualan</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 10px; color: #333; padding: 20px; }

        .header { text-align: center; margin-bottom: 16px; border-bottom: 2px solid #c8a97e; padding-bottom: 12px; }
        .header h2 { font-size: 16px; color: #c8a97e; margin-bottom: 4px; }
        .header p { font-size: 10px; color: #666; }

        .summary { display: table; width: 100%; margin-bottom: 16px; border-collapse: collapse; }
        .summary-item { display: table-cell; padding: 8px 12px; background: #f9f9f9; border: 1px solid #ddd; text-align: center; }
        .summary-item .label { font-size: 9px; color: #888; text-transform: uppercase; letter-spacing: 0.04em; }
        .summary-item .value { font-size: 13px; font-weight: bold; margin-top: 3px; }

        table { width: 100%; border-collapse: collapse; margin-top: 8px; font-size: 9px; }
        thead th {
            background: #1a1d27;
            color: white;
            padding: 6px 5px;
            text-align: left;
            font-size: 9px;
            letter-spacing: 0.03em;
        }
        tbody td { padding: 5px; border-bottom: 1px solid #eee; vertical-align: middle; }
        tbody tr:nth-child(even) td { background: #f9f9f9; }
        tbody tr:hover td { background: #fff3e0; }

        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .text-green { color: #2e7d32; font-weight: 600; }
        .text-red { color: #c62828; font-weight: 600; }
        .text-gold { color: #c8a97e; font-weight: 600; }

        .tfoot-row td {
            font-weight: bold;
            background: #f0f0f0;
            border-top: 2px solid #c8a97e;
            padding: 6px 5px;
        }

        .badge {
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 8px;
            font-weight: 600;
        }
        .badge-cash     { background: #e8f5e9; color: #2e7d32; }
        .badge-qris     { background: #e3f2fd; color: #1565c0; }
        .badge-transfer { background: #fff3e0; color: #e65100; }

        .footer { margin-top: 20px; text-align: right; font-size: 9px; color: #999; border-top: 1px solid #eee; padding-top: 8px; }

        .page-break { page-break-after: always; }
    </style>
</head>
<body>

{{-- Header --}}
<div class="header">
    <h2>☕ CONTACT COFFEE — Laporan Penjualan</h2>
    <p>Periode: {{ \Carbon\Carbon::parse($dari)->format('d M Y') }} — {{ \Carbon\Carbon::parse($sampai)->format('d M Y') }}</p>
    <p>Dicetak: {{ now()->format('d M Y H:i:s') }}</p>
</div>

{{-- Summary --}}
@php
    $totalModal  = 0;
    $totalProfit = 0;
    foreach($data['transaksi'] as $trx) {
        foreach($trx->detailTransaksi as $detail) {
            $modalPerUnit = 0;
            if ($detail->menu && $detail->menu->resepProduk) {
                foreach($detail->menu->resepProduk as $resep) {
                    $modalPerUnit += $resep->jumlah * $resep->bahanBaku->harga_per_satuan;
                }
            }
            $totalModal  += $modalPerUnit * $detail->qty;
            $totalProfit += $detail->subtotal - ($modalPerUnit * $detail->qty);
        }
    }
@endphp

<table style="margin-bottom:16px; border-collapse:collapse; width:100%;">
    <tr>
        <td style="padding:8px; background:#f9f9f9; border:1px solid #ddd; text-align:center; width:16%;">
            <div style="font-size:9px; color:#888;">TRANSAKSI</div>
            <div style="font-size:14px; font-weight:bold;">{{ $data['total_transaksi'] }}</div>
        </td>
        <td style="padding:8px; background:#f9f9f9; border:1px solid #ddd; text-align:center; width:21%;">
            <div style="font-size:9px; color:#888;">TOTAL OMZET</div>
            <div style="font-size:12px; font-weight:bold; color:#c8a97e;">Rp {{ number_format($data['omzet'], 0, ',', '.') }}</div>
        </td>
        <td style="padding:8px; background:#f9f9f9; border:1px solid #ddd; text-align:center; width:21%;">
            <div style="font-size:9px; color:#888;">TOTAL MODAL</div>
            <div style="font-size:12px; font-weight:bold; color:#e07c3a;">Rp {{ number_format($totalModal, 0, ',', '.') }}</div>
        </td>
        <td style="padding:8px; background:#f9f9f9; border:1px solid #ddd; text-align:center; width:21%;">
            <div style="font-size:9px; color:#888;">TOTAL PROFIT</div>
            <div style="font-size:12px; font-weight:bold; color:#2e7d32;">Rp {{ number_format($totalProfit, 0, ',', '.') }}</div>
        </td>
        <td style="padding:8px; background:#f9f9f9; border:1px solid #ddd; text-align:center; width:21%;">
            <div style="font-size:9px; color:#888;">MARGIN</div>
            <div style="font-size:12px; font-weight:bold; color:#1565c0;">
                {{ $data['omzet'] > 0 ? number_format(($totalProfit / $data['omzet']) * 100, 1) : 0 }}%
            </div>
        </td>
    </tr>
</table>

{{-- Detail Transaksi --}}
<table>
    <thead>
        <tr>
            <th>No. Transaksi</th>
            <th>Tanggal</th>
            <th>Jam</th>
            <th>Kasir</th>
            <th>Menu</th>
            <th class="text-center">Qty</th>
            <th class="text-right">Harga</th>
            <th class="text-right">Subtotal</th>
            <th class="text-right">Modal</th>
            <th class="text-right">Profit</th>
            <th class="text-right">Total Bayar</th>
            <th class="text-center">Metode</th>
        </tr>
    </thead>
    <tbody>
        @php $grandTotal = 0; $grandModal = 0; $grandProfit = 0; @endphp
        @forelse($data['transaksi'] as $trx)
            @foreach($trx->detailTransaksi as $detail)
                @php
                    $modalPerUnit = 0;
                    if ($detail->menu && $detail->menu->resepProduk) {
                        foreach($detail->menu->resepProduk as $resep) {
                            $modalPerUnit += $resep->jumlah * $resep->bahanBaku->harga_per_satuan;
                        }
                    }
                    $totalModalItem  = $modalPerUnit * $detail->qty;
                    $profitItem      = $detail->subtotal - $totalModalItem;
                    $grandTotal     += $detail->subtotal;
                    $grandModal     += $totalModalItem;
                    $grandProfit    += $profitItem;
                @endphp
                <tr>
                    <td style="font-size:8px;">{{ $trx->nomor_transaksi }}</td>
                    <td class="text-center">{{ $trx->tanggal->format('d/m/Y') }}</td>
                    <td class="text-center">{{ $trx->tanggal->format('H:i') }}</td>
                    <td>{{ $trx->user->name }}</td>
                    <td>{{ $detail->nama_menu }}</td>
                    <td class="text-center">{{ $detail->qty }}</td>
                    <td class="text-right">{{ number_format($detail->harga_saat_transaksi, 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                    <td class="text-right" style="color:#e07c3a;">{{ number_format($totalModalItem, 0, ',', '.') }}</td>
                    <td class="text-right {{ $profitItem >= 0 ? 'text-green' : 'text-red' }}">
                        {{ number_format($profitItem, 0, ',', '.') }}
                    </td>
                    <td class="text-right text-gold">{{ number_format($trx->total, 0, ',', '.') }}</td>
                    <td class="text-center">
                        <span class="badge badge-{{ $trx->metode_bayar }}">
                            {{ strtoupper($trx->metode_bayar) }}
                        </span>
                    </td>
                </tr>
            @endforeach
        @empty
            <tr>
                <td colspan="12" style="text-align:center; color:#999; padding:20px;">
                    Tidak ada transaksi pada periode ini.
                </td>
            </tr>
        @endforelse
    </tbody>
    @if($data['transaksi']->count() > 0)
    <tfoot>
        <tr class="tfoot-row">
            <td colspan="7" style="text-align:right; padding:6px 5px; font-size:10px;">TOTAL</td>
            <td class="text-right" style="padding:6px 5px;">{{ number_format($grandTotal, 0, ',', '.') }}</td>
            <td class="text-right" style="padding:6px 5px; color:#e07c3a;">{{ number_format($grandModal, 0, ',', '.') }}</td>
            <td class="text-right {{ $grandProfit >= 0 ? 'text-green' : 'text-red' }}" style="padding:6px 5px;">
                {{ number_format($grandProfit, 0, ',', '.') }}
            </td>
            <td class="text-right text-gold" style="padding:6px 5px;">{{ number_format($data['omzet'], 0, ',', '.') }}</td>
            <td></td>
        </tr>
    </tfoot>
    @endif
</table>

{{-- Footer --}}
<div class="footer">
    Dicetak oleh: {{ auth()->user()->name }} — SIM Coffee Shop Contact Coffee
</div>

</body>
</html>