<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ResepProduk;
use App\Models\Menu;
use App\Models\BahanBaku;

class ResepProdukSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil ID menu
        $americano  = Menu::where('nama_menu', 'Americano')->first();
        $latte      = Menu::where('nama_menu', 'Latte')->first();
        $cappuccino = Menu::where('nama_menu', 'Cappuccino')->first();
        $matcha     = Menu::where('nama_menu', 'Matcha Latte')->first();
        $chocolate  = Menu::where('nama_menu', 'Chocolate')->first();

        // Ambil ID bahan
        $arabica   = BahanBaku::where('nama_bahan', 'Arabica Bean')->first();
        $milk      = BahanBaku::where('nama_bahan', 'Fresh Milk')->first();
        $matchaPow = BahanBaku::where('nama_bahan', 'Matcha Powder')->first();
        $chocoPow  = BahanBaku::where('nama_bahan', 'Chocolate Powder')->first();

        $data = [
            // Americano
            ['menu_id' => $americano->id,  'bahan_baku_id' => $arabica->id,   'jumlah' => 18],

            // Latte
            ['menu_id' => $latte->id,      'bahan_baku_id' => $arabica->id,   'jumlah' => 18],
            ['menu_id' => $latte->id,      'bahan_baku_id' => $milk->id,      'jumlah' => 180],

            // Cappuccino
            ['menu_id' => $cappuccino->id, 'bahan_baku_id' => $arabica->id,   'jumlah' => 18],
            ['menu_id' => $cappuccino->id, 'bahan_baku_id' => $milk->id,      'jumlah' => 120],

            // Matcha Latte
            ['menu_id' => $matcha->id,     'bahan_baku_id' => $matchaPow->id, 'jumlah' => 10],
            ['menu_id' => $matcha->id,     'bahan_baku_id' => $milk->id,      'jumlah' => 180],

            // Chocolate
            ['menu_id' => $chocolate->id,  'bahan_baku_id' => $chocoPow->id,  'jumlah' => 20],
            ['menu_id' => $chocolate->id,  'bahan_baku_id' => $milk->id,      'jumlah' => 150],
        ];

        foreach ($data as $item) {
            ResepProduk::create($item);
        }
    }
}