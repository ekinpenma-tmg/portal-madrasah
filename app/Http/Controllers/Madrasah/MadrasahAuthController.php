<?php

namespace App\Http\Controllers\Madrasah;

use App\Http\Controllers\Controller;
use App\Models\MadrasahUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class MadrasahAuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::guard('madrasah')->check()) {
            return redirect()->route('madrasah.dashboard');
        }
        return view('madrasah.auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'nsm'      => 'required|string',
            'password' => 'required|string',
        ], [
            'nsm.required'      => 'NSM wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $madrasah = MadrasahUser::where('nsm', $request->nsm)->first();

        if (! $madrasah || ! Hash::check($request->password, $madrasah->password)) {
            return back()
                ->withErrors(['nsm' => 'NSM atau password salah.'])
                ->withInput();
        }

        if (! $madrasah->is_active) {
            return back()
                ->withErrors(['nsm' => 'Akun madrasah tidak aktif. Hubungi admin Penma.'])
                ->withInput();
        }

        Auth::guard('madrasah')->login($madrasah, $request->boolean('remember'));
        $request->session()->regenerate();
        $madrasah->update(['last_login' => now()]);

        return redirect()->route('madrasah.dashboard');
    }

    public function logout(Request $request)
    {
        Auth::guard('madrasah')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('home');
    }
}
