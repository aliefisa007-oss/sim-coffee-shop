<?php

namespace App\Services;

use App\Exceptions\InsufficientStockException;
use App\Repositories\Contracts\BahanBakuRepositoryInterface;
use App\Repositories\Contracts\RiwayatStokRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class StokService
{
    public function __construct(
        protected BahanBakuRepositoryInterface $bahanBakuRepo,
        protected RiwayatStokRepositoryInterface $riwayatStokRepo
    ) {}

    public function validasiStokUntukTransaksi(array $items): void
    {
        foreach ($items as $item) {
            $menu = \App\Models\Menu::with('resepProduk.bahanBaku')
                                    ->findOrFail($item['menu_id']);
            foreach ($menu->resepProduk as $resep) {
                $dibutuhkan = $resep->jumlah * $item['qty'];
                $bahan      = $resep->bahanBaku;
                if ($bahan->stok < $dibutuhkan) {
                    throw new InsufficientStockException(
                        "Stok {$bahan->nama_bahan} tidak cukup. " .
                        "Dibutuhkan: {$dibutuhkan} {$bahan->satuan}, " .
                        "Tersedia: {$bahan->stok} {$bahan->satuan}"
                    );
                }
            }
        }
    }

    public function kurangiStokDariTransaksi(array $items, int $transaksiId): void
    {
        foreach ($items as $item) {
            $menu = \App\Models\Menu::with('resepProduk.bahanBaku')
                                    ->findOrFail($item['menu_id']);
            foreach ($menu->resepProduk as $resep) {
                $dibutuhkan  = $resep->jumlah * $item['qty'];
                $bahan       = $resep->bahanBaku;
                $stokSebelum = $bahan->stok;
                $stokSesudah = $stokSebelum - $dibutuhkan;

                $this->bahanBakuRepo->updateStok($bahan->id, $stokSesudah);

                $this->riwayatStokRepo->create([
                    'bahan_baku_id' => $bahan->id,
                    'user_id'       => Auth::id(),
                    'transaksi_id'  => $transaksiId,
                    'tipe'          => 'keluar',
                    'jumlah'        => $dibutuhkan,
                    'stok_sebelum'  => $stokSebelum,
                    'stok_sesudah'  => $stokSesudah,
                    'keterangan'    => "Otomatis dari transaksi #{$transaksiId}",
                ]);
            }
        }
    }

    public function stokMasuk(
    int $bahanId,
    float $jumlah,
    string $keterangan = '',
    ?float $hargaPerSatuan = null
): void {
    $bahan       = $this->bahanBakuRepo->findById($bahanId);
    $stokSebelum = $bahan->stok;
    $stokSesudah = $stokSebelum + $jumlah;

    // Update stok
    $updateData = ['stok' => $stokSesudah];

    // Update harga jika diisi
    if ($hargaPerSatuan !== null && $hargaPerSatuan > 0) {
        $updateData['harga_per_satuan'] = $hargaPerSatuan;
    }

    \App\Models\BahanBaku::where('id', $bahanId)->update($updateData);

    // Catat riwayat
    $keteranganLog = $keterangan ?: 'Stok masuk manual';
    if ($hargaPerSatuan !== null && $hargaPerSatuan > 0) {
        $keteranganLog .= " | Harga diupdate: Rp " . number_format($hargaPerSatuan, 0, ',', '.');
    }

    $this->riwayatStokRepo->create([
        'bahan_baku_id' => $bahanId,
        'user_id'       => Auth::id(),
        'transaksi_id'  => null,
        'tipe'          => 'masuk',
        'jumlah'        => $jumlah,
        'stok_sebelum'  => $stokSebelum,
        'stok_sesudah'  => $stokSesudah,
        'keterangan'    => $keteranganLog,
    ]);
}
    

    /**
     * Penyesuaian stok (stock opname) — dipakai saat stok fisik hasil
     * hitung ulang berbeda dari stok sistem (rusak, tumpah, salah catat, dll).
     * Selisih bisa naik maupun turun. Keterangan WAJIB diisi untuk audit trail.
     */
    public function stokPenyesuaian(int $bahanId, float $stokFisik, string $keterangan): void
    {
        $bahan       = $this->bahanBakuRepo->findById($bahanId);
        $stokSebelum = $bahan->stok;
        $selisih     = $stokFisik - $stokSebelum;

        if ($selisih == 0) {
            return; // tidak ada perubahan, tidak perlu dicatat
        }

        $this->bahanBakuRepo->updateStok($bahanId, $stokFisik);

        $this->riwayatStokRepo->create([
            'bahan_baku_id' => $bahanId,
            'user_id'       => Auth::id(),
            'transaksi_id'  => null,
            'tipe'          => 'penyesuaian',
            'jumlah'        => abs($selisih),
            'stok_sebelum'  => $stokSebelum,
            'stok_sesudah'  => $stokFisik,
            'keterangan'    => sprintf(
                '%s (%s%s %s) — %s',
                'Penyesuaian stok opname',
                $selisih > 0 ? '+' : '-',
                number_format(abs($selisih), 2),
                $bahan->satuan,
                $keterangan
            ),
        ]);
    }

    public function stokKeluar(int $bahanId, float $jumlah, string $keterangan = ''): void
    {
        $bahan = $this->bahanBakuRepo->findById($bahanId);
        if ($bahan->stok < $jumlah) {
            throw new InsufficientStockException("Stok tidak mencukupi.");
        }

        $stokSebelum = $bahan->stok;
        $stokSesudah = $stokSebelum - $jumlah;

        $this->bahanBakuRepo->updateStok($bahanId, $stokSesudah);
        $this->riwayatStokRepo->create([
            'bahan_baku_id' => $bahanId,
            'user_id'       => Auth::id(),
            'transaksi_id'  => null,
            'tipe'          => 'keluar',
            'jumlah'        => $jumlah,
            'stok_sebelum'  => $stokSebelum,
            'stok_sesudah'  => $stokSesudah,
            'keterangan'    => $keterangan ?: 'Stok keluar manual',
        ]);
    }
}