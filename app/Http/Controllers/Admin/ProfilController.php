<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProfilOrganisasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfilController extends Controller
{
    public function index()
    {
        $profil = ProfilOrganisasi::all()->keyBy('key');
        return view('admin.profil.index', compact('profil'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'nama_organisasi' => 'sometimes|required|string|max:255',
            'alamat'          => 'sometimes|required|string',
            'telepon'         => 'sometimes|required|string|max:30',
            'email'           => 'sometimes|required|email',
            'visi'            => 'sometimes|required|string',
            'misi_1'          => 'sometimes|required|string',
            'misi_2'          => 'nullable|string',
            'misi_3'          => 'nullable|string',
            'misi_4'          => 'nullable|string',
            'misi_5'          => 'nullable|string',
            'misi_6'          => 'nullable|string',
            'logo'            => 'nullable|image|mimes:png,jpg,jpeg,svg|max:2048',
        ]);

        // Upload logo jika ada
        if ($request->hasFile('logo')) {
            $logoLama = ProfilOrganisasi::getValue('logo_path');
            if ($logoLama && Storage::disk('public')->exists($logoLama)) {
                Storage::disk('public')->delete($logoLama);
            }
            $path = $request->file('logo')->store('logo', 'public');
            ProfilOrganisasi::setValue('logo_path', $path);

            return redirect()->route('admin.profil.index')
                ->with('success', 'Logo berhasil diperbarui.');
        }

        // Simpan field teks
        foreach ($request->except('_token', '_method', 'logo') as $key => $value) {
            ProfilOrganisasi::setValue($key, $value ?? '');
        }

        return redirect()->route('admin.profil.index')
            ->with('success', 'Profil berhasil diperbarui.');
    }

    public function deleteLogo()
    {
        $logoPath = ProfilOrganisasi::getValue('logo_path');
        if ($logoPath && Storage::disk('public')->exists($logoPath)) {
            Storage::disk('public')->delete($logoPath);
        }
        ProfilOrganisasi::setValue('logo_path', '');

        return redirect()->route('admin.profil.index')
            ->with('success', 'Logo berhasil dihapus.');
    }
}
