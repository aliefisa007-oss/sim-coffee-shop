<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Owner\StokMasukRequest;
use App\Services\StokService;
use App\Repositories\Contracts\BahanBakuRepositoryInterface;

class StokController extends Controller
{
    public function __construct(
        protected StokService $stokService,
        protected BahanBakuRepositoryInterface $bahanBakuRepo
    ) {}

    public function masuk(int $bahanId)
    {
        $bahan = $this->bahanBakuRepo->findById($bahanId);
        return view('owner.stok.masuk', compact('bahan'));
    }

    public function simpanMasuk(StokMasukRequest $request, int $bahanId)
{
    $this->stokService->stokMasuk(
        $bahanId,
        $request->jumlah,
        $request->keterangan ?? '',
        $request->harga_per_satuan ? (float) $request->harga_per_satuan : null
    );

    return redirect()->route('owner.bahan-baku.index')
                     ->with('success', 'Stok berhasil ditambahkan.');
}

    public function keluar(int $bahanId)
    {
        $bahan = $this->bahanBakuRepo->findById($bahanId);
        return view('owner.stok.keluar', compact('bahan'));
    }

    public function simpanKeluar(StokMasukRequest $request, int $bahanId)
    {
        $this->stokService->stokKeluar($bahanId, $request->jumlah, $request->keterangan);
        return redirect()->route('owner.bahan-baku.index')
                         ->with('success', 'Stok berhasil dikurangi.');
    }
}