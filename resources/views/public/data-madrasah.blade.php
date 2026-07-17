@extends('layouts.app')
@section('title', 'Data Madrasah')

@push('styles')
<style>
    /* ═══════════════════════════════════════════
       PALETTE — Konsep B (emerald + lime), selaras
       dengan Beranda / Profil / Download / Status
    ═══════════════════════════════════════════ */
    :root {
        --green-900: #0b1a12;
        --green-800: #065f46;
        --green-700: #0a7a5a;
        --green-600: #16966b;
        --green-500: #22b37c;
        --green-100: #eafbe7;
        --gold:      #a3e635;
        --gold-light:#d4f299;
    }

    /* ── HERO ── */
    .hero-data {
        background: linear-gradient(135deg, var(--green-900) 0%, var(--green-700) 55%, var(--green-600) 100%);
        position: relative;
        overflow: hidden;
        padding: 3.5rem 0 3rem;
    }
    /* Dekoratif lingkaran transparan */
    .hero-data::before {
        content: '';
        position: absolute;
        width: 500px; height: 500px;
        border-radius: 50%;
        border: 60px solid rgba(255,255,255,0.04);
        top: -180px; right: -120px;
        pointer-events: none;
    }
    .hero-data::after {
        content: '';
        position: absolute;
        width: 280px; height: 280px;
        border-radius: 50%;
        border: 40px solid rgba(255,255,255,0.04);
        bottom: -100px; left: -60px;
        pointer-events: none;
    }
    .hero-dot-pattern {
        position: absolute;
        inset: 0;
        background-image: radial-gradient(rgba(255,255,255,0.06) 1px, transparent 1px);
        background-size: 28px 28px;
        pointer-events: none;
    }

    /* Breadcrumb */
    .breadcrumb-sep { color: rgba(255,255,255,0.35); margin: 0 6px; }

    /* Summary pills di hero */
    .hero-pill {
        background: rgba(255,255,255,0.10);
        backdrop-filter: blur(8px);
        border: 1px solid rgba(255,255,255,0.15);
        border-radius: 1rem;
        padding: 0.85rem 1.4rem;
        text-align: center;
        transition: background 0.2s;
    }
    .hero-pill:hover { background: rgba(255,255,255,0.16); }

    /* ── SECTION TITLE ── */
    .section-title {
        font-size: 1.15rem;
        font-weight: 800;
        color: var(--green-800);
        display: flex;
        align-items: center;
        gap: 0.6rem;
        margin-bottom: 1.25rem;
    }
    .section-title::before {
        content: '';
        display: inline-block;
        width: 4px; height: 1.2rem;
        border-radius: 99px;
        background: linear-gradient(to bottom, var(--gold), var(--green-600));
        flex-shrink: 0;
    }

    /* ── STAT CARDS PER JENJANG ── */
    .stat-card {
        background: white;
        border-radius: 1.1rem;
        padding: 1.4rem 1.25rem 1.1rem;
        box-shadow: 0 2px 16px rgba(13,74,47,0.08);
        border-top: 4px solid;
        transition: transform 0.2s, box-shadow 0.2s;
        position: relative;
        overflow: hidden;
    }
    .stat-card::after {
        content: '';
        position: absolute;
        width: 80px; height: 80px;
        border-radius: 50%;
        background: currentColor;
        opacity: 0.04;
        bottom: -20px; right: -20px;
    }
    .stat-card:hover { transform: translateY(-4px); box-shadow: 0 10px 32px rgba(13,74,47,0.14); }
    .stat-card.ra    { border-color: #f59e0b; }
    .stat-card.mi    { border-color: var(--green-600); }
    .stat-card.mts   { border-color: #6366f1; }
    .stat-card.ma    { border-color: var(--gold); }

    .stat-icon {
        width: 44px; height: 44px;
        border-radius: 0.75rem;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.3rem;
        flex-shrink: 0;
    }

    /* ── PROGRESS BAR ── */
    .progress-bar {
        height: 7px;
        border-radius: 99px;
        background: #e8f0ec;
        overflow: hidden;
    }
    .progress-fill {
        height: 100%;
        border-radius: 99px;
        transition: width 1s cubic-bezier(.4,0,.2,1);
    }

    /* ── DONUT ── */
    .donut {
        width: 120px; height: 120px;
        border-radius: 50%;
        position: relative; flex-shrink: 0;
    }
    .donut-hole {
        position: absolute; inset: 20px;
        background: white; border-radius: 50%;
        display: flex; align-items: center; justify-content: center; flex-direction: column;
        box-shadow: inset 0 2px 8px rgba(0,0,0,0.06);
    }

    /* ── CHART CARD (Status / Akreditasi / Kecamatan) ── */
    .chart-card {
        background: white;
        border-radius: 1.1rem;
        padding: 1.4rem;
        box-shadow: 0 2px 16px rgba(13,74,47,0.08);
        border-top: 3px solid var(--green-700);
    }

    /* ── SISWA CARD ── */
    .siswa-card {
        background: white;
        border-radius: 1rem;
        padding: 1.25rem;
        box-shadow: 0 2px 12px rgba(13,74,47,0.07);
        border-top: 4px solid;
    }

    /* ── FILTER BAR ── */
    .filter-bar {
        background: white;
        border-radius: 1rem;
        padding: 1.1rem 1.4rem;
        box-shadow: 0 2px 14px rgba(13,74,47,0.08);
        margin-bottom: 1.25rem;
        border: 1px solid rgba(27,107,58,0.08);
    }
    .filter-bar input,
    .filter-bar select {
        border: 1.5px solid #d1e8da;
        border-radius: 0.6rem;
        padding: 0.45rem 0.75rem;
        font-size: 0.85rem;
        background: #f8fdf9;
        transition: border-color 0.2s, box-shadow 0.2s;
        outline: none;
    }
    .filter-bar input:focus,
    .filter-bar select:focus {
        border-color: var(--green-600);
        box-shadow: 0 0 0 3px rgba(45,134,83,0.12);
    }
    .btn-cari {
        background: linear-gradient(135deg, var(--green-700), var(--green-600));
        color: white;
        padding: 0.5rem 1.2rem;
        border-radius: 0.6rem;
        font-size: 0.85rem;
        font-weight: 700;
        border: none;
        cursor: pointer;
        transition: opacity 0.2s, transform 0.15s;
        box-shadow: 0 2px 8px rgba(27,107,58,0.25);
    }
    .btn-cari:hover { opacity: 0.88; transform: translateY(-1px); }

    /* ── TABEL ── */
    .tbl-madrasah { width: 100%; border-collapse: collapse; }
    .tbl-madrasah thead th {
        background: linear-gradient(135deg, var(--green-800), var(--green-700));
        color: white;
        font-size: 0.72rem;
        text-transform: uppercase;
        letter-spacing: 0.07em;
        padding: 0.8rem 1rem;
        font-weight: 700;
    }
    .tbl-madrasah thead th:first-child { border-radius: 0; }
    .tbl-madrasah tbody tr { border-bottom: 1px solid #edf3ef; transition: background 0.15s; }
    .tbl-madrasah tbody tr:hover { background: #f0f9f3; }
    .tbl-madrasah tbody td { padding: 0.8rem 1rem; font-size: 0.85rem; vertical-align: middle; }

    /* ── BADGE ── */
    .badge-jenjang {
        padding: 3px 10px; border-radius: 99px;
        font-size: 0.7rem; font-weight: 800; letter-spacing: 0.04em;
        display: inline-block;
    }
    .badge-ra  { background: #fef3c7; color: #92400e; }
    .badge-mi  { background: #d1fae5; color: #065f46; }
    .badge-mts { background: #ede9fe; color: #4c1d95; }
    .badge-ma  { background: #fef9c3; color: #713f12; border: 1px solid #fcd34d; }

    .badge-akr {
        padding: 3px 10px; border-radius: 99px;
        font-size: 0.7rem; font-weight: 800;
        display: inline-block;
    }
    .badge-a  { background: #d1fae5; color: #065f46; }
    .badge-b  { background: #dbeafe; color: #1e40af; }
    .badge-c  { background: #fef3c7; color: #92400e; }
    .badge-tt { background: #f3f4f6; color: #6b7280; }

    .badge-status {
        padding: 3px 10px; border-radius: 99px;
        font-size: 0.7rem; font-weight: 700;
        display: inline-block;
    }
    .badge-negeri  { background: #dcfce7; color: #166534; }
    .badge-swasta  { background: #f0f9ff; color: #0c4a6e; }

    /* ── PAGINATION ── */
    .pagination-wrap { padding: 1rem 1.25rem; border-top: 1px solid #edf3ef; display: flex; align-items: center; justify-content: space-between; }

    /* ── EMPTY STATE ── */
    .empty-state { padding: 5rem 1rem; text-align: center; }
    .empty-icon { font-size: 3.5rem; margin-bottom: 1rem; }
</style>
@endpush

@section('content')

{{-- ════════════════════════════════════════
     HERO
════════════════════════════════════════ --}}
<div class="hero-data ">
    <div class="hero-dot-pattern"></div>
    <div class="max-w-7xl mx-auto px-4 relative z-10 text-center">

        {{-- Judul --}}
        <h1 class="text-3xl md:text-4xl font-extrabold text-white mb-2 leading-tight">
            Data Madrasah
        </h1>
        <p class="text-white/65 text-sm max-w-2xl leading-relaxed mx-auto">
            Data madrasah di bawah pembinaan Seksi Pendidikan Madrasah Kemenag Kabupaten Temanggung.
            @if($tahunData)
                Pembaruan terakhir:
                <strong class="text-white font-bold">{{ $tahunData }}</strong>
            @endif
        </p>

        {{-- Summary Pills --}}
        <div class="flex flex-wrap gap-3 mt-6 justify-center">
            <div class="hero-pill">
                <div class="text-2xl font-extrabold text-white tracking-tight">{{ number_format($totalMadrasah) }}</div>
                <div class="text-white/55 text-xs mt-0.5 font-medium">Total Madrasah</div>
            </div>
            @if($totalSiswa > 0)
            <div class="hero-pill">
                <div class="text-2xl font-extrabold text-white tracking-tight">{{ number_format($totalSiswa) }}</div>
                <div class="text-white/55 text-xs mt-0.5 font-medium">Total Siswa ({{ $tahunPelajaran }})</div>
            </div>
            @endif
            <div class="hero-pill">
                <div class="text-2xl font-extrabold text-white tracking-tight">{{ count($rekapKecamatan) }}</div>
                <div class="text-white/55 text-xs mt-0.5 font-medium">Kecamatan</div>
            </div>
            @if(!empty($rekapStatus))
            <div class="hero-pill">
                <div class="text-2xl font-extrabold text-white tracking-tight">{{ $rekapStatus['Negeri'] ?? 0 }}</div>
                <div class="text-white/55 text-xs mt-0.5 font-medium">Madrasah Negeri</div>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- ════════════════════════════════════════
     MAIN CONTENT
════════════════════════════════════════ --}}
<div class="max-w-7xl mx-auto px-4 py-10">

    {{-- ── STAT CARDS PER JENJANG ── --}}
    <p class="section-title">Rekap Per Jenjang</p>

    @php
        $jenjangConfig = [
            'RA'  => ['label' => 'RA / BA', 'css' => 'ra',  'icon' => '🏫', 'color' => '#f59e0b',  'bg' => '#fef3c7'],
            'MI'  => ['label' => 'MI',       'css' => 'mi',  'icon' => '🎒', 'color' => '#2d8653',  'bg' => '#d1fae5'],
            'MTs' => ['label' => 'MTs',      'css' => 'mts', 'icon' => '📚', 'color' => '#6366f1',  'bg' => '#ede9fe'],
            'MA'  => ['label' => 'MA',        'css' => 'ma',  'icon' => '🎓', 'color' => '#d4af37',  'bg' => '#fef9c3'],
        ];
    @endphp

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-10">
        @foreach($jenjangConfig as $key => $cfg)
        @php
            $jumlah = $rekapJenjang[$key] ?? 0;
            $siswa  = $rekapSiswa[$key]   ?? null;
            $pct    = $totalMadrasah > 0 ? round($jumlah / $totalMadrasah * 100) : 0;
        @endphp
        <div class="stat-card {{ $cfg['css'] }}">
            <div class="flex items-start justify-between mb-3">
                <div>
                    <p class="text-xs text-gray-400 font-bold uppercase tracking-widest mb-1">{{ $cfg['label'] }}</p>
                    <p class="text-3xl font-extrabold text-gray-800 leading-none">{{ number_format($jumlah) }}</p>
                    <p class="text-xs text-gray-400 mt-1">madrasah</p>
                </div>
                <div class="stat-icon" style="background:{{ $cfg['bg'] }}">
                    <span>{{ $cfg['icon'] }}</span>
                </div>
            </div>
            <div class="progress-bar mb-2">
                <div class="progress-fill" style="width:{{ $pct }}%; background:{{ $cfg['color'] }}"></div>
            </div>
            <div class="flex justify-between text-xs text-gray-400">
                <span class="font-semibold" style="color:{{ $cfg['color'] }}">{{ $pct }}% dari total</span>
                @if($siswa)
                <span>{{ number_format($siswa['total']) }} siswa</span>
                @endif
            </div>
        </div>
        @endforeach
    </div>

    {{-- ── BARIS CHART: STATUS / AKREDITASI / KECAMATAN ── --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-10">

        {{-- Status Negeri vs Swasta --}}
        <div class="chart-card">
            <p class="section-title" style="font-size:1rem; margin-bottom:1rem">Status Madrasah</p>
            @php
                $negeri = $rekapStatus['Negeri'] ?? 0;
                $swasta = $rekapStatus['Swasta'] ?? 0;
                $pctN   = $totalMadrasah > 0 ? round($negeri / $totalMadrasah * 100) : 0;
                $pctS   = 100 - $pctN;
                $donut  = "conic-gradient(var(--green-600) 0% {$pctN}%, #e5e7eb {$pctN}% 100%)";
            @endphp
            <div class="flex items-center gap-5 mt-2">
                <div class="donut" style="background:{{ $donut }}">
                    <div class="donut-hole">
                        <span class="text-sm font-extrabold text-gray-700">{{ $pctN }}%</span>
                        <span class="text-xs text-gray-400">Negeri</span>
                    </div>
                </div>
                <div class="flex-1 space-y-3">
                    <div>
                        <div class="flex justify-between text-sm mb-1.5">
                            <span class="flex items-center gap-1.5 text-gray-600 font-medium">
                                <span class="w-2.5 h-2.5 rounded-full inline-block" style="background:var(--green-600)"></span>Negeri
                            </span>
                            <span class="font-bold text-gray-800">{{ $negeri }}</span>
                        </div>
                        <div class="progress-bar"><div class="progress-fill" style="width:{{ $pctN }}%;background:var(--green-600)"></div></div>
                    </div>
                    <div>
                        <div class="flex justify-between text-sm mb-1.5">
                            <span class="flex items-center gap-1.5 text-gray-600 font-medium">
                                <span class="w-2.5 h-2.5 rounded-full bg-gray-300 inline-block"></span>Swasta
                            </span>
                            <span class="font-bold text-gray-800">{{ $swasta }}</span>
                        </div>
                        <div class="progress-bar"><div class="progress-fill bg-gray-300" style="width:{{ $pctS }}%"></div></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Akreditasi --}}
        <div class="chart-card">
            <p class="section-title" style="font-size:1rem; margin-bottom:1rem">Akreditasi</p>
            @php
                $akrConfig = [
                    'A'  => ['color' => 'bg-emerald-500', 'hex' => '#10b981', 'label' => 'A — Unggul'],
                    'B'  => ['color' => 'bg-blue-500',    'hex' => '#3b82f6', 'label' => 'B — Baik'],
                    'C'  => ['color' => 'bg-amber-400',   'hex' => '#fbbf24', 'label' => 'C — Cukup'],
                ];
            @endphp
            <div class="space-y-3 mt-1">
                @foreach($rekapAkreditasi as $akr => $jml)
                @php $pctA = $totalMadrasah > 0 ? round($jml / $totalMadrasah * 100) : 0; @endphp
                <div>
                    <div class="flex justify-between text-sm mb-1.5">
                        <span class="font-medium text-gray-600">{{ $akrConfig[$akr]['label'] ?? $akr }}</span>
                        <span class="font-bold text-gray-800">{{ $jml }} <span class="text-gray-400 text-xs font-normal">({{ $pctA }}%)</span></span>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill {{ $akrConfig[$akr]['color'] ?? 'bg-gray-400' }}" style="width:{{ $pctA }}%"></div>
                    </div>
                </div>
                @endforeach
                @php $ttlAkr = $rekapAkreditasi['TT'] ?? ($totalMadrasah - array_sum(array_intersect_key($rekapAkreditasi, $akrConfig))); @endphp
                @if($ttlAkr > 0)
                @php $pctTt = $totalMadrasah > 0 ? round($ttlAkr / $totalMadrasah * 100) : 0; @endphp
                <div>
                    <div class="flex justify-between text-sm mb-1.5">
                        <span class="font-medium text-gray-400">Belum Terakreditasi</span>
                        <span class="font-bold text-gray-500">{{ $ttlAkr }} <span class="text-gray-400 text-xs font-normal">({{ $pctTt }}%)</span></span>
                    </div>
                    <div class="progress-bar"><div class="progress-fill bg-gray-300" style="width:{{ $pctTt }}%"></div></div>
                </div>
                @endif
            </div>
        </div>

        {{-- Sebaran Kecamatan --}}
        <div class="chart-card">
            <p class="section-title" style="font-size:1rem; margin-bottom:1rem">Sebaran Kecamatan</p>
            @php $maxKec = max(array_values($rekapKecamatan) ?: [1]); @endphp
            <div class="space-y-2 mt-1">
                @foreach(array_slice($rekapKecamatan, 0, 7, true) as $kec => $jml)
                @php $pctKec = round($jml / $maxKec * 100); @endphp
                <div class="flex items-center gap-2">
                    <span class="text-xs text-gray-500 w-28 truncate font-medium" title="{{ $kec }}">{{ $kec }}</span>
                    <div class="flex-1 progress-bar">
                        <div class="progress-fill" style="width:{{ $pctKec }}%; background: linear-gradient(to right, var(--green-700), var(--green-500))"></div>
                    </div>
                    <span class="text-xs font-bold text-gray-700 w-6 text-right">{{ $jml }}</span>
                </div>
                @endforeach
                @if(count($rekapKecamatan) > 7)
                <p class="text-xs text-gray-400 mt-1 pt-1 border-t border-gray-100">+{{ count($rekapKecamatan) - 7 }} kecamatan lainnya</p>
                @endif
            </div>
        </div>
    </div>

    {{-- ── DATA SISWA PER JENJANG ── --}}
    @if(!empty($rekapSiswa))
    <p class="section-title">Data Siswa — Tahun Pelajaran {{ $tahunPelajaran }}</p>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-10">
        @foreach($jenjangConfig as $key => $cfg)
        @php $siswa = $rekapSiswa[$key] ?? null; @endphp
        @if($siswa)
        <div class="siswa-card" style="border-color:{{ $cfg['color'] }}">
            <div class="flex items-center justify-between mb-2">
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">{{ $cfg['label'] }}</p>
                <span class="text-lg">{{ $cfg['icon'] }}</span>
            </div>
            <p class="text-2xl font-extrabold text-gray-800">{{ number_format($siswa['total']) }}</p>
            <p class="text-xs text-gray-400 mb-3">total siswa</p>
            <div class="flex gap-4 text-xs border-t border-gray-100 pt-2 mt-1">
                <div class="flex items-center gap-1">
                    <span class="w-2 h-2 rounded-full bg-blue-400 inline-block"></span>
                    <span class="text-gray-500">L: <strong class="text-gray-700">{{ number_format($siswa['total_laki']) }}</strong></span>
                </div>
                <div class="flex items-center gap-1">
                    <span class="w-2 h-2 rounded-full bg-pink-400 inline-block"></span>
                    <span class="text-gray-500">P: <strong class="text-gray-700">{{ number_format($siswa['total_perempuan']) }}</strong></span>
                </div>
            </div>
        </div>
        @endif
        @endforeach
    </div>
    @endif

    {{-- ── DAFTAR MADRASAH ── --}}
    <p class="section-title">Daftar Madrasah</p>

    {{-- Filter Bar --}}
    <div class="filter-bar">
        <form id="form-filter" class="flex flex-wrap gap-3 items-end">
            <div>
                <label class="block text-xs text-gray-500 mb-1 font-bold">Cari Madrasah</label>
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Nama / NSM / Kecamatan..."
                    class="w-56">
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1 font-bold">Jenjang</label>
                <select name="jenjang">
                    <option value="">Semua Jenjang</option>
                    @foreach(['RA','MI','MTs','MA'] as $j)
                    <option value="{{ $j }}" {{ request('jenjang') == $j ? 'selected' : '' }}>{{ $j }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1 font-bold">Kecamatan</label>
                <select name="kecamatan">
                    <option value="">Semua Kecamatan</option>
                    @foreach($daftarKecamatan as $kec)
                    <option value="{{ $kec }}" {{ request('kecamatan') == $kec ? 'selected' : '' }}>{{ $kec }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1 font-bold">Status</label>
                <select name="status">
                    <option value="">Semua Status</option>
                    <option value="Negeri" {{ request('status') == 'Negeri' ? 'selected' : '' }}>Negeri</option>
                    <option value="Swasta" {{ request('status') == 'Swasta' ? 'selected' : '' }}>Swasta</option>
                </select>
            </div>
            <div class="flex gap-2 items-end">
                <button type="submit" class="btn-cari">
                    <svg xmlns="http://www.w3.org/2000/svg" class="inline w-4 h-4 mr-1 -mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/></svg>
                    Cari
                </button>
                @if(request()->hasAny(['search','jenjang','kecamatan','status']))
                <a href="#" data-reset
                   class="bg-gray-100 text-gray-500 px-4 py-2 rounded-lg text-sm font-semibold hover:bg-gray-200 transition inline-flex items-center gap-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    Reset
                </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Tabel — diupdate via AJAX --}}
    <div id="tabel-wrapper" class="bg-white rounded-2xl shadow overflow-hidden" style="box-shadow:0 4px 24px rgba(13,74,47,0.09)">
        @include('public.partials.tabel-madrasah', ['madrasah' => $madrasah, 'rekapSiswa' => $rekapSiswa])
    </div>

</div>

@push('scripts')
<script>
(function() {
    const wrapper  = document.getElementById('tabel-wrapper');
    const form     = document.getElementById('form-filter');
    const endpoint = '{{ route("data-madrasah.tabel") }}';

    // Loading overlay
    function setLoading(on) {
        wrapper.style.opacity = on ? '0.5' : '1';
        wrapper.style.pointerEvents = on ? 'none' : '';
    }

    // Fetch tabel via AJAX
    function fetchTabel(params) {
        setLoading(true);
        fetch(endpoint + '?' + params.toString(), {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.text())
        .then(html => {
            wrapper.innerHTML = html;
            setLoading(false);
            // Re-bind pagination links
            bindPagination();
            // Scroll smooth ke tabel
            wrapper.scrollIntoView({ behavior: 'smooth', block: 'start' });
        })
        .catch(() => setLoading(false));
    }

    // Submit filter
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        const params = new URLSearchParams(new FormData(this));
        // Hapus param kosong
        [...params.keys()].forEach(k => { if (!params.get(k)) params.delete(k); });
        fetchTabel(params);
    });

    // Reset filter
    document.addEventListener('click', function(e) {
        const resetBtn = e.target.closest('[data-reset]');
        if (resetBtn) {
            e.preventDefault();
            form.reset();
            fetchTabel(new URLSearchParams());
        }
    });

    // Bind pagination links (delegation)
    function bindPagination() {
        wrapper.querySelectorAll('.pagination a, [rel="next"], [rel="prev"]').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const url    = new URL(this.href);
                const params = url.searchParams;
                // Gabung dengan filter aktif
                new FormData(form).forEach((v, k) => { if (v) params.set(k, v); });
                fetchTabel(params);
            });
        });
    }

    // Init
    bindPagination();
})();
</script>
@endpush
@endsection