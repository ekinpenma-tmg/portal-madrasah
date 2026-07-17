<?php

namespace Database\Seeders;

use App\Models\Layanan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class LayananSeeder extends Seeder
{
    /**
     * Mengisi 18 poin Standar Pelayanan Penma Kemenag Temanggung.
     * Nama & kategori sudah terisi — syarat, waktu proses, biaya, alur,
     * dan lampiran PDF silakan dilengkapi lewat menu admin > Pelayanan.
     *
     * Aman dijalankan berkali-kali (updateOrCreate berdasarkan slug).
     */
    public function run(): void
    {
        $daftar = [
            'Pelayanan Perizinan' => [
                'Standar Pelayanan Izin Pendirian Madrasah',
                'Standar Pelayanan Perubahan/Penggantian/Kerusakan Izin Operasional Pendirian Madrasah',
                'Standar Pelayanan Izin Penutupan Madrasah',
                'Permohonan Izin Operasional Lembaga Pendidikan Keagamaan',
            ],
            'Pelayanan Rekomendasi' => [
                'Standar Pelayanan Rekomendasi Pengangkatan Kepala Madrasah Swasta',
                'Penerbitan Rekomendasi Pengangkatan Kepala Satuan Pendidikan Keagamaan',
                'Penerbitan Rekomendasi Bantuan Operasional Satuan Pendidikan Keagamaan',
                'Penerbitan Rekomendasi Bantuan Sarana dan Prasarana Satuan Pendidikan Keagamaan',
            ],
            'Pelayanan Ijazah dan Dokumen' => [
                'Penerbitan Surat Keterangan Pengganti Ijazah Hilang/Rusak',
                'Penerbitan Surat Keterangan Pengganti Ijazah/Transkrip Nilai/Sertifikat Profesi',
                'Legalisasi Salinan Piagam Penghargaan Siswa/Pendidik/Tenaga Kependidikan',
            ],
            'Pelayanan Data' => [
                'Pemutakhiran, Perbaikan, dan Penyediaan Data Peserta Didik',
            ],
            'Pelayanan Konsultasi' => [
                'Standar Pelayanan Konsultasi Aplikasi EMIS 4.0',
                'Standar Pelayanan Konsultasi Aplikasi EMIS GTK (SIMPATIKA)',
                'Standar Pelayanan Konsultasi Pembayaran TPG',
                'Standar Pelayanan Konsultasi BOS/BOP',
                'Standar Pelayanan Konsultasi PIP',
                'Standar Pelayanan Konsultasi Pendaftaran Profesi Guru',
            ],
        ];

        $iconPerKategori = [
            'Pelayanan Perizinan'          => 'perizinan',
            'Pelayanan Rekomendasi'        => 'rekomendasi',
            'Pelayanan Ijazah dan Dokumen' => 'ijazah',
            'Pelayanan Data'               => 'data',
            'Pelayanan Konsultasi'         => 'konsultasi',
        ];

        $urutan = 1;

        foreach ($daftar as $kategori => $items) {
            foreach ($items as $nama) {
                $slug = Str::slug($nama);

                Layanan::updateOrCreate(
                    ['slug' => $slug],
                    [
                        'kategori' => $kategori,
                        'nama'     => $nama,
                        'icon'     => $iconPerKategori[$kategori] ?? 'dokumen',
                        'urutan'   => $urutan,
                        'aktif'    => true,
                        // syarat, alur, waktu_proses, biaya, dasar_hukum, ringkasan
                        // sengaja dikosongkan — lengkapi lewat admin > Pelayanan > Edit
                    ]
                );

                $urutan++;
            }
        }
    }
}
