<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\InsufficientStockException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Owner\StokMasukRequest;
use App\Http\Requests\Owner\StoreBahanBakuRequest;
use App\Http\Resources\BahanBakuResource;
use App\Http\Resources\RiwayatStokResource;
use App\Repositories\Contracts\BahanBakuRepositoryInterface;
use App\Repositories\Contracts\RiwayatStokRepositoryInterface;
use App\Services\StokService;
use Illuminate\Http\Request;

class StokApiController extends Controller
{
    public function __construct(
        protected StokService $stokService,
        protected BahanBakuRepositoryInterface $bahanBakuRepo,
        protected RiwayatStokRepositoryInterface $riwayatStokRepo
    ) {}

    // GET /api/v1/bahan-baku
    // Catatan: filter 'satuan' dikirim untuk konsistensi dengan web controller,
    // TAPI BahanBakuRepository::getAll() saat ini cuma memproses filter 'search'
    // — 'satuan' diam-diam diabaikan (bug lama di repository, bukan di sini).
    public function index(Request $request)
    {
        $bahan = $this->bahanBakuRepo->getAll($request->only(['search', 'satuan']));

        return BahanBakuResource::collection($bahan);
    }

    // GET /api/v1/bahan-baku/{id}
    public function show(int $id)
    {
        $bahan = $this->bahanBakuRepo->findById($id);

        return new BahanBakuResource($bahan);
    }

    // POST /api/v1/bahan-baku
    public function store(StoreBahanBakuRequest $request)
    {
        $bahan = $this->bahanBakuRepo->create($request->validated());

        return new BahanBakuResource($bahan);
    }

    // PUT /api/v1/bahan-baku/{id}
    // Catatan: belum ada versi web untuk update bahan baku, jadi validasi
    // dibuat manual di sini (bukan reuse StoreBahanBakuRequest) supaya
    // aturan unique:kode_bahan mengabaikan barisnya sendiri saat update.
    public function update(Request $request, int $id)
    {
        $validated = $request->validate([
            'kode_bahan' => ['sometimes', 'string', 'max:10', 'unique:bahan_baku,kode_bahan,' . $id],
            'nama_bahan' => ['sometimes', 'string', 'max:150'],
            'satuan' => ['sometimes', 'in:gram,ml,pcs,botol'],
            'stok' => ['sometimes', 'numeric', 'min:0'],
            'stok_minimum' => ['sometimes', 'numeric', 'min:0'],
            'harga_per_satuan' => ['sometimes', 'numeric', 'min:0'],
        ]);

        $bahan = $this->bahanBakuRepo->update($id, $validated);

        return new BahanBakuResource($bahan);
    }

    // DELETE /api/v1/bahan-baku/{id}
    // Mereplikasi persis logic dari Owner\BahanBakuController::destroy() —
    // cek dulu apakah masih dipakai di resep, baru hapus riwayat stok, baru hapus bahan.
    public function destroy(int $id)
    {
        $bahan = $this->bahanBakuRepo->findById($id);

        if ($bahan->resepProduk()->exists()) {
            return response()->json([
                'message' => 'Bahan baku tidak bisa dihapus karena masih digunakan di resep produk. Hapus dari resep dulu.',
            ], 422);
        }

        $bahan->riwayatStok()->delete();
        $this->bahanBakuRepo->delete($id);

        return response()->json(['message' => 'Bahan baku berhasil dihapus']);
    }

    // GET /api/v1/bahan-baku/menipis (untuk notifikasi stok menipis)
    public function menipis()
    {
        $bahan = $this->bahanBakuRepo->getMenipis();

        return BahanBakuResource::collection($bahan);
    }

    // GET /api/v1/bahan-baku/fast-moving?limit=10&hari=30
    public function fastMoving(Request $request)
    {
        $limit = (int) $request->get('limit', 10);
        $hari  = (int) $request->get('hari', 30);

        $bahan = $this->bahanBakuRepo->getTopFastMoving($limit, $hari);

        return BahanBakuResource::collection($bahan);
    }

    // GET /api/v1/bahan-baku/nilai-total
    public function nilaiTotal()
    {
        return response()->json([
            'nilai_total_stok' => $this->bahanBakuRepo->getNilaiTotalStok(),
        ]);
    }

    // POST /api/v1/bahan-baku/{id}/masuk
    public function stokMasuk(StokMasukRequest $request, int $id)
    {
        try {
            $this->stokService->stokMasuk(
                $id,
                $request->jumlah,
                $request->keterangan ?? '',
                $request->harga_per_satuan ? (float) $request->harga_per_satuan : null
            );

            return new BahanBakuResource($this->bahanBakuRepo->findById($id));
        } catch (InsufficientStockException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    // POST /api/v1/bahan-baku/{id}/keluar
    public function stokKeluar(StokMasukRequest $request, int $id)
    {
        try {
            $this->stokService->stokKeluar($id, $request->jumlah, $request->keterangan ?? '');

            return new BahanBakuResource($this->bahanBakuRepo->findById($id));
        } catch (InsufficientStockException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    // GET /api/v1/riwayat-stok?bahan_baku_id=&tipe=
    public function riwayat(Request $request)
    {
        $riwayat = $this->riwayatStokRepo->getAll($request->only(['bahan_baku_id', 'tipe']));

        return RiwayatStokResource::collection($riwayat);
    }

    // GET /api/v1/riwayat-stok/bahan/{bahanId}
    public function riwayatByBahan(int $bahanId)
    {
        $riwayat = $this->riwayatStokRepo->getByBahan($bahanId);

        return RiwayatStokResource::collection($riwayat);
    }
}
