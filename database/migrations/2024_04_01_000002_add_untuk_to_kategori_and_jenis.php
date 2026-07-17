<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tambah kolom 'untuk' di kategori_arsip
        Schema::table('kategori_arsip', function (Blueprint $table) {
            $table->enum('untuk', ['guru', 'madrasah', 'semua'])
                ->default('guru')
                ->comment('Menentukan siapa yang bisa menggunakan kategori ini');
        });

        // Tambah kolom 'untuk' di jenis_dokumen
        Schema::table('jenis_dokumen', function (Blueprint $table) {
            $table->enum('untuk', ['guru', 'madrasah', 'semua'])
                ->default('semua')
                ->comment('Menentukan siapa yang bisa mengajukan jenis dokumen ini');
        });
    }

    public function down(): void
    {
        Schema::table('kategori_arsip', function (Blueprint $table) {
            $table->dropColumn('untuk');
        });
        Schema::table('jenis_dokumen', function (Blueprint $table) {
            $table->dropColumn('untuk');
        });
    }
};
