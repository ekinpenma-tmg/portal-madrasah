<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengajuan;
use Illuminate\Http\Request;

class TindakanCepatController extends Controller
{
    // ─────────────────────────────────────────
    // DAFTAR SEMUA PENGAJUAN (semua status)
    // ─────────────────────────────────────────
    public function index(Request $request)
    {
        $query = Pengajuan::with('jenisDokumen')
                    ->where('status', 'pending')
                    ->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('kode_ajuan',    'like', "%$search%")
                  ->orWhere('nama_guru',     'like', "%$search%")
                  ->orWhere('nip',           'like', "%$search%")
                  ->orWhere('nama_madrasah', 'like', "%$search%")
                  ->orWhere('token',         'like', "%$search%");
            });
        }

        if ($request->filled('jenis')) {
            $query->where('jenis_dokumen_id', $request->jenis);
        }

        $pengajuan    = $query->paginate(20)->withQueryString();
        $pendingCount = Pengajuan::where('status', 'pending')->count();
        $jenisDokumen = \App\Models\JenisDokumen::orderBy('nama')->get();

        return view('admin.tindakan-cepat.index', compact('pengajuan', 'pendingCount', 'jenisDokumen'));
    }

    // ─────────────────────────────────────────
    // TERIMA PENGAJUAN (dari tindakan cepat)
    // ─────────────────────────────────────────
    public function terima(Request $request, $id)
    {
        $pengajuan = Pengajuan::findOrFail($id);

        if ($pengajuan->status !== 'pending') {
            return back()->with('error', 'Pengajuan ini sudah diproses sebelumnya.');
        }

        $pengajuan->update([
            'status'         => 'diterima',
            'catatan'        => $request->catatan ?? null,
            'tanggal_proses' => now(),
        ]);

        return back()->with('success', "Pengajuan {$pengajuan->kode_ajuan} berhasil diterima.");
    }

    // ─────────────────────────────────────────
    // TOLAK PENGAJUAN (dari tindakan cepat)
    // ─────────────────────────────────────────
    public function tolak(Request $request, $id)
    {
        $pengajuan = Pengajuan::findOrFail($id);

        if ($pengajuan->status !== 'pending') {
            return back()->with('error', 'Pengajuan ini sudah diproses sebelumnya.');
        }

        $request->validate([
            'catatan' => 'required|string|max:500',
        ], [
            'catatan.required' => 'Alasan penolakan wajib diisi.',
        ]);

        $pengajuan->update([
            'status'         => 'ditolak',
            'catatan'        => $request->catatan,
            'tanggal_proses' => now(),
        ]);

        return back()->with('success', "Pengajuan {$pengajuan->kode_ajuan} telah ditolak.");
    }
}
