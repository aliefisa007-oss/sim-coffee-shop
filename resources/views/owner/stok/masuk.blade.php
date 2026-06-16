@extends('layouts.app')
@section('title', 'Stok Masuk')
@section('page-title', 'Stok Masuk')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="chart-card">
            <div class="chart-title">📦 Tambah Stok — {{ $bahan->nama_bahan }}</div>

            {{-- Info Stok Sekarang --}}
            <div class="alert-row mb-4">
                <div>
                    <div style="font-size:11px; color:#888; margin-bottom:2px;">Stok Saat Ini</div>
                    <div style="font-size:22px; font-weight:700; color:#c8a97e;">
                        {{ $bahan->stok }} {{ $bahan->satuan }}
                    </div>
                </div>
                <div style="text-align:right;">
                    <div style="font-size:11px; color:#888; margin-bottom:2px;">Harga Saat Ini</div>
                    <div style="font-size:16px; font-weight:700; color:#5b8dee;">
                        Rp {{ number_format($bahan->harga_per_satuan, 0, ',', '.') }}/{{ $bahan->satuan }}
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('owner.stok.simpan-masuk', $bahan->id) }}">
                @csrf

                {{-- Jumlah Masuk --}}
                <div class="mb-3">
                    <label style="font-size:11px; color:#888; text-transform:uppercase;">
                        Jumlah Masuk ({{ $bahan->satuan }})
                    </label>
                    <input type="number" name="jumlah" class="form-control mt-1"
                           placeholder="0" step="0.01" min="0.01" required>
                    @error('jumlah')
                        <div style="color:#e07c7c; font-size:11px; margin-top:4px;">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Update Harga --}}
                <div class="mb-3">
                    <label style="font-size:11px; color:#888; text-transform:uppercase;">
                        Update Harga Beli per {{ $bahan->satuan }} (Rp)
                    </label>
                    <input type="number" name="harga_per_satuan" class="form-control mt-1"
                           placeholder="Kosongkan jika harga tidak berubah"
                           step="0.01" min="0"
                           value="{{ $bahan->harga_per_satuan > 0 ? $bahan->harga_per_satuan : '' }}">
                    <div style="font-size:10px; color:#555; margin-top:4px;">
                        💡 Isi jika harga beli berubah dari supplier. Kosongkan jika harga sama.
                    </div>
                </div>

                {{-- Keterangan --}}
                <div class="mb-4">
                    <label style="font-size:11px; color:#888; text-transform:uppercase;">Keterangan</label>
                    <input type="text" name="keterangan" class="form-control mt-1"
                           placeholder="Contoh: Restock dari supplier A">
                </div>

                {{-- Preview --}}
                <div id="previewBox" style="display:none; background:#0f1117; border-radius:8px; padding:12px; margin-bottom:16px; border:1px solid #23262f;">
                    <div style="font-size:11px; color:#888; margin-bottom:8px; text-transform:uppercase; letter-spacing:0.06em;">Preview Setelah Restok</div>
                    <div style="display:flex; justify-content:space-between; font-size:12px; margin-bottom:4px;">
                        <span style="color:#888;">Stok Sebelum</span>
                        <span style="color:#e8e6e0;">{{ $bahan->stok }} {{ $bahan->satuan }}</span>
                    </div>
                    <div style="display:flex; justify-content:space-between; font-size:12px; margin-bottom:4px;">
                        <span style="color:#888;">Tambahan</span>
                        <span id="previewJumlah" style="color:#3ecf8e;">+0 {{ $bahan->satuan }}</span>
                    </div>
                    <div style="display:flex; justify-content:space-between; font-size:14px; font-weight:700; border-top:1px solid #23262f; padding-top:8px; margin-top:4px;">
                        <span style="color:#888;">Stok Sesudah</span>
                        <span id="previewTotal" style="color:#c8a97e;">{{ $bahan->stok }} {{ $bahan->satuan }}</span>
                    </div>
                    <div id="previewHarga" style="display:none; display:flex; justify-content:space-between; font-size:12px; margin-top:8px; padding-top:8px; border-top:1px solid #23262f;">
                        <span style="color:#888;">Harga Baru</span>
                        <span id="previewHargaVal" style="color:#5b8dee; font-weight:600;"></span>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <a href="{{ route('owner.bahan-baku.index') }}"
                       style="flex:1; text-align:center; padding:10px; border-radius:8px; border:1px solid #2a2d38; color:#888; text-decoration:none; font-size:13px;">
                        Batal
                    </a>
                    <button type="submit" class="btn-gold" style="flex:1; padding:10px; border-radius:8px; font-size:13px;">
                        Simpan Stok Masuk
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const stokAwal = {{ $bahan->stok }};
const satuan   = '{{ $bahan->satuan }}';
const fmt      = n => 'Rp ' + Number(n).toLocaleString('id-ID');

document.querySelector('input[name="jumlah"]').addEventListener('input', function() {
    const jumlah = parseFloat(this.value) || 0;
    const preview = document.getElementById('previewBox');

    if (jumlah > 0) {
        preview.style.display = 'block';
        document.getElementById('previewJumlah').textContent = '+' + jumlah + ' ' + satuan;
        document.getElementById('previewTotal').textContent  = (stokAwal + jumlah) + ' ' + satuan;
    } else {
        preview.style.display = 'none';
    }
});

document.querySelector('input[name="harga_per_satuan"]').addEventListener('input', function() {
    const harga       = parseFloat(this.value) || 0;
    const previewHrg  = document.getElementById('previewHarga');
    const previewHVal = document.getElementById('previewHargaVal');

    if (harga > 0) {
        previewHrg.style.display  = 'flex';
        previewHVal.textContent   = fmt(harga) + '/' + satuan;
    } else {
        previewHrg.style.display  = 'none';
    }

    // Tampilkan preview box kalau harga diisi meski jumlah belum
    const jumlah = parseFloat(document.querySelector('input[name="jumlah"]').value) || 0;
    if (jumlah > 0 || harga > 0) {
        document.getElementById('previewBox').style.display = 'block';
    }
});
</script>
@endpush