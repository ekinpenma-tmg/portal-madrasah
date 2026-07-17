<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Layanan extends Model
{
    protected $table = 'layanan';

    protected $fillable = [
        'kategori',
        'nama',
        'slug',
        'icon',
        'ringkasan',
        'deskripsi',
        'dasar_hukum',
        'syarat',
        'alur',
        'waktu_proses',
        'biaya',
        'sop_file_path',
        'sop_nama_asli',
        'aktif',
        'urutan',
    ];

    protected $casts = [
        'aktif' => 'boolean',
    ];

    /**
     * Gunakan slug pada Route::model binding, mis. Route::get('/pelayanan/{layanan:slug}', ...)
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function scopeAktif($query)
    {
        return $query->where('aktif', true);
    }

    public function scopeUrut($query)
    {
        return $query->orderBy('urutan')->orderBy('nama');
    }

    /** Pecah kolom `syarat` (1 baris = 1 item) jadi array, buang baris kosong. */
    public function getSyaratListAttribute(): array
    {
        return $this->splitLines($this->syarat);
    }

    /** Pecah kolom `alur` (1 baris = 1 tahapan) jadi array, buang baris kosong. */
    public function getAlurListAttribute(): array
    {
        return $this->splitLines($this->alur);
    }

    protected function splitLines(?string $value): array
    {
        if (blank($value)) {
            return [];
        }

        return collect(preg_split('/\r\n|\r|\n/', $value))
            ->map(fn ($line) => trim($line))
            ->filter()
            ->values()
            ->all();
    }

    /**
     * Preset ikon (heroicons outline path) yang bisa dipilih admin lewat dropdown,
     * supaya tidak perlu tempel path SVG manual tiap tambah layanan.
     */
    public static function iconOptions(): array
    {
        return [
            'dokumen'     => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
            'perizinan'   => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
            'rekomendasi' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
            'ijazah'      => 'M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422A12.083 12.083 0 0118 20.944m0-6.366L12 14m6 .578l-6-3.422M12 14v7',
            'data'        => 'M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4',
            'konsultasi'  => 'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z',
        ];
    }
}
