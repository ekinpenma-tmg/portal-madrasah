<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Madrasah;
use App\Models\SiswaMadrasah;
use Illuminate\Http\Request;

class DataMadrasahController extends Controller
{
    public function index(Request $request)
    {
        $tahunPelajaran = SiswaMadrasah::tahunTerbaru() ?? date('Y') . '/' . (date('Y') + 1);

        // ── Statistik untuk infografis ──
        $rekapJenjang    = Madrasah::rekapJenjang();
        $rekapKecamatan  = Madrasah::rekapKecamatan();
        $rekapStatus     = Madrasah::rekapStatus();
        $rekapAkreditasi = Madrasah::rekapAkreditasi();
        $rekapSiswa      = SiswaMadrasah::rekapSiswaPerJenjang($tahunPelajaran);
        $totalMadrasah   = Madrasah::aktif()->count();
        $totalSiswa      = SiswaMadrasah::where('tahun_pelajaran', $tahunPelajaran)->sum('total_siswa');

        // ── Tabel daftar madrasah (publik: tanpa nama_kepala) ──
        $query = Madrasah::aktif()
            ->select(['id', 'nsm', 'npsn', 'nama_madrasah', 'jenjang', 'status', 'kecamatan', 'alamat', 'akreditasi'])
            ->with(['siswaLatest']);

        // Filter
        if ($request->jenjang) {
            $query->where('jenjang', $request->jenjang);
        }
        if ($request->kecamatan) {
            $query->where('kecamatan', $request->kecamatan);
        }
        if ($request->status) {
            $query->where('status', $request->status);
        }
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('nama_madrasah', 'like', '%' . $request->search . '%')
                  ->orWhere('nsm', 'like', '%' . $request->search . '%')
                  ->orWhere('kecamatan', 'like', '%' . $request->search . '%');
            });
        }

        $madrasah         = $query->orderBy('jenjang')->orderBy('nama_madrasah')->paginate(20)->withQueryString();
        $daftarKecamatan  = Madrasah::aktif()->distinct()->orderBy('kecamatan')->pluck('kecamatan');
        $tahunData        = Madrasah::aktif()->max('tahun_data');

        return view('public.data-madrasah', compact(
            'rekapJenjang', 'rekapKecamatan', 'rekapStatus', 'rekapAkreditasi',
            'rekapSiswa', 'totalMadrasah', 'totalSiswa',
            'madrasah', 'daftarKecamatan', 'tahunPelajaran', 'tahunData'
        ));
    }

    /**
     * AJAX endpoint — return hanya partial tabel
     */
    public function tabel(Request $request)
    {
        $query = Madrasah::aktif()
            ->select(['id', 'nsm', 'npsn', 'nama_madrasah', 'jenjang', 'status', 'kecamatan', 'alamat', 'akreditasi'])
            ->with(['siswaLatest']);

        if ($request->jenjang)   $query->where('jenjang', $request->jenjang);
        if ($request->kecamatan) $query->where('kecamatan', $request->kecamatan);
        if ($request->status)    $query->where('status', $request->status);
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('nama_madrasah', 'like', '%' . $request->search . '%')
                  ->orWhere('nsm', 'like', '%' . $request->search . '%')
                  ->orWhere('kecamatan', 'like', '%' . $request->search . '%');
            });
        }

        $madrasah   = $query->orderBy('jenjang')->orderBy('nama_madrasah')->paginate(20)->withQueryString();
        $rekapSiswa = SiswaMadrasah::rekapSiswaPerJenjang(SiswaMadrasah::tahunTerbaru() ?? '');

        return view('public.partials.tabel-madrasah', compact('madrasah', 'rekapSiswa'));
    }
}
