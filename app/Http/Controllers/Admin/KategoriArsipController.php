<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KategoriArsip;
use Illuminate\Http\Request;

class KategoriArsipController extends Controller
{
    public function index()
    {
        $kategori = KategoriArsip::orderBy('urutan')->orderBy('nama')->get();
        return view('admin.kategori-arsip.index', compact('kategori'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama'      => 'required|string|max:100|unique:kategori_arsip,nama',
            'deskripsi' => 'nullable|string|max:255',
            'urutan'    => 'nullable|integer|min:0',
            'untuk'     => 'required|in:guru,madrasah,semua',
        ], [
            'nama.required'  => 'Nama kategori wajib diisi.',
            'nama.unique'    => 'Nama kategori ini sudah ada.',
            'untuk.required' => 'Pilih kategori ini untuk siapa.',
            'untuk.in'       => 'Nilai tidak valid.',
        ]);

        KategoriArsip::create([
            'nama'      => $validated['nama'],
            'deskripsi' => $validated['deskripsi'] ?? null,
            'urutan'    => $validated['urutan'] ?? 0,
            'untuk'     => $validated['untuk'],
            'aktif'     => true,
        ]);

        return back()->with('success', "Kategori \"{$validated['nama']}\" berhasil ditambahkan.");
    }

    public function update(Request $request, $id)
    {
        $kategori = KategoriArsip::findOrFail($id);

        $validated = $request->validate([
            'nama'      => "required|string|max:100|unique:kategori_arsip,nama,{$id}",
            'deskripsi' => 'nullable|string|max:255',
            'urutan'    => 'nullable|integer|min:0',
            'untuk'     => 'required|in:guru,madrasah,semua',
        ], [
            'nama.required'  => 'Nama kategori wajib diisi.',
            'untuk.required' => 'Pilih kategori ini untuk siapa.',
        ]);

        $kategori->update([
            'nama'      => $validated['nama'],
            'deskripsi' => $validated['deskripsi'] ?? null,
            'urutan'    => $validated['urutan'] ?? $kategori->urutan,
            'untuk'     => $validated['untuk'],
        ]);

        return back()->with('success', "Kategori \"{$kategori->nama}\" berhasil diperbarui.");
    }

    public function toggleAktif($id)
    {
        $kategori = KategoriArsip::findOrFail($id);
        $kategori->update(['aktif' => ! $kategori->aktif]);

        $status = $kategori->aktif ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "Kategori \"{$kategori->nama}\" berhasil {$status}.");
    }

    public function destroy($id)
    {
        $kategori    = KategoriArsip::findOrFail($id);
        $jumlahArsip = $kategori->arsip()->count() + $kategori->arsipMadrasah()->count();

        if ($jumlahArsip > 0) {
            return back()->with('error', "Kategori \"{$kategori->nama}\" tidak bisa dihapus karena masih digunakan oleh {$jumlahArsip} arsip.");
        }

        $nama = $kategori->nama;
        $kategori->delete();

        return back()->with('success', "Kategori \"{$nama}\" berhasil dihapus.");
    }
}
