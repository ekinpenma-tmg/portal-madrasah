<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('layanan', function (Blueprint $table) {
            $table->id();
            $table->string('kategori');            // contoh: "Pelayanan Perizinan"
            $table->string('nama');
            $table->string('slug')->unique();
            $table->string('icon')->nullable();     // lihat helper Layanan::iconOptions()
            $table->string('ringkasan')->nullable(); // teaser singkat untuk kartu daftar
            $table->text('deskripsi')->nullable();
            $table->text('dasar_hukum')->nullable();
            $table->text('syarat')->nullable();      // 1 baris = 1 syarat
            $table->text('alur')->nullable();        // 1 baris = 1 tahapan
            $table->string('waktu_proses')->nullable(); // contoh: "3 hari kerja"
            $table->string('biaya')->nullable();     // contoh: "Gratis" / "Rp50.000"
            $table->string('sop_file_path')->nullable();  // lampiran PDF resmi (opsional)
            $table->string('sop_nama_asli')->nullable();
            $table->boolean('aktif')->default(true);
            $table->integer('urutan')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('layanan');
    }
};
