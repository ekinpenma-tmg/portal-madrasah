<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Memaksa akun Guru/Madrasah yang masih pakai password DEFAULT (PegID/NSM)
 * untuk ganti password dulu sebelum bisa akses fitur lain di portalnya.
 *
 * Kenapa ini dibutuhkan: password default guru = PegID miliknya sendiri,
 * dan password default madrasah = NSM miliknya sendiri — dua-duanya adalah
 * kode yang gampang beredar (ada di SK, dapodik, dokumen resmi, dsb).
 * Sebelum ada middleware ini, sistem cuma menampilkan banner peringatan di
 * dashboard tapi tidak pernah benar-benar memaksa ganti password, jadi akun
 * yang belum pernah dipakai bisa selamanya "menganggur" dengan password
 * default yang gampang ditebak siapa pun yang tahu PegID/NSM-nya.
 *
 * Dipakai dengan parameter nama guard, misal:
 *   ->middleware(['auth.guru', 'force.password:guru'])
 *   ->middleware(['auth.madrasah', 'force.password:madrasah'])
 *
 * Guard yang dipakai HARUS punya route bernama "{guard}.password.form",
 * "{guard}.password.update", dan "{guard}.logout".
 */
class ForcePasswordChange
{
    public function handle(Request $request, Closure $next, string $guard): Response
    {
        $user = Auth::guard($guard)->user();

        // Kalau belum login sama sekali, biarkan lolos — itu urusan
        // middleware auth.guru/auth.madrasah, bukan middleware ini.
        if (! $user) {
            return $next($request);
        }

        // Sudah pernah ganti password → jalan seperti biasa.
        if ($user->password_changed) {
            return $next($request);
        }

        // Jangan sampai halaman ganti-password & logout sendiri ikut
        // ke-redirect, nanti jadi infinite redirect loop.
        $rutePengecualian = ["{$guard}.password.form", "{$guard}.password.update", "{$guard}.logout"];
        if ($request->routeIs($rutePengecualian)) {
            return $next($request);
        }

        return redirect()
            ->route("{$guard}.password.form")
            ->with('warning', 'Akun Anda masih menggunakan password default. Silakan ganti password terlebih dahulu sebelum melanjutkan.');
    }
}
