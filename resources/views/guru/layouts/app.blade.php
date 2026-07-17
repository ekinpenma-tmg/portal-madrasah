<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Portal Guru') — Arsip Digital</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        // Intercept submit pada form/tombol yang punya [data-confirm], ganti confirm() bawaan
        // browser dengan modal custom. Dipanggil lewat x-init="setupConfirmModal($el)" di <body>.
        function setupConfirmModal(rootEl) {
            document.addEventListener('submit', function (e) {
                var form = e.target.closest('form[data-confirm]');
                if (form && form.dataset.confirmed !== 'true') {
                    e.preventDefault();
                    var data = Alpine.$data(rootEl);
                    data.confirmMsg   = form.getAttribute('data-confirm');
                    data.confirmType  = form.dataset.confirmType  || 'danger';
                    data.confirmLabel = form.dataset.confirmLabel || (form.dataset.confirmType === 'primary' ? 'Ya, Lanjutkan' : 'Ya, Hapus');
                    data.confirmForm  = form;
                    data.confirmOpen  = true;
                }
            });
        }
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    colors: {
                        primary: { 50:'#eafaec',100:'#d4f4d8',200:'#a8e9b0',300:'#7cd889',400:'#5ccb5f',500:'#3ab84a',600:'#1fa337',700:'#009929',800:'#006419',900:'#003d0d' },
                        accent: { 50:'#fefef2',100:'#fcfcd6',200:'#f9fab0',300:'#f5f649',400:'#ebed17',500:'#d1d313',600:'#a9ab0f',700:'#84860c' },
                    },
                    fontSize: { '2xs': ['0.65rem', { lineHeight: '1rem' }] }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Inter', sans-serif; background: #f3f4f6; }
        [x-cloak] { display: none !important; }
        @keyframes fadeUp { from { opacity:0; transform:translateY(6px); } to { opacity:1; transform:none; } }
        .fade-in { animation: fadeUp .25s ease both; }

        /* Sidebar nav */
        .nav-group-label {
            font-size: 10px; font-weight: 600; letter-spacing: 0.07em; text-transform: uppercase;
            color: #6fc074; padding: 14px 10px 4px;
        }
        .nav-item {
            display: flex; align-items: center; gap: 8px;
            padding: 7px 10px; border-radius: 8px;
            font-size: 13px; font-weight: 500; color: #a8e9b0;
            text-decoration: none; transition: all .12s ease; white-space: nowrap;
        }
        .nav-item:hover { background: rgba(255,255,255,0.08); color: #fff; }
        .nav-item.active { background: rgba(255,255,255,0.14); color: #fff; }
        .nav-item.active svg { color: #ebed17; }
        .nav-dot { width: 5px; height: 5px; border-radius: 50%; background: transparent; flex-shrink: 0; transition: .15s; }
        .nav-item.active .nav-dot { background: #ebed17; }

        .badge { display: inline-flex; align-items: center; font-size: 11px; font-weight: 500; padding: 2px 8px; border-radius: 20px; }
        .badge-green  { background: #dcfce7; color: #15803d; }
        .badge-yellow { background: #fef9c3; color: #854d0e; }
        .badge-red    { background: #fee2e2; color: #991b1b; }
        .badge-gray   { background: #f4f4f5; color: #52525b; }
        .badge-blue   { background: #dbeafe; color: #1d4ed8; }
        .badge-purple { background: #ede9fe; color: #6d28d9; }

        .stat-card { background: #fff; border: 1px solid #ebebeb; border-radius: 10px; padding: 14px 16px; border-left: 3px solid #009929; }
        .stat-card.amber { border-left-color: #d97706; }
        .stat-card.blue  { border-left-color: #2563eb; }
        .stat-card.purple { border-left-color: #7c3aed; }
        .stat-card .stat-label { font-size: 11px; color: #999; margin-bottom: 4px; }
        .stat-card .stat-value { font-size: 24px; font-weight: 600; color: #009929; line-height: 1; }
        .stat-card.amber .stat-value { color: #d97706; }
        .stat-card.blue .stat-value  { color: #2563eb; }
        .stat-card.purple .stat-value { color: #7c3aed; }
        .stat-card .stat-sub { font-size: 11px; color: #999; margin-top: 4px; }

        .data-table { width: 100%; border-collapse: collapse; font-size: 13px; }
        .data-table th { padding: 10px 14px; text-align: left; font-size: 11px; font-weight: 600; color: #999; border-bottom: 1px solid #ebebeb; background: #fafafa; text-transform: uppercase; letter-spacing: 0.03em; }
        .data-table td { padding: 12px 14px; color: #333; border-bottom: 1px solid #f2f2f2; height: 48px; box-sizing: border-box; }
        .data-table tr:last-child td { border-bottom: none; }
        .data-table tr:hover td { background: #fafafa; }

        .btn-xs { display: inline-flex; align-items: center; gap: 4px; font-size: 12px; font-weight: 500; padding: 5px 11px; border-radius: 7px; cursor: pointer; border: 1px solid; transition: all .12s; white-space: nowrap; }
        .btn-primary-xs { background: #009929; color: #fff; border-color: #009929; }
        .btn-primary-xs:hover { background: #00801f; }
        .btn-ghost-xs { background: transparent; color: #555; border-color: #ddd; }
        .btn-ghost-xs:hover { background: #f4f4f4; color: #111; }

        .btn-icon { display:inline-flex; align-items:center; justify-content:center; width:30px; height:30px; border-radius:7px; color:#6b7280; border:1px solid #e8e8e8; transition: all .12s; }
        .btn-icon:hover { background:#f4f4f5; color:#111; }
        .btn-icon.blue:hover { background:#dbeafe; color:#1d4ed8; border-color:#bfdbfe; }
        .btn-icon.danger:hover { background:#fee2e2; color:#dc2626; border-color:#fca5a5; }
        .btn-icon.success:hover { background:#dcfce7; color:#16a34a; border-color:#bbf7d0; }

        /* Bottom navigation — mobile only. Menggantikan pola "buka hamburger
           dulu baru pilih menu": menu paling sering dipakai guru tiap hari
           (Beranda, Pengajuan, Arsip) langsung bisa ditap dari mana saja,
           tanpa perlu buka drawer. "Akun" tetap membuka drawer/sidebar yang
           sama seperti sebelumnya, isinya menu yang lebih jarang dipakai. */
        .bottom-nav {
            position: fixed; left: 0; right: 0; bottom: 0; z-index: 40;
            display: flex; background: #fff; border-top: 1px solid #e5e7eb;
            padding-bottom: env(safe-area-inset-bottom, 0px);
            box-shadow: 0 -2px 10px rgba(0,0,0,0.04);
        }
        .bottom-nav-item {
            flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center;
            gap: 3px; min-height: 58px; padding: 6px 2px;
            font-size: 10.5px; font-weight: 500; color: #9ca3af;
            text-decoration: none; -webkit-tap-highlight-color: transparent;
        }
        .bottom-nav-item svg { width: 21px; height: 21px; }
        .bottom-nav-item.active { color: #009929; }
        .bottom-nav-item:active { background: #f9fafb; }
        .bottom-nav-item .dot-active {
            width: 4px; height: 4px; border-radius: 50%; background: #009929;
            position: absolute; margin-top: -30px;
        }

        /* Perbesar tombol aksi utama (Simpan, Hapus, Batal, Tambah, dll) khusus
           di layar mobile — versi desktop (≥768px) TIDAK berubah sama sekali.
           .btn-xs aslinya cuma ~24px tinggi (padding 5px + font 12px), kekecilan
           untuk jempol. .btn-icon di dalam tabel desktop-only (dibungkus
           `hidden md:block`) aman ikut kena rule ini karena toh gak dirender
           di lebar <768px. */
        @media (max-width: 767px) {
            .btn-xs {
                padding: 11px 18px;
                font-size: 13.5px;
                border-radius: 9px;
                min-height: 44px;
            }
            .btn-icon {
                width: 44px;
                height: 44px;
                border-radius: 9px;
            }
        }
    </style>
</head>
<body class="min-h-screen"
      x-data="{ mobileNav: false, confirmOpen: false, confirmMsg: '', confirmForm: null, confirmType: 'danger', confirmLabel: 'Lanjutkan' }"
      x-init="setupConfirmModal($el)">

{{-- ── TOP LOADING BAR — mirip GitHub/YouTube, muncul tiap pindah halaman ── --}}
<div id="topbar-loader" class="fixed top-0 left-0 h-[3px] z-[9999] pointer-events-none"
     style="width:0%; opacity:0; background:linear-gradient(90deg,#5ccb5f,#009929); box-shadow:0 0 8px rgba(0,153,41,0.5);"></div>
<script>
(function () {
    var bar = document.getElementById('topbar-loader');
    function start() {
        bar.style.transition = 'none';
        bar.style.width = '0%';
        bar.style.opacity = '1';
        bar.offsetHeight; // force reflow
        bar.style.transition = 'width 8s cubic-bezier(0.1,0.9,0.2,1), opacity .2s';
        requestAnimationFrame(function () { bar.style.width = '85%'; });
    }
    document.addEventListener('click', function (e) {
        var a = e.target.closest('a');
        if (!a || !a.href) return;
        if (a.target === '_blank' || a.hasAttribute('download')) return;
        if (a.getAttribute('href').startsWith('#') || a.getAttribute('href').startsWith('javascript:')) return;
        if (e.metaKey || e.ctrlKey || e.shiftKey || e.button !== 0) return;
        try {
            if (new URL(a.href, location.href).origin !== location.origin) return;
        } catch (err) { return; }
        start();
    });
    document.addEventListener('submit', function (e) {
        if (e.target.tagName === 'FORM') start();
    });
    window.addEventListener('pageshow', function () {
        bar.style.transition = 'none';
        bar.style.width = '0%';
        bar.style.opacity = '0';
    });
})();
</script>
<div class="flex h-screen overflow-hidden">

    {{-- ── BACKDROP MOBILE ────────────────────────────────────────── --}}
    <div x-show="mobileNav" x-cloak @click="mobileNav = false" x-transition.opacity class="fixed inset-0 bg-black/40 z-40 md:hidden"></div>

    {{-- ── SIDEBAR ─────────────────────────────────────────────────── --}}
    <aside class="w-60 flex-shrink-0 flex flex-col fixed md:static inset-y-0 left-0 z-50 transform transition-transform duration-200"
           :class="mobileNav ? 'translate-x-0' : '-translate-x-full md:translate-x-0'"
           style="background:#003d0d">

        {{-- Logo --}}
        <div class="flex items-center gap-2.5 px-4 py-3.5 flex-shrink-0" style="border-bottom:1px solid rgba(255,255,255,0.08)">
            <div class="w-7 h-7 bg-white/95 rounded-lg flex items-center justify-center overflow-hidden flex-shrink-0">
                @if(\App\Models\ProfilOrganisasi::getValue('logo_path'))
                    <img src="{{ Storage::url(\App\Models\ProfilOrganisasi::getValue('logo_path')) }}"
                         alt="Logo" class="w-full h-full object-contain p-0.5">
                @else
                    <svg class="w-3.5 h-3.5 text-primary-700" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2L2 7l10 5 10-5-10-5z"/>
                    </svg>
                @endif
            </div>
            <div class="min-w-0">
                <p class="text-xs font-semibold leading-tight text-white truncate">Portal Guru</p>
                <p class="text-2xs text-primary-300 leading-tight truncate">Arsip Digital</p>
            </div>
            <button @click="mobileNav = false" class="ml-auto md:hidden text-primary-300 hover:text-white p-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        {{-- Nav groups --}}
        <nav class="flex-1 overflow-y-auto px-2 py-2 space-y-0.5">
            @php
                $navGroups = [
                    'Utama' => [
                        ['route' => 'guru.dashboard', 'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6', 'label' => 'Dashboard'],
                    ],
                    'Arsip Digital' => [
                        ['route' => 'guru.arsip.index', 'also' => ['guru.arsip.edit'], 'icon' => 'M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4', 'label' => 'Arsip Saya'],
                        ['route' => 'guru.arsip.create', 'icon' => 'M12 4v16m8-8H4', 'label' => 'Tambah Arsip'],
                    ],
                    'Pengajuan' => [
                        ['route' => 'guru.pengajuan.index', 'also' => ['guru.pengajuan.form'], 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'label' => 'Ajukan Dokumen'],
                        ['route' => 'guru.pengajuan.riwayat', 'also' => ['guru.pengajuan.show'], 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z', 'label' => 'Riwayat Pengajuan'],
                    ],
                    'Akun' => [
                        ['route' => 'guru.profil.form', 'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z', 'label' => 'Edit Profil'],
                        ['route' => 'guru.password.form', 'icon' => 'M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z', 'label' => 'Ganti Password'],
                    ],
                ];
            @endphp

            @foreach($navGroups as $groupLabel => $items)
                <div class="nav-group-label">{{ $groupLabel }}</div>
                @foreach($items as $menu)
                    @php
                        $active = request()->routeIs($menu['route']);
                        foreach (($menu['also'] ?? []) as $r) { if (request()->routeIs($r)) $active = true; }
                    @endphp
                    <a href="{{ route($menu['route']) }}" class="nav-item {{ $active ? 'active' : '' }}">
                        <span class="nav-dot flex-shrink-0"></span>
                        <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="{{ $menu['icon'] }}"/>
                        </svg>
                        <span class="truncate">{{ $menu['label'] }}</span>
                    </a>
                @endforeach
            @endforeach
        </nav>

        {{-- Bottom: identitas akun + logout --}}
        <div class="px-2 py-2.5 flex-shrink-0" style="border-top:1px solid rgba(255,255,255,0.08)">
            <div class="flex items-center gap-2 px-2 py-2 mb-1">
                <div class="w-7 h-7 rounded-full bg-white/10 flex items-center justify-center text-white text-xs font-semibold flex-shrink-0">
                    {{ strtoupper(substr(auth('guru')->user()->nama, 0, 1)) }}
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-white text-xs font-medium truncate">{{ auth('guru')->user()->nama }}</p>
                    <p class="text-2xs text-primary-300 font-mono truncate">{{ auth('guru')->user()->pegid }}</p>
                </div>
            </div>
            <form method="POST" action="{{ route('guru.logout') }}">
                @csrf
                <button type="submit" class="nav-item w-full text-left" style="color:#fca5a5">
                    <span class="nav-dot flex-shrink-0"></span>
                    <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    <span class="truncate">Logout</span>
                </button>
            </form>
        </div>
    </aside>

    {{-- ── MAIN ────────────────────────────────────────────────────── --}}
    <div class="flex-1 flex flex-col overflow-hidden">

        {{-- Topbar (judul halaman). Hamburger drawer lama dipindah fungsinya
             ke tombol "Akun" di bottom nav mobile, jadi gak ada 2 pintu
             masuk navigasi yang tumpang tindih. --}}
        <header class="flex items-center gap-3 px-4 md:px-6 flex-shrink-0" style="height:52px; background:#fff; border-bottom:1px solid #e5e5e5;">
            <span class="text-sm font-semibold text-gray-800">@yield('title', 'Dashboard')</span>
        </header>

        <div class="flex-1 overflow-y-auto">
            <div class="px-4 md:px-6 pt-5 pb-24 md:pb-5 fade-in">

                {{-- ── FLASH — dipindah jadi toast mengambang, lihat dekat penutup <body> ── --}}
                @php
                    $fS = session('success'); $fE = session('error');
                    $fW = session('warning'); $fI = session('info');
                    session()->forget(['success','error','warning','info']);
                    $flashes = array_filter([
                        $fS ? ['msg'=>$fS,'bg'=>'bg-green-50','border'=>'border-green-100','ic'=>'text-green-500','tc'=>'text-green-800','icon'=>'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'] : null,
                        $fE ? ['msg'=>$fE,'bg'=>'bg-red-50',  'border'=>'border-red-100',  'ic'=>'text-red-500',  'tc'=>'text-red-800',  'icon'=>'M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'] : null,
                        $fW ? ['msg'=>$fW,'bg'=>'bg-amber-50','border'=>'border-amber-100','ic'=>'text-amber-500','tc'=>'text-amber-800','icon'=>'M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z'] : null,
                        $fI ? ['msg'=>$fI,'bg'=>'bg-blue-50', 'border'=>'border-blue-100', 'ic'=>'text-blue-500', 'tc'=>'text-blue-800', 'icon'=>'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'] : null,
                    ]);
                @endphp

                {{-- ── PASSWORD DEFAULT WARNING ─────────────────────────────── --}}
                @if(!auth('guru')->user()->password_changed)
                <div class="pb-4">
                    <div class="bg-amber-50 border border-amber-100 rounded-lg px-4 py-2.5 flex items-center justify-between gap-3 flex-wrap">
                        <div class="flex items-center gap-2">
                            <svg class="w-3.5 h-3.5 text-amber-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                            </svg>
                            <span class="text-amber-800 text-xs">Anda masih menggunakan <strong>password default</strong>. Segera ganti untuk keamanan akun Anda.</span>
                        </div>
                        <a href="{{ route('guru.password.form') }}" class="flex-shrink-0 btn-xs btn-primary-xs">Ganti Sekarang</a>
                    </div>
                </div>
                @endif

                {{-- ── CONTENT ────────────────────────────────────────────── --}}
                @yield('content')

                <p class="text-2xs text-gray-400 text-center pt-6 pb-2">
                    Portal Arsip Digital — {{ \App\Models\ProfilOrganisasi::getValue('nama_instansi') ?? 'Kemenag' }}
                </p>
            </div>
        </div>
    </div>
</div>

{{-- ── MODAL KONFIRMASI CUSTOM — ganti confirm() bawaan browser ── --}}
<div x-show="confirmOpen" x-cloak
     class="fixed inset-0 z-[10000] flex items-center justify-center p-4"
     x-transition.opacity>
    <div class="absolute inset-0 bg-black/40" @click="confirmOpen = false"></div>
    <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-xs p-5"
         x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
        <div class="w-10 h-10 rounded-full flex items-center justify-center mb-3"
             :class="confirmType === 'primary' ? 'bg-primary-50' : 'bg-red-50'">
            <svg x-show="confirmType !== 'primary'" class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
            <svg x-show="confirmType === 'primary'" x-cloak class="w-5 h-5 text-primary-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <p class="text-sm text-gray-800 font-medium mb-5" style="white-space: pre-line;" x-text="confirmMsg"></p>
        <div class="flex gap-2">
            <button type="button" @click="confirmOpen = false"
                class="flex-1 py-2.5 rounded-lg border border-gray-200 text-gray-600 text-sm font-medium hover:bg-gray-50 transition">
                Batal
            </button>
            <button type="button"
                @click="confirmForm.dataset.confirmed = 'true'; confirmForm.requestSubmit(); confirmOpen = false"
                class="flex-1 py-2.5 rounded-lg text-white text-sm font-semibold transition"
                :class="confirmType === 'primary' ? 'bg-primary-700 hover:bg-primary-800' : 'bg-red-600 hover:bg-red-700'"
                x-text="confirmLabel">
            </button>
        </div>
    </div>
</div>

{{-- ── TOAST NOTIFICATIONS — mengambang di pojok, auto-hilang sendiri ── --}}
@if(count($flashes))
<div class="fixed top-4 left-4 right-4 md:left-auto md:right-4 md:max-w-sm z-[9998] flex flex-col gap-2 pointer-events-none">
    @foreach($flashes as $flash)
    <div class="pointer-events-auto"
         x-data="{ show: false }" x-init="requestAnimationFrame(() => show = true); setTimeout(() => show = false, 4500)"
         x-show="show" x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 -translate-y-2 md:translate-y-0 md:translate-x-4"
         x-transition:enter-end="opacity-100 translate-y-0 translate-x-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0 translate-x-0"
         x-transition:leave-end="opacity-0 -translate-y-2 md:translate-y-0 md:translate-x-4">
        <div class="{{ $flash['bg'] }} border {{ $flash['border'] }} rounded-lg shadow-lg px-4 py-3 flex items-center gap-3">
            <svg class="w-4 h-4 {{ $flash['ic'] }} flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $flash['icon'] }}"/>
            </svg>
            <span class="{{ $flash['tc'] }} text-xs font-medium flex-1">{{ $flash['msg'] }}</span>
            <button @click="show = false" class="{{ $flash['ic'] }} opacity-60 hover:opacity-100 transition flex-shrink-0">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    </div>
    @endforeach
</div>
@endif

{{-- ── BOTTOM NAVIGATION (mobile only) ──────────────────────────────
     Menu yang paling sering dipakai guru tiap hari bisa langsung ditap
     dari mana saja, tanpa perlu buka drawer dulu. "Akun" membuka drawer
     (sidebar) yang sama seperti sebelumnya, isinya menu yang lebih jarang
     dipakai (Tambah Arsip, Riwayat Dokumen, Edit Profil, Ganti
     Password, Logout). --}}
@php
    $bottomNavItems = [
        ['route' => 'guru.dashboard', 'label' => 'Beranda', 'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
        ['route' => 'guru.pengajuan.index', 'also' => ['guru.pengajuan.form', 'guru.pengajuan.riwayat', 'guru.pengajuan.show'], 'label' => 'Pengajuan', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
        ['route' => 'guru.arsip.index', 'also' => ['guru.arsip.create', 'guru.arsip.edit'], 'label' => 'Arsip', 'icon' => 'M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4'],
    ];
    $bottomNavActive = false;
    foreach ($bottomNavItems as $it) {
        if (request()->routeIs($it['route']) || collect($it['also'] ?? [])->contains(fn ($r) => request()->routeIs($r))) {
            $bottomNavActive = true;
        }
    }
@endphp
<nav class="bottom-nav md:hidden">
    @foreach($bottomNavItems as $item)
        @php
            $active = request()->routeIs($item['route']);
            foreach (($item['also'] ?? []) as $r) { if (request()->routeIs($r)) $active = true; }
        @endphp
        <a href="{{ route($item['route']) }}" class="bottom-nav-item {{ $active ? 'active' : '' }}">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ $active ? '2' : '1.6' }}" d="{{ $item['icon'] }}"/>
            </svg>
            <span>{{ $item['label'] }}</span>
        </a>
    @endforeach
    <button type="button" @click="mobileNav = true" class="bottom-nav-item {{ !$bottomNavActive ? 'active' : '' }}">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ !$bottomNavActive ? '2' : '1.6' }}" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
        </svg>
        <span>Akun</span>
    </button>
</nav>

@stack('scripts')
</body>
</html>
