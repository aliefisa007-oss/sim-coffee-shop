<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResepProduk extends Model
{
    use HasFactory;

    protected $table = 'resep_produk';

    protected $fillable = [
        'menu_id', 'bahan_baku_id', 'jumlah',
    ];

    protected function casts(): array
    {
        return ['jumlah' => 'decimal:2'];
    }

    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }

    public function bahanBaku(): BelongsTo
    {
        return $this->belongsTo(BahanBaku::class);
    }
}