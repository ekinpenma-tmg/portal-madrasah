<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArsipGuru extends Model
{
    protected $table = 'arsip_guru';

    protected $fillable = [
        'guru_id',
        'kategori_id',
        'judul',
        'keterangan',
        'link_gdrive',
        'tahun',
        'is_verified',
        'catatan_admin',
    ];

    protected function casts(): array
    {
        return [
            'is_verified' => 'boolean',
            'tahun'       => 'integer',
        ];
    }

    // ─── Relasi ──────────────────────────────────────────────────

    public function guru(): BelongsTo
    {
        return $this->belongsTo(GuruUser::class, 'guru_id');
    }

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(KategoriArsip::class, 'kategori_id');
    }

    // ─── Helper ──────────────────────────────────────────────────

    /**
     * Konversi berbagai format link Google Drive menjadi link pratinjau embed
     * Mendukung: /file/d/{id}/view, /open?id={id}, /drive/folders/{id}
     */
    public function getEmbedUrlAttribute(): ?string
    {
        $link = $this->link_gdrive;

        // Format: https://drive.google.com/file/d/{ID}/view
        if (preg_match('/\/file\/d\/([a-zA-Z0-9_-]+)/', $link, $m)) {
            return 'https://drive.google.com/file/d/' . $m[1] . '/preview';
        }

        // Format: https://drive.google.com/open?id={ID}
        if (preg_match('/[?&]id=([a-zA-Z0-9_-]+)/', $link, $m)) {
            return 'https://drive.google.com/file/d/' . $m[1] . '/preview';
        }

        return null;
    }

    /**
     * Cek apakah link terlihat valid (format Google Drive)
     */
    public function isValidGdriveLink(): bool
    {
        return str_contains($this->link_gdrive, 'drive.google.com')
            || str_contains($this->link_gdrive, 'docs.google.com');
    }

    // ─── Scope ───────────────────────────────────────────────────

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopeByKategori($query, int $kategoriId)
    {
        return $query->where('kategori_id', $kategoriId);
    }
}
