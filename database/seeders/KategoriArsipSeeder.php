<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Seed kategori arsip bawaan.
 * Admin bisa menambah/mengubah/menghapus kategori ini lewat panel admin.
 * Jalankan: php artisan db:seed --class=KategoriArsipSeeder
 */
class KategoriArsipSeeder extends Seeder
{
    public function run(): void
    {
        $kategori = [
            ['nama' => 'Ijazah & Sertifikat',          'deskripsi' => 'Ijazah pendidikan formal dan sertifikat lainnya',           'urutan' => 1],
            ['nama' => 'SK Mengajar / SK Tugas',        'deskripsi' => 'Surat Keputusan penugasan mengajar atau tugas tambahan',    'urutan' => 2],
            ['nama' => 'Sertifikat Pendidik (Serdik)',  'deskripsi' => 'Sertifikat profesi pendidik dari kemdikbud/kemenag',        'urutan' => 3],
            ['nama' => 'Berkas Kepegawaian',            'deskripsi' => 'Dokumen NIP, NUPTK, NRG, dan identitas kepegawaian lain',  'urutan' => 4],
            ['nama' => 'Dokumen Pelatihan / Diklat',    'deskripsi' => 'Sertifikat pelatihan, diklat, bimtek, workshop',           'urutan' => 5],
            ['nama' => 'Laporan / Portofolio',          'deskripsi' => 'Laporan kinerja, PTK, portofolio, karya ilmiah',           'urutan' => 6],
        ];

        foreach ($kategori as $item) {
            DB::table('kategori_arsip')->updateOrInsert(
                ['nama' => $item['nama']],
                array_merge($item, [
                    'aktif'      => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }
}
