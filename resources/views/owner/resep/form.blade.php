@extends('layouts.app')
@section('title', 'Resep: ' . $menu->nama_menu)
@section('page-title', 'Resep — ' . $menu->nama_menu)

@section('content')
<div class="row g-3">
    {{-- Form Tambah Bahan --}}
    <div class="col-md-4">
        <div class="chart-card">
            <div class="chart-title">➕ Tambah Bahan ke Resep</div>
            <form method="POST" action="{{ route('owner.resep.store') }}">
                @csrf
                <input type="hidden" name="menu_id" value="{{ $menu->id }}">
                <div class="mb-3">
                    <label style="font-size:11px; color:#888; text-transform:uppercase;">Bahan Baku</label>
                    <select name="bahan_baku_id" required
                            style="width:100%; background:#0f1117; border:1px solid #2a2d38; color:#e8e6e0; border-radius:8px; padding:8px 12px; font-size:12px; margin-top:4px;">
                        <option value="">Pilih bahan baku...</option>
                        @foreach($bahanBaku as $bahan)
                            <option value="{{ $bahan->id }}">
                                {{ $bahan->nama_bahan }} ({{ $bahan->satuan }})
                                — Rp {{ number_format($bahan->harga_per_satuan, 0, ',', '.') }}/{{ $bahan->satuan }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label style="font-size:11px; color:#888; text-transform:uppercase;">Jumlah per Porsi</label>
                    <input type="number" name="jumlah" step="0.01" min="0.01" required
                           style="width:100%; background:#0f1117; border:1px solid #2a2d38; color:#e8e6e0; border-radius:8px; padding:8px 12px; font-size:12px; margin-top:4px;"
                           placeholder="Contoh: 18">
                </div>
                <button type="submit" class="btn-gold w-100">Tambah ke Resep</button>
            </form>

            <div class="mt-3">
                <a href="{{ route('owner.resep.index') }}"
                   style="display:block; text-align:center; padding:8px; border-radius:8px; border:1px solid #2a2d38; color:#888; text-decoration:none; font-size:12px;">
                    ← Kembali
                </a>
            </div>
        </div>

        {{-- Info Menu --}}
        <div class="chart-card mt-3">
            <div class="chart-title">☕ Info Menu</div>
            <div style="font-size:13px; font-weight:600; margin-bottom:4px;">{{ $menu->nama_menu }}</div>
            <div style="font-size:11px; color:#888; margin-bottom:12px;">{{ $menu->kategori->nama_kategori }}</div>
            <div style="display:flex; justify-content:space-between; font-size:12px; margin-bottom:6px;">
                <span style="color:#888;">Harga Jual</span>
                <span style="color:#c8a97e; font-weight:600;">Rp {{ number_format($menu->harga_jual, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>

    {{-- Resep & Kalkulasi --}}
    <div class="col-md-8">
        <div class="chart-card">
            <div class="chart-title">📜 Komposisi Resep — {{ $menu->nama_menu }}</div>

            @php
                $modalPerCup = 0;
                foreach($menu->resepProduk as $resep) {
                    $modalPerCup += $resep->jumlah * $resep->bahanBaku->harga_per_satuan;
                }
                $hargaJual   = $menu->harga_jual;
                $profit      = $hargaJual - $modalPerCup;
                $margin      = $hargaJual > 0 ? ($profit / $hargaJual) * 100 : 0;
            @endphp

            {{-- Tabel Komposisi --}}
            <table class="table-dark-custom mb-4">
                <thead>
                    <tr>
                        <th>Bahan Baku</th>
                        <th>Jumlah</th>
                        <th>Harga/Satuan</th>
                        <th>Subtotal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($menu->resepProduk as $resep)
                    @php
                        $subtotalBahan = $resep->jumlah * $resep->bahanBaku->harga_per_satuan;
                    @endphp
                    <tr>
                        <td style="font-weight:500;">{{ $resep->bahanBaku->nama_bahan }}</td>
                        <td style="color:#c8a97e;">
                            {{ $resep->jumlah }} {{ $resep->bahanBaku->satuan }}
                        </td>
                        <td style="color:#888;">
                            Rp {{ number_format($resep->bahanBaku->harga_per_satuan, 0, ',', '.') }}/{{ $resep->bahanBaku->satuan }}
                        </td>
                        <td style="color:#e8e6e0; font-weight:500;">
                            Rp {{ number_format($subtotalBahan, 0, ',', '.') }}
                        </td>
                        <td>
                            <form method="POST" action="{{ route('owner.resep.destroy', $resep->id) }}"
                                  onsubmit="return confirm('Hapus bahan ini?')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        style="padding:3px 10px; border-radius:6px; border:1px solid #2a2d38; background:transparent; color:#e07c7c; font-size:11px; cursor:pointer;">
                                    Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" style="text-align:center; color:#555; padding:20px;">
                            Belum ada bahan. Tambah dari form di sebelah kiri.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            {{-- Kalkulasi HPP --}}
            @if($menu->resepProduk->count() > 0)
            <div style="background:#0f1117; border-radius:12px; padding:20px; border:1px solid #23262f;">
                <div style="font-size:12px; font-weight:600; color:#c8a97e; text-transform:uppercase; letter-spacing:0.08em; margin-bottom:16px;">
                    📊 Kalkulasi HPP per Cup
                </div>

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:16px;">
                    {{-- Modal per Cup --}}
                    <div style="background:#1a1d27; border-radius:10px; padding:14px; border:1px solid #23262f;">
                        <div style="font-size:10px; color:#666; text-transform:uppercase; letter-spacing:0.06em; margin-bottom:6px;">Modal per Cup</div>
                        <div style="font-size:18px; font-weight:700; color:#e8e6e0;">
                            Rp {{ number_format($modalPerCup, 0, ',', '.') }}
                        </div>
                        <div style="font-size:10px; color:#555; margin-top:4px;">Total biaya bahan baku</div>
                    </div>

                    {{-- Harga Jual --}}
                    <div style="background:#1a1d27; border-radius:10px; padding:14px; border:1px solid #23262f;">
                        <div style="font-size:10px; color:#666; text-transform:uppercase; letter-spacing:0.06em; margin-bottom:6px;">Harga Jual</div>
                        <div style="font-size:18px; font-weight:700; color:#c8a97e;">
                            Rp {{ number_format($hargaJual, 0, ',', '.') }}
                        </div>
                        <div style="font-size:10px; color:#555; margin-top:4px;">Harga ke pelanggan</div>
                    </div>

                    {{-- Profit --}}
                    <div style="background:#1a1d27; border-radius:10px; padding:14px; border:1px solid {{ $profit >= 0 ? '#1a5c30' : '#5a2020' }};">
                        <div style="font-size:10px; color:#666; text-transform:uppercase; letter-spacing:0.06em; margin-bottom:6px;">Profit per Cup</div>
                        <div style="font-size:18px; font-weight:700; color:{{ $profit >= 0 ? '#3ecf8e' : '#e07c7c' }};">
                            Rp {{ number_format($profit, 0, ',', '.') }}
                        </div>
                        <div style="font-size:10px; color:#555; margin-top:4px;">Harga jual - modal</div>
                    </div>

                    {{-- Margin --}}
                    <div style="background:#1a1d27; border-radius:10px; padding:14px; border:1px solid {{ $margin >= 30 ? '#1a5c30' : ($margin >= 0 ? '#3a2a14' : '#5a2020') }};">
                        <div style="font-size:10px; color:#666; text-transform:uppercase; letter-spacing:0.06em; margin-bottom:6px;">Margin</div>
                        <div style="font-size:18px; font-weight:700; color:{{ $margin >= 30 ? '#3ecf8e' : ($margin >= 0 ? '#c8a97e' : '#e07c7c') }};">
                            {{ number_format($margin, 1) }}%
                        </div>
                        <div style="font-size:10px; color:#555; margin-top:4px;">
                            @if($margin >= 50)
                                🟢 Margin sangat baik
                            @elseif($margin >= 30)
                                🟢 Margin baik
                            @elseif($margin >= 15)
                                🟡 Margin cukup
                            @else
                                🔴 Margin rendah
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Progress Bar Margin --}}
                <div style="margin-top:8px;">
                    <div style="display:flex; justify-content:space-between; font-size:11px; color:#666; margin-bottom:6px;">
                        <span>Modal</span>
                        <span>Profit</span>
                    </div>
                    <div style="height:8px; background:#23262f; border-radius:4px; overflow:hidden;">
                        @php $modalPct = $hargaJual > 0 ? ($modalPerCup / $hargaJual) * 100 : 100; @endphp
                        <div style="height:100%; width:{{ min($modalPct, 100) }}%; background:linear-gradient(90deg, #e07c3a, #c8a97e); border-radius:4px;"></div>
                    </div>
                    <div style="display:flex; justify-content:space-between; font-size:10px; color:#555; margin-top:4px;">
                        <span>{{ number_format($modalPct, 1) }}% modal</span>
                        <span>{{ number_format(100 - $modalPct, 1) }}% profit</span>
                    </div>
                </div>

                {{-- Rekomendasi Harga --}}
                <div style="margin-top:16px; padding:12px; background:#161920; border-radius:8px; border:1px solid #23262f;">
                    <div style="font-size:11px; color:#888; margin-bottom:8px; font-weight:600;">💡 Rekomendasi Harga Jual</div>
                    <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:8px;">
                        <div style="text-align:center;">
                            <div style="font-size:10px; color:#555; margin-bottom:4px;">Margin 30%</div>
                            <div style="font-size:13px; font-weight:600; color:#5b8dee;">
                                Rp {{ number_format($modalPerCup / 0.7, 0, ',', '.') }}
                            </div>
                        </div>
                        <div style="text-align:center; border-left:1px solid #23262f; border-right:1px solid #23262f;">
                            <div style="font-size:10px; color:#555; margin-bottom:4px;">Margin 50%</div>
                            <div style="font-size:13px; font-weight:600; color:#c8a97e;">
                                Rp {{ number_format($modalPerCup / 0.5, 0, ',', '.') }}
                            </div>
                        </div>
                        <div style="text-align:center;">
                            <div style="font-size:10px; color:#555; margin-bottom:4px;">Margin 70%</div>
                            <div style="font-size:13px; font-weight:600; color:#3ecf8e;">
                                Rp {{ number_format($modalPerCup / 0.3, 0, ',', '.') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection