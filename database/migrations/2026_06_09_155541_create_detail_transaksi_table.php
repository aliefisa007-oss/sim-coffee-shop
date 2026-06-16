<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detail_transaksi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaksi_id')
                  ->constrained('transaksi')
                  ->cascadeOnDelete();
            $table->foreignId('menu_id')
                  ->constrained('menus')
                  ->restrictOnDelete();
            $table->string('nama_menu', 150);
            $table->decimal('harga_saat_transaksi', 10, 2);
            $table->integer('qty');
            $table->decimal('subtotal', 10, 2);
            $table->timestamps();
            $table->index('transaksi_id');
            $table->index('menu_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_transaksi');
    }
};