<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk #{{ $transaksi->nomor_transaksi }}</title>
    <style>
        /* ── RESET ── */
        * { margin: 0; padding: 0; box-sizing: border-box; }

        /* ── SCREEN VIEW ── */
        body {
            background: #f0f0f0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            min-height: 100vh;
            padding: 30px 10px;
            font-family: 'Courier New', Courier, monospace;
        }

        .receipt-wrapper {
            background: #fff;
            width: 320px;
            padding: 24px 20px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.13);
            border-radius: 4px;
            position: relative;
        }

        /* Efek gigi atas struk */
        .receipt-wrapper::before {
            content: '';
            display: block;
            height: 12px;
            background:
                radial-gradient(circle at 6px 12px, #f0f0f0 6px, transparent 6px),
                radial-gradient(circle at 18px 12px, #f0f0f0 6px, transparent 6px),
                radial-gradient(circle at 30px 12px, #f0f0f0 6px, transparent 6px),
                radial-gradient(circle at 42px 12px, #f0f0f0 6px, transparent 6px),
                radial-gradient(circle at 54px 12px, #f0f0f0 6px, transparent 6px),
                radial-gradient(circle at 66px 12px, #f0f0f0 6px, transparent 6px),
                radial-gradient(circle at 78px 12px, #f0f0f0 6px, transparent 6px),
                radial-gradient(circle at 90px 12px, #f0f0f0 6px, transparent 6px),
                radial-gradient(circle at 102px 12px, #f0f0f0 6px, transparent 6px),
                radial-gradient(circle at 114px 12px, #f0f0f0 6px, transparent 6px),
                radial-gradient(circle at 126px 12px, #f0f0f0 6px, transparent 6px),
                radial-gradient(circle at 138px 12px, #f0f0f0 6px, transparent 6px),
                radial-gradient(circle at 150px 12px, #f0f0f0 6px, transparent 6px),
                radial-gradient(circle at 162px 12px, #f0f0f0 6px, transparent 6px),
                radial-gradient(circle at 174px 12px, #f0f0f0 6px, transparent 6px),
                radial-gradient(circle at 186px 12px, #f0f0f0 6px, transparent 6px),
                radial-gradient(circle at 198px 12px, #f0f0f0 6px, transparent 6px),
                radial-gradient(circle at 210px 12px, #f0f0f0 6px, transparent 6px),
                radial-gradient(circle at 222px 12px, #f0f0f0 6px, transparent 6px),
                radial-gradient(circle at 234px 12px, #f0f0f0 6px, transparent 6px),
                radial-gradient(circle at 246px 12px, #f0f0f0 6px, transparent 6px),
                radial-gradient(circle at 258px 12px, #f0f0f0 6px, transparent 6px),
                radial-gradient(circle at 270px 12px, #f0f0f0 6px, transparent 6px),
                radial-gradient(circle at 282px 12px, #f0f0f0 6px, transparent 6px),
                radial-gradient(circle at 294px 12px, #f0f0f0 6px, transparent 6px),
                radial-gradient(circle at 306px 12px, #f0f0f0 6px, transparent 6px);
            background-size: 12px 12px;
            margin: -24px -20px 16px;
        }

        /* ── HEADER ── */
        .header { text-align: center; margin-bottom: 16px; }

        .logo-area {
            width: 64px;
            height: 64px;
            background: #1a1208;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 10px;
            font-size: 28px;
        }

        .cafe-name {
            font-size: 17px;
            font-weight: 900;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: #1a1208;
            line-height: 1.2;
        }

        .cafe-tagline {
            font-size: 9px;
            color: #888;
            letter-spacing: 1px;
            margin-top: 3px;
            font-style: italic;
        }

        .cafe-info {
            font-size: 9px;
            color: #666;
            margin-top: 8px;
            line-height: 1.7;
        }

        /* ── DIVIDER ── */
        .divider-dash {
            border: none;
            border-top: 1px dashed #bbb;
            margin: 12px 0;
        }
        .divider-solid {
            border: none;
            border-top: 1px solid #333;
            margin: 12px 0;
        }
        .divider-double {
            border: none;
            border-top: 3px double #333;
            margin: 12px 0;
        }

        /* ── ORDER INFO ── */
        .order-number {
            text-align: center;
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 1px;
            margin-bottom: 10px;
            color: #1a1208;
        }

        .info-table { width: 100%; font-size: 10px; }
        .info-table tr td { padding: 1.5px 0; }
        .info-table .label { color: #666; width: 45%; }
        .info-table .value { color: #1a1208; font-weight: 600; text-align: right; }

        /* ── ITEMS ── */
        .items-header {
            display: flex;
            justify-content: space-between;
            font-size: 9px;
            font-weight: 700;
            letter-spacing: 1px;
            color: #888;
            text-transform: uppercase;
            margin-bottom: 6px;
        }

        .item-row { margin-bottom: 8px; }
        .item-row .item-name {
            font-size: 11px;
            font-weight: 700;
            color: #1a1208;
        }
        .item-row .item-detail {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 10px;
            color: #555;
            margin-top: 1px;
        }
        .item-row .item-subtotal {
            font-weight: 700;
            color: #1a1208;
        }

        /* ── PAYMENT ── */
        .payment-row {
            display: flex;
            justify-content: space-between;
            font-size: 10px;
            margin-bottom: 3px;
            color: #444;
        }
        .payment-row.discount { color: #e07c3a; }
        .payment-row.tax { color: #888; }

        .total-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 6px 0;
        }
        .total-row .total-label {
            font-size: 14px;
            font-weight: 900;
            letter-spacing: 1px;
            color: #1a1208;
        }
        .total-row .total-value {
            font-size: 16px;
            font-weight: 900;
            color: #1a1208;
        }

        .payment-method-badge {
            display: inline-block;
            padding: 3px 10px;
            background: #1a1208;
            color: #fff;
            font-size: 9px;
            font-weight: 700;
            letter-spacing: 2px;
            border-radius: 2px;
            margin-top: 4px;
        }

        .kembalian-row {
            display: flex;
            justify-content: space-between;
            font-size: 11px;
            font-weight: 600;
            color: #2e7d32;
            margin-top: 4px;
        }

        /* ── CATATAN ── */
        .catatan-box {
            background: #f9f6f0;
            border: 1px dashed #c8a97e;
            border-radius: 4px;
            padding: 8px 10px;
            margin: 10px 0;
        }
        .catatan-label {
            font-size: 9px;
            font-weight: 700;
            letter-spacing: 1px;
            color: #888;
            text-transform: uppercase;
            margin-bottom: 3px;
        }
        .catatan-text {
            font-size: 11px;
            color: #1a1208;
            font-weight: 600;
        }

        /* ── BARCODE TEXT ── */
        .barcode-area {
            text-align: center;
            margin: 12px 0;
        }
        .barcode-text {
            font-size: 8px;
            letter-spacing: 4px;
            color: #bbb;
        }
        .barcode-num {
            font-size: 9px;
            color: #aaa;
            margin-top: 2px;
            letter-spacing: 2px;
        }

        /* ── FOOTER ── */
        .footer {
            text-align: center;
            margin-top: 10px;
        }
        .footer-main {
            font-size: 11px;
            font-weight: 700;
            color: #1a1208;
            letter-spacing: 1px;
        }
        .footer-sub {
            font-size: 9px;
            color: #888;
            margin-top: 3px;
        }
        .footer-hashtag {
            font-size: 10px;
            color: #c8a97e;
            font-weight: 700;
            margin-top: 6px;
            letter-spacing: 1px;
        }

        /* ── ACTION BUTTONS ── */
        .action-buttons {
            display: flex;
            gap: 8px;
            justify-content: center;
            margin-top: 20px;
            width: 320px;
        }
        .btn-print {
            flex: 1;
            padding: 12px;
            background: #1a1208;
            border: none;
            border-radius: 8px;
            color: #c8a97e;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            letter-spacing: 1px;
        }
        .btn-close {
            padding: 12px 16px;
            background: #f0f0f0;
            border: none;
            border-radius: 8px;
            color: #666;
            font-size: 13px;
            cursor: pointer;
        }
        .btn-pdf {
            padding: 12px 16px;
            background: #e07c3a;
            border: none;
            border-radius: 8px;
            color: #fff;
            font-size: 13px;
            cursor: pointer;
        }

        /* ── PRINT STYLES ── */
        @media print {
            body {
                background: #fff;
                padding: 0;
                display: block;
            }

            .receipt-wrapper {
                box-shadow: none;
                border-radius: 0;
                width: 100%;
                padding: 8px 10px;
            }

            .receipt-wrapper::before { display: none; }
            .action-buttons { display: none !important; }

            /* 58mm thermal */
            @page {
                size: 58mm auto;
                margin: 2mm;
            }

            .cafe-name { font-size: 14px; }
            .total-row .total-value { font-size: 14px; }
        }
    </style>
</head>
<body>

<div class="receipt-wrapper">

    {{-- ── HEADER ── --}}
    <div class="header">
        <div class="logo-area">
    <img src="{{ asset('images/logo.png') }}"
         alt="Contact Coffee"
         style="width:64px; height:64px; border-radius:50%; object-fit:cover;">
</div>
        <div class="cafe-name">CONTACT COFFEE<br>& EATERY</div>
        <div class="cafe-tagline">Cozy place • Coffee • Friendliness</div>
        <div class="cafe-info">
            Jl. Tidar, Kloncing, Sumbersari, Jember<br>
            +62821-8734-876 · @contact.coffee
        </div>
    </div>

    <hr class="divider-double">

    {{-- ── ORDER INFO ── --}}
    <div class="order-number">
        ── ORDER #{{ str_pad($transaksi->id, 3, '0', STR_PAD_LEFT) }} ──
    </div>

    <table class="info-table">
        <tr>
            <td class="label">No. Transaksi</td>
            <td class="value">{{ $transaksi->nomor_transaksi }}</td>
        </tr>
        <tr>
            <td class="label">Tanggal</td>
            <td class="value">{{ $transaksi->tanggal->format('d/m/Y') }}</td>
        </tr>
        <tr>
            <td class="label">Jam</td>
            <td class="value">{{ $transaksi->tanggal->format('H:i:s') }}</td>
        </tr>
        <tr>
            <td class="label">Kasir</td>
            <td class="value">{{ $transaksi->user->name }}</td>
        </tr>
        <tr>
            <td class="label">Metode Bayar</td>
            <td class="value">{{ strtoupper($transaksi->metode_bayar) }}</td>
        </tr>
    </table>

    <hr class="divider-dash">

    {{-- ── ITEMS ── --}}
    <div class="items-header">
        <span>Item</span>
        <span>Qty &nbsp; Total</span>
    </div>

    @foreach($transaksi->detailTransaksi as $detail)
    <div class="item-row">
        <div class="item-name">{{ $detail->nama_menu }}</div>
        <div class="item-detail">
            <span>Rp {{ number_format($detail->harga_saat_transaksi, 0, ',', '.') }} / pcs</span>
            <span>
                {{ $detail->qty }}x &nbsp;
                <span class="item-subtotal">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</span>
            </span>
        </div>
    </div>
    @endforeach

    <hr class="divider-dash">

    {{-- ── PAYMENT DETAIL ── --}}
    <div class="payment-row">
        <span>Subtotal</span>
        <span>Rp {{ number_format($transaksi->subtotal, 0, ',', '.') }}</span>
    </div>

    @if($transaksi->diskon > 0)
    <div class="payment-row discount">
        <span>Diskon</span>
        <span>- Rp {{ number_format($transaksi->diskon, 0, ',', '.') }}</span>
    </div>
    @endif

    @if($transaksi->pajak > 0)
    <div class="payment-row tax">
        <span>Pajak (10%)</span>
        <span>Rp {{ number_format($transaksi->pajak, 0, ',', '.') }}</span>
    </div>
    @endif

    <hr class="divider-solid">

    <div class="total-row">
        <span class="total-label">TOTAL</span>
        <span class="total-value">Rp {{ number_format($transaksi->total, 0, ',', '.') }}</span>
    </div>

    {{-- Metode Bayar Badge --}}
    <div style="text-align:center; margin-top:6px;">
        <span class="payment-method-badge">{{ strtoupper($transaksi->metode_bayar) }}</span>
    </div>

    {{-- Kembalian (hanya untuk cash) --}}
    @if($transaksi->metode_bayar === 'cash' && isset($transaksi->uang_bayar) && $transaksi->uang_bayar > 0)
    <div class="kembalian-row" style="margin-top:8px;">
        <span>Uang Bayar</span>
        <span>Rp {{ number_format($transaksi->uang_bayar, 0, ',', '.') }}</span>
    </div>
    <div class="kembalian-row">
        <span>Kembalian</span>
        <span>Rp {{ number_format($transaksi->uang_bayar - $transaksi->total, 0, ',', '.') }}</span>
    </div>
    @endif

    {{-- ── CATATAN ── --}}
    @if($transaksi->catatan)
    <div class="catatan-box">
        <div class="catatan-label">📝 Catatan</div>
        <div class="catatan-text">{{ $transaksi->catatan }}</div>
    </div>
    @endif

    <hr class="divider-dash">

    {{-- ── BARCODE AREA ── --}}
    <div class="barcode-area">
        <div class="barcode-text">||||  ||||||||  ||||  |||||||  ||||</div>
        <div class="barcode-num">{{ $transaksi->nomor_transaksi }}</div>
    </div>

    <hr class="divider-dash">

    {{-- ── FOOTER ── --}}
    <div class="footer">
        <div class="footer-main">Terimakasih sudah mampir ❤️</div>
        <div class="footer-sub">Sampai jumpa kembali</div>
        <div class="footer-hashtag">#contactpeople</div>
    </div>

</div>

{{-- ── ACTION BUTTONS ── --}}
<div class="action-buttons no-print">
    <button class="btn-print" onclick="window.print()">🖨️ Cetak Struk</button>
    <button class="btn-pdf" onclick="downloadPDF()">📄 PDF</button>
    <button class="btn-close" onclick="window.close()">✕ Tutup</button>
</div>

<script>
function downloadPDF() {
    window.print();
}

// Auto print kalau dibuka dari POS
const urlParams = new URLSearchParams(window.location.search);
if (urlParams.get('autoprint') === '1') {
    window.onload = () => setTimeout(() => window.print(), 500);
}
</script>

</body>
</html>