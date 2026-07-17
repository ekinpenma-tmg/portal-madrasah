@extends('layouts.app')
@section('title', 'Profil Organisasi')

@section('content')

    {{-- Hero --}}
    <section class="hex-pattern relative overflow-hidden">
        <div class="absolute inset-0" style="background: linear-gradient(135deg, rgba(5,46,22,0.85), rgba(22,101,52,0.75))">
        </div>
        <div class="relative max-w-4xl mx-auto px-4 py-6 text-center">
            <h1 class="text-4xl md:text-3xl font-extrabold text-white mb-3">
                {{ $profil['nama_organisasi']->value ?? 'Seksi Pendidikan Madrasah' }}</h1>
            <h3 class="text-2xl md:text-xl font-extrabold text-white mb-3">Kantor Kementerian Agama Kabupaten Temanggung</h3>
            <p class="text-primary-200 text-sm max-w-xl mx-auto">
                {{ $profil['alamat']->value ?? 'Kantor Kementerian Agama Kabupaten Temanggung' }}</p>
        </div>
    </section>

    <div class="max-w-4xl mx-auto px-4 py-12 space-y-8">
        {{-- Struktur Organisasi --}}
        <div>
            <div class="flex items-center gap-3 mb-8">
                <div class="w-8 h-8 bg-primary-700 rounded-lg flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
                <h2 class="font-bold text-gray-800">Struktur Organisasi</h2>
            </div>

            @php
                $kepala =
                    $staff->firstWhere('jabatan', 'Kepala Seksi Pendidikan Madrasah') ??
                    ($staff->first(fn($s) => str_contains(strtolower($s->jabatan), 'kepala')) ?? $staff->first());
                $anggota = $staff->filter(fn($s) => $s->id !== $kepala?->id);
                $inisialColors = [
                    'bg-emerald-100 text-emerald-700',
                    'bg-blue-100 text-blue-700',
                    'bg-purple-100 text-purple-700',
                    'bg-amber-100 text-amber-700',
                    'bg-rose-100 text-rose-700',
                    'bg-cyan-100 text-cyan-700',
                    'bg-indigo-100 text-indigo-700',
                ];
            @endphp

            {{-- Kepala Seksi — tampil di tengah, lebih besar --}}
            @if ($kepala)
                <div class="flex justify-center mb-2">
                    <div class="relative bg-white rounded-2xl shadow-md border-2 border-primary-200 p-6 text-center w-64 hover:shadow-lg hover:-translate-y-1 transition-all duration-200"
                        style="background: linear-gradient(145deg, #ffffff, #f0fdf4)">
                        {{-- Crown icon --}}
                        <div class="absolute -top-3 left-1/2 -translate-x-1/2">
                            <div class="bg-primary-700 text-white text-xs font-bold px-3 py-1 rounded-full shadow">
                                Pimpinan
                            </div>
                        </div>
                        {{-- Foto --}}
                        <div
                            class="w-24 h-24 mx-auto mb-4 rounded-2xl overflow-hidden border-4 border-primary-300 shadow-md flex items-center justify-center bg-primary-50">
                            @if ($kepala->foto)
                                <img src="{{ Storage::url($kepala->foto) }}" alt="{{ $kepala->nama }}"
                                    class="w-full h-full object-cover">
                            @else
                                <span
                                    class="text-4xl font-extrabold text-primary-600">{{ substr($kepala->nama, 0, 1) }}</span>
                            @endif
                        </div>
                        <h3 class="font-extrabold text-gray-800 text-base leading-tight">{{ $kepala->nama }}</h3>
                        <p
                            class="text-primary-700 text-xs mt-2 font-semibold bg-primary-50 border border-primary-200 px-3 py-1.5 rounded-full inline-block">
                            {{ $kepala->jabatan }}
                        </p>
                    </div>
                </div>

                {{-- Garis penghubung ke staff --}}
                @if ($anggota->count() > 0)
                    <div class="flex justify-center mb-2">
                        <div class="w-px h-8 bg-primary-200"></div>
                    </div>
                    <div class="flex justify-center mb-2">
                        <div class="h-px bg-primary-200" style="width: calc(100% - 6rem)"></div>
                    </div>
                @endif
            @endif

            {{-- Staff — grid di bawah --}}
            @if ($anggota->count() > 0)
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                    @foreach ($anggota as $i => $s)
                        @php $colorClass = $inisialColors[$i % count($inisialColors)]; @endphp
                        <div
                            class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 text-center hover:shadow-md hover:-translate-y-1 transition-all duration-200 group">
                            {{-- Foto --}}
                            <div
                                class="w-20 h-20 mx-auto mb-3 rounded-2xl overflow-hidden border-2 border-gray-100 group-hover:border-primary-200 transition-colors flex items-center justify-center {{ $s->foto ? '' : $colorClass }} bg-opacity-60">
                                @if ($s->foto)
                                    <img src="{{ Storage::url($s->foto) }}" alt="{{ $s->nama }}"
                                        class="w-full h-full object-cover">
                                @else
                                    <span class="text-2xl font-extrabold">{{ substr($s->nama, 0, 1) }}</span>
                                @endif
                            </div>
                            <h3 class="font-bold text-gray-800 text-sm leading-tight">{{ $s->nama }}</h3>
                            <p
                                class="text-primary-600 text-xs mt-1.5 font-medium bg-primary-50 px-3 py-1 rounded-full inline-block">
                                {{ $s->jabatan }}
                            </p>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Visi --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="flex items-center gap-3 px-6 py-4 border-b border-gray-100">
                <div class="w-8 h-8 bg-primary-700 rounded-lg flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                </div>
                <h2 class="font-bold text-gray-800">Visi</h2>
            </div>
            <div class="px-6 py-5">
                <p class="text-gray-600 leading-relaxed text-sm italic"
                    style="border-left: 3px solid #15803d; padding-left: 1rem;">
                    "{{ $profil['visi']->value ?? '' }}"
                </p>
            </div>
        </div>

        {{-- Misi --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="flex items-center gap-3 px-6 py-4 border-b border-gray-100">
                <div class="w-8 h-8 bg-primary-700 rounded-lg flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
                <h2 class="font-bold text-gray-800">Misi</h2>
            </div>
            <div class="px-6 py-5">
                <ul class="space-y-3">
                    @for ($i = 1; $i <= 6; $i++)
                        @if (isset($profil["misi_$i"]) && $profil["misi_$i"]->value)
                            <li class="flex items-start gap-3">
                                <span
                                    class="w-5 h-5 bg-primary-700 text-white rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0 mt-0.5">{{ $i }}</span>
                                <p class="text-gray-600 text-sm leading-relaxed">{{ $profil["misi_$i"]->value }}</p>
                            </li>
                        @endif
                    @endfor
                </ul>
            </div>
        </div>



    </div>
@endsection
