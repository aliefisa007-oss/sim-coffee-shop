@extends('layouts.app')
@section('title', 'Bahan Baku')
@section('page-title', 'Bahan Baku')

@section('content')
<div class="row g-3">
    <div class="col-md-4">
        <div class="chart-card">
            <div class="chart-title">➕ Tambah Bahan Baku</div>
            <form method="POST" action="{{ route('owner.bahan-baku.store') }}">
                @csrf
                <div class="mb-3">
                    <label style="font-size:11px; color:#888; text-transform:uppercase;">Kode Bahan</label>
                    <input type="text" name="kode_bahan" class="form-control mt-1" placeholder="BHN-007" required>
                </div>
                <div class="mb-3">
                    <label style="font-size:11px; color:#888; text-transform:uppercase;">Nama Bahan</label>
                    <input type="text" name="nama_bahan" class="form-control mt-1" placeholder="Contoh: Oat Milk" required>
                </div>
                <div class="mb-3">
                    <label style="font-size:11px; color:#888; text-transform:uppercase;">Satuan</label>
                    <select name="satuan" class="form-select mt-1" required>
                        <option value="gram">Gram</option>
                        <option value="ml">ML</option>
                        <option value="pcs">PCS</option>
                        <option value="botol">Botol</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label style="font-size:11px; color:#888; text-transform:uppercase;">Stok Awal</label>
                    <input type="number" name="stok" class="form-control mt-1" placeholder="0" step="0.01" min="0" required>
                </div>
                <div class="mb-3">
                    <label style="font-size:11px; color:#888; text-transform:uppercase;">Stok Minimum</label>
                    <input type="number" name="stok_minimum" class="form-control mt-1" placeholder="0" step="0.01" min="0" required>
                </div>
                <div class="mb-3">
    <label style="font-size:11px; color:#888; text-transform:uppercase;">Harga Beli per Satuan (Rp)</label>
    <input type="number" name="harga_per_satuan" class="form-control mt-1"
           placeholder="Contoh: 150" step="0.01" min="0" required>
    <div style="font-size:10px; color:#555; margin-top:4px;">
        Harga beli per gram/ml/pcs
    </div>
</div>
                <button type="submit" class="btn-gold w-100">Simpan</button>
            </form>
        </div>

        @if($stokMenipis->count() > 0)
        <div class="chart-card mt-3">
            <div class="chart-title" style="color:#e07c3a;">⚠️ Stok Menipis</div>
            @foreach($stokMenipis as $bahan)
                <div class="alert-row mb-2">
                    <div>
                        <div style="font-size:12px; font-weight:500;">{{ $bahan->nama_bahan }}</div>
                        <div style="font-size:10px; color:#555;">Min: {{ $bahan->stok_minimum }} {{ $bahan->satuan }}</div>
                    </div>
                    <div style="color:#e07c3a; font-weight:600; font-size:12px;">
                        {{ $bahan->stok }} {{ $bahan->satuan }}
                    </div>
                </div>
            @endforeach
        </div>
        @endif
    </div>

    <div class="col-md-8">
        <div class="chart-card">
            <div class="chart-title">🧂 Daftar Bahan Baku</div>
            <table class="table-dark-custom">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Nama</th>
                        <th>Satuan</th>
                        <th>Stok</th>
                        <th>Min</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bahanBaku as $bahan)
                    <tr>
                        <td style="color:#c8a97e; font-weight:600;">{{ $bahan->kode_bahan }}</td>
                        <td>{{ $bahan->nama_bahan }}</td>
                        <td style="color:#888;">{{ $bahan->satuan }}</td>
                        <td style="font-weight:600; color:{{ $bahan->isMenipis() ? '#e07c3a' : '#3ecf8e' }};">
                            {{ $bahan->stok }}
                        </td>
                        <td style="color:#666;">{{ $bahan->stok_minimum }}</td>
                        <td>
                            @if($bahan->isMenipis())
                                <span class="badge-nonaktif">Menipis</span>
                            @else
                                <span class="badge-aktif">Aman</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex gap-1 flex-wrap">
                                <a href="{{ route('owner.stok.masuk', $bahan->id) }}"
                                   style="padding:3px 8px; border-radius:6px; border:1px solid #2a2d38; color:#3ecf8e; font-size:11px; text-decoration:none;">
                                   + Masuk
                                </a>
                                <a href="{{ route('owner.stok.keluar', $bahan->id) }}"
                                   style="padding:3px 8px; border-radius:6px; border:1px solid #2a2d38; color:#e07c7c; font-size:11px; text-decoration:none;">
                                   - Keluar
                                </a>
                                <a href="{{ route('owner.riwayat-stok.by-bahan', $bahan->id) }}"
                                   style="padding:3px 8px; border-radius:6px; border:1px solid #2a2d38; color:#5b8dee; font-size:11px; text-decoration:none;">
                                   Riwayat
                                </a>
                                <form method="POST" action="{{ route('owner.bahan-baku.destroy', $bahan->id) }}"
                                      onsubmit="return confirm('Hapus bahan baku ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            style="padding:3px 8px; border-radius:6px; border:1px solid #2a2d38; background:transparent; color:#e07c7c; font-size:11px; cursor:pointer;">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="text-align:center; color:#555; padding:30px;">
                            Belum ada bahan baku.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-3">{{ $bahanBaku->links() }}</div>
        </div>
    </div>
</div>
@endsection