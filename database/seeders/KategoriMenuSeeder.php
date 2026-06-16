<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\KategoriMenu;

class KategoriMenuSeeder extends Seeder
{
    public function run(): void
    {
        KategoriMenu::create(['kode_kategori' => 'KAT-001', 'nama_kategori' => 'Coffee']);
        KategoriMenu::create(['kode_kategori' => 'KAT-002', 'nama_kategori' => 'Non Coffee']);
        KategoriMenu::create(['kode_kategori' => 'KAT-003', 'nama_kategori' => 'Food']);
        KategoriMenu::create(['kode_kategori' => 'KAT-004', 'nama_kategori' => 'Snack']);
    }
}