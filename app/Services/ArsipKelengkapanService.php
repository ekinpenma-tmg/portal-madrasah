<?php

namespace App\Services;

use App\Models\ArsipGuru;
use App\Models\ArsipMadrasah;
use App\Models\KategoriArsip;
use Illuminate\Support\Collection;

class ArsipKelengkapanService
{
    /**
     * Hitung kelengkapan arsip GURU: dari kategori yang tersedia untuk guru,
     * berapa yang sudah diisi masing-masing guru, dan kategori apa saja yang
     * masih kosong (dipakai untuk laporan kelengkapan lintas guru/madrasah,
     * bukan cuma checklist personal seperti di dashboard).
     *
     * @param  Collection  $guruList  Koleksi model GuruUser
     */
    public function hitungGuru(Collection $guruList): Collection
    {
        if ($guruList->isEmpty()) {
            return collect();
        }

        $kategoriWajib = KategoriArsip::untukGuru()->pluck('nama', 'id');
        $totalKategori = $kategoriWajib->count();
        $guruIds       = $guruList->pluck('id');

        // Kategori_id unik yang sudah pernah diisi, per guru — dalam satu query,
        // bukan query per guru satu-satu.
        $terisiMap = ArsipGuru::whereIn('guru_id', $guruIds)
            ->whereIn('kategori_id', $kategoriWajib->keys())
            ->select('guru_id', 'kategori_id')
            ->distinct()
            ->get()
            ->groupBy('guru_id')
            ->map(fn ($rows) => $rows->pluck('kategori_id')->all());

        return $guruList->map(function ($guru) use ($kategoriWajib, $totalKategori, $terisiMap) {
            $terisiIds = $terisiMap->get($guru->id, []);
            $belum     = $kategoriWajib->except($terisiIds);

            return (object) [
                'pemilik'        => $guru,
                'total_kategori' => $totalKategori,
                'total_terisi'   => count($terisiIds),
                'total_belum'    => $belum->count(),
                'kategori_belum' => $belum->values(),
                'persen_lengkap' => $totalKategori > 0 ? round(count($terisiIds) / $totalKategori * 100) : null,
            ];
        });
    }

    /**
     * Hitung kelengkapan arsip MADRASAH (dokumen institusi, bukan dokumen guru).
     *
     * @param  Collection  $madrasahUserList  Koleksi model MadrasahUser
     */
    public function hitungMadrasah(Collection $madrasahUserList): Collection
    {
        if ($madrasahUserList->isEmpty()) {
            return collect();
        }

        $kategoriWajib = KategoriArsip::untukMadrasah()->pluck('nama', 'id');
        $totalKategori = $kategoriWajib->count();
        $madrasahIds   = $madrasahUserList->pluck('id');

        $terisiMap = ArsipMadrasah::whereIn('madrasah_user_id', $madrasahIds)
            ->whereIn('kategori_id', $kategoriWajib->keys())
            ->select('madrasah_user_id', 'kategori_id')
            ->distinct()
            ->get()
            ->groupBy('madrasah_user_id')
            ->map(fn ($rows) => $rows->pluck('kategori_id')->all());

        return $madrasahUserList->map(function ($madrasahUser) use ($kategoriWajib, $totalKategori, $terisiMap) {
            $terisiIds = $terisiMap->get($madrasahUser->id, []);
            $belum     = $kategoriWajib->except($terisiIds);

            return (object) [
                'pemilik'        => $madrasahUser,
                'total_kategori' => $totalKategori,
                'total_terisi'   => count($terisiIds),
                'total_belum'    => $belum->count(),
                'kategori_belum' => $belum->values(),
                'persen_lengkap' => $totalKategori > 0 ? round(count($terisiIds) / $totalKategori * 100) : null,
            ];
        });
    }
}
