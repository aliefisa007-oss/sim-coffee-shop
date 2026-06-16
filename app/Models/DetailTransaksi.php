<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetailTransaksi extends Model
{
    use HasFactory;

    protected $table = 'detail_transaksi';

    protected $fillable = [
        'transaksi_id', 'menu_id', 'nama_menu',
        'harga_saat_transaksi', 'qty', 'subtotal',
    ];

    protected function casts(): array
    {
        return [
            'harga_saat_transaksi' => 'decimal:2',
            'subtotal'             => 'decimal:2',
            'qty'                  => 'integer',
        ];
    }

    public function transaksi(): BelongsTo
    {
        return $this->belongsTo(Transaksi::class);
    }

    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }
}