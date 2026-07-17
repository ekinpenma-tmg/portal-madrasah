<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AdminUserController extends Controller
{
    // ─────────────────────────────────────────
    // DAFTAR SEMUA AKUN ADMIN
    // ─────────────────────────────────────────
    public function index()
    {
        $admins = User::latest()->paginate(15);
        return view('admin.admin-users.index', compact('admins'));
    }

    // ─────────────────────────────────────────
    // FORM TAMBAH ADMIN
    // ─────────────────────────────────────────
    public function create()
    {
        return view('admin.admin-users.create');
    }

    // ─────────────────────────────────────────
    // SIMPAN ADMIN BARU
    // ─────────────────────────────────────────
    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|max:255|unique:users,email',
            'password' => ['required', 'confirmed', Password::min(8)],
            'role'     => 'required|in:super_admin,staff',
        ], [
            'name.required'      => 'Nama wajib diisi.',
            'email.required'     => 'Email wajib diisi.',
            'email.email'        => 'Format email tidak valid.',
            'email.unique'       => 'Email sudah digunakan akun lain.',
            'password.required'  => 'Password wajib diisi.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'password.min'       => 'Password minimal 8 karakter.',
            'role.required'      => 'Peran akun wajib dipilih.',
            'role.in'            => 'Peran akun tidak valid.',
        ]);

        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => $request->role,
        ]);

        return redirect()->route('admin.admin-users.index')
            ->with('success', 'Akun admin ' . $request->name . ' berhasil ditambahkan.');
    }

    // ─────────────────────────────────────────
    // FORM EDIT ADMIN
    // ─────────────────────────────────────────
    public function edit($id)
    {
        $admin = User::findOrFail($id);
        return view('admin.admin-users.edit', compact('admin'));
    }

    // ─────────────────────────────────────────
    // UPDATE ADMIN
    // ─────────────────────────────────────────
    public function update(Request $request, $id)
    {
        $admin = User::findOrFail($id);

        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|max:255|unique:users,email,' . $id,
            'password' => ['nullable', 'confirmed', Password::min(8)],
            'role'     => 'required|in:super_admin,staff',
        ], [
            'name.required'      => 'Nama wajib diisi.',
            'email.required'     => 'Email wajib diisi.',
            'email.email'        => 'Format email tidak valid.',
            'email.unique'       => 'Email sudah digunakan akun lain.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'password.min'       => 'Password minimal 8 karakter.',
            'role.required'      => 'Peran akun wajib dipilih.',
            'role.in'            => 'Peran akun tidak valid.',
        ]);

        // Tidak boleh ubah peran akun sendiri — supaya tidak ada yang
        // (sengaja atau tidak sengaja) menurunkan diri sendiri dari Super
        // Admin lalu terkunci dari fitur yang dia buka sendiri.
        if ($admin->id === Auth::id() && $request->role !== $admin->role) {
            return back()->with('error', 'Tidak bisa mengubah peran akun yang sedang Anda gunakan sendiri.');
        }

        // Tidak boleh menurunkan Super Admin terakhir jadi staff — nanti
        // tidak ada satu pun akun yang bisa kelola akun admin lagi.
        if ($admin->role === 'super_admin' && $request->role !== 'super_admin'
            && User::where('role', 'super_admin')->count() <= 1) {
            return back()->with('error', 'Minimal harus ada 1 akun Super Admin.');
        }

        $data = [
            'name'  => $request->name,
            'email' => $request->email,
            'role'  => $request->role,
        ];

        // Hanya update password jika diisi
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $admin->update($data);

        return redirect()->route('admin.admin-users.index')
            ->with('success', 'Akun admin ' . $request->name . ' berhasil diperbarui.');
    }

    // ─────────────────────────────────────────
    // HAPUS ADMIN
    // ─────────────────────────────────────────
    public function destroy($id)
    {
        $admin = User::findOrFail($id);

        // Tidak boleh hapus akun sendiri
        if ($admin->id === Auth::id()) {
            return back()->with('error', 'Tidak bisa menghapus akun yang sedang digunakan.');
        }

        // Tidak boleh hapus jika hanya tersisa 1 admin
        if (User::count() <= 1) {
            return back()->with('error', 'Minimal harus ada 1 akun admin aktif.');
        }

        // Tidak boleh hapus Super Admin terakhir — kalau tidak, tidak ada
        // satu pun akun yang bisa kelola akun admin lagi setelah ini.
        if ($admin->role === 'super_admin' && User::where('role', 'super_admin')->count() <= 1) {
            return back()->with('error', 'Minimal harus ada 1 akun Super Admin.');
        }

        $nama = $admin->name;
        $admin->delete();

        return redirect()->route('admin.admin-users.index')
            ->with('success', 'Akun admin ' . $nama . ' berhasil dihapus.');
    }
}
