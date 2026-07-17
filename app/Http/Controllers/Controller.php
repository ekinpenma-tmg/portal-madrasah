<?php

namespace App\Http\Controllers;

abstract class Controller
{
    /**
     * Validasi bahwa sebuah URL benar-benar mengarah ke host Google Drive/Docs,
     * bukan cuma mengandung teks "drive.google.com" di suatu tempat pada string-nya
     * (mis. https://evil.com/?x=drive.google.com akan LOLOS kalau pakai str_contains).
     * Dipakai bareng oleh GuruPortalController & MadrasahPortalController.
     */
    protected function isValidGoogleDriveUrl(?string $url): bool
    {
        if (! $url) {
            return false;
        }

        $host = parse_url($url, PHP_URL_HOST);
        if (! $host) {
            return false;
        }

        return in_array(strtolower($host), ['drive.google.com', 'docs.google.com'], true);
    }

    /**
     * Cek apakah pemilik ($ownerColumn = $ownerId) sudah punya arsip lain di
     * kategori & tahun yang sama — supaya gak ada dokumen dobel-tanpa-sadar
     * (mis. "Ijazah 2024" ke-upload dua kali), sambil tetap membolehkan
     * riwayat multi-tahun untuk kategori yang memang berulang tiap tahun
     * (mis. "Laporan Tahunan 2024" dan "Laporan Tahunan 2025" adalah dua
     * entri yang sah, bukan duplikat).
     *
     * Kalau $tahun kosong (null), dokumen dianggap "sekali pakai" (tidak
     * terikat tahun tertentu) — cukup dicek kategori-nya saja.
     *
     * Dipakai bareng oleh GuruPortalController & MadrasahPortalController.
     */
    protected function sudahAdaArsipSerupa(
        string $model,
        string $ownerColumn,
        int $ownerId,
        int $kategoriId,
        ?int $tahun,
        ?int $kecualiId = null
    ): bool {
        $query = $model::where($ownerColumn, $ownerId)->where('kategori_id', $kategoriId);

        $query = $tahun
            ? $query->where('tahun', $tahun)
            : $query->whereNull('tahun');

        if ($kecualiId) {
            $query->where('id', '!=', $kecualiId);
        }

        return $query->exists();
    }
}
