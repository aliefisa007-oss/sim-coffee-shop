<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            KategoriMenuSeeder::class,
            BahanBakuSeeder::class,
            MenuSeeder::class,
            ResepProdukSeeder::class,
        ]);
    }
}