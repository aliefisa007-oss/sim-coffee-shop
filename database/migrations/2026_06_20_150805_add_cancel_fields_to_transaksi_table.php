<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::table('transaksi', function (Blueprint $table) {
        if (!Schema::hasColumn('transaksi', 'alasan_batal')) {
            $table->text('alasan_batal')->nullable()->after('status');
        }
        if (!Schema::hasColumn('transaksi', 'dibatal_oleh')) {
            $table->string('dibatal_oleh')->nullable()->after('alasan_batal');
        }
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