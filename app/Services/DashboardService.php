<?php

namespace App\Services;

use App\Repositories\Contracts\TransaksiRepositoryInterface;
use App\Repositories\Contracts\BahanBakuRepositoryInterface;

class DashboardService
{
    public function __construct(
        protected TransaksiRepositoryInterface $transaksiRepo,
        protected BahanBakuRepositoryInterface $bahanBakuRepo
    ) {}

    public function getRingkasan(): array
    {
        $hariIni  = $this->transaksiRepo->getHariIni();
        $bulanIni = $this->transaksiRepo->getBulanIni();

        return [
            'omzet_hari_ini'        => $hariIni->sum('total'),
            'omzet_bulan_ini'       => $bulanIni->sum('total'),
            'total_transaksi_hari'  => $hariIni->count(),
            'total_transaksi_bulan' => $bulanIni->count(),
            'stok_menipis'          => $this->bahanBakuRepo->getMenipis(),
            'top_menu'              => $this->transaksiRepo->getTopMenu(5),
        ];
    }

    public function getGrafik7Hari(): array
    {
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $tanggal = now()->subDays($i);
            $omzet   = \App\Models\Transaksi::selesai()
                ->whereDate('tanggal', $tanggal)->sum('total');
            $data[]  = [
                'tanggal' => $tanggal->format('d M'),
                'omzet'   => (float) $omzet,
            ];
        }
        return $data;
    }

    public function getGrafikBulanan(): array
    {
        $data = [];
        for ($i = 11; $i >= 0; $i--) {
            $bulan = now()->subMonths($i);
            $omzet = \App\Models\Transaksi::selesai()
                ->whereYear('tanggal', $bulan->year)
                ->whereMonth('tanggal', $bulan->month)
                ->sum('total');
            $data[] = [
                'bulan' => $bulan->format('M Y'),
                'omzet' => (float) $omzet,
            ];
        }
        return $data;
    }

    public function getStokDashboard(): array
    {
        $stokMenipis = $this->bahanBakuRepo->getMenipis();

        return [
            'stok_menipis'        => $stokMenipis,
            'jumlah_stok_menipis' => $stokMenipis->count(),
            'nilai_total_stok'    => $this->bahanBakuRepo->getNilaiTotalStok(),
            'top_fast_moving'     => $this->bahanBakuRepo->getTopFastMoving(10, 30),
            'top_menu'            => $this->transaksiRepo->getTopMenu(10),
        ];
    }
}