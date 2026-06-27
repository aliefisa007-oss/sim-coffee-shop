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

    public function update(Request $request, int $id)
{
    $request->validate([
        'kode_bahan'       => ['required', 'string', 'max:10', 'unique:bahan_baku,kode_bahan,' . $id],
        'nama_bahan'       => ['required', 'string', 'max:150'],
        'satuan'           => ['required', 'in:gram,ml,pcs,botol'],
        'stok_minimum'     => ['required', 'numeric', 'min:0'],
        'harga_per_satuan' => ['required', 'numeric', 'min:0'],
    ]);

    $this->repo->update($id, $request->only([
        'kode_bahan',
        'nama_bahan',
        'satuan',
        'stok_minimum',
        'harga_per_satuan',
    ]));

    return back()->with('success', 'Bahan baku berhasil diperbarui.');
}

   public function destroy(int $id)
{
    $bahan = $this->repo->findById($id);

    // Cek apakah masih dipakai di resep
    if ($bahan->resepProduk()->exists()) {
        return back()->with('error', 'Bahan baku tidak bisa dihapus karena masih digunakan di resep produk. Hapus dari resep dulu.');
    }

    // Hapus riwayat stok dulu
    $bahan->riwayatStok()->delete();

    // Hapus bahan baku
    $this->repo->delete($id);

    return back()->with('success', 'Bahan baku berhasil dihapus.');
}
}