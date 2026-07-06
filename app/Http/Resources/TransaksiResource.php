<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TransaksiResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'nomor_transaksi' => $this->nomor_transaksi,
            'kasir' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
            ],
            'tanggal' => $this->tanggal->toIso8601String(),
            'metode_bayar' => $this->metode_bayar,
            'subtotal' => (float) $this->subtotal,
            'diskon' => (float) $this->diskon,
            'pajak' => (float) $this->pajak,
            'total' => (float) $this->total,
            'catatan' => $this->catatan,
            'status' => $this->status,
            'alasan_batal' => $this->alasan_batal,
            'dibatalkan_at' => $this->dibatalkan_at?->toIso8601String(),
            'dibatalkan_oleh' => $this->whenLoaded('dibatalkanOleh', fn () => $this->dibatalkanOleh?->name),
            'items' => $this->whenLoaded('detailTransaksi', fn () => $this->detailTransaksi->map(fn ($d) => [
                'id' => $d->id,
                'menu_id' => $d->menu_id,
                'nama_menu' => $d->nama_menu,
                'harga_saat_transaksi' => (float) $d->harga_saat_transaksi,
                'qty' => $d->qty,
                'subtotal' => (float) $d->subtotal,
            ])),
            'created_at' => $this->created_at->toIso8601String(),
        ];
    }
}
