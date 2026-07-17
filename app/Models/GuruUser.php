<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class GuruUser extends Authenticatable
{
    use Notifiable;

    // ─── Guard terpisah dari admin ───────────────────────────────
    // Pastikan config/auth.php sudah ditambahkan guard 'guru'
    // (lihat file config/auth.php pada Tahap 1 ini)
    protected $guard = 'guru';

    protected $table = 'guru_users';

    protected $fillable = [
        'madrasah_id',
        'pegid',
        'nama',
        'email',
        'password',
        'no_hp',
        'is_active',
        'password_changed',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password'         => 'hashed',
            'is_active'        => 'boolean',
            'password_changed' => 'boolean',
        ];
    }

    // ─── Relasi ──────────────────────────────────────────────────

    public function madrasah(): BelongsTo
    {
        return $this->belongsTo(Madrasah::class, 'madrasah_id');
    }

    public function arsip(): HasMany
    {
        return $this->hasMany(ArsipGuru::class, 'guru_id');
    }

    public function pengajuan(): HasMany
    {
        return $this->hasMany(Pengajuan::class, 'guru_user_id');
    }

    // ─── Helper ──────────────────────────────────────────────────

    /**
     * Nama madrasah untuk ditampilkan (fallback jika relasi null)
     */
    public function getNamaMadrasahAttribute(): string
    {
        return $this->madrasah?->nama_madrasah ?? '—';
    }

    /**
     * Cek apakah masih menggunakan password default (pegid)
     */
    public function isUsingDefaultPassword(): bool
    {
        return ! $this->password_changed;
    }

    /**
     * Cek apakah nomor HP sudah diisi dengan nilai yang valid
     * (bukan kosong / null / placeholder "0" dari hasil import)
     */
    public function hasNoHpValid(): bool
    {
        $v = trim((string) $this->no_hp);
        return $v !== '' && $v !== '0';
    }

    /**
     * Cek apakah email sudah diisi dengan nilai yang valid
     * (bukan kosong / null / placeholder "-" dari hasil import)
     */
    public function hasEmailValid(): bool
    {
        $v = trim((string) $this->email);
        return $v !== '' && $v !== '-';
    }

    /**
     * Cek apakah data kontak (minimal no HP) sudah lengkap
     * untuk bisa mengajukan dokumen tanpa mengisi ulang.
     */
    public function isKontakLengkap(): bool
    {
        return $this->hasNoHpValid();
    }

    // ─── Scope ───────────────────────────────────────────────────

    public function scopeAktif($query)
    {
        return $query->where('is_active', true);
    }
}
