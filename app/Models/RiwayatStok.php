<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RiwayatStok extends Model
{
    use HasFactory;

    protected $table = 'riwayat_stok';

    protected $fillable = [
        'bahan_baku_id', 'user_id', 'transaksi_id',
        'tipe', 'jumlah', 'stok_sebelum', 'stok_sesudah', 'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'jumlah'       => 'decimal:2',
            'stok_sebelum' => 'decimal:2',
            'stok_sesudah' => 'decimal:2',
        ];
    }

    public function bahanBaku(): BelongsTo
    {
        return $this->belongsTo(BahanBaku::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transaksi(): BelongsTo
    {
        return $this->belongsTo(Transaksi::class);
    }
}