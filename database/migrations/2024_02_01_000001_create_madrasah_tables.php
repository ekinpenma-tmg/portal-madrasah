<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel data madrasah (dari import EMIS)
        Schema::create('madrasah', function (Blueprint $table) {
            $table->id();
            $table->string('nsm', 20)->unique()->comment('Nomor Statistik Madrasah');
            $table->string('npsn', 10)->nullable()->comment('Nomor Pokok Sekolah Nasional');
            $table->string('nama_madrasah');
            $table->enum('jenjang', ['RA', 'MI', 'MTs', 'MA']);
            $table->enum('status', ['Negeri', 'Swasta']);
            $table->string('kecamatan');
            $table->text('alamat')->nullable();
            $table->string('akreditasi', 10)->nullable()->comment('A, B, C, atau Belum Terakreditasi');
            $table->string('nama_kepala')->nullable()->comment('Admin only - tidak tampil publik');
            $table->string('tahun_data', 10)->comment('Tahun data diimport, misal: 2024');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Tabel data siswa per madrasah (dari import EMIS siswa)
        Schema::create('siswa_madrasah', function (Blueprint $table) {
            $table->id();
            $table->foreignId('madrasah_id')->constrained('madrasah')->onDelete('cascade');
            $table->string('nsm', 20)->comment('Redundan untuk kemudahan query');
            $table->string('tahun_pelajaran', 10)->comment('Misal: 2024/2025');
            $table->integer('siswa_laki')->default(0);
            $table->integer('siswa_perempuan')->default(0);
            $table->integer('total_siswa')->default(0);
            $table->timestamps();

            $table->unique(['madrasah_id', 'tahun_pelajaran']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('siswa_madrasah');
        Schema::dropIfExists('madrasah');
    }
};
