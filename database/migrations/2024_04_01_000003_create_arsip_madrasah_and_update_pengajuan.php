<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel arsip madrasah — struktur mengikuti arsip_guru
        Schema::create('arsip_madrasah', function (Blueprint $table) {
            $table->id();
            $table->foreignId('madrasah_user_id')
                ->constrained('madrasah_users')
                ->onDelete('cascade');
            $table->foreignId('kategori_arsip_id')
                ->constrained('kategori_arsip')
                ->onDelete('cascade');
            $table->string('judul');
            $table->string('link_gdrive');
            $table->year('tahun')->nullable();
            $table->string('keterangan')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->text('catatan_admin')->nullable();
            $table->timestamps();

            // 1 madrasah hanya boleh 1 arsip per kategori
            $table->unique(['madrasah_user_id', 'kategori_arsip_id']);
        });

        // Tambah kolom madrasah_user_id ke tabel pengajuan
        // nullable: pengajuan publik dan dari guru tetap tidak terpengaruh
        Schema::table('pengajuan', function (Blueprint $table) {
            $table->foreignId('madrasah_user_id')
                ->nullable()
                ->after('guru_user_id')
                ->constrained('madrasah_users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('pengajuan', function (Blueprint $table) {
            $table->dropConstrainedForeignId('madrasah_user_id');
        });
        Schema::dropIfExists('arsip_madrasah');
    }
};
