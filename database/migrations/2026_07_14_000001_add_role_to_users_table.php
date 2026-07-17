<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Sebelumnya semua akun di tabel `users` (admin) punya hak yang sama
     * persis — siapapun bisa hapus admin lain, reset semua akun guru/
     * madrasah sekaligus, dsb. Migration ini menambah kolom `role` supaya
     * ada 2 tingkatan:
     *   - super_admin : hak penuh (kelola akun admin, reset massal, dll)
     *   - staff       : tidak bisa kelola akun admin lain / reset massal
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 20)->default('staff')->after('password');
        });

        // Semua akun yang SUDAH ADA sebelum fitur ini dinaikkan jadi
        // super_admin, supaya tidak ada admin yang tiba-tiba kehilangan
        // akses ke fitur yang biasa dia pakai. Akun BARU yang dibuat
        // setelah ini defaultnya 'staff' kecuali dipilih lain oleh
        // super_admin saat membuatnya.
        DB::table('users')->update(['role' => 'super_admin']);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }
};
