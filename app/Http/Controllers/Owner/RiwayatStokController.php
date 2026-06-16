<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\RiwayatStokRepositoryInterface;
use App\Repositories\Contracts\BahanBakuRepositoryInterface;
use Illuminate\Http\Request;

class RiwayatStokController extends Controller
{
    public function __construct(
        protected RiwayatStokRepositoryInterface $riwayatRepo,
        protected BahanBakuRepositoryInterface $bahanRepo
    ) {}

    public function index(Request $request)
    {
        $riwayat   = $this->riwayatRepo->getAll($request->only(['bahan_baku_id', 'tipe', 'dari', 'sampai']));
        $bahanBaku = $this->bahanRepo->getAll([]);
        return view('owner.bahan-baku.riwayat', compact('riwayat', 'bahanBaku'));
    }

    public function byBahan(int $bahanId)
    {
        $riwayat = $this->riwayatRepo->getByBahan($bahanId);
        $bahan   = $this->bahanRepo->findById($bahanId);
        return view('owner.bahan-baku.riwayat', compact('riwayat', 'bahan'));
    }
}