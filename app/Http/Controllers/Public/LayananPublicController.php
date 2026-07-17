<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Layanan;
use Illuminate\Support\Facades\Storage;

class LayananPublicController extends Controller
{
    public function index()
    {
        // Dikelompokkan per kategori: A. Pelayanan Perizinan, B. Pelayanan Rekomendasi, dst.
        $layanan = Layanan::aktif()->urut()->get()->groupBy('kategori');

        return view('public.layanan.index', compact('layanan'));
    }

    public function show(Layanan $layanan)
    {
        abort_unless($layanan->aktif, 404);

        $lainnya = Layanan::aktif()
            ->urut()
            ->where('kategori', $layanan->kategori)
            ->where('id', '!=', $layanan->id)
            ->take(4)
            ->get();

        return view('public.layanan.show', compact('layanan', 'lainnya'));
    }

    public function sop(Layanan $layanan)
    {
        abort_unless($layanan->aktif && $layanan->sop_file_path, 404);

        return Storage::disk('public')->download(
            $layanan->sop_file_path,
            $layanan->sop_nama_asli ?? ($layanan->nama.'.pdf')
        );
    }
}
