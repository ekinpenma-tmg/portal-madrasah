<?php

namespace App\Http\Controllers\Madrasah;

use App\Http\Controllers\Controller;
use App\Models\GuruUser;
use App\Services\ArsipKelengkapanService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MadrasahLaporanController extends Controller
{
    private function madrasah()
    {
        return Auth::guard('madrasah')->user();
    }

    // ─────────────────────────────────────────
    // KELENGKAPAN ARSIP — guru di madrasah ini, kategori mana yang belum diisi
    // ─────────────────────────────────────────
    public function arsip(Request $request, ArsipKelengkapanService $service)
    {
        $madrasah = $this->madrasah();

        $guruList = GuruUser::with('madrasah')
            ->where('madrasah_id', $madrasah->madrasah_id)
            ->where('is_active', true)
            ->orderBy('nama')
            ->get();

        $rekap = $service->hitungGuru($guruList);

        if ($request->boolean('hanya_belum_lengkap')) {
            $rekap = $rekap->filter(fn ($r) => $r->total_belum > 0)->values();
        }

        return view('madrasah.laporan.arsip', compact('rekap'));
    }
}
