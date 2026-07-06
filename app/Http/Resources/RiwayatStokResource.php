<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RiwayatStokResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'bahan_baku' => [
                'id' => $this->bahanBaku->id,
                'kode_bahan' => $this->bahanBaku->kode_bahan,
                'nama_bahan' => $this->bahanBaku->nama_bahan,
                'satuan' => $this->bahanBaku->satuan,
            ],
            'user' => $this->user ? [
                'id' => $this->user->id,
                'name' => $this->user->name,
            ] : null,
            'transaksi_id' => $this->transaksi_id,
            'nomor_transaksi' => $this->whenLoaded('transaksi', fn () => $this->transaksi?->nomor_transaksi),
            'tipe' => $this->tipe,
            'jumlah' => (float) $this->jumlah,
            'stok_sebelum' => (float) $this->stok_sebelum,
            'stok_sesudah' => (float) $this->stok_sesudah,
            'keterangan' => $this->keterangan,
            'created_at' => $this->created_at->toIso8601String(),
        ];
    }
}
