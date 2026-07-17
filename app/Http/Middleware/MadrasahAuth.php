<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MadrasahAuth
{
    public function handle(Request $request, Closure $next)
    {
        if (! Auth::guard('madrasah')->check()) {
            return redirect()->route('madrasah.login')
                ->with('info', 'Silakan login terlebih dahulu untuk mengakses Portal Madrasah.');
        }

        // Pastikan akun masih aktif
        if (! Auth::guard('madrasah')->user()->is_active) {
            Auth::guard('madrasah')->logout();
            $request->session()->invalidate();
            return redirect()->route('madrasah.login')
                ->with('error', 'Akun madrasah Anda telah dinonaktifkan. Hubungi admin Penma untuk informasi lebih lanjut.');
        }

        return $next($request);
    }
}
