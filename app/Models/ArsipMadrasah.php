<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArsipMadrasah extends Model
{
    protected $table = 'arsip_madrasah';

    protected $fillable = [
        'madrasah_user_id',
        'kategori_id',        // ← nama kolom asli di tabel
        'judul',
        'link_gdrive',
        'tahun',
        'keterangan',
        'is_verified',
        'catatan_admin',
    ];

    protected $casts = [
        'is_verified' => 'boolean',
        'tahun'       => 'integer',
    ];

    public function madrasahUser(): BelongsTo
    {
        return $this->belongsTo(MadrasahUser::class, 'madrasah_user_id');
    }

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(KategoriArsip::class, 'kategori_id');
    }
}
