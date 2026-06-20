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
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transaksi as $trx)
            <tr style="{{ $trx->status === 'batal' ? 'opacity:0.6;' : '' }}">
                <td style="color:#e8e6e0; font-weight:500;">{{ $trx->nomor_transaksi }}</td>
                <td style="color:#888;">{{ $trx->user->name }}</td>
                <td>
                    <span style="padding:3px 8px; border-radius:6px; font-size:10px; font-weight:600;
                        background:{{ $trx->metode_bayar === 'cash' ? 'rgba(62,207,142,0.12)' : ($trx->metode_bayar === 'qris' ? 'rgba(91,141,238,0.12)' : 'rgba(200,169,126,0.12)') }};
                        color:{{ $trx->metode_bayar === 'cash' ? '#3ecf8e' : ($trx->metode_bayar === 'qris' ? '#5b8dee' : '#c8a97e') }};">
                        {{ strtoupper($trx->metode_bayar) }}
                    </span>
                </td>
                <td style="color:#3ecf8e; font-weight:600;">
                    Rp {{ number_format($trx->total, 0, ',', '.') }}
                </td>
                <td style="color:#666;">{{ $trx->tanggal->format('d/m/Y H:i') }}</td>
                <td>
                    @if($trx->status === 'batal')
                        <span style="padding:3px 8px; border-radius:6px; font-size:10px; font-weight:600; background:rgba(224,124,122,0.12); color:#e07c7c;">
                            BATAL
                        </span>
                    @else
                        <span style="padding:3px 8px; border-radius:6px; font-size:10px; font-weight:600; background:rgba(62,207,142,0.12); color:#3ecf8e;">
                            SELESAI
                        </span>
                    @endif
                </td>
                <td>
                    @if($trx->status === 'batal')
                        <div style="font-size:10px; color:#666; max-width:150px;">
                            {{ $trx->alasan_batal ?? '-' }}
                        </div>
                    @else
                        <button onclick="showCancelModal({{ $trx->id }}, '{{ $trx->nomor_transaksi }}')"
                                style="padding:4px 10px; border-radius:6px; border:1px solid #5a2020; background:transparent; color:#e07c7c; font-size:11px; cursor:pointer;">
                            ✕ Batalkan
                        </button>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align:center; color:#555; padding:40px;">
                    Belum ada transaksi.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="mt-3">{{ $transaksi->links() }}</div>
</div>

{{-- Modal Cancel --}}
<div id="modalCancel" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.7); z-index:999; align-items:center; justify-content:center;">
    <div style="background:#1a1d27; border:1px solid #5a2020; border-radius:16px; padding:30px; max-width:400px; width:90%;">
        <div style="font-size:14px; font-weight:600; color:#e07c7c; margin-bottom:4px;">
            ⚠️ Batalkan Transaksi
        </div>
        <div id="cancelNomor" style="font-size:11px; color:#888; margin-bottom:16px;"></div>

        <form id="formCancel" method="POST">
            @csrf
            <div class="mb-3">
                <label style="font-size:11px; color:#888; text-transform:uppercase;">Alasan Pembatalan</label>
                <input type="text" name="alasan_batal" class="form-control mt-1"
                       placeholder="Contoh: Pelanggan membatalkan pesanan" required>
            </div>
            <div style="background:#2a1414; border:1px solid #5a2020; border-radius:8px; padding:10px; margin-bottom:16px; font-size:11px; color:#e07c7c;">
                ⚠️ Stok bahan baku akan dikembalikan otomatis setelah pembatalan.
            </div>
            <div class="d-flex gap-2">
                <button type="button" onclick="tutupModalCancel()"
                        style="flex:1; padding:10px; border-radius:8px; border:1px solid #2a2d38; background:transparent; color:#888; cursor:pointer;">
                    Tutup
                </button>
                <button type="submit"
                        style="flex:1; padding:10px; border-radius:8px; border:none; background:#c62828; color:white; font-weight:700; cursor:pointer;">
                    Ya, Batalkan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function showCancelModal(id, nomor) {
    document.getElementById('cancelNomor').textContent = 'No. Transaksi: ' + nomor;
    document.getElementById('formCancel').action = '/kasir/transaksi/' + id + '/cancel';
    document.getElementById('modalCancel').style.display = 'flex';
}

function tutupModalCancel() {
    document.getElementById('modalCancel').style.display = 'none';
}
</script>
@endpush