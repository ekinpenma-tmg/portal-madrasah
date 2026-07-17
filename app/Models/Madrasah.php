<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Madrasah extends Model
{
    protected $table = 'madrasah';

    protected $fillable = [
        'nsm', 'npsn', 'nama_madrasah', 'jenjang', 'status',
        'kecamatan', 'alamat', 'akreditasi', 'nama_kepala',
        'tahun_data', 'is_active',
    ];

    protected $casts = [
        'is_active'   => 'boolean',
    ];

    // ─── Relasi ──────────────────────────────────────────────

    public function siswa(): HasMany
    {
        return $this->hasMany(SiswaMadrasah::class, 'madrasah_id');
    }

    public function siswaLatest(): HasOne
    {
        return $this->hasOne(SiswaMadrasah::class, 'madrasah_id')
            ->latestOfMany('tahun_pelajaran');
    }

    // Relasi ke akun portal madrasah (1 madrasah = 1 akun)
    public function madrasahUser(): HasOne
    {
        return $this->hasOne(MadrasahUser::class, 'madrasah_id');
    }

    // ─── Scope ───────────────────────────────────────────────

    public function scopeAktif($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeJenjang($query, string $jenjang)
    {
        return $query->where('jenjang', $jenjang);
    }

    /**
     * Label lengkap & konsisten buat ditampilkan di semua dropdown/select madrasah
     * di seluruh aplikasi, format: "[Jenjang] Nama Madrasah — NSM".
     * NSM disertakan supaya admin gak bingung kalau ada nama madrasah yang sama
     * (misalnya beberapa "RA AL HUDA" di kecamatan berbeda).
     * Pakai accessor ini ($m->label_lengkap) di semua tempat baru juga ya,
     * jangan tulis ulang format manual di blade — biar kalau mau ganti format
     * lagi nanti, cukup ubah di satu tempat ini.
     */
    public function getLabelLengkapAttribute(): string
    {
        return "[{$this->jenjang}] {$this->nama_madrasah} — {$this->nsm}";
    }

    // ─── Static helpers ──────────────────────────────────────

    public static function rekapJenjang(): array
    {
        return static::aktif()
            ->selectRaw('jenjang, count(*) as total')
            ->groupBy('jenjang')
            ->orderByRaw("FIELD(jenjang, 'RA', 'MI', 'MTs', 'MA')")
            ->pluck('total', 'jenjang')
            ->toArray();
    }

    public static function rekapKecamatan(): array
    {
        return static::aktif()
            ->selectRaw('kecamatan, count(*) as total')
            ->groupBy('kecamatan')
            ->orderBy('total', 'desc')
            ->pluck('total', 'kecamatan')
            ->toArray();
    }

    public static function rekapStatus(): array
    {
        return static::aktif()
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();
    }

    public static function rekapAkreditasi(): array
    {
        return static::aktif()
            ->selectRaw('akreditasi, count(*) as total')
            ->groupBy('akreditasi')
            ->orderBy('total', 'desc')
            ->pluck('total', 'akreditasi')
            ->toArray();
    }
}
