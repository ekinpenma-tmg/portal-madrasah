<?php

namespace App\Http\Controllers\Madrasah;

use App\Http\Controllers\Controller;
use App\Models\ArsipGuru;
use App\Models\GuruUser;
use App\Models\Pengajuan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MadrasahGuruMonitorController extends Controller
{
    private function madrasah()
    {
        return Auth::guard('madrasah')->user();
    }

    // ─────────────────────────────────────────
    // LIST GURU DI BAWAH MADRASAH INI (read-only)
    // ─────────────────────────────────────────
    public function index(Request $request)
    {
        $madrasah = $this->madrasah();

        $query = GuruUser::where('madrasah_id', $madrasah->madrasah_id)
            ->withCount([
                'arsip as total_arsip',
                'arsip as total_verified' => fn ($q) => $q->where('is_verified', true),
                'pengajuan as total_pending' => fn ($q) => $q->where('status', 'pending'),
            ]);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('nama', 'like', "%{$s}%")
                  ->orWhere('pegid', 'like', "%{$s}%");
            });
        }

        $guruList = $query->orderBy('nama')->paginate(15)->withQueryString();

        $stats = [
            'total_guru' => GuruUser::where('madrasah_id', $madrasah->madrasah_id)->count(),
            'total_arsip' => ArsipGuru::whereHas('guru', fn ($q) => $q->where('madrasah_id', $madrasah->madrasah_id))->count(),
            'total_pending' => Pengajuan::whereHas('guruUser', fn ($q) => $q->where('madrasah_id', $madrasah->madrasah_id))
                ->where('status', 'pending')->count(),
        ];

        return view('madrasah.guru.index', compact('guruList', 'stats'));
    }

    // ─────────────────────────────────────────
    // DETAIL GURU (arsip + pengajuan, read-only)
    // ─────────────────────────────────────────
    public function show($id)
    {
        $madrasah = $this->madrasah();

        // scoped ke madrasah_id sendiri — mencegah madrasah lain intip data guru madrasah lain
        $guru = GuruUser::where('madrasah_id', $madrasah->madrasah_id)->findOrFail($id);

        $arsipList = ArsipGuru::where('guru_id', $guru->id)
            ->with('kategori')
            ->latest()
            ->get();

        $pengajuanList = Pengajuan::where('guru_user_id', $guru->id)
            ->with('jenisDokumen')
            ->latest()
            ->get();

        return view('madrasah.guru.show', compact('guru', 'arsipList', 'pengajuanList'));
    }
}
