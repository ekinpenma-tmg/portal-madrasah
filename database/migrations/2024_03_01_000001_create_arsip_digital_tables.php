<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ─────────────────────────────────────────────────────────
        // 1. TABEL AKUN GURU
        //    - Terpisah dari tabel users (admin)
        //    - Password default = pegid (nomor identitas guru)
        //    - Terhubung ke tabel madrasah yang sudah ada
        // ─────────────────────────────────────────────────────────
        Schema::create('guru_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('madrasah_id')
                  ->nullable()
                  ->constrained('madrasah')
                  ->onDelete('set null')
                  ->comment('Madrasah tempat guru bertugas');
            $table->string('pegid', 30)->unique()->comment('Nomor identitas guru, sekaligus password default');
            $table->string('nama');
            $table->string('email')->nullable()->unique();
            $table->string('password')->comment('Default = hash(pegid), bisa diganti guru');
            $table->string('no_hp', 20)->nullable();
            $table->boolean('is_active')->default(true)->comment('Admin bisa non-aktifkan akun');
            $table->boolean('password_changed')->default(false)->comment('Penanda apakah password sudah diubah dari default');
            $table->rememberToken();
            $table->timestamps();
        });

        // ─────────────────────────────────────────────────────────
        // 2. TABEL KATEGORI ARSIP
        //    - Dinamis, dikelola admin (tambah/ubah/hapus)
        //    - Contoh: Ijazah, SK Mengajar, Serdik, dll
        // ─────────────────────────────────────────────────────────
        Schema::create('kategori_arsip', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->text('deskripsi')->nullable();
            $table->integer('urutan')->default(0)->comment('Urutan tampil di form');
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });

        // ─────────────────────────────────────────────────────────
        // 3. TABEL ARSIP GURU
        //    - Menyimpan metadata + link Google Drive
        //    - Tidak menyimpan file fisik di server
        // ─────────────────────────────────────────────────────────
        Schema::create('arsip_guru', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guru_id')
                  ->constrained('guru_users')
                  ->onDelete('cascade')
                  ->comment('Pemilik arsip');
            $table->foreignId('kategori_id')
                  ->constrained('kategori_arsip')
                  ->onDelete('restrict')
                  ->comment('Kategori dokumen');
            $table->string('judul')->comment('Nama/judul dokumen');
            $table->text('keterangan')->nullable()->comment('Deskripsi singkat dokumen');
            $table->string('link_gdrive')->comment('URL Google Drive yang sudah dibagikan (shared link)');
            $table->year('tahun')->nullable()->comment('Tahun dokumen diterbitkan');
            $table->boolean('is_verified')->default(false)->comment('Admin bisa menandai sudah terverifikasi');
            $table->text('catatan_admin')->nullable()->comment('Catatan dari admin jika ada');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('arsip_guru');
        Schema::dropIfExists('kategori_arsip');
        Schema::dropIfExists('guru_users');
    }
};
