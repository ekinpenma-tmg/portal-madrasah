<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jenis_dokumen', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->text('deskripsi')->nullable();
            $table->string('icon')->default('document');
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });

        Schema::create('pengajuan', function (Blueprint $table) {
            $table->id();
            $table->string('kode_ajuan')->unique();
            $table->foreignId('jenis_dokumen_id')->constrained('jenis_dokumen')->onDelete('restrict');
            $table->string('nama_madrasah');
            $table->string('nism', 20);
            $table->string('nama_pengajuan');
            $table->string('email')->nullable();
            $table->string('no_hp', 20)->nullable();
            $table->string('file_dokumen');
            $table->string('nama_file_asli')->nullable();
            $table->enum('status', ['pending', 'diterima', 'ditolak'])->default('pending');
            $table->text('catatan')->nullable();
            $table->timestamp('tanggal_proses')->nullable();
            $table->timestamps();
        });

        Schema::create('file_download', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->text('deskripsi')->nullable();
            $table->string('file_path');
            $table->string('nama_file_asli')->nullable();
            $table->string('kategori')->nullable();
            $table->integer('jumlah_download')->default(0);
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });

        Schema::create('profil_organisasi', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value');
            $table->timestamps();
        });

        Schema::create('staff', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('jabatan');
            $table->string('foto')->nullable();
            $table->integer('urutan')->default(0);
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff');
        Schema::dropIfExists('profil_organisasi');
        Schema::dropIfExists('file_download');
        Schema::dropIfExists('pengajuan');
        Schema::dropIfExists('jenis_dokumen');
    }
};
