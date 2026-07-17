@extends('layouts.app')
@section('title', 'Masuk untuk Mengajukan Dokumen')

@section('content')
<section class="max-w-3xl mx-auto px-4 py-20">
    <div class="text-center mb-10">
        <div class="w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-5"
            style="background: linear-gradient(135deg, #065f46, #0a7a5a); box-shadow: 0 8px 20px rgba(6,95,70,0.25);">
            <svg class="w-8 h-8" style="color:#a3e635" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
            </svg>
        </div>
        <h1 class="text-2xl md:text-3xl font-extrabold text-gray-900 mb-3" style="font-family:'Sora',sans-serif;">Login Diperlukan untuk Mengajukan Dokumen</h1>
        <p class="text-gray-500 max-w-xl mx-auto leading-relaxed">
            Untuk pengajuan <span class="font-semibold text-gray-700">{{ $jenis->nama }}</span>, silakan masuk terlebih
            dahulu ke akun Guru atau Madrasah Anda. Data seperti nama, NIP/NSM, dan kontak akan otomatis terisi dari
            akun Anda sehingga proses pengajuan jadi lebih cepat dan pendataan lebih akurat.
        </p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
        <a href="{{ route('guru.login') }}"
            class="group bg-white rounded-2xl p-7 border border-gray-100 text-center transition-all duration-200 hover:-translate-y-1"
            style="box-shadow: 0 4px 20px rgba(0,0,0,0.06);">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center mx-auto mb-4"
                style="background: linear-gradient(135deg, #dcfce7, #bbf7d0);">
                <svg class="w-6 h-6" style="color:#065f46" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                </svg>
            </div>
            <h3 class="font-bold text-gray-900 mb-1" style="font-family:'Sora',sans-serif;">Portal Guru</h3>
            <p class="text-gray-400 text-xs mb-4">Untuk pengajuan atas nama guru</p>
            <span class="inline-flex items-center gap-1 text-sm font-bold group-hover:gap-2 transition-all" style="color:#065f46">
                Masuk Portal Guru
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                </svg>
            </span>
        </a>

        <a href="{{ route('madrasah.login') }}"
            class="group bg-white rounded-2xl p-7 border border-gray-100 text-center transition-all duration-200 hover:-translate-y-1"
            style="box-shadow: 0 4px 20px rgba(0,0,0,0.06);">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center mx-auto mb-4"
                style="background: linear-gradient(135deg, #ecfccb, #d9f99d);">
                <svg class="w-6 h-6" style="color:#4d7c0f" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
            </div>
            <h3 class="font-bold text-gray-900 mb-1" style="font-family:'Sora',sans-serif;">Portal Madrasah</h3>
            <p class="text-gray-400 text-xs mb-4">Untuk pengajuan atas nama lembaga</p>
            <span class="inline-flex items-center gap-1 font-bold text-sm group-hover:gap-2 transition-all" style="color:#4d7c0f">
                Masuk Portal Madrasah
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                </svg>
            </span>
        </a>
    </div>

    <p class="text-center text-gray-400 text-xs mt-8">
        Belum punya akun? Hubungi admin Seksi Pendidikan Madrasah untuk didaftarkan.
    </p>
</section>
@endsection
