<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JenisDokumen extends Model
{
    protected $table    = 'jenis_dokumen';
    protected $fillable = ['nama', 'deskripsi', 'syarat', 'icon', 'aktif', 'untuk'];
    protected $casts    = ['aktif' => 'boolean'];

    public function pengajuan(): HasMany
    {
        return $this->hasMany(Pengajuan::class);
    }

    public function scopeAktif($query)
    {
        return $query->where('aktif', true);
    }
}
