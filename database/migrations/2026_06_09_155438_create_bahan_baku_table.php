<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bahan_baku', function (Blueprint $table) {
            $table->id();
            $table->string('kode_bahan', 10)->unique();
            $table->string('nama_bahan', 150);
            $table->enum('satuan', ['gram', 'ml', 'pcs', 'botol']);
            $table->decimal('stok', 10, 2)->default(0);
            $table->decimal('stok_minimum', 10, 2)->default(0);
            $table->timestamps();

            // Index — sering dicek untuk notifikasi stok menipis
            $table->index('stok');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bahan_baku');
    }
};