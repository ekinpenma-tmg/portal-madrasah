<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin') — Portal Madrasah</title>
    <script>
        // Set tema sebelum halaman dirender, biar tidak ada "kedipan" putih->gelap
        (function () {
            try {
                if (localStorage.getItem('admin-theme') === 'dark') {
                    document.documentElement.classList.add('dark');
                }
            } catch (e) {}
        })();
        function toggleAdminTheme() {
            var isDark = document.documentElement.classList.toggle('dark');
            try { localStorage.setItem('admin-theme', isDark ? 'dark' : 'light'); } catch (e) {}
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="admin-panel" x-data="{ sidebar: true, mobileNav: false, isDesktop: window.innerWidth >= 768 }"
    x-init="window.addEventListener('resize', () => { isDesktop = window.innerWidth >= 768; if (isDesktop) mobileNav = false; })">

    <div id="admin-loadbar"></div>

    @include('partials.toast')
    @include('partials.confirm-modal')


    <div class="flex h-screen overflow-hidden">

        {{-- ══ BACKDROP MOBILE ══ --}}
        <div x-show="mobileNav" x-cloak @click="mobileNav = false" x-transition.opacity
            class="fixed inset-0 bg-black/40 z-40 md:hidden"></div>

        {{-- ══ SIDEBAR ══ --}}
        <aside :class="{ 'md:w-12': !sidebar, 'translate-x-0': mobileNav, '-translate-x-full md:translate-x-0': !mobileNav }"
            class="w-52 flex flex-col flex-shrink-0 fixed md:static inset-y-0 left-0 z-50 transform transition-all duration-200 overflow-hidden"
            style="background:#0c0c0c; border-right:1px solid rgba(255,255,255,0.05)">

            {{-- Logo --}}
            <div class="flex items-center gap-2.5 px-3 py-3.5 flex-shrink-0"
                style="border-bottom:1px solid rgba(255,255,255,0.05)">
                <div
                    class="w-6 h-6 rounded-md flex items-center justify-center flex-shrink-0 overflow-hidden bg-primary-700">
                    @if (\App\Models\ProfilOrganisasi::getValue('logo_path'))
                        <img src="{{ Storage::url(\App\Models\ProfilOrganisasi::getValue('logo_path')) }}"
                            alt="Logo" class="w-full h-full object-contain p-0.5">
                    @else
                        <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2L2 7l10 5 10-5-10-5z" />
                        </svg>
                    @endif
                </div>
                <div x-show="sidebar" x-transition:enter="transition-opacity duration-100"
                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="min-w-0">
                    <p class="text-white text-xs font-semibold leading-tight truncate">Admin Panel</p>
                    <p class="text-zinc-500 text-2xs leading-tight truncate">
                        {{ \App\Models\ProfilOrganisasi::getValue('nama_instansi') ?? 'Portal Madrasah' }}
                    </p>
                </div>
                <button @click="mobileNav = false" class="ml-auto md:hidden text-zinc-500 hover:text-white p-1">
                    <i data-lucide="x" stroke-width="2" class="w-4 h-4"></i>
                </button>
            </div>

            {{-- Nav --}}
            <nav class="flex-1 overflow-y-auto px-1.5 py-2 space-y-0.5">
                @php
                    $groups = [
                        'Utama' => [
                            [
                                'route' => 'admin.dashboard',
                                'lucide' => 'layout-dashboard',
                                'label' => 'Dashboard',
                            ],
                            [
                                'route' => 'admin.pengajuan.index',
                                'lucide' => 'file-text',
                                'label' => 'Pengajuan',
                            ],
                            [
                                'route' => 'admin.tindakan-cepat.index',
                                'lucide' => 'zap',
                                'label' => 'Tindakan Cepat',
                            ],
                            [
                                'route' => 'admin.riwayat.index',
                                'lucide' => 'history',
                                'label' => 'Riwayat',
                            ],
                        ],
                        'Arsip Digital' => [
                            [
                                'route' => 'admin.guru-users.index',
                                'lucide' => 'users',
                                'label' => 'Akun Guru',
                            ],
                            [
                                'route' => 'admin.arsip-guru.index',
                                'lucide' => 'archive',
                                'label' => 'Arsip Guru',
                            ],
                            [
                                'route' => 'admin.laporan-arsip.guru',
                                'also'  => ['admin.laporan-arsip.madrasah'],
                                'lucide' => 'clipboard-check',
                                'label' => 'Kelengkapan Arsip',
                            ],
                            ['route' => 'admin.madrasah-users.index', 'lucide' => 'building-2', 'label' => 'Akun Madrasah'],
                            [
                                'route' => 'admin.arsip-madrasah.index',
                                'lucide' => 'archive-restore',
                                'label' => 'Arsip Madrasah',
                            ],
                            [
                                'route' => 'admin.kategori-arsip.index',
                                'lucide' => 'tags',
                                'label' => 'Kategori Arsip',
                            ],
                        ],
                        'Data & Konten' => [
                            [
                                'route' => 'admin.data-madrasah.index',
                                'lucide' => 'database',
                                'label' => 'Data Madrasah',
                            ],
                            [
                                'route' => 'admin.layanan.index',
                                'also'  => ['admin.layanan.create', 'admin.layanan.edit'],
                                'lucide' => 'briefcase',
                                'label' => 'Kelola Pelayanan',
                            ],
                            [
                                'route' => 'admin.download.index',
                                'lucide' => 'download',
                                'label' => 'File Download',
                            ],
                            [
                                'route' => 'admin.jenis-dokumen.index',
                                'lucide' => 'file-type',
                                'label' => 'Jenis Dokumen',
                            ],
                            [
                                'route' => 'admin.staff.index',
                                'lucide' => 'id-card',
                                'label' => 'Staff',
                            ],
                        ],
                        'Pengaturan' => [
                            [
                                'route' => 'admin.admin-users.index',
                                'lucide' => 'user-cog',
                                'label' => 'Kelola Admin',
                                'super_only' => true,
                            ],
                            [
                                'route' => 'admin.profil.index',
                                'lucide' => 'settings',
                                'label' => 'Profil Organisasi',
                            ],
                        ],
                    ];
                @endphp

                @foreach ($groups as $groupLabel => $items)
                    <div x-show="sidebar" x-transition class="nav-group-label">{{ $groupLabel }}</div>
                    <div x-show="!sidebar" class="my-2" style="border-top:1px solid rgba(255,255,255,0.05)"></div>

                    @foreach ($items as $menu)
                        @continue(($menu['super_only'] ?? false) && ! Auth::user()->isSuperAdmin())
                        @php $active = request()->routeIs($menu['route']); @endphp
                        <a href="{{ route($menu['route']) }}" class="nav-item {{ $active ? 'active' : '' }}"
                            title="{{ $menu['label'] }}">
                            <span class="nav-dot flex-shrink-0"></span>
                            <i data-lucide="{{ $menu['lucide'] }}" stroke-width="1.8" class="nav-icon w-3.5 h-3.5 flex-shrink-0"></i>
                            <span x-show="sidebar" x-transition class="truncate">{{ $menu['label'] }}</span>
                        </a>
                    @endforeach
                @endforeach
            </nav>

            {{-- Bottom --}}
            <div class="px-1.5 py-2 flex-shrink-0" style="border-top:1px solid rgba(255,255,255,0.05)">
                <a href="{{ route('home') }}" target="_blank" class="nav-item" title="Lihat Website">
                    <span class="nav-dot flex-shrink-0"></span>
                    <i data-lucide="external-link" stroke-width="1.8" class="nav-icon w-3.5 h-3.5 flex-shrink-0"></i>
                    <span x-show="sidebar" x-transition class="truncate">Lihat Website</span>
                </a>
            </div>
        </aside>

        {{-- ══ MAIN ══ --}}
        <div class="flex-1 flex flex-col overflow-hidden">

            {{-- Topbar --}}
            <header class="admin-topbar flex items-center justify-between px-5 flex-shrink-0"
                style="height:48px; background:linear-gradient(180deg,#ffffff,#fcfcfc); border-bottom:1px solid #ececec; box-shadow:0 1px 3px rgba(0,0,0,0.03), 0 4px 14px rgba(0,0,0,0.02);">
                <div class="flex items-center gap-3">
                    {{-- TOMBOL TUNGGAL: buka drawer di mobile, collapse sidebar di desktop.
                         Sengaja pakai x-show (dikontrol Alpine/JS) alih-alih class md:hidden,
                         supaya perilakunya tetap benar walau asset Tailwind (npm run build)
                         belum di-rebuild ulang — menghindari 2 ikon dobel muncul di mobile. --}}
                    <button @click="isDesktop ? (sidebar = !sidebar) : (mobileNav = true)"
                        x-show="isDesktop || !mobileNav" x-cloak
                        class="btn-icon" aria-label="Toggle menu">
                        <span class="inline-flex transition-transform duration-200" :class="!sidebar && 'rotate-90'">
                            <i data-lucide="menu" stroke-width="2" class="w-3.5 h-3.5"></i>
                        </span>
                    </button>
                    <span class="text-sm font-medium text-zinc-700">@yield('title', 'Dashboard')</span>
                </div>

                <div class="flex items-center gap-2" x-data="{ open: false }">
                    @yield('topbar-actions')
                    @hasSection('topbar-actions')
                        <div class="topbar-divider"></div>
                    @endif

                    {{-- Toggle mode gelap/terang --}}
                    <button onclick="toggleAdminTheme()" class="btn-icon" aria-label="Ganti tema" title="Ganti tema terang/gelap">
                        <i data-lucide="moon" stroke-width="2" class="w-3.5 h-3.5 theme-icon-moon"></i>
                        <i data-lucide="sun" stroke-width="2" class="w-3.5 h-3.5 theme-icon-sun" style="display:none"></i>
                    </button>
                    <div class="topbar-divider"></div>

                    <div class="relative">
                        <button @click="open = !open"
                            class="flex items-center gap-2 px-2 py-1 rounded-md text-zinc-600 hover:bg-zinc-100 transition">
                            <div
                                class="w-6 h-6 rounded-full flex items-center justify-center text-white text-xs font-semibold flex-shrink-0 bg-primary-700">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                            <span
                                class="hidden sm:block text-xs font-medium text-zinc-700">{{ auth()->user()->name }}</span>
                            <span class="inline-flex transition-transform duration-200" :class="open && 'rotate-180'">
                                <i data-lucide="chevron-down" stroke-width="2" class="w-3 h-3 text-zinc-400"></i>
                            </span>
                        </button>
                        <div x-show="open" @click.outside="open = false" x-transition
                            class="absolute right-0 mt-1.5 w-44 bg-white rounded-lg border border-zinc-100 py-1 z-50"
                            style="box-shadow:0 8px 24px rgba(0,0,0,0.08)">
                            <div class="px-3 py-2" style="border-bottom:1px solid #f5f5f5">
                                <p class="text-xs font-semibold text-zinc-800 truncate">{{ auth()->user()->name }}</p>
                                <p class="text-2xs text-zinc-400 truncate">{{ auth()->user()->email }}</p>
                            </div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                    class="flex items-center gap-2 w-full px-3 py-2 text-xs text-red-600 hover:bg-red-50 transition">
                                    <i data-lucide="log-out" stroke-width="2" class="w-3.5 h-3.5"></i>
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            {{-- Flash Messages → Toast (lihat partials.toast, di-include di atas) --}}
            @php
                $flashSuccess = session('success');
                $flashError = session('error');
                $flashWarning = session('warning');
                $flashInfo = session('info') ?? session('message');
                session()->forget(['success', 'error', 'warning', 'info', 'message']);
            @endphp
            @if($flashSuccess || $flashError || $flashWarning || $flashInfo)
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    @if($flashSuccess) toast(@js($flashSuccess), 'success'); @endif
                    @if($flashError) toast(@js($flashError), 'error'); @endif
                    @if($flashWarning) toast(@js($flashWarning), 'warning'); @endif
                    @if($flashInfo) toast(@js($flashInfo), 'info'); @endif
                });
            </script>
            @endif

            {{-- Content --}}
            <main class="flex-1 overflow-y-auto px-5 py-5">
                @yield('content')
            </main>
        </div>
    </div>

    <script>
        // ── Top loading bar (pengganti skeleton untuk app server-render/Blade) ──
        (function () {
            var bar = document.getElementById('admin-loadbar');
            if (!bar) return;
            function start() {
                bar.style.transition = 'none';
                bar.style.width = '0%';
                bar.style.opacity = '1';
                // force reflow biar transisi berikutnya kepakai
                bar.offsetHeight;
                bar.style.transition = 'width .5s ease, opacity .3s ease';
                bar.style.width = '30%';
                setTimeout(function () { bar.style.width = '75%'; }, 180);
            }
            document.addEventListener('click', function (e) {
                var a = e.target.closest('a[href]');
                if (!a || !a.closest('.admin-panel')) return;
                var href = a.getAttribute('href');
                if (!href || href.startsWith('#') || href.startsWith('javascript:')) return;
                if (a.target === '_blank' || a.hasAttribute('download')) return;
                if (e.metaKey || e.ctrlKey || e.shiftKey) return;
                start();
            });
            document.addEventListener('submit', function (e) {
                if (e.target.closest && e.target.closest('.admin-panel')) start();
            });
        })();

        // ── Count-up untuk angka besar di stat card ──
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.admin-panel .text-3xl.font-bold').forEach(function (el) {
                var raw = el.textContent.trim();
                if (!/^[\d.,]+$/.test(raw)) return;
                var target = parseInt(raw.replace(/[.,]/g, ''), 10);
                if (isNaN(target)) return;
                var duration = 650, start = null;
                function step(ts) {
                    if (!start) start = ts;
                    var progress = Math.min((ts - start) / duration, 1);
                    var eased = 1 - Math.pow(1 - progress, 3);
                    el.textContent = Math.round(eased * target).toLocaleString('en-US');
                    if (progress < 1) requestAnimationFrame(step);
                    else el.textContent = target.toLocaleString('en-US');
                }
                requestAnimationFrame(step);
            });
        });
    </script>

    @stack('scripts')
</body>

</html>
