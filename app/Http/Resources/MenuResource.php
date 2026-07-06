<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MenuResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'kode_menu' => $this->kode_menu,
            'nama_menu' => $this->nama_menu,
            'kategori' => [
                'id' => $this->kategori->id,
                'kode_kategori' => $this->kategori->kode_kategori,
                'nama_kategori' => $this->kategori->nama_kategori,
            ],
            'harga_jual' => (float) $this->harga_jual,
            'status_aktif' => (bool) $this->status_aktif,
            'foto_url' => $this->foto_url,
            'deskripsi' => $this->deskripsi,
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
        ];
    }
}
