<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transaksi', function (Blueprint $table) {
            $table->text('alasan_batal')->nullable()->after('status');
            $table->timestamp('dibatalkan_at')->nullable()->after('alasan_batal');
            $table->foreignId('dibatalkan_oleh')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete()
                  ->after('dibatalkan_at');
        });
    }

    public function down(): void
    {
        Schema::table('transaksi', function (Blueprint $table) {
            $table->dropForeign(['dibatalkan_oleh']);
            $table->dropColumn(['alasan_batal', 'dibatalkan_at', 'dibatalkan_oleh']);
        });
    }
};