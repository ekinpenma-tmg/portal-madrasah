<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\FileDownload;
use App\Models\JenisDokumen;
use Illuminate\Support\Facades\Storage;

class DownloadPublicController extends Controller
{
    public function index()
    {
        $files        = FileDownload::aktif()->latest()->get()->groupBy('kategori');
        $jenisDokumen = JenisDokumen::aktif()->get();
        return view('public.download', compact('files', 'jenisDokumen'));
    }

    public function unduh($id)
    {
        $file = FileDownload::aktif()->findOrFail($id);
        $file->incrementDownload();
        return Storage::disk('public')->download($file->file_path, $file->nama_file_asli ?? $file->nama);
    }
}
