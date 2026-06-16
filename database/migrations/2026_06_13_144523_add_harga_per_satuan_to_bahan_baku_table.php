<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bahan_baku', function (Blueprint $table) {
            $table->decimal('harga_per_satuan', 10, 2)->default(0)->after('stok_minimum');
        });
    }

    public function down(): void
    {
        Schema::table('bahan_baku', function (Blueprint $table) {
            $table->dropColumn('harga_per_satuan');
        });
    }
};