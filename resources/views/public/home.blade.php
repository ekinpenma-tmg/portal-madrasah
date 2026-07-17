@extends('layouts.app')
@section('title', 'Beranda')

@push('styles')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@500;600;700;800&display=swap" rel="stylesheet">
    <style>
        /* ============================================================
           KONSEP B — PORTAL DIGITAL MODERN
           Emerald + lime, tipografi Sora untuk display, panel produk
           di hero, ticker status live, kartu gerbang gradient.
           ============================================================ */
        :root{
            --ink:#0b1a12;
            --emerald:#065f46;
            --emerald-2:#0a7a5a;
            --lime:#a3e635;
            --lime-2:#84cc16;
            --sub:#5b6b60;
            --line:#e4e9e5;
            --surface:#f6f8f6;
        }
        .display-b{ font-family:'Sora', sans-serif; }
        /* Navbar & footer kini di-restyle secara global lewat layouts/app.blade.php */

        .fade-up{ animation: fadeUp .6s ease both; }
        @keyframes fadeUp{ from{opacity:0; transform:translateY(14px);} to{opacity:1; transform:translateY(0);} }

        /* ticker */
        .ticker-wrap{ background: var(--ink); overflow:hidden; padding:9px 0; }
        .ticker{ display:flex; gap:48px; white-space:nowrap; animation:tickerScroll 26s linear infinite; width:max-content; }
        .ticker:hover{ animation-play-state: paused; }
        @keyframes tickerScroll{ from{transform:translateX(0);} to{transform:translateX(-50%);} }
        .ticker span{ font-size:12px; color:rgba(255,255,255,0.7); font-weight:500; display:flex; align-items:center; gap:8px; }
        .ticker .dot{ width:6px; height:6px; border-radius:50%; background:var(--lime); flex-shrink:0; }

        /* hero */
        .hero-b{ background: var(--surface); padding:72px 0 40px; position:relative; overflow:hidden; }
        .badge-b{ display:inline-flex; align-items:center; gap:8px; background:#dcfce7; color:var(--emerald); font-size:12px; font-weight:700; padding:7px 16px; border-radius:9999px; margin-bottom:22px; }
        .badge-b .dot{ width:6px; height:6px; border-radius:50%; background:var(--emerald-2); animation:pulseDot 1.6s ease-in-out infinite; }
        @keyframes pulseDot{ 0%,100%{opacity:1;} 50%{opacity:0.3;} }
        h1.hero-title-b{ font-family:'Sora',sans-serif; font-weight:800; font-size:44px; line-height:1.08; letter-spacing:-0.03em; color:#0b1a12; margin-bottom:20px; }
        h1.hero-title-b .accent{ color: var(--emerald-2); }
        .hero-b p.lead{ font-size:16px; color:var(--sub); line-height:1.65; max-width:480px; margin-bottom:30px; }
        .btn-primary-b{ background:var(--emerald); color:#fff; font-weight:700; font-size:14px; padding:15px 26px; border-radius:12px; display:inline-flex; align-items:center; gap:8px; box-shadow:0 10px 24px rgba(6,95,70,0.25); transition: all .2s ease; text-decoration:none; }
        .btn-primary-b:hover{ background:var(--emerald-2); color:#fff; transform:translateY(-2px); }
        .btn-secondary-b{ background:#fff; border:1px solid var(--line); color:#0b1a12; font-weight:600; font-size:14px; padding:15px 26px; border-radius:12px; display:inline-flex; align-items:center; gap:8px; transition:all .2s ease; text-decoration:none; }
        .btn-secondary-b:hover{ transform:translateY(-2px); border-color:var(--emerald); }
        .trust-b{ font-size:12.5px; color:var(--sub); display:flex; align-items:center; gap:8px; margin-top:22px; }

        .device{ background:#fff; border-radius:22px; border:1px solid var(--line); box-shadow:0 30px 60px -20px rgba(6,40,25,0.18); padding:20px; position:relative; }
        .device-top{ display:flex; justify-content:space-between; align-items:center; margin-bottom:16px; }
        .device-top p{ font-size:12px; font-weight:700; color:var(--sub); text-transform:uppercase; letter-spacing:0.05em; }
        .device-dots span{ width:7px; height:7px; border-radius:50%; background:var(--line); display:inline-block; margin-left:5px; }
        .mini-stat-grid{ display:grid; grid-template-columns:repeat(4,1fr); gap:8px; margin-bottom:14px; }
        .mini-stat{ background: var(--surface); border-radius:12px; padding:12px 10px; }
        .mini-stat .v{ font-family:'Sora',sans-serif; font-weight:700; font-size:20px; font-variant-numeric:tabular-nums; }
        .mini-stat .l{ font-size:9.5px; color:var(--sub); text-transform:uppercase; letter-spacing:0.03em; margin-top:2px; }
        .mini-list{ background: var(--surface); border-radius:12px; padding:6px; }
        .mini-row{ display:flex; align-items:center; gap:10px; padding:10px; border-radius:8px; }
        .mini-row:hover{ background:#fff; }
        .mini-row .ic{ width:30px; height:30px; border-radius:8px; background:var(--emerald); display:flex; align-items:center; justify-content:center; flex-shrink:0; }
        .mini-row .ic svg{ width:14px; height:14px; color:var(--lime); }
        .mini-row .tt{ font-size:12.5px; font-weight:600; }
        .mini-row .tt2{ font-size:10.5px; color:var(--sub); }
        .mini-row .st{ margin-left:auto; font-size:10px; font-weight:700; padding:3px 9px; border-radius:9999px; white-space:nowrap; }
        .st-ok{ background:#dcfce7; color:var(--emerald); }
        .st-pending{ background:#fef3c7; color:#b45309; }
        .float-card{ position:absolute; background:#fff; border:1px solid var(--line); border-radius:14px; padding:12px 16px; box-shadow:0 16px 32px rgba(6,40,25,0.15); display:flex; align-items:center; gap:10px; }
        .float-card.one{ top:-16px; right:-18px; }
        .float-card.two{ bottom:-14px; left:-16px; }
        .float-card .ic2{ width:28px; height:28px; border-radius:8px; background:var(--lime); display:flex; align-items:center; justify-content:center; flex-shrink:0; }
        .float-card .ic2 svg{ width:14px; height:14px; color:var(--ink); }
        .float-card p{ font-size:11.5px; font-weight:700; }
        .float-card span{ font-size:9.5px; color:var(--sub); }

        .eyebrow-b{ display:inline-flex; align-items:center; gap:8px; background:#fff; border:1px solid var(--line); font-size:11.5px; font-weight:700; color:var(--emerald); padding:6px 14px; border-radius:9999px; margin-bottom:18px; text-transform:uppercase; letter-spacing:0.05em; }
        h2.sec-title-b{ font-family:'Sora',sans-serif; font-weight:800; font-size:32px; letter-spacing:-0.02em; color:#0b1a12; }
        .sec-desc-b{ color:var(--sub); font-size:15px; line-height:1.7; }

        .fcard-b{ background:#fff; border:1px solid var(--line); border-radius:18px; padding:30px; transition:transform .2s, box-shadow .2s; }
        .fcard-b:hover{ transform:translateY(-4px); box-shadow:0 20px 40px -14px rgba(6,40,25,0.15); }
        .fcard-b .fi{ width:46px; height:46px; border-radius:12px; background:var(--emerald); display:flex; align-items:center; justify-content:center; margin-bottom:18px; }
        .fcard-b .fi svg{ width:22px; height:22px; color:var(--lime); }
        .fcard-b h3{ font-family:'Sora',sans-serif; font-weight:700; font-size:16.5px; margin-bottom:8px; color:#0b1a12; }
        .fcard-b p{ font-size:13.5px; color:var(--sub); line-height:1.65; }

        .doc-chip-b{ background:#fff; border:1px solid var(--line); border-radius:16px; padding:22px; transition: border-color .2s, transform .2s; text-decoration:none; display:block; }
        .doc-chip-b:hover{ border-color:var(--emerald-2); transform:translateY(-3px); }
        .doc-chip-b .code{ font-family:'Sora',sans-serif; font-weight:800; font-size:22px; color:var(--emerald); margin-bottom:10px; }
        .doc-chip-b .tag{ font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:0.05em; color:var(--sub); background:var(--surface); display:inline-block; padding:4px 10px; border-radius:9999px; margin-bottom:12px; }
        .doc-chip-b h4{ font-size:13.5px; font-weight:700; color:#0b1a12; }

        .gate-section-b{ background:var(--ink); border-radius:32px; padding:60px 44px; color:#fff; }
        .gate-section-b .eyebrow-b{ background:rgba(255,255,255,0.08); border-color:rgba(255,255,255,0.14); color:var(--lime); }
        .gate-section-b h2{ color:#fff; }
        .gate-section-b .sec-desc-b{ color:rgba(255,255,255,0.55); }
        .gate2-b{ display:grid; grid-template-columns:1fr 1fr; gap:18px; margin-top:44px; }
        .gcard-b{ background:rgba(255,255,255,0.05); border:1px solid rgba(255,255,255,0.1); border-radius:20px; padding:34px; }
        .gcard-b.highlight{ background:linear-gradient(155deg, var(--emerald), var(--emerald-2)); border-color:transparent; }
        .gcard-b .gi{ width:44px; height:44px; border-radius:12px; background:var(--lime); display:flex; align-items:center; justify-content:center; margin-bottom:20px; }
        .gcard-b .gi svg{ width:20px; height:20px; color:var(--ink); }
        .gcard-b h3{ font-family:'Sora',sans-serif; font-size:20px; font-weight:700; margin-bottom:10px; }
        .gcard-b p{ font-size:13.5px; color:rgba(255,255,255,0.6); line-height:1.65; margin-bottom:20px; }
        .gcard-b ul{ margin-bottom:22px; }
        .gcard-b li{ font-size:12px; color:rgba(255,255,255,0.6); padding:4px 0; display:flex; gap:8px; align-items:flex-start; }
        .gcard-b li svg{ width:13px; height:13px; color:var(--lime); flex-shrink:0; margin-top:2px; }
        .btn-lime-b{ background:var(--lime); color:var(--ink); font-weight:700; font-size:13.5px; padding:13px 24px; border-radius:9999px; display:inline-flex; gap:8px; align-items:center; text-decoration:none; }
        .btn-ghost-b{ background:rgba(255,255,255,0.08); border:1px solid rgba(255,255,255,0.16); color:#fff; font-weight:700; font-size:13.5px; padding:13px 24px; border-radius:9999px; display:inline-flex; gap:8px; align-items:center; text-decoration:none; }

        @media (max-width:860px){
            h1.hero-title-b{ font-size:32px; }
            .mini-stat-grid{ grid-template-columns:repeat(2,1fr); }
            .gate2-b{ grid-template-columns:1fr; }
            .gate-section-b{ padding:40px 22px; border-radius:20px; }
        }
    </style>
@endpush

@section('content')

    {{-- ══════════ TICKER STATUS LIVE ══════════ --}}
    @php
        $totalAjuan   = \App\Models\Pengajuan::count();
        $pendingAjuan = \App\Models\Pengajuan::pending()->count();
        $diterimaAjuan = \App\Models\Pengajuan::diterima()->count();
        $ditolakAjuan = \App\Models\Pengajuan::ditolak()->count();
    @endphp
    <div class="ticker-wrap">
        @php
            $tickerItems = [
                $totalAjuan . ' pengajuan tercatat di portal ini',
                $diterimaAjuan . ' dokumen sudah selesai diverifikasi',
                $pendingAjuan . ' pengajuan sedang menunggu proses',
                'Arsip digital tersinkron via Google Drive',
            ];
        @endphp
        <div class="ticker">
            @for ($i = 0; $i < 2; $i++)
                @foreach ($tickerItems as $t)
                    <span><span class="dot"></span>{{ $t }}</span>
                @endforeach
            @endfor
        </div>
    </div>

    {{-- ══════════ HERO ══════════ --}}
    <section class="hero-b">
        <div class="max-w-7xl mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-10 items-center">
                <div class="fade-up">
                    <div class="badge-b"><span class="dot"></span>Layanan Online Resmi</div>
                    <h1 class="hero-title-b">Administrasi madrasah, <span class="accent">selesai dari satu layar.</span></h1>
                    <p class="lead">Arsip dokumen digital dan pengajuan berkas guru &amp; madrasah — dalam satu akun, terpantau real-time, tanpa bolak-balik kantor.</p>
                    <div class="flex flex-wrap gap-3">
                        <a href="#portal-akses" class="btn-primary-b">
                            Masuk ke Portal
                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" /></svg>
                        </a>
                        <a href="{{ route('status.index') }}" class="btn-secondary-b">Cek Status Ajuan</a>
                    </div>
                    <div class="trust-b">
                        <svg width="15" height="15" fill="none" stroke="#065f46" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        Digunakan oleh guru &amp; madrasah binaan Kemenag Temanggung
                    </div>
                </div>

                <div class="fade-up" style="animation-delay:.1s; position:relative; padding:20px;">
                    <div class="device">
                        <div class="device-top">
                            <p>Ringkasan Pengajuan</p>
                            <div class="device-dots"><span></span><span></span><span></span></div>
                        </div>
                        <div class="mini-stat-grid">
                            <div class="mini-stat"><div class="v">{{ $totalAjuan }}</div><div class="l">Total</div></div>
                            <div class="mini-stat"><div class="v">{{ $pendingAjuan }}</div><div class="l">Proses</div></div>
                            <div class="mini-stat"><div class="v">{{ $diterimaAjuan }}</div><div class="l">Diterima</div></div>
                            <div class="mini-stat"><div class="v">{{ $ditolakAjuan }}</div><div class="l">Ditolak</div></div>
                        </div>
                        <div class="mini-list">
                            <div class="mini-row">
                                <div class="ic"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg></div>
                                <div><div class="tt">Ijazah S1 PAI</div><div class="tt2">Ijazah &amp; Sertifikat</div></div>
                                <div class="st st-ok">Selesai</div>
                            </div>
                            <div class="mini-row">
                                <div class="ic"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg></div>
                                <div><div class="tt">SK Tugas 2024</div><div class="tt2">SK Mengajar</div></div>
                                <div class="st st-ok">Selesai</div>
                            </div>
                            <div class="mini-row">
                                <div class="ic"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg></div>
                                <div><div class="tt">Serdik Kemenag</div><div class="tt2">Sertifikat Pendidik</div></div>
                                <div class="st st-pending">Pending</div>
                            </div>
                        </div>
                        <div class="float-card one">
                            <div class="ic2"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" /></svg></div>
                            <div><p>Terverifikasi</p><span>Diperiksa admin</span></div>
                        </div>
                        <div class="float-card two">
                            <div class="ic2"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg></div>
                            <div><p>Real-time</p><span>Update status ajuan</span></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ══════════ FITUR UTAMA ══════════ --}}
    <section class="max-w-7xl mx-auto px-4 py-20">
        <div class="text-center mb-12">
            <div class="eyebrow-b">Fitur Utama</div>
            <h2 class="sec-title-b mb-3">Semua kebutuhan administrasi, satu portal</h2>
            <p class="sec-desc-b max-w-xl mx-auto">Dua layanan inti yang bisa diakses guru dan madrasah setelah masuk ke akun masing-masing.</p>
        </div>

        @php
            $fiturUtama = [
                [
                    'title' => 'Arsip Digital',
                    'desc' => 'Simpan dan kelola dokumen kepegawaian maupun kelembagaan secara digital — ijazah, SK, sertifikat, akreditasi, dan berkas penting lainnya, tanpa arsip fisik manual.',
                    'icon' => 'M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4',
                ],
                [
                    'title' => 'Pengajuan Dokumen',
                    'desc' => 'Ajukan dokumen resmi langsung dari akun Anda — data pengaju otomatis terisi, status dapat dipantau real-time hingga selesai diverifikasi admin.',
                    'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2',
                ],
            ];
        @endphp

        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            @foreach ($fiturUtama as $f)
                <div class="fcard-b">
                    <div class="fi">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="{{ $f['icon'] }}" />
                        </svg>
                    </div>
                    <h3>{{ $f['title'] }}</h3>
                    <p>{{ $f['desc'] }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- ══════════ JENIS DOKUMEN ══════════ --}}
    @if ($jenisDokumen->count())
        <section class="max-w-7xl mx-auto px-4 pb-20">
            <div class="text-center mb-12">
                <div class="eyebrow-b">Jenis Dokumen</div>
                <h2 class="sec-title-b mb-3">Pilih dokumen yang ingin diajukan</h2>
                <p class="sec-desc-b max-w-xl mx-auto">Klik salah satu untuk masuk ke portal yang sesuai dan mulai mengajukan.</p>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @foreach ($jenisDokumen->take(8) as $jd)
                    @php
                        $tagLabel = match ($jd->untuk ?? 'semua') {
                            'guru' => 'Khusus Guru',
                            'madrasah' => 'Khusus Madrasah',
                            default => 'Guru & Madrasah',
                        };
                    @endphp
                    <a href="{{ route('pengajuan.form', $jd->id) }}" class="doc-chip-b">
                        <div class="code">S{{ str_pad($jd->id, 2, '0', STR_PAD_LEFT) }}</div>
                        <div class="tag">{{ $tagLabel }}</div>
                        <h4>{{ $jd->nama }}</h4>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    {{-- ══════════ PILIH PORTAL (GERBANG LOGIN) ══════════ --}}
    <section id="portal-akses" class="max-w-7xl mx-auto px-4 pb-24">
        <div class="gate-section-b">
            <div class="eyebrow-b">Mulai di Sini</div>
            <h2 class="sec-title-b mb-3">Pilih portal Anda</h2>
            <p class="sec-desc-b max-w-xl">Seluruh layanan arsip digital dan pengajuan dokumen kini dilakukan melalui akun resmi. Masuk sesuai peran Anda untuk melanjutkan.</p>

            <div class="gate2-b">
                {{-- Portal Guru --}}
                <div class="gcard-b highlight">
                    <div class="gi">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" /></svg>
                    </div>
                    <h3>Portal Guru</h3>
                    <p>Arsip dokumen pribadi dan pengajuan berkas kepegawaian — ijazah, SK mengajar, sertifikat pendidik, dan berkas lainnya.</p>
                    <ul>
                        <li><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" /></svg>Data pengaju otomatis terisi</li>
                        <li><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" /></svg>Riwayat pengajuan tersimpan</li>
                        <li><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" /></svg>Arsip digital via link Google Drive</li>
                    </ul>
                    <a href="{{ route('guru.login') }}" class="btn-lime-b">
                        Masuk Portal Guru
                        <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" /></svg>
                    </a>
                </div>

                {{-- Portal Madrasah --}}
                @if (Route::has('madrasah.login'))
                    <div class="gcard-b">
                        <div class="gi">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                        </div>
                        <h3>Portal Madrasah</h3>
                        <p>Kelola arsip kelembagaan dan ajukan berkas resmi — izin operasional, akreditasi, data siswa, dan berkas lainnya.</p>
                        <ul>
                            <li><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" /></svg>Data lembaga otomatis terisi</li>
                            <li><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" /></svg>Arsip digital via link Google Drive</li>
                        </ul>
                        <a href="{{ route('madrasah.login') }}" class="btn-ghost-b">
                            Masuk Portal Madrasah
                            <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" /></svg>
                        </a>
                    </div>
                @endif
            </div>

            <p class="text-center text-xs mt-8" style="color:rgba(255,255,255,0.4);">
                Belum punya akun Guru/Madrasah? Hubungi admin Seksi Pendidikan Madrasah untuk didaftarkan.
            </p>
        </div>
    </section>

@endsection
