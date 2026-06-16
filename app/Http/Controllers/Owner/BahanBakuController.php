<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Owner\StoreBahanBakuRequest;
use App\Repositories\Contracts\BahanBakuRepositoryInterface;
use Illuminate\Http\Request;

class BahanBakuController extends Controller
{
    public function __construct(
        protected BahanBakuRepositoryInterface $repo
    ) {}

    public function index(Request $request)
    {
        $bahanBaku   = $this->repo->getAll($request->only(['search', 'satuan']));
        $stokMenipis = $this->repo->getMenipis();
        return view('owner.bahan-baku.index', compact('bahanBaku', 'stokMenipis'));
    }

    public function store(StoreBahanBakuRequest $request)
    {
        $this->repo->create($request->validated());
        return back()->with('success', 'Bahan baku berhasil ditambahkan.');
    }

    public function destroy(int $id)
{
    $bahan = $this->repo->findById($id);

    // Cek apakah masih dipakai di resep
    if ($bahan->resepProduk()->exists()) {
        return back()->with('error', 'Bahan baku tidak bisa dihapus karena masih digunakan di resep produk.');
    }

    $this->repo->delete($id);
    return back()->with('success', 'Bahan baku berhasil dihapus.');
}
}