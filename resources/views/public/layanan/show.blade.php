@extends('layouts.app')
@section('title', $layanan->nama)

@push('styles')
<style>
    .info-card{ background:#fff; border:1px solid #e4e9e5; border-radius:16px; padding:20px; }
    .info-card .l{ font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.05em; color:#5b6b60; margin-bottom:6px; }
    .info-card .v{ font-family:'Sora',sans-serif; font-weight:700; font-size:16px; color:#0b1a12; }
    .step{ display:flex; gap:16px; }
    .step .sn{ width:30px; height:30px; border-radius:50%; background:#065f46; color:#a3e635; display:flex; align-items:center; justify-content:center; font-family:'Sora',sans-serif; font-weight:700; font-size:13px; flex-shrink:0; }
    .step-line{ width:1px; background:#e4e9e5; flex:1; margin:6px 0; }
    .check-item{ display:flex; gap:10px; align-items:flex-start; font-size:14px; color:#374151; padding:10px 0; border-bottom:1px solid #f3f4f6; }
    .check-item:last-child{ border-bottom:none; }
    .check-item svg{ width:16px; height:16px; color:#065f46; flex-shrink:0; margin-top:2px; }
    .cta-box{ background:#0b1a12; border-radius:20px; padding:28px; color:#fff; }
    .btn-lime{ background:#a3e635; color:#0b1a12; font-weight:700; font-size:13.5px; padding:12px 22px; border-radius:9999px; display:inline-flex; gap:8px; align-items:center; text-decoration:none; }
    .kategori-chip{ display:inline-flex; align-items:center; gap:6px; background:rgba(255,255,255,0.12); color:#d9f99d; font-size:11.5px; font-weight:700; padding:5px 14px; border-radius:9999px; margin-bottom:14px; }
</style>
@endpush

@section('content')

    {{-- Hero --}}
    <section class="hex-pattern relative overflow-hidden">
        <div class="absolute inset-0" style="background: linear-gradient(135deg, rgba(5,46,22,0.85), rgba(22,101,52,0.75))"></div>
        <div class="relative max-w-4xl mx-auto px-4 py-8">
            <a href="{{ route('layanan.index') }}" class="inline-flex items-center gap-1.5 text-primary-200 text-xs font-semibold mb-4 hover:text-white transition">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                Semua Pelayanan
            </a>
            <div class="kategori-chip">{{ $layanan->kategori }}</div>
            <h1 class="text-2xl md:text-3xl font-extrabold text-white mb-2">{{ $layanan->nama }}</h1>
            @if ($layanan->ringkasan)
                <p class="text-primary-200 text-sm max-w-2xl">{{ $layanan->ringkasan }}</p>
            @endif
        </div>
    </section>

    <div class="max-w-4xl mx-auto px-4 py-12">

        {{-- Info ringkas: waktu & biaya --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-10">
            <div class="info-card">
                <div class="l">Waktu Proses</div>
                <div class="v">{{ $layanan->waktu_proses ?? 'Belum ditentukan' }}</div>
            </div>
            <div class="info-card">
                <div class="l">Biaya</div>
                <div class="v">{{ $layanan->biaya ?? 'Gratis' }}</div>
            </div>
        </div>

        @if ($layanan->deskripsi)
            <div class="mb-10">
                <h2 class="font-bold text-gray-900 mb-3" style="font-family:'Sora',sans-serif;">Deskripsi</h2>
                <p class="text-gray-600 text-sm leading-relaxed">{{ $layanan->deskripsi }}</p>
            </div>
        @endif

        @if ($layanan->dasar_hukum)
            <div class="mb-10 bg-gray-50 rounded-2xl p-5 border border-gray-100">
                <h2 class="font-bold text-gray-900 mb-2 text-sm" style="font-family:'Sora',sans-serif;">Dasar Hukum</h2>
                <p class="text-gray-500 text-xs leading-relaxed">{{ $layanan->dasar_hukum }}</p>
            </div>
        @endif

        @if (count($layanan->syarat_list))
            <div class="mb-10">
                <h2 class="font-bold text-gray-900 mb-3" style="font-family:'Sora',sans-serif;">Persyaratan</h2>
                <div class="bg-white rounded-2xl border border-gray-100 p-5">
                    @foreach ($layanan->syarat_list as $s)
                        <div class="check-item">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            <span>{{ $s }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        @if (count($layanan->alur_list))
            <div class="mb-12">
                <h2 class="font-bold text-gray-900 mb-5" style="font-family:'Sora',sans-serif;">Alur / Tahapan</h2>
                <div>
                    @foreach ($layanan->alur_list as $i => $a)
                        <div class="step">
                            <div class="flex flex-col items-center">
                                <div class="sn">{{ $i + 1 }}</div>
                                @if (! $loop->last)
                                    <div class="step-line"></div>
                                @endif
                            </div>
                            <div class="pb-6 pt-1">
                                <p class="text-sm text-gray-700 font-medium">{{ $a }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        @if (! count($layanan->syarat_list) && ! count($layanan->alur_list) && ! $layanan->deskripsi)
            <div class="mb-10 bg-amber-50 border border-amber-100 rounded-2xl p-5 text-sm text-amber-700">
                Rincian syarat &amp; alur untuk layanan ini sedang dilengkapi oleh admin. Silakan hubungi kantor untuk informasi lebih lanjut.
            </div>
        @endif

        {{-- Dokumen resmi (opsional) --}}
        @if ($layanan->sop_file_path)
            <div class="cta-box">
                <h3 class="font-bold text-lg mb-2" style="font-family:'Sora',sans-serif;">Dokumen Resmi</h3>
                <p class="text-sm mb-5" style="color:rgba(255,255,255,0.6);">
                    Unduh dokumen Standar Pelayanan resmi untuk keperluan arsip atau lampiran.
                </p>
                <a href="{{ route('layanan.sop', $layanan->slug) }}" class="btn-lime">
                    <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    Unduh Dokumen Resmi (PDF)
                </a>
            </div>
        @endif

        {{-- Layanan lain di kategori yang sama --}}
        @if ($lainnya->isNotEmpty())
            <div class="mt-14">
                <h3 class="font-bold text-gray-900 mb-4 text-sm" style="font-family:'Sora',sans-serif;">Layanan lain di {{ $layanan->kategori }}</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @foreach ($lainnya as $l)
                        <a href="{{ route('layanan.show', $l->slug) }}" class="block bg-white border border-gray-100 rounded-xl p-4 hover:border-green-300 transition">
                            <p class="text-sm font-bold text-gray-800">{{ $l->nama }}</p>
                            @if ($l->waktu_proses)
                                <p class="text-xs text-gray-400 mt-1">{{ $l->waktu_proses }}</p>
                            @endif
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
@endsection
