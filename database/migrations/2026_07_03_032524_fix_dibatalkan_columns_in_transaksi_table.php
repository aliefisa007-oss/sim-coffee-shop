<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Migration sebelumnya (add_cancel_fields_to_transaksi_table) hanya
     * membuat kolom 'alasan_batal' dan 'dibatal_oleh' (typo, seharusnya
     * 'dibatalkan_oleh'). Kolom 'dibatalkan_at' tidak pernah dibuat sama
     * sekali. Migration ini menambahkan kolom yang benar-benar dipakai
     * oleh TransaksiController & model Transaksi.
     */
    public function up(): void
    {
        Schema::table('transaksi', function (Blueprint $table) {
            if (!Schema::hasColumn('transaksi', 'dibatalkan_at')) {
                $table->timestamp('dibatalkan_at')->nullable()->after('alasan_batal');
            }

            if (!Schema::hasColumn('transaksi', 'dibatalkan_oleh')) {
                $table->foreignId('dibatalkan_oleh')
                    ->nullable()
                    ->after('dibatalkan_at')
                    ->constrained('users')
                    ->nullOnDelete();
            }
        });

        // Kolom 'dibatal_oleh' (typo dari migration sebelumnya) dibiarkan
        // saja di database, tidak dipakai oleh aplikasi, aman diabaikan.
        // Kalau mau dibersihkan, uncomment baris di bawah:
        //
        // Schema::table('transaksi', function (Blueprint $table) {
        //     if (Schema::hasColumn('transaksi', 'dibatal_oleh')) {
        //         $table->dropColumn('dibatal_oleh');
        //     }
        // });
    }

    public function down(): void
    {
        Schema::table('transaksi', function (Blueprint $table) {
            if (Schema::hasColumn('transaksi', 'dibatalkan_oleh')) {
                $table->dropForeign(['dibatalkan_oleh']);
                $table->dropColumn('dibatalkan_oleh');
            }
            if (Schema::hasColumn('transaksi', 'dibatalkan_at')) {
                $table->dropColumn('dibatalkan_at');
            }
        });
    }
};