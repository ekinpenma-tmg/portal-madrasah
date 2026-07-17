@extends('layouts.app')
@section('title', 'Pengajuan Berhasil')

@section('content')
    <div class="min-h-[80vh] flex items-center justify-center px-4 py-8">
        <div class="w-full max-w-md">

            {{-- Card utama --}}
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">

                {{-- Header hijau --}}
                <div class="px-8 pt-8 pb-6 text-center" style="background: linear-gradient(135deg, #052e16, #166534)">
                    <div class="w-14 h-14 bg-white/15 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <h1 class="text-xl font-extrabold text-white mb-1">Pengajuan Berhasil!</h1>
                    <p class="text-white/60 text-xs">Dokumen Anda sedang menunggu proses</p>
                </div>

                <div class="px-6 py-5">
                    {{-- Kode Ajuan --}}
                    <div class="rounded-2xl px-5 py-4 mb-5 text-center"
                        style="background: linear-gradient(135deg, #f0fdf4, #dcfce7); border: 1.5px dashed #86efac">
                        <p class="text-xs text-gray-400 font-semibold uppercase tracking-widest mb-1.5">Kode Ajuan Anda</p>
                        <div class="flex items-center justify-center gap-2">
                            <p class="text-2xl font-extrabold text-primary-700 tracking-widest font-mono" id="kode-ajuan">
                                {{ $pengajuan->kode_ajuan }}</p>
                            <button onclick="copyKode()" id="btn-copy"
                                class="w-8 h-8 bg-primary-100 hover:bg-primary-200 text-primary-600 rounded-lg flex items-center justify-center transition"
                                title="Salin kode">
                                <svg id="icon-copy" class="w-4 h-4" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                </svg>
                                <svg id="icon-check" class="w-4 h-4 hidden text-green-600" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                            </button>
                        </div>
                        <p class="text-xs text-gray-400 mt-1.5 flex items-center justify-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Simpan kode ini untuk memantau status
                        </p>
                    </div>

                    {{-- Info ringkas --}}
                    <div class="space-y-2.5 mb-5">
                        <div class="flex items-center justify-between py-2 border-b border-gray-50">
                            <span class="text-xs text-gray-400 font-medium">Jenis Dokumen</span>
                            <span class="text-sm font-bold text-gray-800">{{ $pengajuan->jenisDokumen->nama }}</span>
                        </div>
                        <div class="flex items-center justify-between py-2 border-b border-gray-50">
                            <span class="text-xs text-gray-400 font-medium">Nama Guru</span>
                            <span class="text-sm font-bold text-gray-800">{{ $pengajuan->nama_guru }}</span>
                        </div>
                        <div class="flex items-center justify-between py-2">
                            <span class="text-xs text-gray-400 font-medium">Nama Madrasah</span>
                            <span
                                class="text-sm font-bold text-gray-800 text-right max-w-[60%]">{{ $pengajuan->nama_madrasah }}</span>
                        </div>
                    </div>

                    {{-- Status badge --}}
                    <div
                        class="flex items-center justify-center gap-2 bg-yellow-50 border border-yellow-100 rounded-xl py-2.5 mb-5">
                        <span class="w-2 h-2 rounded-full bg-yellow-400 animate-pulse"></span>
                        <span class="text-yellow-700 text-sm font-bold">Menunggu Diproses</span>
                    </div>

                    {{-- Buttons --}}
                    <div class="flex gap-3">
                        <a href="{{ route('status.index') }}"
                            class="flex-1 text-white text-sm font-semibold py-3 rounded-xl transition text-center hover:opacity-90 flex items-center justify-center gap-2"
                            style="background: linear-gradient(135deg, #15803d, #22c55e)">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            Pantau Status
                        </a>
                        <a href="{{ route('home') }}"
                            class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-semibold py-3 rounded-xl transition text-center">
                            Kembali
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>
    @push('scripts')
        <script>
            function copyKode() {
                const kode = document.getElementById('kode-ajuan').textContent;
                navigator.clipboard.writeText(kode).then(() => {
                    // Ganti icon jadi centang
                    document.getElementById('icon-copy').classList.add('hidden');
                    document.getElementById('icon-check').classList.remove('hidden');
                    document.getElementById('btn-copy').classList.add('bg-green-100', 'text-green-600');

                    // Kembalikan icon semula setelah 2 detik
                    setTimeout(() => {
                        document.getElementById('icon-copy').classList.remove('hidden');
                        document.getElementById('icon-check').classList.add('hidden');
                        document.getElementById('btn-copy').classList.remove('bg-green-100', 'text-green-600');
                    }, 2000);
                });
            }
        </script>
    @endpush
@endsection
