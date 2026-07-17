<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FileDownload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DownloadAdminController extends Controller
{
    public function index()
    {
        $files = FileDownload::latest()->paginate(15);
        return view('admin.download.index', compact('files'));
    }

    public function create()
    {
        return view('admin.download.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama'      => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'kategori'  => 'nullable|string|max:100',
            'file'      => 'required|file|max:10240',
        ]);

        $file         = $request->file('file');
        $namaFileAsli = $file->getClientOriginalName();
        $filePath     = $file->store('file-download', 'public');

        FileDownload::create([
            'nama'           => $request->nama,
            'deskripsi'      => $request->deskripsi,
            'kategori'       => $request->kategori,
            'file_path'      => $filePath,
            'nama_file_asli' => $namaFileAsli,
        ]);

        return redirect()->route('admin.download.index')->with('success', 'File berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $file = FileDownload::findOrFail($id);
        return view('admin.download.edit', compact('file'));
    }

    public function update(Request $request, $id)
    {
        $file = FileDownload::findOrFail($id);

        $request->validate([
            'nama'      => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'kategori'  => 'nullable|string|max:100',
            'file'      => 'nullable|file|max:10240',
            'aktif'     => 'boolean',
        ]);

        $data = [
            'nama'      => $request->nama,
            'deskripsi' => $request->deskripsi,
            'kategori'  => $request->kategori,
            'aktif'     => $request->boolean('aktif'),
        ];

        if ($request->hasFile('file')) {
            Storage::disk('public')->delete($file->file_path);
            $newFile              = $request->file('file');
            $data['file_path']    = $newFile->store('file-download', 'public');
            $data['nama_file_asli'] = $newFile->getClientOriginalName();
        }

        $file->update($data);

        return redirect()->route('admin.download.index')->with('success', 'File berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $file = FileDownload::findOrFail($id);
        Storage::disk('public')->delete($file->file_path);
        $file->delete();

        return redirect()->route('admin.download.index')->with('success', 'File berhasil dihapus.');
    }
}
