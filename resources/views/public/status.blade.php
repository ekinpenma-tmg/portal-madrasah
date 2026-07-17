@extends('layouts.app')
@section('title', 'Status Pengajuan')

@section('content')

    {{-- Hero --}}
    <section class="hex-pattern relative overflow-hidden">
        <div class="absolute inset-0" style="background: linear-gradient(135deg, rgba(5,46,22,0.85), rgba(22,101,52,0.75))">
        </div>
        <div class="relative max-w-4xl mx-auto px-4 py-6 text-center">
            <h1 class="text-3xl font-extrabold text-white mb-2">Cek Status Pengajuan</h1>
            <p class="text-primary-200 text-sm max-w-xl mx-auto">Masukkan kode ajuan yang Anda terima saat mengirim dokumen
            </p>
        </div>
    </section>

    <div class="max-w-md mx-auto px-4 py-8" style="min-height: calc(100vh - 270px)">

        {{-- Form Cari --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 mb-4">
            <form action="{{ route('status.cari') }}" method="POST">
                @csrf
                <div class="flex gap-2">
                    <input type="text" name="kode_ajuan" value="{{ $kode ?? old('kode_ajuan') }}"
                        class="flex-1 border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 uppercase tracking-wider font-mono @error('kode_ajuan') border-red-400 @enderror"
                        placeholder="DOK-XXXXXX">
                    <button type="submit"
                        class="bg-primary-700 hover:bg-primary-800 text-white font-bold px-5 py-2.5 rounded-xl transition text-sm">
                        Cari
                    </button>
                </div>
                @error('kode_ajuan')
                    <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                @enderror
            </form>
        </div>

        @isset($kode)
            @if ($pengajuan)
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">

                    @php
                        $isD = $pengajuan->status === 'diterima';
                        $isT = $pengajuan->status === 'ditolak';
                        $bgGrad = $isD
                            ? 'linear-gradient(135deg,#052e16,#166534)'
                            : ($isT
                                ? 'linear-gradient(135deg,#450a0a,#b91c1c)'
                                : 'linear-gradient(135deg,#451a03,#b45309)');
                    @endphp
                    <div class="px-6 pt-6 pb-5 text-center" style="background: {{ $bgGrad }}">
                        <div class="w-12 h-12 bg-white/15 rounded-2xl flex items-center justify-center mx-auto mb-3">
                            @if ($isD)
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                            @elseif($isT)
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            @else
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            @endif
                        </div>
                        <p class="text-xl font-extrabold text-white mb-1">{{ $pengajuan->status_label }}</p>
                        <p class="text-white/50 text-xs font-mono">{{ $pengajuan->kode_ajuan }}</p>

                        @if ($pengajuan->status === 'pending' && $posisiAntrian !== null)
                            <div class="mt-3 bg-white/15 rounded-xl px-4 py-2 inline-block">
                                @if ($posisiAntrian === 1)
                                    <p class="text-white text-xs font-bold">🎉 Antrian pertama — segera diproses!</p>
                                @else
                                    <p class="text-white text-xs">Posisi antrian: <span
                                            class="font-extrabold text-base">#{{ $posisiAntrian }}</span></p>
                                @endif
                            </div>
                        @endif

                        @if ($pengajuan->tanggal_proses)
                            <p class="text-white/40 text-xs mt-2">Diproses:
                                {{ $pengajuan->tanggal_proses->format('d M Y, H:i') }}</p>
                        @endif
                    </div>

                    <div class="px-5 py-4">
                        <div class="space-y-2.5">
                            <div class="flex items-center justify-between py-2 border-b border-gray-50">
                                <span class="text-xs text-gray-400 font-medium">Jenis Dokumen</span>
                                <span class="text-sm font-bold text-gray-800">{{ $pengajuan->jenisDokumen->nama }}</span>
                            </div>
                            <div class="flex items-center justify-between py-2 border-b border-gray-50">
                                <span class="text-xs text-gray-400 font-medium">Nama Guru</span>
                                <span class="text-sm font-bold text-gray-800">{{ $pengajuan->nama_guru }}</span>
                            </div>
                            <div
                                class="flex items-center justify-between py-2 {{ $pengajuan->catatan ? 'border-b border-gray-50' : '' }}">
                                <span class="text-xs text-gray-400 font-medium">Nama Madrasah</span>
                                <span
                                    class="text-sm font-bold text-gray-800 text-right max-w-[60%]">{{ $pengajuan->nama_madrasah }}</span>
                            </div>
                        </div>

                        @if ($pengajuan->catatan)
                            <div
                                class="mt-3 rounded-xl px-4 py-3 {{ $isD ? 'bg-green-50 border border-green-100' : 'bg-red-50 border border-red-100' }}">
                                <p class="text-xs font-bold {{ $isD ? 'text-green-700' : 'text-red-700' }} mb-1">📝 Catatan
                                    Admin</p>
                                <p class="text-sm {{ $isD ? 'text-green-800' : 'text-red-800' }}">{{ $pengajuan->catatan }}
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            @else
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-10 text-center">
                    <div class="w-14 h-14 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-3">
                        <svg class="w-7 h-7 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <h3 class="font-bold text-gray-700 mb-1">Kode Tidak Ditemukan</h3>
                    <p class="text-gray-400 text-sm">Kode <span
                            class="font-mono font-bold text-primary-700">{{ $kode }}</span> tidak ditemukan.</p>
                </div>
            @endif
        @endisset

    </div>
@endsection
