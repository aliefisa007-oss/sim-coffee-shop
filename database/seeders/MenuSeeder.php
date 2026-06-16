<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Menu;
use App\Models\KategoriMenu;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        $coffee    = KategoriMenu::where('nama_kategori', 'Coffee')->first();
        $nonCoffee = KategoriMenu::where('nama_kategori', 'Non Coffee')->first();

        if (!$coffee || !$nonCoffee) {
            $this->command->error('Kategori tidak ditemukan! Jalankan KategoriMenuSeeder dulu.');
            return;
        }

        $data = [
            ['kode_menu' => 'MNU-001', 'nama_menu' => 'Americano',    'kategori_id' => $coffee->id,    'harga_jual' => 18000, 'status_aktif' => 1],
            ['kode_menu' => 'MNU-002', 'nama_menu' => 'Latte',        'kategori_id' => $coffee->id,    'harga_jual' => 22000, 'status_aktif' => 1],
            ['kode_menu' => 'MNU-003', 'nama_menu' => 'Cappuccino',   'kategori_id' => $coffee->id,    'harga_jual' => 22000, 'status_aktif' => 1],
            ['kode_menu' => 'MNU-004', 'nama_menu' => 'Matcha Latte', 'kategori_id' => $nonCoffee->id, 'harga_jual' => 25000, 'status_aktif' => 1],
            ['kode_menu' => 'MNU-005', 'nama_menu' => 'Chocolate',    'kategori_id' => $nonCoffee->id, 'harga_jual' => 20000, 'status_aktif' => 1],
        ];

        foreach ($data as $item) {
            Menu::create($item);
        }
    }
}