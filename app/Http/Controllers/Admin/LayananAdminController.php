<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Layanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class LayananAdminController extends Controller
{
    public function index()
    {
        $layanan = Layanan::urut()->paginate(15);

        return view('admin.layanan.index', compact('layanan'));
    }

    public function create()
    {
        $iconOptions   = Layanan::iconOptions();
        $kategoriList  = $this->kategoriList();

        return view('admin.layanan.create', compact('iconOptions', 'kategoriList'));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['slug'] = $this->uniqueSlug($data['nama']);

        if ($request->hasFile('sop_file')) {
            $file = $request->file('sop_file');
            $data['sop_file_path'] = $file->store('layanan-sop', 'public');
            $data['sop_nama_asli'] = $file->getClientOriginalName();
        }

        Layanan::create($data);

        return redirect()->route('admin.layanan.index')->with('success', 'Layanan berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $layanan      = Layanan::findOrFail($id);
        $iconOptions  = Layanan::iconOptions();
        $kategoriList = $this->kategoriList();

        return view('admin.layanan.edit', compact('layanan', 'iconOptions', 'kategoriList'));
    }

    public function update(Request $request, $id)
    {
        $layanan = Layanan::findOrFail($id);
        $data    = $this->validated($request);

        if ($request->input('nama') !== $layanan->nama) {
            $data['slug'] = $this->uniqueSlug($data['nama'], $layanan->id);
        }

        if ($request->hasFile('sop_file')) {
            if ($layanan->sop_file_path) {
                Storage::disk('public')->delete($layanan->sop_file_path);
            }
            $file = $request->file('sop_file');
            $data['sop_file_path'] = $file->store('layanan-sop', 'public');
            $data['sop_nama_asli'] = $file->getClientOriginalName();
        }

        $data['aktif'] = $request->boolean('aktif');

        $layanan->update($data);

        return redirect()->route('admin.layanan.index')->with('success', 'Layanan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $layanan = Layanan::findOrFail($id);

        if ($layanan->sop_file_path) {
            Storage::disk('public')->delete($layanan->sop_file_path);
        }

        $layanan->delete();

        return redirect()->route('admin.layanan.index')->with('success', 'Layanan berhasil dihapus.');
    }

    public function toggleAktif($id)
    {
        $layanan = Layanan::findOrFail($id);
        $layanan->update(['aktif' => ! $layanan->aktif]);

        return back()->with('success', 'Status layanan diperbarui.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'kategori'     => 'required|string|max:150',
            'nama'         => 'required|string|max:255',
            'icon'         => 'nullable|string|max:100',
            'ringkasan'    => 'nullable|string|max:255',
            'deskripsi'    => 'nullable|string',
            'dasar_hukum'  => 'nullable|string',
            'syarat'       => 'nullable|string',
            'alur'         => 'nullable|string',
            'waktu_proses' => 'nullable|string|max:100',
            'biaya'        => 'nullable|string|max:100',
            'sop_file'     => 'nullable|file|mimes:pdf|max:10240',
            'urutan'       => 'nullable|integer|min:0',
        ]);
    }

    private function uniqueSlug(string $nama, ?int $ignoreId = null): string
    {
        $base = Str::slug($nama);
        $slug = $base;
        $i    = 1;

        while (
            Layanan::where('slug', $slug)
                ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $base.'-'.(++$i);
        }

        return $slug;
    }

    /** Daftar kategori bawaan (tetap bisa diketik bebas lewat input di form). */
    private function kategoriList(): array
    {
        return [
            'Pelayanan Perizinan',
            'Pelayanan Rekomendasi',
            'Pelayanan Ijazah dan Dokumen',
            'Pelayanan Data',
            'Pelayanan Konsultasi',
        ];
    }
}
