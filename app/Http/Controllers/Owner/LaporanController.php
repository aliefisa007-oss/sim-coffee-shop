<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Services\LaporanService;
use App\Exports\LaporanExport;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function __construct(
        protected LaporanService $laporanService
    ) {}

    public function index(Request $request)
    {
        $filter = $request->get('filter', 'harian');
        $dari   = $request->get('dari', now()->startOfMonth()->format('Y-m-d'));
        $sampai = $request->get('sampai', now()->format('Y-m-d'));
        $data   = $this->laporanService->getLaporan($filter, $dari, $sampai);

        return view('owner.laporan.index', compact('data', 'filter', 'dari', 'sampai'));
    }
    public function bulanan(Request $request)
{
    $bulan = $request->get('bulan', now()->format('Y-m'));
    $tahun = substr($bulan, 0, 4);
    $bln   = substr($bulan, 5, 2);

    // Ambil semua hari dalam bulan
    $awalBulan  = \Carbon\Carbon::createFromDate($tahun, $bln, 1)->startOfMonth();
    $akhirBulan = \Carbon\Carbon::createFromDate($tahun, $bln, 1)->endOfMonth();

    // Data per hari
    $dataHarian = [];
    $current    = $awalBulan->copy();

    while ($current <= $akhirBulan) {
        $tanggal = $current->format('Y-m-d');

        $transaksiHari = \App\Models\Transaksi::selesai()
            ->whereDate('tanggal', $tanggal)
            ->with(['detailTransaksi.menu.resepProduk.bahanBaku'])
            ->get();

        $totalPenjualan = $transaksiHari->sum('total');
        $jumlahTransaksi = $transaksiHari->count();

        // Hitung jumlah item
        $jumlahItem = $transaksiHari->sum(function($trx) {
            return $trx->detailTransaksi->sum('qty');
        });

        // Hitung laba
        $totalModal = 0;
        foreach ($transaksiHari as $trx) {
            foreach ($trx->detailTransaksi as $detail) {
                $modalPerUnit = 0;
                if ($detail->menu && $detail->menu->resepProduk) {
                    foreach ($detail->menu->resepProduk as $resep) {
                        $modalPerUnit += $resep->jumlah * $resep->bahanBaku->harga_per_satuan;
                    }
                }
                $totalModal += $modalPerUnit * $detail->qty;
            }
        }
        $laba = $totalPenjualan - $totalModal;

        $dataHarian[] = [
            'tanggal'         => $current->format('d'),
            'hari'            => $current->translatedFormat('l'),
            'tanggal_lengkap' => $current->format('d/m/Y'),
            'total_penjualan' => $totalPenjualan,
            'jumlah_transaksi'=> $jumlahTransaksi,
            'jumlah_item'     => $jumlahItem,
            'modal'           => $totalModal,
            'laba'            => $laba,
            'is_today'        => $current->isToday(),
            'is_weekend'      => $current->isWeekend(),
        ];

        $current->addDay();
    }

    // Ringkasan bulan
    $ringkasan = [
        'total_penjualan'  => collect($dataHarian)->sum('total_penjualan'),
        'total_transaksi'  => collect($dataHarian)->sum('jumlah_transaksi'),
        'total_item'       => collect($dataHarian)->sum('jumlah_item'),
        'total_modal'      => collect($dataHarian)->sum('modal'),
        'total_laba'       => collect($dataHarian)->sum('laba'),
        'hari_terbaik'     => collect($dataHarian)->sortByDesc('total_penjualan')->first(),
        'rata_per_hari'    => collect($dataHarian)->where('total_penjualan', '>', 0)->avg('total_penjualan'),
    ];

    return view('owner.laporan.bulanan', compact(
        'dataHarian', 'ringkasan', 'bulan', 'awalBulan'
    ));
}
public function exportBulananExcel(Request $request)
{
    $bulan = $request->get('bulan', now()->format('Y-m'));
    $tahun = substr($bulan, 0, 4);
    $bln   = substr($bulan, 5, 2);

    $awalBulan  = \Carbon\Carbon::createFromDate($tahun, $bln, 1)->startOfMonth();
    $akhirBulan = \Carbon\Carbon::createFromDate($tahun, $bln, 1)->endOfMonth();

    $dataHarian = [];
    $current    = $awalBulan->copy();

    while ($current <= $akhirBulan) {
        $tanggal = $current->format('Y-m-d');

        $transaksiHari = \App\Models\Transaksi::selesai()
            ->whereDate('tanggal', $tanggal)
            ->with(['detailTransaksi.menu.resepProduk.bahanBaku'])
            ->get();

        $totalPenjualan  = $transaksiHari->sum('total');
        $jumlahTransaksi = $transaksiHari->count();
        $jumlahItem      = $transaksiHari->sum(fn($trx) => $trx->detailTransaksi->sum('qty'));

        $totalModal = 0;
        foreach ($transaksiHari as $trx) {
            foreach ($trx->detailTransaksi as $detail) {
                $modalPerUnit = 0;
                if ($detail->menu && $detail->menu->resepProduk) {
                    foreach ($detail->menu->resepProduk as $resep) {
                        $modalPerUnit += $resep->jumlah * $resep->bahanBaku->harga_per_satuan;
                    }
                }
                $totalModal += $modalPerUnit * $detail->qty;
            }
        }

        $dataHarian[] = [
            'tanggal'          => $current->format('d'),
            'hari'             => $current->translatedFormat('l'),
            'tanggal_lengkap'  => $current->format('d/m/Y'),
            'total_penjualan'  => $totalPenjualan,
            'jumlah_transaksi' => $jumlahTransaksi,
            'jumlah_item'      => $jumlahItem,
            'modal'            => $totalModal,
            'laba'             => $totalPenjualan - $totalModal,
            'is_today'         => $current->isToday(),
            'is_weekend'       => $current->isWeekend(),
        ];

        $current->addDay();
    }

    $ringkasan = [
        'total_penjualan' => collect($dataHarian)->sum('total_penjualan'),
        'total_transaksi' => collect($dataHarian)->sum('jumlah_transaksi'),
        'total_item'      => collect($dataHarian)->sum('jumlah_item'),
        'total_modal'     => collect($dataHarian)->sum('modal'),
        'total_laba'      => collect($dataHarian)->sum('laba'),
    ];

    return \Maatwebsite\Excel\Facades\Excel::download(
        new \App\Exports\LaporanBulananExport($dataHarian, $ringkasan, $bulan),
        "laporan-bulanan-{$bulan}.xlsx"
    );
}

    public function exportPdf(Request $request)
    {
        $filter = $request->get('filter', 'harian');
        $dari   = $request->get('dari', now()->startOfMonth()->format('Y-m-d'));
        $sampai = $request->get('sampai', now()->format('Y-m-d'));
        $data   = $this->laporanService->getLaporan($filter, $dari, $sampai);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView(
            'owner.laporan.pdf',
            compact('data', 'dari', 'sampai')
        )->setPaper('a4', 'portrait');

        return $pdf->download("laporan-{$dari}-{$sampai}.pdf");
    }

    public function exportExcel(Request $request)
    {
        $dari   = $request->get('dari', now()->startOfMonth()->format('Y-m-d'));
        $sampai = $request->get('sampai', now()->format('Y-m-d'));

        return \Maatwebsite\Excel\Facades\Excel::download(
            new LaporanExport($dari, $sampai),
            "laporan-{$dari}-{$sampai}.xlsx"
        );
    }
}