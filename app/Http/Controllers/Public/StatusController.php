<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Pengajuan;
use App\Models\JenisDokumen;
use Illuminate\Http\Request;

class StatusController extends Controller
{
    public function index()
    {
        $jenisDokumen = JenisDokumen::aktif()->get();
        return view('public.status', compact('jenisDokumen'));
    }

    public function cari(Request $request)
    {
        $request->validate([
            'kode_ajuan' => 'required|string',
        ], [
            'kode_ajuan.required' => 'Kode ajuan wajib diisi.',
        ]);

        $kode      = strtoupper(trim($request->kode_ajuan));
        $pengajuan = Pengajuan::with('jenisDokumen')
            ->where('kode_ajuan', $kode)
            ->first();

        $jenisDokumen = JenisDokumen::aktif()->get();

        // Hitung posisi antrian jika status pending
        $posisiAntrian = null;
        if ($pengajuan && $pengajuan->status === 'pending') {
            // Hitung berapa pengajuan pending yang masuk SEBELUM pengajuan ini
            $posisiAntrian = Pengajuan::where('status', 'pending')
                ->where('id', '<=', $pengajuan->id)
                ->count();
        }

        return view('public.status', compact('pengajuan', 'kode', 'jenisDokumen', 'posisiAntrian'));
    }
}
