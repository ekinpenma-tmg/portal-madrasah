<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Constraint lama: unique(madrasah_user_id, kategori_id) — cuma boleh ADA
     * 1 ARSIP PER KATEGORI SELAMANYA, gak peduli tahunnya beda. Ini menyulitkan
     * kategori yang sifatnya tahunan (mis. "Laporan Tahunan") karena madrasah
     * gak bisa simpan riwayat tahun-tahun sebelumnya, harus timpa terus.
     *
     * Constraint baru: unique(madrasah_user_id, kategori_id, tahun) — boleh
     * ada beberapa arsip untuk kategori yang sama selama tahunnya beda, tapi
     * tetap gak boleh dobel persis di kategori+tahun yang sama.
     *
     * PENTING: index baru harus dibuat DULU sebelum index lama dihapus.
     * MySQL/InnoDB menolak DROP INDEX kalau index itu satu-satunya yang
     * menopang foreign key constraint kolom kategori_id (error 1553) — jadi
     * urutannya dibalik: bikin index baru dulu (yang juga bisa menopang FK
     * yang sama), baru index lama aman dihapus.
     */
    public function up(): void
    {
        // 1) Buat index baru dulu — sekarang ADA 2 index yang menopang kategori_id,
        //    jadi index lama nanti aman dihapus.
        Schema::table('arsip_madrasah', function (Blueprint $table) {
            $table->unique(['madrasah_user_id', 'kategori_id', 'tahun'], 'arsip_madrasah_owner_kategori_tahun_unique');
        });

        // 2) Baru cari & hapus index unik lama (nama dicari otomatis, bukan
        //    di-hardcode, supaya migration ini tetap aman dijalankan walau
        //    nama index sebenarnya di database sedikit berbeda dari yang
        //    tertulis di migration pembuatnya dulu).
        $indexLama = collect(DB::select("SHOW INDEX FROM arsip_madrasah WHERE Non_unique = 0 AND Key_name != 'PRIMARY'"))
            ->groupBy('Key_name')
            ->filter(fn ($cols) => $cols->pluck('Column_name')->contains('kategori_id'))
            ->keys()
            ->reject(fn ($name) => $name === 'arsip_madrasah_owner_kategori_tahun_unique')
            ->first();

        if ($indexLama) {
            DB::statement("ALTER TABLE arsip_madrasah DROP INDEX `{$indexLama}`");
        }
    }

    public function down(): void
    {
        Schema::table('arsip_madrasah', function (Blueprint $table) {
            $table->unique(['madrasah_user_id', 'kategori_id'], 'arsip_madrasah_madrasah_user_id_kategori_arsip_id_unique');
        });

        DB::statement('ALTER TABLE arsip_madrasah DROP INDEX `arsip_madrasah_owner_kategori_tahun_unique`');
    }
};
