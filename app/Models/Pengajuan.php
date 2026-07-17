<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pengajuan extends Model
{
    use SoftDeletes;

    protected $table = 'pengajuan';

    protected $fillable = [
        'kode_ajuan',
        'guru_user_id',
        'madrasah_user_id',
        'jenis_dokumen_id',
        'nama_guru',
        'nip',
        'nama_madrasah',
        'token',
        'email',
        'no_hp',
        'file_dokumen',
        'nama_file_asli',
        'status',
        'catatan',
        'tanggal_proses',
    ];

    protected $casts = [
        'tanggal_proses' => 'datetime',
        'deleted_at'     => 'datetime',
    ];

    // ─── Relasi ──────────────────────────────────────────────

    public function jenisDokumen(): BelongsTo
    {
        return $this->belongsTo(JenisDokumen::class);
    }

    public function guruUser(): BelongsTo
    {
        return $this->belongsTo(GuruUser::class, 'guru_user_id');
    }

    public function madrasahUser(): BelongsTo
    {
        return $this->belongsTo(MadrasahUser::class, 'madrasah_user_id');
    }

    // ─── Generate kode ───────────────────────────────────────

    public static function generateKode(): string
    {
        do {
            $acak = strtoupper(bin2hex(random_bytes(3)));
            $kode = 'DOK-' . $acak;
        } while (self::withTrashed()->where('kode_ajuan', $kode)->exists());

        return $kode;
    }

    // ─── Accessor ────────────────────────────────────────────

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'pending'  => 'bg-yellow-100 text-yellow-800',
            'diterima' => 'bg-green-100 text-green-800',
            'ditolak'  => 'bg-red-100 text-red-800',
            default    => 'bg-gray-100 text-gray-800',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending'  => 'Menunggu',
            'diterima' => 'Diterima',
            'ditolak'  => 'Ditolak',
            default    => 'Tidak Diketahui',
        };
    }

    // ─── Scope ───────────────────────────────────────────────

    public function scopePending($query)  { return $query->where('status', 'pending'); }
    public function scopeDiterima($query) { return $query->where('status', 'diterima'); }
    public function scopeDitolak($query)  { return $query->where('status', 'ditolak'); }

    // Pengajuan dari akun guru
    public function scopeDariGuru($query)
    {
        return $query->whereNotNull('guru_user_id');
    }

    // Pengajuan dari akun madrasah
    public function scopeDariMadrasah($query)
    {
        return $query->whereNotNull('madrasah_user_id');
    }

    // Pengajuan dari form publik (tanpa login)
    public function scopeDariPublik($query)
    {
        return $query->whereNull('guru_user_id')
                     ->whereNull('madrasah_user_id');
    }
}
