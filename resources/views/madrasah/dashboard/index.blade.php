@extends('madrasah.layouts.app')
@section('title', 'Dashboard')

@section('content')
@php
    $m = auth('madrasah')->user();
    $persen = $totalKategoriTersedia > 0 ? round(($totalKategori / $totalKategoriTersedia) * 100) : 0;
@endphp

{{-- Header madrasah --}}
<div class="bg-white rounded-xl border border-gray-200 px-4 py-3 mb-4 flex items-center justify-between fade-in">
    <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white font-semibold text-base flex-shrink-0 bg-primary-700">
            {{ strtoupper(substr($m->nama_madrasah, 0, 1)) }}
        </div>
        <div>
            <p class="text-sm font-semibold text-gray-900">{{ $m->nama_madrasah }}</p>
            <p class="text-xs text-gray-400 mt-0.5">
                NSM: <span class="font-mono">{{ $m->nsm }}</span>
                · {{ $m->madrasah?->jenjang ?? '—' }}
                @if($m->madrasah?->kecamatan) · Kec. {{ $m->madrasah->kecamatan }} @endif
            </p>
        </div>
    </div>
    <span class="badge {{ $m->is_active ? 'badge-green' : 'badge-gray' }}">
        <span class="w-1.5 h-1.5 rounded-full inline-block mr-1 {{ $m->is_active ? 'bg-green-600' : 'bg-gray-400' }}"></span>
        {{ $m->is_active ? 'Akun aktif' : 'Nonaktif' }}
    </span>
</div>

{{-- 3 Stat utama --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-4 fade-in">
    <a href="{{ route('madrasah.arsip.index') }}" class="stat-card group block transition hover:shadow-md hover:-translate-y-0.5">
        <div class="flex items-center justify-between">
            <div>
                <div class="stat-value">{{ $totalArsip }}</div>
                <div class="stat-sub">Total arsip diunggah</div>
            </div>
            <div class="w-9 h-9 rounded-lg bg-primary-50 flex items-center justify-center flex-shrink-0 group-hover:bg-primary-100 transition">
                <svg class="w-4 h-4 text-primary-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
            </div>
        </div>
    </a>
    <a href="{{ route('madrasah.arsip.index', ['verified' => 1]) }}" class="stat-card group block transition hover:shadow-md hover:-translate-y-0.5">
        <div class="flex items-center justify-between">
            <div>
                <div class="stat-value">{{ $totalVerified }}</div>
                <div class="stat-sub">Arsip terverifikasi</div>
            </div>
            <div class="w-9 h-9 rounded-lg bg-primary-50 flex items-center justify-center flex-shrink-0 group-hover:bg-primary-100 transition">
                <svg class="w-4 h-4 text-primary-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
            </div>
        </div>
    </a>
    <a href="{{ route('madrasah.pengajuan.riwayat', ['status' => 'pending']) }}" class="stat-card amber group block transition hover:shadow-md hover:-translate-y-0.5">
        <div class="flex items-center justify-between">
            <div>
                <div class="stat-value">{{ $totalPengajuanPending }}</div>
                <div class="stat-sub">Pengajuan menunggu</div>
            </div>
            <div class="w-9 h-9 rounded-lg bg-amber-50 flex items-center justify-center flex-shrink-0 group-hover:bg-amber-100 transition">
                <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>
    </a>
</div>

{{-- 2 kolom: progress, pengajuan --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 fade-in">

    {{-- Kelengkapan arsip --}}
    <div class="bg-white rounded-xl border border-gray-200 p-4">
        <div class="flex items-center justify-between mb-3">
            <h3 class="text-sm font-semibold text-gray-800">Kelengkapan Arsip</h3>
            <span class="text-xs text-gray-400">{{ $totalKategori }} / {{ $totalKategoriTersedia }} kategori</span>
        </div>

        @forelse($kategoriChecklist as $item)
        <div class="flex items-center gap-2 py-1.5 border-b border-gray-50 last:border-0">
            <span class="w-2 h-2 rounded-full flex-shrink-0 {{ $item['terisi'] ? 'bg-gold-500' : 'bg-gray-300' }}"></span>
            <span class="text-xs text-gray-700 flex-1 truncate">{{ $item['nama'] }}</span>
            <span class="badge {{ $item['terisi'] ? 'badge-green' : 'badge-gray' }}">{{ $item['terisi'] ? 'Ada' : 'Belum' }}</span>
        </div>
        @empty
        <p class="text-xs text-gray-400 text-center py-6">Belum ada kategori arsip tersedia.</p>
        @endforelse

        @if($totalKategoriTersedia > 0)
        <div class="mt-3 bg-gray-100 rounded-full h-1.5 overflow-hidden">
            <div class="h-full bg-gold-500 rounded-full transition-all" style="width: {{ $persen }}%"></div>
        </div>
        <p class="text-xs text-gray-400 mt-1 text-right">{{ $persen }}% lengkap</p>
        @endif
    </div>

    {{-- Pengajuan terakhir --}}
    <div class="bg-white rounded-xl border border-gray-200 p-4">
        <div class="flex items-center justify-between mb-3">
            <h3 class="text-sm font-semibold text-gray-800">Pengajuan Terakhir</h3>
            <a href="{{ route('madrasah.pengajuan.riwayat') }}" class="text-xs text-primary-700 font-medium hover:underline">Lihat semua</a>
        </div>

        @forelse($pengajuanTerakhir as $p)
        @php
            $dot = $p->status === 'diterima' ? 'bg-green-600' : ($p->status === 'ditolak' ? 'bg-red-500' : 'bg-amber-500');
            $badgeClass = $p->status === 'diterima' ? 'badge-green' : ($p->status === 'ditolak' ? 'badge-red' : 'badge-yellow');
        @endphp
        <div class="flex items-center gap-2.5 py-2 border-b border-gray-50 last:border-0">
            <span class="w-2 h-2 rounded-full flex-shrink-0 {{ $dot }}"></span>
            <div class="min-w-0 flex-1">
                <p class="text-xs font-medium text-gray-800 truncate">{{ $p->jenisDokumen->nama }}</p>
                <p class="text-2xs text-gray-400 font-mono">{{ $p->kode_ajuan }}</p>
            </div>
            <span class="badge {{ $badgeClass }}">{{ $p->status_label }}</span>
        </div>
        @empty
        <div class="text-center py-6">
            <p class="text-xs text-gray-400">Belum ada pengajuan dokumen.</p>
            <a href="{{ route('madrasah.pengajuan.index') }}" class="inline-flex items-center gap-1.5 mt-2 btn-xs btn-primary-xs">
                Ajukan Dokumen
            </a>
        </div>
        @endforelse
    </div>
</div>
@endsection