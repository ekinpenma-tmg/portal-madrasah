@extends('layouts.admin')
@section('title', 'Detail Pengajuan')

@section('content')
<div class="max-w-3xl fade-in">

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-2 text-xs text-zinc-400 mb-4">
        <a href="{{ route('admin.pengajuan.index') }}" class="hover:text-zinc-600 transition">Pengajuan</a>
        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
        <span class="text-zinc-600 font-medium font-mono">{{ $pengajuan->kode_ajuan }}</span>
    </div>

    {{-- Hero card --}}
    <div class="bg-white rounded-2xl border border-zinc-100 overflow-hidden mb-4"
         style="box-shadow:0 1px 4px rgba(0,0,0,0.04)">

        {{-- Header gelap --}}
        <div class="px-5 py-4 flex items-center justify-between"
             style="background:#0c0c0c; border-bottom:1px solid rgba(255,255,255,0.06)">
            <div>
                <p class="text-2xs font-semibold text-zinc-500 uppercase tracking-widest mb-1">Kode Ajuan</p>
                <p class="text-xl font-bold font-mono tracking-widest text-white">{{ $pengajuan->kode_ajuan }}</p>
            </div>
            <span class="text-xs font-bold px-3 py-1.5 rounded-lg
                @if($pengajuan->status==='pending')  bg-amber-400/20 text-amber-400 ring-1 ring-amber-400/30
                @elseif($pengajuan->status==='diterima') bg-green-400/20 text-green-400 ring-1 ring-green-400/30
                @else bg-red-400/20 text-red-400 ring-1 ring-red-400/30 @endif">
                {{ $pengajuan->status_label }}
            </span>
        </div>

        {{-- Data grid --}}
        <div class="p-5">
            <div class="grid grid-cols-2 gap-2 mb-4">
                @foreach([
                    ['l'=>'Nama Guru',     'v'=>$pengajuan->nama_guru],
                    ['l'=>'NIP',           'v'=>$pengajuan->nip ?: '—'],
                    ['l'=>'Nama Madrasah', 'v'=>$pengajuan->nama_madrasah],
                    ['l'=>'Jenis Dokumen', 'v'=>$pengajuan->jenisDokumen->nama],
                    ['l'=>'Email',         'v'=>$pengajuan->email ?: '—'],
                    ['l'=>'No. HP',        'v'=>$pengajuan->no_hp ?: '—'],
                    ['l'=>'Tanggal Kirim', 'v'=>$pengajuan->created_at->format('d M Y, H:i')],
                    ['l'=>'Tanggal Proses','v'=>$pengajuan->tanggal_proses?->format('d M Y, H:i') ?? '—'],
                ] as $item)
                <div class="bg-zinc-50 rounded-lg px-3 py-2.5">
                    <p class="text-2xs text-zinc-400 font-medium uppercase tracking-wider mb-0.5">{{ $item['l'] }}</p>
                    <p class="text-xs font-semibold text-zinc-800">{{ $item['v'] }}</p>
                </div>
                @endforeach
            </div>

            @if($pengajuan->catatan)
            <div class="bg-amber-50 border border-amber-100 rounded-lg px-3 py-2.5 mb-4">
                <p class="text-2xs font-semibold text-amber-600 uppercase tracking-wider mb-0.5">Catatan</p>
                <p class="text-xs text-zinc-700">{{ $pengajuan->catatan }}</p>
            </div>
            @endif

            {{-- File PDF --}}
            <div class="border border-zinc-100 rounded-xl overflow-hidden"
                 x-data="{ preview: false, fullscreen: false }">

                {{-- Bar file --}}
                <div class="flex items-center gap-3 px-4 py-2.5 bg-zinc-50" style="border-bottom:1px solid #f0f0f0">
                    <div class="w-7 h-7 bg-red-100 rounded-lg flex items-center justify-center flex-shrink-0">
                        <svg class="w-3.5 h-3.5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-semibold text-zinc-700 truncate">{{ $pengajuan->nama_file_asli ?? 'Dokumen Pengajuan' }}</p>
                        <p class="text-2xs text-zinc-400">File PDF</p>
                    </div>
                    <div class="flex items-center gap-1.5 flex-shrink-0">
                        <button @click="preview = !preview"
                            class="inline-flex items-center gap-1 text-2xs font-semibold px-2.5 py-1.5 rounded-lg border transition"
                            :class="preview ? 'bg-primary-50 border-primary-200 text-primary-700' : 'bg-white border-zinc-200 text-zinc-600 hover:border-zinc-300'">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <span x-text="preview ? 'Tutup' : 'Preview'"></span>
                        </button>
                        <button x-show="preview" @click="fullscreen = !fullscreen" x-transition
                            class="inline-flex items-center gap-1 text-2xs font-semibold px-2.5 py-1.5 rounded-lg border border-zinc-200 bg-white text-zinc-600 hover:border-zinc-300 transition">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/>
                            </svg>
                            <span x-text="fullscreen ? 'Kecil' : 'Perbesar'"></span>
                        </button>
                        <a href="{{ route('admin.pengajuan.dokumen', $pengajuan->id) }}" target="_blank"
                           class="inline-flex items-center gap-1 text-2xs font-semibold px-2.5 py-1.5 rounded-lg text-white bg-primary-700 hover:bg-primary-800 transition">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                            </svg>
                            Buka PDF
                        </a>
                    </div>
                </div>

                {{-- Preview iframe --}}
                <div x-show="preview" x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                    <div x-show="!fullscreen">
                        <iframe src="{{ route('admin.pengajuan.dokumen', $pengajuan->id) }}"
                                class="w-full border-0 bg-zinc-100" style="height:560px"
                                title="Preview PDF"></iframe>
                    </div>
                    <div x-show="fullscreen" x-cloak class="fixed inset-0 z-50 flex flex-col" style="background:rgba(0,0,0,0.9)">
                        <div class="flex items-center justify-between px-4 py-2.5 bg-zinc-900 flex-shrink-0" style="border-bottom:1px solid rgba(255,255,255,0.08)">
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 bg-red-500/20 rounded-lg flex items-center justify-center">
                                    <svg class="w-3 h-3 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <p class="text-white text-xs font-semibold">{{ $pengajuan->kode_ajuan }} — {{ $pengajuan->nama_guru }}</p>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <a href="{{ route('admin.pengajuan.dokumen', $pengajuan->id) }}" target="_blank"
                                   class="text-2xs font-semibold px-2.5 py-1.5 rounded-lg text-white bg-primary-700 hover:bg-primary-800 transition">
                                    Buka di Tab Baru
                                </a>
                                <button @click="fullscreen=false"
                                    class="text-2xs font-semibold px-2.5 py-1.5 rounded-lg bg-zinc-700 hover:bg-zinc-600 text-white transition">
                                    ✕ Tutup
                                </button>
                            </div>
                        </div>
                        <iframe src="{{ route('admin.pengajuan.dokumen', $pengajuan->id) }}" class="flex-1 w-full border-0"></iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Aksi Terima / Tolak (hanya jika pending) --}}
    @if($pengajuan->status === 'pending')
    <div class="grid grid-cols-2 gap-3">

        {{-- Terima --}}
        <div class="bg-white rounded-xl border border-zinc-100 p-4" x-data="{ open: false }">
            <div class="flex items-center gap-2 mb-1">
                <div class="w-6 h-6 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <svg class="w-3 h-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <p class="text-xs font-semibold text-green-700">Terima Pengajuan</p>
            </div>
            <p class="text-2xs text-zinc-400 mb-3">Pengajuan diterima dan status diperbarui.</p>
            <button @click="open = !open"
                class="w-full text-xs font-semibold py-2 rounded-lg text-white bg-green-600 hover:bg-green-700 transition">
                Terima
            </button>
            <div x-show="open" x-transition class="mt-3">
                <form action="{{ route('admin.pengajuan.terima', $pengajuan->id) }}" method="POST">
                    @csrf
                    <textarea name="catatan" rows="2" placeholder="Catatan (opsional)"
                              class="w-full border border-zinc-200 rounded-lg px-3 py-2 text-xs mb-2 focus:outline-none focus:ring-1 focus:ring-green-400 resize-none"></textarea>
                    <button type="submit"
                        class="w-full text-xs font-bold py-2 rounded-lg text-white bg-green-700 hover:bg-green-800 transition">
                        Konfirmasi Terima
                    </button>
                </form>
            </div>
        </div>

        {{-- Tolak --}}
        <div class="bg-white rounded-xl border border-zinc-100 p-4" x-data="{ open: false }">
            <div class="flex items-center gap-2 mb-1">
                <div class="w-6 h-6 bg-red-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <svg class="w-3 h-3 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </div>
                <p class="text-xs font-semibold text-red-700">Tolak Pengajuan</p>
            </div>
            <p class="text-2xs text-zinc-400 mb-3">Wajib isi alasan penolakan.</p>
            <button @click="open = !open"
                class="w-full text-xs font-semibold py-2 rounded-lg text-white bg-red-600 hover:bg-red-700 transition">
                Tolak
            </button>
            <div x-show="open" x-transition class="mt-3">
                <form action="{{ route('admin.pengajuan.tolak', $pengajuan->id) }}" method="POST">
                    @csrf
                    <textarea name="catatan" rows="2" required placeholder="Alasan penolakan (wajib)"
                              class="w-full border border-zinc-200 rounded-lg px-3 py-2 text-xs mb-2 focus:outline-none focus:ring-1 focus:ring-red-400 resize-none"></textarea>
                    <button type="submit"
                        class="w-full text-xs font-bold py-2 rounded-lg text-white bg-red-700 hover:bg-red-800 transition">
                        Konfirmasi Tolak
                    </button>
                </form>
            </div>
        </div>
    </div>
    @endif

</div>
@endsection
