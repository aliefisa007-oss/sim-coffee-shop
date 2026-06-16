<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder {
    public function run(): void {
        // Owner
        User::create([
            'name' => 'Admin Owner',
            'email' => 'owner@coffee.com',
            'password' => Hash::make('123456'),
            'role' => 'owner',
            'is_active' => true,
        ]);

        // Kasir 1
        User::create([
            'name' => 'Budi Kasir',
            'email' => 'budi@coffee.com',
            'password' => Hash::make('123456'),
            'role' => 'kasir',
            'is_active' => true,
        ]);

        // Kasir 2
        User::create([
            'name' => 'Siti Kasir',
            'email' => 'siti@coffee.com',
            'password' => Hash::make('123456'),
            'role' => 'kasir',
            'is_active' => true,
        ]);
    }
}