@extends('layouts.app')
@section('title', 'Stok Keluar')
@section('page-title', 'Stok Keluar')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="chart-card">
            <div class="chart-title">📤 Kurangi Stok — {{ $bahan->nama_bahan }}</div>
            <div class="alert-row mb-4">
                <div>
                    <div style="font-size:12px; color:#888;">Stok Saat Ini</div>
                    <div style="font-size:20px; font-weight:700; color:#c8a97e;">{{ $bahan->stok }} {{ $bahan->satuan }}</div>
                </div>
                <div style="font-size:11px; color:#666;">Min: {{ $bahan->stok_minimum }} {{ $bahan->satuan }}</div>
            </div>
            <form method="POST" action="{{ route('owner.stok.simpan-keluar', $bahan->id) }}">
                @csrf
                <div class="mb-3">
                    <label style="font-size:11px; color:#888; text-transform:uppercase;">Jumlah Keluar ({{ $bahan->satuan }})</label>
                    <input type="number" name="jumlah" class="form-control mt-1" placeholder="0" step="0.01" min="0.01" required>
                </div>
                <div class="mb-4">
                    <label style="font-size:11px; color:#888; text-transform:uppercase;">Keterangan</label>
                    <input type="text" name="keterangan" class="form-control mt-1" placeholder="Contoh: Penyesuaian stok">
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('owner.bahan-baku.index') }}"
                       style="flex:1; text-align:center; padding:10px; border-radius:8px; border:1px solid #2a2d38; color:#888; text-decoration:none;">
                        Batal
                    </a>
                    <button type="submit" class="btn-gold" style="flex:1; padding:10px; border-radius:8px;">
                        Simpan Stok Keluar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection