<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengajuan;
use Illuminate\Http\Request;

class RiwayatController extends Controller
{
    public function index(Request $request)
    {
        $query = Pengajuan::with('jenisDokumen')
            ->whereIn('status', ['diterima', 'ditolak'])
            ->latest('tanggal_proses');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('kode_ajuan',    'like', "%$search%")
                  ->orWhere('nama_guru',     'like', "%$search%")
                  ->orWhere('nama_madrasah', 'like', "%$search%")
                  ->orWhere('nip',           'like', "%$search%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $pengajuan = $query->paginate(15)->withQueryString();
        return view('admin.riwayat.index', compact('pengajuan'));
    }
}
