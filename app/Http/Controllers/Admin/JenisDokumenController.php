<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JenisDokumen;
use Illuminate\Http\Request;

class JenisDokumenController extends Controller
{
    public static array $icons = [
        'document'    => ['label' => 'Dokumen',    'path' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
    ];

    public function index()
    {
        $jenis = JenisDokumen::withCount('pengajuan')->orderBy('nama')->get();
        $icons = self::$icons;
        return view('admin.jenis-dokumen.index', compact('jenis', 'icons'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama'      => 'required|string|max:100|unique:jenis_dokumen,nama',
            'deskripsi' => 'nullable|string|max:500',
            'syarat'    => 'nullable|string|max:255',
            'icon'      => 'nullable|string|max:50',
            'untuk'     => 'required|in:guru,madrasah,semua',
        ], [
            'nama.required'  => 'Nama jenis dokumen wajib diisi.',
            'nama.unique'    => 'Nama jenis dokumen sudah ada.',
            'untuk.required' => 'Pilih jenis dokumen ini untuk siapa.',
        ]);

        JenisDokumen::create([
            'nama'      => $request->nama,
            'deskripsi' => $request->deskripsi,
            'syarat'    => $request->syarat,
            'icon'      => $request->icon ?? 'document',
            'untuk'     => $request->untuk,
            'aktif'     => true,
        ]);

        return redirect()->route('admin.jenis-dokumen.index')
            ->with('success', 'Jenis dokumen berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $jenis = JenisDokumen::findOrFail($id);

        $request->validate([
            'nama'      => 'required|string|max:100|unique:jenis_dokumen,nama,' . $id,
            'deskripsi' => 'nullable|string|max:500',
            'syarat'    => 'nullable|string|max:255',
            'icon'      => 'nullable|string|max:50',
            'untuk'     => 'required|in:guru,madrasah,semua',
        ], [
            'nama.required'  => 'Nama jenis dokumen wajib diisi.',
            'nama.unique'    => 'Nama jenis dokumen sudah ada.',
            'untuk.required' => 'Pilih jenis dokumen ini untuk siapa.',
        ]);

        $jenis->update([
            'nama'      => $request->nama,
            'deskripsi' => $request->deskripsi,
            'syarat'    => $request->syarat,
            'icon'      => $request->icon ?? 'document',
            'untuk'     => $request->untuk,
        ]);

        return redirect()->route('admin.jenis-dokumen.index')
            ->with('success', 'Jenis dokumen berhasil diperbarui.');
    }

    public function toggleAktif($id)
    {
        $jenis  = JenisDokumen::findOrFail($id);
        $jenis->update(['aktif' => ! $jenis->aktif]);
        $status = $jenis->aktif ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->route('admin.jenis-dokumen.index')
            ->with('success', "Jenis dokumen berhasil {$status}.");
    }

    public function destroy($id)
    {
        $jenis = JenisDokumen::withCount('pengajuan')->findOrFail($id);
        if ($jenis->pengajuan_count > 0) {
            return redirect()->route('admin.jenis-dokumen.index')
                ->with('error', "Tidak bisa dihapus — ada {$jenis->pengajuan_count} pengajuan yang menggunakan jenis ini.");
        }
        $jenis->delete();
        return redirect()->route('admin.jenis-dokumen.index')
            ->with('success', 'Jenis dokumen berhasil dihapus.');
    }
}