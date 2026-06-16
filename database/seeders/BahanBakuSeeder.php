<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BahanBaku;

class BahanBakuSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'kode_bahan'   => 'BHN-001',
                'nama_bahan'   => 'Arabica Bean',
                'satuan'       => 'gram',
                'stok'         => 5000,
                'stok_minimum' => 500,
            ],
            [
                'kode_bahan'   => 'BHN-002',
                'nama_bahan'   => 'Fresh Milk',
                'satuan'       => 'ml',
                'stok'         => 10000,
                'stok_minimum' => 1000,
            ],
            [
                'kode_bahan'   => 'BHN-003',
                'nama_bahan'   => 'Matcha Powder',
                'satuan'       => 'gram',
                'stok'         => 2000,
                'stok_minimum' => 200,
            ],
            [
                'kode_bahan'   => 'BHN-004',
                'nama_bahan'   => 'Chocolate Powder',
                'satuan'       => 'gram',
                'stok'         => 2000,
                'stok_minimum' => 200,
            ],
            [
                'kode_bahan'   => 'BHN-005',
                'nama_bahan'   => 'Vanilla Syrup',
                'satuan'       => 'ml',
                'stok'         => 3000,
                'stok_minimum' => 300,
            ],
            [
                'kode_bahan'   => 'BHN-006',
                'nama_bahan'   => 'Caramel Syrup',
                'satuan'       => 'ml',
                'stok'         => 3000,
                'stok_minimum' => 300,
            ],
        ];

        foreach ($data as $item) {
            BahanBaku::create($item);
        }
    }
}