<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KategoriMenu extends Model
{
    use HasFactory;

    protected $table = 'kategori_menu';

    protected $fillable = [
        'kode_kategori', 'nama_kategori', 'deskripsi',
    ];

    public function menus(): HasMany
    {
        return $this->hasMany(Menu::class, 'kategori_id');
    }

    public function menusAktif(): HasMany
    {
        return $this->hasMany(Menu::class, 'kategori_id')
                    ->where('status_aktif', true);
    }
}