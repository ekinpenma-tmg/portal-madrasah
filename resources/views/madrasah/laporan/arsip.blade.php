@extends('madrasah.layouts.app')
@section('title', 'Kelengkapan Arsip Guru')
@section('content')

<div class="flex items-center justify-between mb-4 flex-wrap gap-2 fade-in">
    <div>
        <h1 class="text-base font-semibold text-zinc-900">Kelengkapan Arsip Guru</h1>
        <p class="text-xs text-zinc-400 mt-0.5">Kategori dokumen yang belum diisi masing-masing guru di madrasah ini</p>
    </div>
</div>

<div class="bg-white rounded-xl border border-zinc-100 px-4 py-3 mb-4">
    <form method="GET" class="flex items-center gap-2">
        <label class="flex items-center gap-1.5 text-xs text-zinc-600">
            <input type="checkbox" name="hanya_belum_lengkap" value="1" {{ request('hanya_belum_lengkap') ? 'checked' : '' }} onchange="this.form.submit()">
            Hanya tampilkan yang belum lengkap
        </label>
    </form>
</div>

<div class="bg-white rounded-xl border border-zinc-100 overflow-hidden">
    @if($rekap->isEmpty())
    <div class="py-14 text-center">
        <svg class="w-10 h-10 mx-auto text-zinc-200 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <p class="text-sm text-zinc-400">{{ request('hanya_belum_lengkap') ? 'Semua guru sudah lengkap dokumennya.' : 'Belum ada guru aktif di madrasah ini.' }}</p>
    </div>
    @else
    {{-- Desktop: tabel --}}
    <div class="hidden md:block">
    <table class="w-full">
        <thead>
            <tr class="tbl-head">
                <th class="px-4 py-2.5 text-left text-2xs font-semibold text-zinc-600 uppercase tracking-wider">Guru</th>
                <th class="px-4 py-2.5 text-center text-2xs font-semibold text-zinc-600 uppercase tracking-wider">Terisi</th>
                <th class="px-4 py-2.5 text-center text-2xs font-semibold text-zinc-600 uppercase tracking-wider">% Lengkap</th>
                <th class="px-4 py-2.5 text-left text-2xs font-semibold text-zinc-600 uppercase tracking-wider">Kategori Belum Diisi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rekap as $r)
            <tr class="tbl-row" style="border-bottom:1px solid #fafafa">
                <td class="px-4 py-3">
                    <p class="text-xs font-medium text-zinc-800">{{ $r->pemilik->nama }}</p>
                    <p class="text-2xs text-zinc-400 font-mono">{{ $r->pemilik->pegid }}</p>
                </td>
                <td class="px-4 py-3 text-center"><span class="text-xs font-semibold text-zinc-800">{{ $r->total_terisi }}/{{ $r->total_kategori }}</span></td>
                <td class="px-4 py-3 text-center">
                    @if($r->persen_lengkap !== null)
                    <span class="text-xs font-semibold {{ $r->persen_lengkap == 100 ? 'text-green-600' : ($r->persen_lengkap >= 50 ? 'text-amber-600' : 'text-red-600') }}">{{ $r->persen_lengkap }}%</span>
                    @else
                    <span class="text-2xs text-zinc-300">—</span>
                    @endif
                </td>
                <td class="px-4 py-3">
                    @if($r->kategori_belum->isEmpty())
                    <span class="badge badge-green">Lengkap</span>
                    @else
                    <div class="flex flex-wrap gap-1 max-w-md">
                        @foreach($r->kategori_belum as $nama)
                        <span class="badge badge-red">{{ $nama }}</span>
                        @endforeach
                    </div>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    </div>

    {{-- Mobile: card list --}}
    <div class="md:hidden divide-y divide-zinc-50">
        @foreach($rekap as $r)
        <div class="p-4">
            <div class="flex items-center justify-between gap-2 mb-2">
                <div class="min-w-0">
                    <p class="text-sm font-medium text-zinc-800 truncate">{{ $r->pemilik->nama }}</p>
                    <p class="text-2xs text-zinc-400 font-mono">{{ $r->pemilik->pegid }}</p>
                </div>
                @if($r->persen_lengkap !== null)
                <span class="text-base font-bold flex-shrink-0 {{ $r->persen_lengkap == 100 ? 'text-green-600' : ($r->persen_lengkap >= 50 ? 'text-amber-600' : 'text-red-600') }}">{{ $r->persen_lengkap }}%</span>
                @endif
            </div>
            <p class="text-xs text-zinc-400 mb-2">{{ $r->total_terisi }}/{{ $r->total_kategori }} kategori terisi</p>
            @if($r->kategori_belum->isEmpty())
            <span class="badge badge-green">Lengkap</span>
            @else
            <div class="flex flex-wrap gap-1">
                @foreach($r->kategori_belum as $nama)
                <span class="badge badge-red">{{ $nama }}</span>
                @endforeach
            </div>
            @endif
        </div>
        @endforeach
    </div>
    @endif
</div>

@endsection
