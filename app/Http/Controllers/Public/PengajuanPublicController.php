<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\JenisDokumen;

class PengajuanPublicController extends Controller
{
    /**
     * Dulu route ini menampilkan form pengajuan anonim (tanpa login).
     * Sekarang seluruh pengajuan WAJIB melalui akun Guru/Madrasah agar
     * data pengaju otomatis terisi & pendataan lebih rapi.
     *
     * Route & method ini tetap dipertahankan (tidak dihapus) supaya
     * link/bookmark lama tidak 404 — tapi diarahkan ke halaman
     * "Pilih Portal" untuk login terlebih dahulu.
     */
    public function form($jenisId)
    {
        $jenis = JenisDokumen::aktif()->findOrFail($jenisId);

        return view('public.pilih-portal', [
            'jenis' => $jenis,
        ]);
    }

    public function sukses($jenisId, $kode)
    {
        // Alur sukses anonim sudah tidak berlaku, arahkan ke pengecekan status.
        return redirect()->route('status.index')
            ->with('info', 'Gunakan kode ajuan Anda untuk memantau status pengajuan di sini.');
    }

    /**
     * Route POST lama (form pengajuan anonim) masih ada yang mengarah ke sini
     * dari bookmark/link lama. Method ini sebelumnya tidak ada sama sekali,
     * jadi kalau ke-hit akan menyebabkan fatal error. Sekarang diarahkan
     * dengan aman ke halaman "Pilih Portal" seperti method form().
     */
    public function store($jenisId)
    {
        $jenis = JenisDokumen::aktif()->findOrFail($jenisId);

        return redirect()->route('pengajuan.form', $jenis->id)
            ->with('info', 'Pengajuan dokumen sekarang wajib melalui akun Guru/Madrasah. Silakan login terlebih dahulu.');
    }
}
