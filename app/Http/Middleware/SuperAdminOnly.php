<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Membatasi aksi-aksi sensitif (kelola akun admin lain, reset data massal
 * guru/madrasah/data-madrasah, dll) hanya untuk akun dengan role
 * 'super_admin'. Akun 'staff' tetap bisa pakai fitur admin sehari-hari,
 * tapi diblok dari aksi yang berdampak ke SELURUH sistem.
 *
 * Middleware ini dipasang SETELAH 'auth', jadi Auth::user() dijamin ada.
 */
class SuperAdminOnly
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::user()->isSuperAdmin()) {
            return back()->with('error', 'Aksi ini hanya bisa dilakukan oleh akun Super Admin.');
        }

        return $next($request);
    }
}
