<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pengajuan', function (Blueprint $table) {
            // Letakkan kolom token setelah nama_madrasah
            // Catatan: nama_madrasah di sini adalah kolom BARU (hasil rename dari nama_pengajuan)
            $table->string('token', 255)->nullable()->after('nama_madrasah');
        });
    }

    public function down(): void
    {
        Schema::table('pengajuan', function (Blueprint $table) {
            $table->dropColumn('token');
        });
    }
};
