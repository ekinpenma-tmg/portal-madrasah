@extends('layouts.app')
@section('title', 'Pelayanan')

@push('styles')
<style>
    .kategori-label{ display:flex; align-items:center; gap:12px; margin-bottom:20px; }
    .kategori-label .huruf{ width:34px; height:34px; border-radius:10px; background:#065f46; color:#a3e635; display:flex; align-items:center; justify-content:center; font-family:'Sora',sans-serif; font-weight:800; font-size:14px; flex-shrink:0; }
    .kategori-label h2{ font-family:'Sora',sans-serif; font-weight:700; font-size:18px; color:#0b1a12; }

    .lyn-card{ background:#fff; border:1px solid #e4e9e5; border-radius:16px; padding:20px; text-decoration:none; display:flex; gap:14px; align-items:flex-start; transition:transform .2s, box-shadow .2s, border-color .2s; }
    .lyn-card:hover{ transform:translateY(-3px); box-shadow:0 16px 32px -14px rgba(6,40,25,0.15); border-color:#0a7a5a; }
    .lyn-card .num{ width:30px; height:30px; border-radius:9px; background:#f6f8f6; color:#065f46; display:flex; align-items:center; justify-content:center; font-family:'Sora',sans-serif; font-weight:700; font-size:12.5px; flex-shrink:0; }
    .lyn-card h3{ font-family:'Sora',sans-serif; font-weight:700; font-size:14.5px; color:#0b1a12; line-height:1.4; margin-bottom:6px; }
    .lyn-card p{ font-size:12.5px; color:#5b6b60; line-height:1.5; }
    .lyn-card .arrow{ margin-left:auto; color:#9ca3af; flex-shrink:0; }
</style>
@endpush

@section('content')

    {{-- Hero --}}
    <section class="hex-pattern relative overflow-hidden">
        <div class="absolute inset-0" style="background: linear-gradient(135deg, rgba(5,46,22,0.85), rgba(22,101,52,0.75))"></div>
        <div class="relative max-w-4xl mx-auto px-4 py-6 text-center">
            <h1 class="text-3xl font-extrabold text-white mb-2">Pelayanan</h1>
            <p class="text-primary-200 text-sm max-w-xl mx-auto">Standar Pelayanan Seksi Pendidikan Madrasah
                Kantor Kementerian Agama Kabupaten Temanggung</p>
        </div>
    </section>

    <div class="max-w-4xl mx-auto px-4 py-14">
        @if ($layanan->isEmpty())
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-16 text-center">
                <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="font-bold text-gray-700 mb-2">Belum ada layanan yang ditampilkan</h3>
                <p class="text-gray-400 text-sm">Daftar standar pelayanan akan segera ditambahkan oleh admin.</p>
            </div>
        @else
            @foreach ($layanan as $kategori => $items)
                <div class="mb-12">
                    <div class="kategori-label">
                        <div class="huruf">{{ chr(65 + $loop->index) }}</div>
                        <h2>{{ $kategori }}</h2>
                    </div>
                    <div class="grid grid-cols-1 gap-3">
                        @foreach ($items as $l)
                            <a href="{{ route('layanan.show', $l->slug) }}" class="lyn-card">
                                <div class="num">{{ $l->urutan }}</div>
                                <div class="flex-1">
                                    <h3>{{ $l->nama }}</h3>
                                    @if ($l->ringkasan)
                                        <p>{{ $l->ringkasan }}</p>
                                    @endif
                                </div>
                                <svg class="arrow w-4 h-4 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endforeach
        @endif
    </div>
@endsection
