<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GuruAuthController extends Controller
{
    public function showLogin()
    {
        // Jika sudah login, langsung ke dashboard
        if (Auth::guard('guru')->check()) {
            return redirect()->route('guru.dashboard');
        }
        return view('guru.auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'pegid'    => 'required|string',
            'password' => 'required|string',
        ], [
            'pegid.required'    => 'Nomor PegID wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $credentials = [
            'pegid'    => $request->pegid,
            'password' => $request->password,
        ];

        // Cek dulu apakah akun ada dan aktif
        $guru = \App\Models\GuruUser::where('pegid', $request->pegid)->first();

        if ($guru && ! $guru->is_active) {
            return back()
                ->withInput($request->only('pegid'))
                ->withErrors(['pegid' => 'Akun Anda tidak aktif. Hubungi admin untuk informasi lebih lanjut.']);
        }

        if (Auth::guard('guru')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('guru.dashboard'));
        }

        return back()
            ->withInput($request->only('pegid'))
            ->withErrors(['pegid' => 'PegID atau password salah. Pastikan password default adalah PegID Anda.']);
    }

    public function logout(Request $request)
    {
        Auth::guard('guru')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('home');
    }
}
