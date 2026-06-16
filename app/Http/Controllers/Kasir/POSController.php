<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Http\Requests\Kasir\StoreTransaksiRequest;
use App\Services\TransaksiService;
use App\Repositories\Contracts\MenuRepositoryInterface;
use App\Exceptions\InsufficientStockException;
use Illuminate\Http\JsonResponse;

class POSController extends Controller
{
    public function __construct(
        protected TransaksiService $transaksiService,
        protected MenuRepositoryInterface $menuRepo
    ) {}

    public function index()
    {
        $menus = $this->menuRepo->getAktif();
        return view('kasir.pos.index', compact('menus'));
    }

    public function store(StoreTransaksiRequest $request): JsonResponse
    {
        try {
            $transaksi = $this->transaksiService->prosesTransaksi($request->validated());
            return response()->json([
                'success'         => true,
                'message'         => 'Transaksi berhasil.',
                'transaksi_id'    => $transaksi->id,
                'nomor_transaksi' => $transaksi->nomor_transaksi,
                'total'           => $transaksi->total,
            ]);
        } catch (InsufficientStockException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Transaksi gagal. Coba lagi.'], 500);
        }
    }
}