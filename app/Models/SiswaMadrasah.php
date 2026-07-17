<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SiswaMadrasah extends Model
{
    protected $table = 'siswa_madrasah';

    protected $fillable = [
        'madrasah_id', 'nsm', 'tahun_pelajaran',
        'siswa_laki', 'siswa_perempuan', 'total_siswa',
    ];

    protected $casts = [
        'siswa_laki'      => 'integer',
        'siswa_perempuan' => 'integer',
        'total_siswa'     => 'integer',
    ];

    public function madrasah(): BelongsTo
    {
        return $this->belongsTo(Madrasah::class, 'madrasah_id');
    }

    /**
     * Rekap total siswa per jenjang untuk tahun pelajaran tertentu
     */
    public static function rekapSiswaPerJenjang(string $tahunPelajaran): array
    {
        return static::join('madrasah', 'siswa_madrasah.madrasah_id', '=', 'madrasah.id')
            ->where('madrasah.is_active', true)
            ->where('siswa_madrasah.tahun_pelajaran', $tahunPelajaran)
            ->selectRaw('madrasah.jenjang, SUM(siswa_madrasah.siswa_laki) as total_laki, SUM(siswa_madrasah.siswa_perempuan) as total_perempuan, SUM(siswa_madrasah.total_siswa) as total')
            ->groupBy('madrasah.jenjang')
            ->orderByRaw("FIELD(madrasah.jenjang, 'RA', 'MI', 'MTs', 'MA')")
            ->get()
            ->keyBy('jenjang')
            ->toArray();
    }

    /**
     * Ambil tahun pelajaran terbaru yang tersedia
     */
    public static function tahunTerbaru(): ?string
    {
        return static::max('tahun_pelajaran');
    }
}
