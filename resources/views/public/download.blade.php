@extends('layouts.app')
@section('title', 'Pusat Informasi')

@section('content')

    {{-- Hero --}}
    <section class="hex-pattern relative overflow-hidden">
        <div class="absolute inset-0" style="background: linear-gradient(135deg, rgba(5,46,22,0.85), rgba(22,101,52,0.75))">
        </div>
        <div class="relative max-w-4xl mx-auto px-4 py-6 text-center">
            <h1 class="text-3xl font-extrabold text-white mb-2">Unduh Dokumen & Informasi</h1>
            <p class="text-primary-200 text-sm max-w-xl mx-auto">Juknis, pengumuman, sosialisasi, dan informasi terbaru dari
                Penma Temanggung</p>
        </div>
    </section>

    <div class="max-w-5xl mx-auto px-4 py-12">
        @if ($files->isEmpty())
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-16 text-center">
                <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                    </svg>
                </div>
                <h3 class="font-bold text-gray-700 mb-2">Belum ada file tersedia</h3>
                <p class="text-gray-400 text-sm">Dokumen dan informasi akan segera ditambahkan oleh admin.</p>
            </div>
        @else
            @foreach ($files as $kategori => $fileList)
                <div class="mb-8">
                    {{-- Kategori header --}}
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-8 h-8 bg-primary-700 rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                            </svg>
                        </div>
                        <h2 class="font-bold text-gray-800">{{ $kategori ?: 'Umum' }}</h2>
                        <span class="text-xs text-gray-400 bg-gray-100 px-2 py-0.5 rounded-full">{{ $fileList->count() }}
                            file</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach ($fileList as $file)
                            <div
                                class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 flex items-center gap-4 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200">
                                <div
                                    class="w-11 h-11 bg-primary-50 rounded-xl flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h3 class="font-bold text-gray-800 text-sm truncate">{{ $file->nama }}</h3>
                                    @if ($file->deskripsi)
                                        <p class="text-gray-400 text-xs mt-0.5 line-clamp-1">{{ $file->deskripsi }}</p>
                                    @endif
                                    <p class="text-gray-300 text-xs mt-1">{{ $file->jumlah_download }}x diunduh</p>
                                </div>
                                <a href="{{ route('download.unduh', $file->id) }}"
                                    class="flex items-center gap-1.5 bg-primary-700 hover:bg-primary-800 text-white text-xs font-semibold px-4 py-2 rounded-xl transition flex-shrink-0">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                    </svg>
                                    Unduh
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        @endif
    </div>
@endsection
