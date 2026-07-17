<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GuruUser;
use App\Models\MadrasahUser;
use App\Models\Pengajuan;

// ============================================================
// DashboardController.php
// Statistik ringkas: status akun (guru & madrasah) dan
// Pengajuan Dokumen — fitur aktif yang dipantau admin.
// ============================================================
class DashboardController extends Controller
{
    public function index()
    {
        // ── Akun: terverifikasi (aktif) vs belum aktivasi (belum ganti password) ──
        $guruAktifCount     = GuruUser::aktif()->count();
        $madrasahAktifCount = MadrasahUser::where('is_active', true)->count();
        $akunTerverifikasi  = $guruAktifCount + $madrasahAktifCount;

        $guruBelumAktivasi     = GuruUser::where('password_changed', false)->count();
        $madrasahBelumAktivasi = MadrasahUser::where('password_changed', false)->count();
        $akunBelumAktivasi     = $guruBelumAktivasi + $madrasahBelumAktivasi;

        // ── Ringkasan Pengajuan Dokumen ──
        $pengajuanPending  = Pengajuan::pending()->count();
        $pengajuanBulanIni = Pengajuan::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)->count();
        $pengajuanTerbaru  = Pengajuan::with('jenisDokumen')->latest()->take(5)->get();

        return view('admin.dashboard', compact(
            'akunTerverifikasi', 'akunBelumAktivasi', 'guruBelumAktivasi', 'madrasahBelumAktivasi',
            'pengajuanPending', 'pengajuanBulanIni', 'pengajuanTerbaru'
        ));
    }
}
