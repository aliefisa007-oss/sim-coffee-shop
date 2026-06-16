<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Menu extends Model
{
    use HasFactory;

    protected $table = 'menus';

    protected $fillable = [
        'kode_menu', 'nama_menu', 'kategori_id',
        'harga_jual', 'status_aktif', 'foto_menu', 'deskripsi',
    ];

    protected function casts(): array
    {
        return [
            'harga_jual'   => 'decimal:2',
            'status_aktif' => 'boolean',
        ];
    }

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(KategoriMenu::class, 'kategori_id');
    }

    public function resepProduk(): HasMany
    {
        return $this->hasMany(ResepProduk::class);
    }

    public function bahanBaku(): BelongsToMany
    {
        return $this->belongsToMany(
            BahanBaku::class, 'resep_produk', 'menu_id', 'bahan_baku_id'
        )->withPivot('jumlah')->withTimestamps();
    }

    public function detailTransaksi(): HasMany
    {
        return $this->hasMany(DetailTransaksi::class);
    }

    public function scopeAktif($query)
    {
        return $query->where('status_aktif', true);
    }

    public function scopeByKategori($query, $kategoriId)
    {
        return $query->where('kategori_id', $kategoriId);
    }

    public function getFotoUrlAttribute(): string
    {
        return $this->foto_menu
            ? asset('uploads/menus/' . $this->foto_menu)
            : asset('assets/img/default-menu.png');
    }
}