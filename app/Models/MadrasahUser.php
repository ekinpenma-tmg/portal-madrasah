<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class MadrasahUser extends Authenticatable
{
    use Notifiable;

    protected $guard = 'madrasah';

    protected $table = 'madrasah_users';

    protected $fillable = [
        'madrasah_id',
        'nsm',
        'nama_pic',
        'email',
        'no_hp',
        'password',
        'is_active',
        'password_changed',
        'last_login',
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
            'last_login'       => 'datetime',
        ];
    }

    // ─── Relasi ──────────────────────────────────────────────

    public function madrasah(): BelongsTo
    {
        return $this->belongsTo(Madrasah::class, 'madrasah_id');
    }

    public function arsip(): HasMany
    {
        return $this->hasMany(ArsipMadrasah::class, 'madrasah_user_id');
    }

    public function pengajuan(): HasMany
    {
        return $this->hasMany(Pengajuan::class, 'madrasah_user_id');
    }

    // ─── Helper ──────────────────────────────────────────────

    public function getNamaMadrasahAttribute(): string
    {
        return $this->madrasah?->nama_madrasah ?? '—';
    }

    public function getNsmMadrasahAttribute(): string
    {
        return $this->madrasah?->nsm ?? $this->nsm;
    }

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

    // ─── Scope ───────────────────────────────────────────────

    public function scopeAktif($query)
    {
        return $query->where('is_active', true);
    }
}
