@extends('layouts.admin')
@section('title', 'Dashboard')

@section('content')

@php
$statBoxes = [
    [
        'label' => 'Ajuan Dokumen',
        'value' => $pengajuanPending,
        'sub'   => $pengajuanBulanIni . ' masuk bulan ini',
        'href'  => route('admin.tindakan-cepat.index'),
        'tint'  => '#eff6ff', 'ink' => '#1d4ed8',
        'icon'  => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
    ],
    [
        'label' => 'Akun Terverifikasi',
        'value' => $akunTerverifikasi,
        'sub'   => 'Guru & madrasah aktif',
        'href'  => route('admin.guru-users.index'),
        'tint'  => '#f0fdf4', 'ink' => '#15803d',
        'icon'  => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
    ],
    [
        'label' => 'Akun Belum Aktivasi',
        'value' => $akunBelumAktivasi,
        'sub'   => $guruBelumAktivasi . ' guru · ' . $madrasahBelumAktivasi . ' madrasah',
        'href'  => route('admin.guru-users.index'),
        'tint'  => '#f5f3ff', 'ink' => '#6d28d9',
        'icon'  => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
    ],
];
@endphp

<style>
.quiet-card  { border: 1px solid #ececec; box-shadow: 0 1px 2px rgba(16,24,40,.03); }
.quiet-row   { transition: background .12s ease; }
.quiet-row:hover { background: #fafafa; }
.chevron { transition: transform .12s ease; }
.stat-box:hover .chevron { transform: translateX(2px); }

/* Grid mentah (bukan andalin utility Tailwind) biar dijamin jalan
   walau CSS Tailwind belum sempat di-rebuild/ke-cache browser. */
.row-cols-4 { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; }
.row-cols-2 { display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px; }
.row-cols-3 { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; }
@media (max-width: 1023px) {
    .row-cols-4, .row-cols-2, .row-cols-3 { grid-template-columns: 1fr; }
}

/* Kunci tinggi ke 1 layar penuh cuma di layar lebar (desktop admin). */
@media (min-width: 1024px) {
    .dash-wrap { height: calc(100vh - 88px); display: flex; flex-direction: column; }
    .dash-rows { flex: 1; min-height: 0; display: flex; flex-direction: column; gap: 12px; }
    .row-fixed { flex-shrink: 0; }
    .row-flex  { flex: 1; min-height: 0; }
    .list-scroll { flex: 1; min-height: 0; overflow-y: auto; }
    .chart-area  { flex: 1; min-height: 0; }
}
</style>

<div class="dash-wrap">

    {{-- ── HEADER ── --}}
    <div class="flex items-center justify-between mb-3 flex-shrink-0 fade-in">
        <div>
            <h1 class="text-sm font-semibold text-gray-900">Selamat datang, {{ auth()->user()->name }}</h1>
            <p class="text-2xs text-gray-400 mt-0.5">{{ now()->translatedFormat('l, d F Y') }}</p>
        </div>
    </div>

    {{-- ── 3 BARIS INDEPENDEN, GAK HARUS SAMA JUMLAH KOLOM ── --}}
    <div class="dash-rows">

        {{-- Baris 1 — 3 stat box sejajar --}}
        <div class="row-fixed row-cols-3">
        @foreach($statBoxes as $s)
        <a href="{{ $s['href'] }}" class="stat-box bg-white rounded-xl border border-zinc-100 px-4 py-4 fade-in hover:-translate-y-0.5 transition block">
            <p class="text-2xs font-semibold text-zinc-400 uppercase tracking-wider mb-2 truncate">{{ $s['label'] }}</p>
            <div class="flex items-end justify-between">
                <p class="text-3xl font-bold text-zinc-900 leading-none">{{ $s['value'] }}</p>
                <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0" style="background:{{ $s['tint'] }}">
                    <svg class="w-4 h-4" style="color:{{ $s['ink'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="{{ $s['icon'] }}"/>
                    </svg>
                </div>
            </div>
            <p class="text-2xs text-zinc-400 truncate mt-1">{{ $s['sub'] }}</p>
        </a>
        @endforeach
        </div>

        {{-- Baris 2 — Ajuan Dokumen Terbaru --}}
        <div class="row-flex">
        <div class="quiet-card bg-white rounded-xl overflow-hidden flex flex-col fade-in h-full">
            <div class="flex items-center justify-between px-4 py-2.5 flex-shrink-0" style="border-bottom:1px solid #f4f4f5">
                <p class="text-xs font-semibold text-gray-700">Ajuan Dokumen Terbaru</p>
                <a href="{{ route('admin.tindakan-cepat.index') }}" class="text-2xs font-medium text-primary-700 hover:text-primary-900">Lihat semua</a>
            </div>
            <div class="list-scroll">
                @forelse($pengajuanTerbaru as $p)
                <div class="quiet-row flex items-center gap-2.5 px-4 py-2">
                    <div class="w-6 h-6 rounded-full flex items-center justify-center flex-shrink-0 text-white text-2xs font-semibold" style="background:linear-gradient(135deg,#15803d,#22c55e)">
                        {{ strtoupper(substr($p->nama_madrasah, 0, 2)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-2xs font-medium text-gray-700 truncate">{{ $p->nama_madrasah }}</p>
                        <p class="text-2xs text-gray-500 truncate">{{ $p->jenisDokumen->nama ?? '—' }}</p>
                    </div>
                    <span class="badge flex-shrink-0
                        @if($p->status === 'pending') badge-yellow
                        @elseif($p->status === 'diterima') badge-green
                        @else badge-red @endif">
                        {{ $p->status_label }}
                    </span>
                </div>
                @empty
                <div class="flex items-center justify-center py-8">
                    <p class="text-2xs text-gray-300">Belum ada pengajuan dokumen</p>
                </div>
                @endforelse
            </div>
        </div>
        </div>

    </div>
</div>

@endsection
