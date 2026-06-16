<?php

namespace App\Services;

use App\Models\Transaksi;
use App\Models\DetailTransaksi;

class LaporanService
{
    public function getLaporan(string $filter, string $dari, string $sampai): array
    {
        [$dari, $sampai] = $this->resolveRange($filter, $dari, $sampai);

        $transaksi = Transaksi::selesai()
            ->with([
        'user',
        'detailTransaksi.menu.resepProduk.bahanBaku'
    ])
    ->rentang($dari, $sampai . ' 23:59:59')
    ->get();

        $topMenu = DetailTransaksi::selectRaw('menu_id, nama_menu, SUM(qty) as total_qty, SUM(subtotal) as total_omzet')
            ->whereHas('transaksi', function ($q) use ($dari, $sampai) {
                $q->selesai()->rentang($dari, $sampai . ' 23:59:59');
            })
            ->groupBy('menu_id', 'nama_menu')
            ->orderByDesc('total_qty')
            ->limit(10)
            ->get();

        return [
            'dari'            => $dari,
            'sampai'          => $sampai,
            'total_transaksi' => $transaksi->count(),
            'omzet'           => $transaksi->sum('total'),
            'top_menu'        => $topMenu,
            'transaksi'       => $transaksi,
            'estimasi_laba'   => $transaksi->sum('total') * 0.35,
        ];
    }

    private function resolveRange(string $filter, string $dari, string $sampai): array
    {
        if ($filter === 'harian') {
            return [now()->format('Y-m-d'), now()->format('Y-m-d')];
        }

        if ($filter === 'mingguan') {
            return [now()->startOfWeek()->format('Y-m-d'), now()->endOfWeek()->format('Y-m-d')];
        }

        if ($filter === 'bulanan') {
            return [now()->startOfMonth()->format('Y-m-d'), now()->endOfMonth()->format('Y-m-d')];
        }

        return [$dari, $sampai];
    }
}