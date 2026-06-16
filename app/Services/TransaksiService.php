<?php

namespace App\Services;

use App\Repositories\Contracts\TransaksiRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class TransaksiService
{
    public function __construct(
        protected TransaksiRepositoryInterface $transaksiRepo,
        protected StokService $stokService
    ) {}

    public function prosesTransaksi(array $data): \App\Models\Transaksi
    {
        $this->stokService->validasiStokUntukTransaksi($data['items']);

        return DB::transaction(function () use ($data) {
            $subtotal = collect($data['items'])->sum('subtotal');
            $diskon   = $data['diskon'] ?? 0;
            $pajak    = $data['pajak'] ?? 0;
            $total    = $subtotal - $diskon + $pajak;

            $transaksi = $this->transaksiRepo->create([
                'nomor_transaksi' => \App\Models\Transaksi::generateNomor(),
                'user_id'         => Auth::id(),
                'tanggal'         => now(),
                'metode_bayar'    => $data['metode_bayar'],
                'subtotal'        => $subtotal,
                'diskon'          => $diskon,
                'pajak'           => $pajak,
                'total'           => $total,
                'catatan'         => $data['catatan'] ?? null,
                'status'          => 'selesai',
            ]);

            $items = array_map(fn($item) => [
                'menu_id'              => $item['menu_id'],
                'nama_menu'            => $item['nama_menu'],
                'harga_saat_transaksi' => $item['harga'],
                'qty'                  => $item['qty'],
                'subtotal'             => $item['subtotal'],
                'created_at'           => now(),
                'updated_at'           => now(),
            ], $data['items']);

            $this->transaksiRepo->createDetail($transaksi->id, $items);
            $this->stokService->kurangiStokDariTransaksi($data['items'], $transaksi->id);

            return $transaksi;
        });
    }
}