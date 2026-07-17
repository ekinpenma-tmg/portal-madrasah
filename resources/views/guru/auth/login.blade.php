<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Guru — Portal Madrasah</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: { extend: {
                fontFamily: { sans: ['Plus Jakarta Sans', 'sans-serif'] },
                colors: {
                    primary: { 50:'#eafaec',100:'#d4f4d8',200:'#a8e9b0',300:'#7cd889',400:'#5ccb5f',500:'#3ab84a',600:'#1fa337',700:'#009929',800:'#006419',900:'#003d0d' },
                    accent: { 50:'#fefef2',100:'#fcfcd6',200:'#f9fab0',300:'#f5f649',400:'#ebed17',500:'#d1d313',600:'#a9ab0f',700:'#84860c' },
                },
            } }
        }
    </script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .login-bg {
            background-color: #003d0d;
            background-image: radial-gradient(circle, rgba(255,255,255,0.065) 1.2px, transparent 1.2px);
            background-size: 28px 28px;
        }
        .login-card { box-shadow: 0 1px 2px rgba(0,0,0,0.04), 0 12px 28px rgba(0,0,0,0.10); }
        .login-card-sheet { box-shadow: 0 -8px 30px rgba(0,0,0,0.14); }
        .text-2xs { font-size: 0.7rem; }
    </style>
</head>
{{--
    MOBILE (<md): gaya "bottom sheet" — logo & nama app besar di area hijau atas,
    form-nya nempel di bawah sebagai kartu putih rounded cuma di atas, biar
    kepake penuh tinggi layar HP (gak ada spasi hijau kosong nganggur kayak
    sebelumnya kalau layar HP-nya tinggi).
    DESKTOP (md+): tetap kartu mengambang di tengah persis seperti sebelumnya,
    tidak diubah sama sekali.
--}}
<body class="login-bg min-h-screen flex flex-col justify-center relative">

    <a href="{{ route('home') }}" class="absolute top-4 left-4 z-10 inline-flex items-center gap-1.5 text-xs font-medium transition" style="color:rgba(255,255,255,0.5)" onmouseover="this.style.color='rgba(255,255,255,0.85)'" onmouseout="this.style.color='rgba(255,255,255,0.5)'">
        <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
        </svg>
        Kembali ke Beranda
    </a>

    {{-- ── Brand — cuma mobile, nempel dekat atas ── --}}
    <div class="md:hidden flex flex-col items-center pt-10 pb-5 px-6 text-center">
        <div class="w-14 h-14 rounded-2xl bg-white/95 flex items-center justify-center overflow-hidden shadow-lg mb-3">
            @if(\App\Models\ProfilOrganisasi::getValue('logo_path'))
                <img src="{{ Storage::url(\App\Models\ProfilOrganisasi::getValue('logo_path')) }}"
                     alt="Logo" class="w-full h-full object-contain p-2">
            @else
                <svg class="w-7 h-7 text-primary-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                        d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
            @endif
        </div>
        <h1 class="text-white text-base font-bold">Portal Madrasah</h1>
        <p class="text-2xs mt-1" style="color:rgba(255,255,255,0.55)">
            {{ \App\Models\ProfilOrganisasi::getValue('nama_instansi') ?? 'Arsip digital guru' }}
        </p>
    </div>

    {{-- ══════════════════════ DESKTOP — disamain gayanya sama mobile (pill input + ikon) ══════════════════════ --}}
    <div class="hidden md:flex flex-1 flex-col items-center justify-center px-4 py-6">
        <div class="login-card w-full max-w-[360px] bg-white rounded-2xl px-7 py-8">

            <div class="text-center mb-5">
                <div class="w-11 h-11 mx-auto mb-3 rounded-xl bg-primary-800 flex items-center justify-center overflow-hidden">
                    @if(\App\Models\ProfilOrganisasi::getValue('logo_path'))
                        <img src="{{ Storage::url(\App\Models\ProfilOrganisasi::getValue('logo_path')) }}"
                             alt="Logo" class="w-full h-full object-contain p-1.5">
                    @else
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    @endif
                </div>
                <h1 class="text-sm font-semibold text-gray-900">Portal Madrasah</h1>
                <p class="text-xs text-gray-400 mt-0.5">
                    {{ \App\Models\ProfilOrganisasi::getValue('nama_instansi') ?? 'Arsip digital guru' }}
                </p>
            </div>

            <h2 class="text-base font-bold text-gray-900 text-center">Masuk sebagai Guru</h2>
            <div class="w-8 h-1 rounded-full bg-accent-400 mx-auto mt-2 mb-5"></div>

            @if($errors->any())
            <div class="mb-3 px-3 py-2 rounded-lg text-xs font-medium text-red-600 bg-red-50 border border-red-100">
                {{ $errors->first() }}
            </div>
            @endif

            <form method="POST" action="{{ route('guru.login.post') }}">
                @csrf
                <div class="mb-3">
                    <label for="pegid-desktop" class="sr-only">Nomor PegID</label>
                    <div class="relative">
                        <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-[18px] h-[18px] text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        <input type="text" id="pegid-desktop" name="pegid" value="{{ old('pegid') }}" required
                            placeholder="Nomor PegID"
                            class="w-full pl-11 pr-4 py-2.5 border border-gray-200 rounded-full text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-primary-300 focus:border-primary-400 transition
                                   @error('pegid') border-red-300 bg-red-50 @enderror">
                    </div>
                </div>

                <div class="mb-4" x-data="{ show: false }">
                    <label for="password-desktop" class="sr-only">Password</label>
                    <div class="relative">
                        <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-[18px] h-[18px] text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 10-8 0v4h8z"/>
                        </svg>
                        <input :type="show ? 'text' : 'password'" id="password-desktop" name="password" required
                            placeholder="Password"
                            class="w-full pl-11 pr-11 py-2.5 border border-gray-200 rounded-full text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-primary-300 focus:border-primary-400 transition">
                        <button type="button" @click="show = !show"
                            class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition">
                            <svg x-show="!show" class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <svg x-show="show" x-cloak class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="flex items-center gap-2 mb-5">
                    <input type="checkbox" name="remember" id="remember-desktop"
                        class="w-3.5 h-3.5 rounded border-gray-300 text-primary-700 focus:ring-primary-500 cursor-pointer">
                    <label for="remember-desktop" class="text-xs text-gray-500 cursor-pointer">Ingat saya</label>
                </div>

                <button type="submit"
                    class="w-full py-3 rounded-full bg-primary-800 hover:opacity-90 active:scale-95 text-white font-semibold text-sm tracking-wide transition-all duration-200">
                    Masuk
                </button>
            </form>

            <div class="text-center mt-5 pt-4 border-t border-gray-100">
                <p class="text-2xs text-gray-300">© {{ date('Y') }} Portal Madrasah</p>
            </div>
        </div>

        @if(Route::has('madrasah.login'))
        <div class="w-full max-w-[360px] text-center mt-3">
            <a href="{{ route('madrasah.login') }}" class="text-xs transition" style="color:rgba(255,255,255,0.5)" onmouseover="this.style.color='rgba(255,255,255,0.85)'" onmouseout="this.style.color='rgba(255,255,255,0.5)'">
                Login sebagai madrasah →
            </a>
        </div>
        @endif
    </div>

    {{-- ══════════════════════ MOBILE — kartu dengan margin kiri-kanan, dipadatkan biar muat 1 layar ══════════════════════ --}}
    <div class="md:hidden mx-5 mb-8 login-card-sheet bg-white rounded-[22px] px-5 pt-6 pb-6">
        <h2 class="text-base font-bold text-gray-900 text-center">Masuk sebagai Guru</h2>
        <div class="w-8 h-1 rounded-full bg-accent-400 mx-auto mt-2 mb-4"></div>

        @if($errors->any())
        <div class="mb-3 px-3 py-2 rounded-lg text-xs font-medium text-red-600 bg-red-50 border border-red-100">
            {{ $errors->first() }}
        </div>
        @endif

        <form method="POST" action="{{ route('guru.login.post') }}">
            @csrf

            <div class="mb-3">
                <label for="pegid-mobile" class="sr-only">Nomor PegID</label>
                <div class="relative">
                    <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-[18px] h-[18px] text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    <input type="text" id="pegid-mobile" name="pegid" value="{{ old('pegid') }}" required
                        placeholder="Nomor PegID"
                        class="w-full pl-11 pr-4 py-2.5 border border-gray-200 rounded-full text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-primary-300 focus:border-primary-400 transition
                               @error('pegid') border-red-300 bg-red-50 @enderror">
                </div>
                @error('pegid')<p class="text-red-500 text-xs mt-1 ml-1">{{ $message }}</p>@enderror
            </div>

            <div class="mb-4" x-data="{ show: false }">
                <label for="password-mobile" class="sr-only">Password</label>
                <div class="relative">
                    <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-[18px] h-[18px] text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 10-8 0v4h8z"/>
                    </svg>
                    <input :type="show ? 'text' : 'password'" id="password-mobile" name="password" required
                        placeholder="Password"
                        class="w-full pl-11 pr-11 py-2.5 border border-gray-200 rounded-full text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-primary-300 focus:border-primary-400 transition">
                    <button type="button" @click="show = !show"
                        class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition">
                        <svg x-show="!show" class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        <svg x-show="show" x-cloak class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="flex items-center gap-2 mb-4">
                <input type="checkbox" name="remember" id="remember-mobile"
                    class="w-4 h-4 rounded border-gray-300 text-primary-700 focus:ring-primary-500 cursor-pointer">
                <label for="remember-mobile" class="text-xs text-gray-500 cursor-pointer">Ingat saya</label>
            </div>

            <button type="submit"
                class="w-full py-3 rounded-full bg-primary-800 hover:opacity-90 active:scale-95 text-white font-semibold text-sm tracking-wide transition-all duration-200">
                Masuk
            </button>

            <div class="text-center mt-4 pt-3 border-t border-gray-100">
                @if(Route::has('madrasah.login'))
                <a href="{{ route('madrasah.login') }}" class="block text-xs text-gray-500 mb-1.5">Login sebagai madrasah →</a>
                @endif
                <p class="text-2xs text-gray-300">© {{ date('Y') }} Portal Madrasah</p>
            </div>
        </form>
    </div>

<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>
</html>
