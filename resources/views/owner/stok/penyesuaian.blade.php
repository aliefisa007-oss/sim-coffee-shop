@extends('layouts.app')
@section('title', 'Penyesuaian Stok')
@section('page-title', 'Penyesuaian Stok')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="chart-card">
            <div class="chart-title">🧮 Penyesuaian Stok (Stock Opname) — {{ $bahan->nama_bahan }}</div>
            <div style="font-size:11px; color:#666; margin-bottom:16px;">
                Gunakan ini kalau stok fisik hasil hitung ulang beda dari stok sistem
                (rusak, tumpah, kadaluarsa, salah catat, dll). Selisihnya akan tercatat
                otomatis di riwayat stok dengan tipe <b>Penyesuaian</b>.
            </div>

            {{-- Info Stok Sekarang --}}
            <div class="alert-row mb-4">
                <div>
                    <div style="font-size:11px; color:#888; margin-bottom:2px;">Stok Sistem Saat Ini</div>
                    <div style="font-size:22px; font-weight:700; color:#c8a97e;">
                        {{ $bahan->stok }} {{ $bahan->satuan }}
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('owner.stok.simpan-penyesuaian', $bahan->id) }}">
                @csrf

                {{-- Stok Fisik --}}
                <div class="mb-3">
                    <label style="font-size:11px; color:#888; text-transform:uppercase;">
                        Stok Fisik Hasil Hitung ({{ $bahan->satuan }})
                    </label>
                    <input type="number" name="stok_fisik" id="stokFisik" class="form-control mt-1"
                           placeholder="0" step="0.01" min="0"
                           value="{{ old('stok_fisik', $bahan->stok) }}" required>
                    @error('stok_fisik')
                        <div style="color:#e07c7c; font-size:11px; margin-top:4px;">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Keterangan (wajib) --}}
                <div class="mb-4">
                    <label style="font-size:11px; color:#888; text-transform:uppercase;">
                        Alasan Penyesuaian <span style="color:#e07c7c;">*wajib</span>
                    </label>
                    <input type="text" name="keterangan" class="form-control mt-1"
                           placeholder="Contoh: Hasil stock opname bulanan, 2 kg rusak kena air"
                           value="{{ old('keterangan') }}" required>
                    @error('keterangan')
                        <div style="color:#e07c7c; font-size:11px; margin-top:4px;">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Preview Selisih --}}
                <div id="previewBox" style="display:none; background:#0f1117; border-radius:8px; padding:12px; margin-bottom:16px; border:1px solid #23262f;">
                    <div style="font-size:11px; color:#888; margin-bottom:8px; text-transform:uppercase; letter-spacing:0.06em;">Preview Selisih</div>
                    <div style="display:flex; justify-content:space-between; font-size:12px; margin-bottom:4px;">
                        <span style="color:#888;">Stok Sistem</span>
                        <span style="color:#e8e6e0;">{{ $bahan->stok }} {{ $bahan->satuan }}</span>
                    </div>
                    <div style="display:flex; justify-content:space-between; font-size:14px; font-weight:700; border-top:1px solid #23262f; padding-top:8px; margin-top:4px;">
                        <span style="color:#888;">Selisih</span>
                        <span id="previewSelisih" style="color:#c8a97e;">0 {{ $bahan->satuan }}</span>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <a href="{{ route('owner.bahan-baku.index') }}"
                       style="flex:1; text-align:center; padding:10px; border-radius:8px; border:1px solid #2a2d38; color:#888; text-decoration:none; font-size:13px;">
                        Batal
                    </a>
                    <button type="submit" class="btn-gold" style="flex:1; padding:10px; border-radius:8px; font-size:13px;">
                        Simpan Penyesuaian
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const stokSistem = {{ $bahan->stok }};
const satuan     = '{{ $bahan->satuan }}';

document.getElementById('stokFisik').addEventListener('input', function() {
    const fisik    = parseFloat(this.value);
    const preview  = document.getElementById('previewBox');
    const selisihEl = document.getElementById('previewSelisih');

    if (isNaN(fisik)) {
        preview.style.display = 'none';
        return;
    }

    const selisih = fisik - stokSistem;
    preview.style.display = 'block';
    selisihEl.textContent = (selisih >= 0 ? '+' : '') + selisih.toFixed(2) + ' ' + satuan;
    selisihEl.style.color = selisih === 0 ? '#888' : (selisih > 0 ? '#3ecf8e' : '#e07c7c');
});
</script>
@endpush
