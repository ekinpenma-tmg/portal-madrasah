@extends('layouts.admin')
@section('title', 'Laporan Kelengkapan Arsip')
@section('content')

<div class="mb-4 fade-in">
    <h1 class="text-base font-semibold text-zinc-900">Laporan Kelengkapan Arsip</h1>
    <p class="text-xs text-zinc-400 mt-0.5">Kategori dokumen mana saja yang belum diisi, per guru/madrasah</p>
</div>

{{-- Tab --}}
<div class="flex gap-1 mb-4 fade-in">
    <a href="{{ route('admin.laporan-arsip.guru') }}" class="px-4 py-2 rounded-lg text-xs font-semibold bg-zinc-900 text-white">Guru</a>
    <a href="{{ route('admin.laporan-arsip.madrasah') }}" class="px-4 py-2 rounded-lg text-xs font-semibold text-zinc-500 hover:bg-zinc-100 transition">Madrasah</a>
</div>

{{-- Filter --}}
<div class="bg-white rounded-xl border border-zinc-100 px-4 py-3 mb-4 fade-in">
    <form method="GET" class="flex flex-wrap gap-2 items-center">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama/PegID guru..."
            class="border border-zinc-200 rounded-lg px-3 py-1.5 text-xs focus:outline-none min-w-44 flex-1">
        <select name="madrasah_id" class="border border-zinc-200 rounded-lg px-3 py-1.5 text-xs bg-white focus:outline-none">
            <option value="">Semua Madrasah</option>
            @foreach($madrasahs as $m)
            <option value="{{ $m->id }}" {{ request('madrasah_id') == $m->id ? 'selected' : '' }}>{{ $m->label_lengkap }}</option>
            @endforeach
        </select>
        <label class="flex items-center gap-1.5 text-xs text-zinc-600 px-2">
            <input type="checkbox" name="hanya_belum_lengkap" value="1" {{ request('hanya_belum_lengkap') ? 'checked' : '' }} onchange="this.form.submit()">
            Hanya yang belum lengkap
        </label>
        <button type="submit" class="px-3 py-1.5 bg-zinc-900 text-white text-xs font-medium rounded-lg hover:bg-zinc-700 transition">Terapkan</button>
        @if(request()->hasAny(['search','madrasah_id','hanya_belum_lengkap']))
        <a href="{{ route('admin.laporan-arsip.guru') }}" class="text-xs text-zinc-400 hover:text-zinc-600">Reset</a>
        @endif
    </form>
</div>

<div class="bg-white rounded-xl border border-zinc-100 overflow-hidden fade-in">
    @if($rekap->isEmpty())
    <div class="py-14 text-center">
        <svg class="w-10 h-10 mx-auto text-zinc-200 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <p class="text-sm text-zinc-400">{{ request('hanya_belum_lengkap') ? 'Semua guru di halaman ini sudah lengkap dokumennya.' : 'Tidak ada guru sesuai filter ini.' }}</p>
    </div>
    @else
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="tbl-head">
                    <th class="px-4 py-2.5 text-left text-2xs font-semibold text-zinc-600 uppercase tracking-wider">Guru</th>
                    <th class="px-4 py-2.5 text-left text-2xs font-semibold text-zinc-600 uppercase tracking-wider hidden md:table-cell">Madrasah</th>
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
                    <td class="px-4 py-3 hidden md:table-cell"><span class="text-xs text-zinc-600">{{ $r->pemilik->madrasah->nama_madrasah ?? '—' }}</span></td>
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
    <div class="px-4 py-3">{{ $guruPage->withQueryString()->links() }}</div>
    @endif
</div>

@endsection
