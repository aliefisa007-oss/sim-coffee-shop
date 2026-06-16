<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->string('kode_menu', 10)->unique();
            $table->string('nama_menu', 150);
            $table->foreignId('kategori_id')
                  ->constrained('kategori_menu')
                  ->restrictOnDelete();
            $table->decimal('harga_jual', 10, 2);
            $table->tinyInteger('status_aktif')->default(1);
            $table->string('foto_menu', 255)->nullable();
            $table->text('deskripsi')->nullable();
            $table->timestamps();
            $table->index('kategori_id');
            $table->index('status_aktif');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menus');
    }
};