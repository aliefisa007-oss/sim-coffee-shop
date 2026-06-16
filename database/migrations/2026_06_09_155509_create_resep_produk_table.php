<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resep_produk', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_id')
                  ->constrained('menus')
                  ->cascadeOnDelete();
            $table->foreignId('bahan_baku_id')
                  ->constrained('bahan_baku')
                  ->restrictOnDelete();
            $table->decimal('jumlah', 10, 2);
            $table->timestamps();

            // Satu bahan tidak boleh duplikat dalam satu resep
            $table->unique(['menu_id', 'bahan_baku_id']);

            // Index
            $table->index('menu_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resep_produk');
    }
};