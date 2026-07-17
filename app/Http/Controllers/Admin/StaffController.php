<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StaffController extends Controller
{
    public function index()
    {
        $staff = Staff::orderBy('urutan')->paginate(15);
        return view('admin.staff.index', compact('staff'));
    }

    public function create()
    {
        return view('admin.staff.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama'    => 'required|string|max:255',
            'jabatan' => 'required|string|max:255',
            'urutan'  => 'required|integer|min:0',
            'foto'    => 'nullable|image|max:2048',
        ]);

        $data = $request->only('nama', 'jabatan', 'urutan');
        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('foto-staff', 'public');
        }

        Staff::create($data);
        return redirect()->route('admin.staff.index')->with('success', 'Staff berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $staff = Staff::findOrFail($id);
        return view('admin.staff.edit', compact('staff'));
    }

    public function update(Request $request, $id)
    {
        $staff = Staff::findOrFail($id);

        $request->validate([
            'nama'    => 'required|string|max:255',
            'jabatan' => 'required|string|max:255',
            'urutan'  => 'required|integer|min:0',
            'aktif'   => 'boolean',
            'foto'    => 'nullable|image|max:2048',
        ]);

        $data = [
            'nama'    => $request->nama,
            'jabatan' => $request->jabatan,
            'urutan'  => $request->urutan,
            'aktif'   => $request->boolean('aktif'),
        ];

        if ($request->hasFile('foto')) {
            if ($staff->foto) Storage::disk('public')->delete($staff->foto);
            $data['foto'] = $request->file('foto')->store('foto-staff', 'public');
        }

        $staff->update($data);
        return redirect()->route('admin.staff.index')->with('success', 'Staff berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $staff = Staff::findOrFail($id);
        if ($staff->foto) Storage::disk('public')->delete($staff->foto);
        $staff->delete();
        return redirect()->route('admin.staff.index')->with('success', 'Staff berhasil dihapus.');
    }
}
