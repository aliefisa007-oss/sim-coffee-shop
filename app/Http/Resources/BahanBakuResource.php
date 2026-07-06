<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BahanBakuResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'kode_bahan' => $this->kode_bahan,
            'nama_bahan' => $this->nama_bahan,
            'satuan' => $this->satuan,
            'stok' => (float) $this->stok,
            'stok_minimum' => (float) $this->stok_minimum,
            'harga_per_satuan' => (float) $this->harga_per_satuan,
            'is_menipis' => $this->isMenipis(),
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
        ];
    }
}
