<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GuruAuth
{
    public function handle(Request $request, Closure $next)
    {
        if (! Auth::guard('guru')->check()) {
            return redirect()->route('guru.login')
                ->with('info', 'Silakan login terlebih dahulu untuk mengakses Portal Guru.');
        }

        // Pastikan akun masih aktif (bisa dinonaktifkan admin kapan saja)
        if (! Auth::guard('guru')->user()->is_active) {
            Auth::guard('guru')->logout();
            $request->session()->invalidate();
            return redirect()->route('guru.login')
                ->with('error', 'Akun Anda telah dinonaktifkan. Hubungi admin untuk informasi lebih lanjut.');
        }

        return $next($request);
    }
}
