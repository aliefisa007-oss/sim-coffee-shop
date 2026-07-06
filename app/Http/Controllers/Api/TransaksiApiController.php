<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\InsufficientStockException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Kasir\StoreTransaksiRequest;
use App\Http\Resources\TransaksiResource;
use App\Models\Menu;
use App\Models\RiwayatStok;
use App\Repositories\Contracts\TransaksiRepositoryInterface;
use App\Services\TransaksiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransaksiApiController extends Controller
{
    public function __construct(
        protected TransaksiService $transaksiService,
        protected TransaksiRepositoryInterface $transaksiRepo
    ) {}

    // GET /api/v1/transaksi
    // Catatan: sama seperti bug filter tanggal di StokApiController,
    // 'dari'/'sampai' dikirim ke repo untuk konsistensi tapi saat ini
    // diabaikan diam-diam oleh TransaksiRepository::getAll() (cuma proses
    // user_id & status). Kasir dipaksa hanya lihat transaksinya sendiri,
    // sama seperti TransaksiController::index() versi web.
    public function index(Request $request)
    {
        $filters = $request->only(['dari', 'sampai', 'status']);

        if (Auth::user()->isKasir()) {
            $filters['user_id'] = Auth::id();
        } elseif ($request->has('user_id')) {
            $filters['user_id'] = $request->user_id;
        }

        $transaksi = $this->transaksiRepo->getAll($filters);

        return TransaksiResource::collection($transaksi);
    }

    // GET /api/v1/transaksi/{id}
    // Catatan: mengikuti perilaku asli — tidak ada pengecekan kepemilikan
    // di sini (sama seperti TransaksiController::show() versi web, yang
    // dipakai baik oleh route kasir maupun owner tanpa filter tambahan).
    public function show(int $id)
    {
        $transaksi = $this->transaksiRepo->findById($id);

        return new TransaksiResource($transaksi);
    }

    // POST /api/v1/transaksi
    public function store(StoreTransaksiRequest $request)
    {
        try {
            $transaksi = $this->transaksiService->prosesTransaksi($request->validated());

            return new TransaksiResource(
                $transaksi->load(['user', 'detailTransaksi'])
            );
        } catch (InsufficientStockException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Transaksi gagal. Coba lagi.'], 500);
        }
    }

    // POST /api/v1/transaksi/{id}/cancel
    // Mereplikasi logic dari TransaksiController::cancel() persis:
    // - kasir cuma boleh cancel transaksinya sendiri (owner tidak dibatasi)
    // - stok bahan baku dikembalikan otomatis via resep produk
    // - dicatat ke riwayat_stok sebagai tipe 'masuk' (retur)
    // Ditambahkan DB::transaction agar proses batal + retur stok atomik
    // (versi web belum pakai ini — flagged sebagai perbaikan, bukan silent copy).
    public function cancel(Request $request, int $id)
    {
        $request->validate([
            'alasan_batal' => ['required', 'string', 'max:255'],
        ]);

        $transaksi = $this->transaksiRepo->findById($id);

        if (Auth::user()->isKasir() && $transaksi->user_id !== Auth::id()) {
            return response()->json([
                'message' => 'Anda tidak bisa membatalkan transaksi ini.',
            ], 403);
        }

        if ($transaksi->status === 'batal') {
            return response()->json([
                'message' => 'Transaksi sudah dibatalkan sebelumnya.',
            ], 422);
        }

        DB::transaction(function () use ($transaksi, $request) {
            $transaksi->update([
                'status' => 'batal',
                'alasan_batal' => $request->alasan_batal,
                'dibatalkan_at' => now(),
                'dibatalkan_oleh' => Auth::id(),
            ]);

            foreach ($transaksi->detailTransaksi as $detail) {
                $menu = Menu::with('resepProduk.bahanBaku')->find($detail->menu_id);

                if (!$menu) {
                    continue;
                }

                foreach ($menu->resepProduk as $resep) {
                    $bahan = $resep->bahanBaku;
                    $stokSebelum = $bahan->stok;
                    $stokSesudah = $stokSebelum + ($resep->jumlah * $detail->qty);

                    $bahan->update(['stok' => $stokSesudah]);

                    RiwayatStok::create([
                        'bahan_baku_id' => $bahan->id,
                        'user_id' => Auth::id(),
                        'transaksi_id' => $transaksi->id,
                        'tipe' => 'masuk',
                        'jumlah' => $resep->jumlah * $detail->qty,
                        'stok_sebelum' => $stokSebelum,
                        'stok_sesudah' => $stokSesudah,
                        'keterangan' => 'Retur dari pembatalan transaksi #' . $transaksi->nomor_transaksi,
                    ]);
                }
            }
        });

        return new TransaksiResource(
            $transaksi->fresh(['user', 'detailTransaksi', 'dibatalkanOleh'])
        );
    }
}
