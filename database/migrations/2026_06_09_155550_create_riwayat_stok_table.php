<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('riwayat_stok', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bahan_baku_id')
                  ->constrained('bahan_baku')
                  ->restrictOnDelete();

            // Nullable — bisa dari sistem otomatis (transaksi POS)
            $table->foreignId('user_id')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            // Nullable — hanya terisi jika pergerakan stok
            // berasal dari transaksi POS
            $table->foreignId('transaksi_id')
                  ->nullable()
                  ->constrained('transaksi')
                  ->nullOnDelete();

            $table->enum('tipe', ['masuk', 'keluar', 'penyesuaian']);
            $table->decimal('jumlah', 10, 2);
            $table->decimal('stok_sebelum', 10, 2);
            $table->decimal('stok_sesudah', 10, 2);
            $table->text('keterangan')->nullable();
            $table->timestamps();

            // Index
            $table->index('bahan_baku_id');
            $table->index('tipe');
            $table->index('created_at');
            $table->index('transaksi_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('riwayat_stok');
    }
};