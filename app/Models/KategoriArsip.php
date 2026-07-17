<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KategoriArsip extends Model
{
    protected $table = 'kategori_arsip';

    protected $fillable = ['nama', 'deskripsi', 'urutan', 'aktif', 'untuk'];

    protected $casts = ['aktif' => 'boolean'];

    // Relasi ke arsip guru — sesuaikan nama FK dengan tabel arsip_guru kamu
    public function arsip(): HasMany
    {
        return $this->hasMany(ArsipGuru::class, 'kategori_id');
    }

    // Relasi ke arsip madrasah — FK di tabel arsip_madrasah adalah kategori_id
    public function arsipMadrasah(): HasMany
    {
        return $this->hasMany(ArsipMadrasah::class, 'kategori_id');
    }

    // ─── Scope ───────────────────────────────────────────────

    public function scopeAktif($query)
    {
        return $query->where('aktif', true)->orderBy('urutan');
    }

    // Hanya untuk guru (guru + semua)
    public function scopeUntukGuru($query)
    {
        return $query->where('aktif', true)
            ->whereIn('untuk', ['guru', 'semua'])
            ->orderBy('urutan');
    }

    // Hanya untuk madrasah (madrasah + semua)
    public function scopeUntukMadrasah($query)
    {
        return $query->where('aktif', true)
            ->whereIn('untuk', ['madrasah', 'semua'])
            ->orderBy('urutan');
    }
}
