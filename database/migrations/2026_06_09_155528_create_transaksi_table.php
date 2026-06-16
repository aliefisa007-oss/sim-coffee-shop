<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaksi', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_transaksi', 20)->unique();
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->restrictOnDelete();
            $table->dateTime('tanggal');
            $table->enum('metode_bayar', ['cash', 'qris', 'transfer']);
            $table->decimal('subtotal', 10, 2);
            $table->decimal('diskon', 10, 2)->default(0);
            $table->decimal('pajak', 10, 2)->default(0);
            $table->decimal('total', 10, 2);
            $table->text('catatan')->nullable();
            $table->enum('status', ['selesai', 'batal'])->default('selesai');
            $table->timestamps();

            // Index — sering difilter untuk laporan & dashboard
            $table->index('tanggal');
            $table->index('user_id');
            $table->index('status');
            $table->index('metode_bayar');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksi');
    }
};