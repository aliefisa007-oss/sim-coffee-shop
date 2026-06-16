<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class BahanBaku extends Model
{
    use HasFactory;

    protected $table = 'bahan_baku';

    protected $fillable = [
    'kode_bahan', 'nama_bahan', 'satuan', 
    'stok', 'stok_minimum', 'harga_per_satuan',
];

   protected function casts(): array
{
    return [
        'stok'             => 'decimal:2',
        'stok_minimum'     => 'decimal:2',
        'harga_per_satuan' => 'decimal:2',
    ];
}

    public function resepProduk(): HasMany
    {
        return $this->hasMany(ResepProduk::class);
    }

    public function riwayatStok(): HasMany
    {
        return $this->hasMany(RiwayatStok::class);
    }

    public function scopeMenipis($query)
    {
        return $query->whereColumn('stok', '<=', 'stok_minimum');
    }

    public function isMenipis(): bool
    {
        return $this->stok <= $this->stok_minimum;
    }

    public function isStokCukup(float $jumlahDibutuhkan): bool
    {
        return $this->stok >= $jumlahDibutuhkan;
    }
}