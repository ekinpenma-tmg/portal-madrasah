<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login — {{ config('app.name', 'Portal Madrasah') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html, body { height: 100%; font-family: 'Plus Jakarta Sans', sans-serif; }

        /* ─── BACKGROUND: dark charcoal (sama seperti sidebar Admin), no extra ornaments ─── */
        .login-bg {
            min-height: 100vh;
            background-color: #0c0c0c;
            background-image: radial-gradient(circle, rgba(255,255,255,0.065) 1.2px, transparent 1.2px);
            background-size: 28px 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px 16px;
            position: relative;
        }

        /* ─── CARD ─── */
        .login-card {
            position: relative; z-index: 10;
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.04), 0 12px 28px rgba(0,0,0,0.10);
            width: 100%;
            max-width: 340px;
            padding: 28px 24px 20px;
        }

        /* Header */
        .card-header {
            text-align: center;
            padding-bottom: 16px;
            margin-bottom: 16px;
            border-bottom: 1px solid #f0f2f0;
        }
        .logo-wrap {
            display: inline-flex; align-items: center; justify-content: center;
            width: 40px; height: 40px; border-radius: 10px;
            background: #0c0c0c;
            margin-bottom: 10px;
        }
        .card-title {
            font-size: 0.9rem; font-weight: 600;
            color: #18181b; margin-bottom: 2px;
        }
        .card-subtitle { font-size: 0.72rem; color: #a1a1aa; font-weight: 500; }
        .card-accent-bar {
            width: 32px; height: 3px; border-radius: 999px;
            background: #d97706; margin: 8px auto 0;
        }

        /* Back link */
        .back-link {
            position: absolute; top: 16px; left: 18px; z-index: 20;
            display: inline-flex; align-items: center; gap: 5px;
            font-size: 0.72rem; font-weight: 500;
            color: rgba(255,255,255,0.50); text-decoration: none;
            font-family: 'Plus Jakarta Sans', sans-serif;
            transition: color 0.15s;
        }
        .back-link:hover { color: rgba(255,255,255,0.85); }

        /* Card footer */
        .card-footer {
            text-align: center; margin-top: 14px; padding-top: 12px;
            border-top: 1px solid #f3f4f6;
            font-size: 0.68rem; color: #d4d4d8;
        }
    </style>
</head>
<body>
<div class="login-bg">

    {{-- Back --}}
    <a href="{{ route('home') }}" class="back-link">
        <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
        </svg>
        Kembali ke Beranda
    </a>

    {{-- Card --}}
    <div class="login-card">

        <div class="card-header">
            <div class="logo-wrap">
                @if(\App\Models\ProfilOrganisasi::getValue('logo_path'))
                    <img src="{{ Storage::url(\App\Models\ProfilOrganisasi::getValue('logo_path')) }}"
                         alt="Logo" class="w-full h-full object-contain p-1.5">
                @else
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                @endif
            </div>
            <h1 class="card-title">Portal Madrasah</h1>
            <p class="card-subtitle">Manajemen dokumen madrasah terpadu</p>
            <div class="card-accent-bar"></div>
        </div>

        {{ $slot }}

        <div class="card-footer">
            © {{ date('Y') }} Portal Madrasah
        </div>

    </div>
</div>
</body>
</html>