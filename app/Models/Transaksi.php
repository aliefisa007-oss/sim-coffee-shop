<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transaksi extends Model
{
    use HasFactory;

    protected $table = 'transaksi';

    protected $fillable = [
        'nomor_transaksi', 'user_id', 'tanggal',
        'metode_bayar', 'subtotal', 'diskon',
        'pajak', 'total', 'catatan', 'status',
    ];

    protected function casts(): array
    {
        return [
            'tanggal'  => 'datetime',
            'subtotal' => 'decimal:2',
            'diskon'   => 'decimal:2',
            'pajak'    => 'decimal:2',
            'total'    => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function detailTransaksi(): HasMany
    {
        return $this->hasMany(DetailTransaksi::class);
    }

    public function riwayatStok(): HasMany
    {
        return $this->hasMany(RiwayatStok::class);
    }

    public function scopeSelesai($query)
    {
        return $query->where('status', 'selesai');
    }

    public function scopeHariIni($query)
    {
        return $query->whereDate('tanggal', today());
    }

    public function scopeBulanIni($query)
    {
        return $query->whereYear('tanggal', now()->year)
                     ->whereMonth('tanggal', now()->month);
    }

    public function scopeRentang($query, $dari, $sampai)
    {
        return $query->whereBetween('tanggal', [$dari, $sampai]);
    }

    public static function generateNomor(): string
    {
        $prefix  = 'TRX';
        $tanggal = now()->format('Ymd');
        $last    = static::whereDate('tanggal', today())->count() + 1;
        return sprintf('%s-%s-%03d', $prefix, $tanggal, $last);
    }
}