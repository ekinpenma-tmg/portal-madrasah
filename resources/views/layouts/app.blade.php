<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description"
        content="Portal layanan administrasi dan informasi madrasah binaan Seksi Pendidikan Madrasah, Kantor Kementerian Agama Kabupaten Temanggung.">
    <meta name="keywords" content="madrasah temanggung, kemenag temanggung, pengajuan dokumen madrasah, penma temanggung">
    <title>@yield('title', 'Sistem Dokumen Madrasah') - {{ \App\Models\ProfilOrganisasi::getValue('nama_organisasi', 'Kemenag') }}</title>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- ══════════════════════════════════════════════════════════
         KONSEP B — PORTAL DIGITAL MODERN (global, seluruh halaman publik)
         Berlaku untuk semua view yang extends layouts.app:
         home, profil, data-madrasah, download, status, pilih-portal,
         sukses, form-pengajuan. Tidak menyentuh layout admin/guru/madrasah.
    ══════════════════════════════════════════════════════════ --}}
    <style>
        :root {
            --kb-ink: #0b1a12;
            --kb-emerald: #065f46;
            --kb-emerald-2: #0a7a5a;
            --kb-lime: #a3e635;
            --kb-lime-2: #84cc16;
        }

        #navbar,
        footer {
            font-family: 'Sora', sans-serif;
        }

        /* Navbar */
        #navbar {
            background: var(--kb-ink) !important;
            border-bottom: 1px solid rgba(255, 255, 255, 0.06);
        }

        #navbar.navbar-scrolled {
            background: rgba(11, 20, 15, 0.94) !important;
            backdrop-filter: blur(10px);
        }

        #navbar .nav-active {
            background: rgba(163, 230, 53, 0.14) !important;
            color: var(--kb-lime) !important;
        }

        #navbar .btn-masuk-pill {
            background: var(--kb-lime) !important;
            color: var(--kb-ink) !important;
        }

        #navbar .btn-masuk-pill:hover {
            background: var(--kb-lime-2) !important;
        }

        /* Footer */
        footer {
            background: var(--kb-ink) !important;
        }

        footer [style*="d4af37"] {
            color: var(--kb-lime) !important;
        }

        footer [style*="212,175,55"] {
            background: linear-gradient(90deg, transparent, rgba(163, 230, 53, 0.35) 30%, rgba(163, 230, 53, 0.55) 50%, rgba(163, 230, 53, 0.35) 70%, transparent) !important;
        }

        /* Hero band dipakai di Profil / Download / Status (.hex-pattern) */
        .hex-pattern {
            background: var(--kb-ink);
        }

        .hex-pattern>.absolute {
            background: linear-gradient(135deg, rgba(6, 20, 14, 0.9), rgba(6, 95, 70, 0.65)) !important;
        }

        .hex-pattern h1,
        .hex-pattern h2,
        .hex-pattern h3 {
            font-family: 'Sora', sans-serif;
        }

        /* Gradient hero hardcoded (dipakai di form-pengajuan & sukses) */
        [style*="052e16"] {
            background: linear-gradient(135deg, var(--kb-ink), var(--kb-emerald)) !important;
        }
    </style>

    @stack('styles')
</head>

<body class="bg-white text-gray-800 font-sans antialiased">

    {{-- ══════════════════════════════════════
     NAVBAR
══════════════════════════════════════ --}}
    <nav id="navbar" class="bg-primary-900 sticky top-0 z-50 transition-all duration-300" x-data="{ open: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">

                {{-- Logo --}}
                <a href="{{ route('home') }}" class="flex items-center gap-3 group">
                    <div
                        class="w-9 h-9 bg-white border border-white/30 rounded-xl flex items-center justify-center group-hover:bg-gray-50 transition overflow-hidden flex-shrink-0">
                        @if (\App\Models\ProfilOrganisasi::getValue('logo_path'))
                            <img src="{{ Storage::url(\App\Models\ProfilOrganisasi::getValue('logo_path')) }}"
                                alt="Logo" class="w-full h-full object-contain p-1">
                        @else
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        @endif
                    </div>
                    <div class="hidden sm:block">
                        <p class="text-white font-bold text-sm leading-tight">
                            {{ \App\Models\ProfilOrganisasi::getValue('nama_organisasi', 'Kantor Kemenag') }}</p>
                        <p class="text-primary-300 text-xs">Kantor Kementerian Agama Temanggung</p>
                    </div>
                </a>

                {{-- Desktop Menu --}}
                <div class="hidden md:flex items-center gap-1">
                    <a href="{{ route('home') }}"
                        class="px-4 py-2 rounded-lg text-sm font-semibold text-white/90 hover:text-white hover:bg-white/10 transition {{ request()->routeIs('home') ? 'nav-active text-white' : '' }}">
                        Beranda
                    </a>
                    <a href="{{ route('profil') }}"
                        class="px-4 py-2 rounded-lg text-sm font-semibold text-white/90 hover:text-white hover:bg-white/10 transition {{ request()->routeIs('profil') ? 'nav-active text-white' : '' }}">
                        Profil
                    </a>
                    <a href="{{ route('data-madrasah.index') }}"
                        class="px-4 py-2 rounded-lg text-sm font-semibold text-white/90 hover:text-white hover:bg-white/10 transition {{ request()->routeIs('data-madrasah.*') ? 'nav-active text-white' : '' }}">
                        Data Madrasah
                    </a>
                    <a href="{{ route('layanan.index') }}"
                        class="px-4 py-2 rounded-lg text-sm font-semibold text-white/90 hover:text-white hover:bg-white/10 transition {{ request()->routeIs('layanan.*') ? 'nav-active text-white' : '' }}">
                        Pelayanan
                    </a>
                    <a href="{{ route('download.index') }}"
                        class="px-4 py-2 rounded-lg text-sm font-semibold text-white/90 hover:text-white hover:bg-white/10 transition {{ request()->routeIs('download.*') ? 'nav-active text-white' : '' }}">
                        Download
                    </a>
                    <a href="{{ route('status.index') }}"
                        class="px-4 py-2 rounded-lg text-sm font-semibold text-white/90 hover:text-white hover:bg-white/10 transition {{ request()->routeIs('status.*') ? 'nav-active text-white' : '' }}">
                        Status Ajuan
                    </a>
                </div>

                {{-- Right side --}}
                <div class="flex items-center gap-3">

                    {{-- Dropdown Login --}}
                    <div x-data="{ loginOpen: false }" class="relative hidden md:block">
                        <button @click="loginOpen = !loginOpen" @click.outside="loginOpen = false"
                            class="btn-masuk-pill flex items-center gap-2 text-sm font-semibold px-4 py-2 rounded-lg bg-white text-green-800 hover:bg-zinc-50 transition-all duration-200 active:scale-95">
                            Masuk
                            <svg class="w-3.5 h-3.5 transition-transform duration-200"
                                :class="loginOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div x-show="loginOpen" x-cloak x-transition:enter="transition ease-out duration-150"
                            x-transition:enter-start="opacity-0 translate-y-1"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-100"
                            x-transition:leave-start="opacity-100 translate-y-0"
                            x-transition:leave-end="opacity-0 translate-y-1"
                            class="absolute right-0 mt-2 w-48 rounded-xl overflow-hidden z-50 py-1.5"
                            style="background:#fff; box-shadow:0 8px 24px rgba(0,0,0,0.12); border:1px solid #f0f0f0;">

                            {{-- Admin --}}
                            @auth
                                <a href="{{ route('admin.dashboard') }}"
                                    class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium text-zinc-700 hover:bg-zinc-50 hover:text-zinc-900 transition group">
                                @else
                                    <a href="{{ route('login') }}"
                                        class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium text-zinc-700 hover:bg-zinc-50 hover:text-zinc-900 transition group">
                                    @endauth
                                    <svg class="w-4 h-4 text-zinc-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16" />
                                    </svg>
                                    <span class="flex-1">Admin</span>
                                    <svg class="w-3 h-3 text-zinc-300 group-hover:text-zinc-500 transition flex-shrink-0"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7" />
                                    </svg>
                                </a>

                                <div style="height:1px; background:#f4f4f5; margin:2px 12px"></div>

                                {{-- Guru --}}
                                <a href="{{ route('guru.login') }}"
                                    class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium text-zinc-700 hover:bg-zinc-50 hover:text-zinc-900 transition group">
                                    <svg class="w-4 h-4 text-zinc-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                                    </svg>
                                    <span class="flex-1">Guru</span>
                                    <svg class="w-3 h-3 text-zinc-300 group-hover:text-zinc-500 transition flex-shrink-0"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7" />
                                    </svg>
                                </a>

                                <div style="height:1px; background:#f4f4f5; margin:2px 12px"></div>

                                {{-- Madrasah --}}
                                @if (Route::has('madrasah.login'))
                                    <a href="{{ route('madrasah.login') }}"
                                        class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium text-zinc-700 hover:bg-zinc-50 hover:text-zinc-900 transition group">
                                        <svg class="w-4 h-4 text-zinc-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                        </svg>
                                        <span class="flex-1">Madrasah</span>
                                        <svg class="w-3 h-3 text-zinc-300 group-hover:text-zinc-500 transition flex-shrink-0"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5l7 7-7 7" />
                                        </svg>
                                    </a>
                                @endif
                        </div>
                    </div>
                </div>

                {{-- Mobile Hamburger --}}
                <button @click="open = !open"
                    class="md:hidden text-white/80 hover:text-white p-1.5 rounded-lg hover:bg-white/10 transition">
                    <svg x-show="!open" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    <svg x-show="open" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
        </div>

        {{-- Mobile Menu --}}
        <div x-show="open" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
            class="md:hidden bg-primary-900/95 backdrop-blur border-t border-white/10 px-4 pb-4 pt-2">
            <a href="{{ route('home') }}"
                class="flex items-center gap-2 py-2.5 text-white/90 text-sm font-medium hover:text-white">
                Beranda
            </a>
            <a href="{{ route('profil') }}"
                class="flex items-center gap-2 py-2.5 text-white/90 text-sm font-medium hover:text-white">
                Profil
            </a>
            <a href="{{ route('data-madrasah.index') }}"
                class="flex items-center gap-2 py-2.5 text-white/90 text-sm font-medium hover:text-white {{ request()->routeIs('data-madrasah.*') ? 'text-white font-bold' : '' }}">
                Data Madrasah
            </a>
            <a href="{{ route('layanan.index') }}"
                class="flex items-center gap-2 py-2.5 text-white/90 text-sm font-medium hover:text-white {{ request()->routeIs('layanan.*') ? 'text-white font-bold' : '' }}">
                Pelayanan
            </a>
            <a href="{{ route('download.index') }}"
                class="flex items-center gap-2 py-2.5 text-white/90 text-sm font-medium hover:text-white">
                Download
            </a>
            <a href="{{ route('status.index') }}"
                class="flex items-center gap-2 py-2.5 text-white/90 text-sm font-medium hover:text-white">
                Status Ajuan
            </a>

            {{-- Divider --}}
            <div class="border-t border-white/10 my-2"></div>

            {{-- Login mobile --}}
            @auth
                <a href="{{ route('admin.dashboard') }}"
                    class="flex items-center gap-3 py-2.5 text-amber-400 text-sm font-semibold hover:text-amber-300">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16" />
                    </svg>
                    Dashboard Admin
                </a>
            @else
                <a href="{{ route('login') }}"
                    class="flex items-center gap-3 py-2.5 text-amber-400 text-sm font-semibold hover:text-amber-300">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16" />
                    </svg>
                    Login Admin
                </a>
            @endauth
            <a href="{{ route('guru.login') }}"
                class="flex items-center gap-3 py-2.5 text-green-400 text-sm font-semibold hover:text-green-300">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                </svg>
                Login Guru
            </a>
            @if (Route::has('madrasah.login'))
                <a href="{{ route('madrasah.login') }}"
                    class="flex items-center gap-3 py-2.5 text-sm font-semibold" style="color:#38bdf8;">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    Login Madrasah
                </a>
            @endif
        </div>
    </nav>

    {{-- Flash Messages --}}
    @if (session('success'))
        <div x-data="{ show: true }" x-show="show" x-transition
            class="bg-green-50 border-l-4 border-green-500 text-green-800 px-6 py-3 flex items-center justify-between max-w-7xl mx-auto mt-4 rounded-lg shadow-sm">
            <span class="text-sm font-medium">✅ {{ session('success') }}</span>
            <button @click="show = false" class="text-green-600 hover:text-green-800 text-lg leading-none">✕</button>
        </div>
    @endif
    @if (session('error'))
        <div x-data="{ show: true }" x-show="show" x-transition
            class="bg-red-50 border-l-4 border-red-500 text-red-800 px-6 py-3 flex items-center justify-between max-w-7xl mx-auto mt-4 rounded-lg shadow-sm">
            <span class="text-sm font-medium">❌ {{ session('error') }}</span>
            <button @click="show = false" class="text-red-600 hover:text-red-800 text-lg leading-none">✕</button>
        </div>
    @endif

    {{-- Main Content --}}
    <main>
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="relative mt-16 overflow-hidden"
        style="background: linear-gradient(160deg, #052e16 0%, #0a3d1f 40%, #0d4a25 100%);">

        {{-- Dekorasi background --}}
        <div class="absolute inset-0 pointer-events-none"
            style="background-image: radial-gradient(rgba(255,255,255,0.03) 1px, transparent 1px); background-size: 28px 28px;">
        </div>
        <div class="absolute top-0 right-0 w-96 h-96 rounded-full pointer-events-none"
            style="background: radial-gradient(circle, rgba(34,197,94,0.06) 0%, transparent 70%); transform: translate(30%, -30%);">
        </div>
        <div class="absolute bottom-0 left-0 w-64 h-64 rounded-full pointer-events-none"
            style="background: radial-gradient(circle, rgba(251,191,36,0.05) 0%, transparent 70%); transform: translate(-30%, 30%);">
        </div>

        {{-- Garis emas tipis di atas footer --}}
        <div class="w-full h-px"
            style="background: linear-gradient(90deg, transparent, rgba(212,175,55,0.4) 30%, rgba(212,175,55,0.6) 50%, rgba(212,175,55,0.4) 70%, transparent);">
        </div>

        <div class="relative max-w-7xl mx-auto px-4 pt-8 pb-6">

            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-10">

                {{-- Kolom 1: Brand --}}
                <div>
                    <div class="flex items-center gap-3 mb-4">
                        <div
                            class="w-12 h-12 bg-white rounded-xl flex items-center justify-center overflow-hidden shadow-lg flex-shrink-0">
                            @if (\App\Models\ProfilOrganisasi::getValue('logo_path'))
                                <img src="{{ Storage::url(\App\Models\ProfilOrganisasi::getValue('logo_path')) }}"
                                    alt="Logo" class="w-full h-full object-contain p-1">
                            @else
                                <svg class="w-6 h-6 text-green-700" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                            @endif
                        </div>
                        <div>
                            <p class="text-yellow-500 font-extrabold text-sm leading-tight">
                                {{ \App\Models\ProfilOrganisasi::getValue('nama_organisasi', 'Seksi Pendidikan Madrasah') }}
                            </p>
                            <p class="text-primary-400 text-xs mt-0.5">Kemenag Kab. Temanggung</p>
                        </div>
                    </div>
                    <p class="text-primary-400 text-sm leading-relaxed">
                        Portal layanan administrasi dan informasi madrasah di bawah pembinaan Seksi Pendidikan Madrasah,
                        Kemenag Kabupaten Temanggung.
                    </p>
                </div>

                {{-- Kolom 2: Kontak Kami --}}
                <div>
                    <h3 class="font-bold text-sm mb-5" style="color: #d4af37;">Kontak Kami</h3>
                    <div class="space-y-3.5">
                        <div class="flex items-start gap-3">
                            <svg class="w-4 h-4 mt-0.5 flex-shrink-0" style="color:#d4af37" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0zM15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span
                                class="text-primary-300 text-sm leading-snug">{{ \App\Models\ProfilOrganisasi::getValue('alamat', 'Jl. Jendral Sudirman No. 121 Temanggung') }}</span>
                        </div>
                        <a href="tel:{{ \App\Models\ProfilOrganisasi::getValue('telepon') }}"
                            class="flex items-center gap-3 group">
                            <svg class="w-4 h-4 flex-shrink-0 group-hover:text-green-400 transition"
                                style="color:#d4af37" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                            <span
                                class="text-primary-300 text-sm group-hover:text-white transition">{{ \App\Models\ProfilOrganisasi::getValue('telepon', '(0xxx) 123456') }}</span>
                        </a>
                        <a href="mailto:{{ \App\Models\ProfilOrganisasi::getValue('email') }}"
                            class="flex items-center gap-3 group">
                            <svg class="w-4 h-4 flex-shrink-0 group-hover:text-green-400 transition"
                                style="color:#d4af37" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            <span
                                class="text-primary-300 text-sm group-hover:text-white transition">{{ \App\Models\ProfilOrganisasi::getValue('email', 'info@kemenag-kab.go.id') }}</span>
                        </a>
                    </div>
                </div>

                {{-- Kolom 3: Layanan Cepat --}}
                <div>
                    <h3 class="font-bold text-sm mb-5" style="color: #d4af37;">Layanan Cepat</h3>
                    <ul class="space-y-3">
                        <li>
                            <a href="{{ route('home') }}#portal-akses" class="flex items-center gap-2.5 group">
                                <svg class="w-4 h-4 flex-shrink-0 text-primary-500 group-hover:text-green-400 transition"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <span class="text-primary-300 text-sm group-hover:text-white transition">Ajukan
                                    Dokumen</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('status.index') }}" class="flex items-center gap-2.5 group">
                                <svg class="w-4 h-4 flex-shrink-0 text-primary-500 group-hover:text-green-400 transition"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                </svg>
                                <span class="text-primary-300 text-sm group-hover:text-white transition">Cek Status
                                    Ajuan</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('data-madrasah.index') }}" class="flex items-center gap-2.5 group">
                                <svg class="w-4 h-4 flex-shrink-0 text-primary-500 group-hover:text-green-400 transition"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                                <span class="text-primary-300 text-sm group-hover:text-white transition">Data
                                    Madrasah</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('profil') }}" class="flex items-center gap-2.5 group">
                                <svg class="w-4 h-4 flex-shrink-0 text-primary-500 group-hover:text-green-400 transition"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span class="text-primary-300 text-sm group-hover:text-white transition">Profil
                                    Seksi</span>
                            </a>
                        </li>
                    </ul>
                </div>

                {{-- Kolom 4: Jam Operasional --}}
                <div class="flex flex-col items-center">
                    <h3 class="font-bold text-sm mb-5" style="color: #d4af37;">Jam Operasional</h3>
                    <div class="space-y-1.5 w-full">
                        @php
                            $jadwal = [
                                'Senin' => ['jam' => '07.30 – 16.00 WIB', 'jumat' => false],
                                'Selasa' => ['jam' => '07.30 – 16.00 WIB', 'jumat' => false],
                                'Rabu' => ['jam' => '07.30 – 16.00 WIB', 'jumat' => false],
                                'Kamis' => ['jam' => '07.30 – 16.00 WIB', 'jumat' => false],
                                'Jumat' => ['jam' => '07.30 – 16.30 WIB', 'jumat' => true],
                            ];
                        @endphp
                        @foreach ($jadwal as $hari => $info)
                            <div
                                class="flex items-center justify-between gap-3 rounded-lg {{ $info['jumat'] ? 'bg-white/5' : '' }}">
                                <div class="flex items-center gap-2">
                                    <span
                                        class="w-1.5 h-1.5 rounded-full flex-shrink-0 {{ $info['jumat'] ? 'bg-yellow-400' : 'bg-primary-500' }}"></span>
                                    <span class="text-primary-300 text-sm w-14">{{ $hari }}</span>
                                </div>
                                <span class="text-white text-sm font-semibold tabular-nums">{{ $info['jam'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Bottom bar --}}
            <div class="border-t pt-6 flex flex-col md:flex-row items-center justify-between gap-3"
                style="border-color: rgba(255,255,255,0.07);">
                <p class="text-primary-600 text-xs">
                    © {{ date('Y') }}
                    {{ \App\Models\ProfilOrganisasi::getValue('nama_organisasi', 'Seksi Pendidikan Madrasah') }}. Hak
                    cipta dilindungi.
                </p>
                <div class="flex items-center gap-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse inline-block"></span>
                    <p class="text-primary-600 text-xs">Portal Madrasah — Kemenag Kab. Temanggung</p>
                </div>
            </div>
        </div>
    </footer>
    <script>
        // Navbar scroll effect
        window.addEventListener('scroll', function() {
            const navbar = document.getElementById('navbar');
            if (window.scrollY > 20) {
                navbar.classList.add('navbar-scrolled');
            } else {
                navbar.classList.remove('navbar-scrolled');
            }
        });
    </script>

    @stack('scripts')
</body>

</html>