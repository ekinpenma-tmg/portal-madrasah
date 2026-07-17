<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pengajuan', function (Blueprint $table) {
            // nama_madrasah → nama_guru
            $table->renameColumn('nama_madrasah', 'nama_guru');
            // nism → nip
            $table->renameColumn('nism', 'nip');
            // nama_pengajuan → nama_madrasah
            $table->renameColumn('nama_pengajuan', 'nama_madrasah');
        });
    }

    public function down(): void
    {
        Schema::table('pengajuan', function (Blueprint $table) {
            $table->renameColumn('nama_guru', 'nama_madrasah');
            $table->renameColumn('nip', 'nism');
            $table->renameColumn('nama_madrasah', 'nama_pengajuan');
        });
    }
};
