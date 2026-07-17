<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ArsipGuru;
use App\Models\ArsipMadrasah;
use App\Models\GuruUser;
use App\Models\KategoriArsip;
use App\Models\Madrasah;
use App\Models\MadrasahUser;
use App\Services\ArsipKelengkapanService;
use Illuminate\Http\Request;

class LaporanArsipAdminController extends Controller
{
    // ─────────────────────────────────────────
    // TAB GURU — kelengkapan arsip per guru, lintas madrasah
    // ─────────────────────────────────────────
    public function guru(Request $request, ArsipKelengkapanService $service)
    {
        $query = GuruUser::with('madrasah')->where('is_active', true)->orderBy('nama');

        if ($request->filled('madrasah_id')) {
            $query->where('madrasah_id', $request->madrasah_id);
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn ($q) => $q->where('nama', 'like', "%{$s}%")
                                        ->orWhere('pegid', 'like', "%{$s}%"));
        }

        if ($request->boolean('hanya_belum_lengkap')) {
            $totalKategoriWajib = KategoriArsip::untukGuru()->count();
            $kategoriWajibIds   = KategoriArsip::untukGuru()->pluck('id');

            $guruIdLengkap = ArsipGuru::whereIn('kategori_id', $kategoriWajibIds)
                ->select('guru_id')
                ->groupBy('guru_id')
                ->havingRaw('COUNT(DISTINCT kategori_id) >= ?', [$totalKategoriWajib])
                ->pluck('guru_id');

            $query->whereNotIn('id', $guruIdLengkap);
        }

        $guruPage = $query->paginate(30)->withQueryString();

        $rekap = $service->hitungGuru(collect($guruPage->items()));

        $madrasahs = Madrasah::aktif()->orderBy('nama_madrasah')->get(['id', 'nama_madrasah', 'jenjang', 'nsm']);

        return view('admin.laporan-arsip.guru', compact('rekap', 'guruPage', 'madrasahs'));
    }

    // ─────────────────────────────────────────
    // TAB MADRASAH — kelengkapan arsip institusi per madrasah
    // ─────────────────────────────────────────
    public function madrasah(Request $request, ArsipKelengkapanService $service)
    {
        $query = MadrasahUser::with('madrasah')->where('is_active', true)->orderBy('nsm');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn ($q) => $q->where('nsm', 'like', "%{$s}%")
                                        ->orWhereHas('madrasah', fn ($m) => $m->where('nama_madrasah', 'like', "%{$s}%")));
        }

        if ($request->boolean('hanya_belum_lengkap')) {
            $totalKategoriWajib = KategoriArsip::untukMadrasah()->count();
            $kategoriWajibIds   = KategoriArsip::untukMadrasah()->pluck('id');

            $madrasahIdLengkap = ArsipMadrasah::whereIn('kategori_id', $kategoriWajibIds)
                ->select('madrasah_user_id')
                ->groupBy('madrasah_user_id')
                ->havingRaw('COUNT(DISTINCT kategori_id) >= ?', [$totalKategoriWajib])
                ->pluck('madrasah_user_id');

            $query->whereNotIn('id', $madrasahIdLengkap);
        }

        $madrasahPage = $query->paginate(30)->withQueryString();

        $rekap = $service->hitungMadrasah(collect($madrasahPage->items()));

        return view('admin.laporan-arsip.madrasah', compact('rekap', 'madrasahPage'));
    }
}
