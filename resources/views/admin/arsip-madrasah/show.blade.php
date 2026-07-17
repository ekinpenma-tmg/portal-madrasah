@extends('layouts.admin')
@section('title', 'Arsip — ' . ($madrasahUser->madrasah->nama_madrasah ?? $madrasahUser->nsm))
@section('content')

<div class="flex items-center justify-between mb-4 fade-in">
    <div class="flex items-center gap-2.5">
        <a href="{{ route('admin.arsip-madrasah.index') }}" class="btn-icon">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <h1 class="text-sm font-semibold text-zinc-900">{{ $madrasahUser->madrasah->nama_madrasah ?? $madrasahUser->nsm }}</h1>
            <p class="text-xs text-zinc-400 mt-0.5">
                <code class="font-mono">{{ $madrasahUser->nsm }}</code>
                · {{ $madrasahUser->madrasah->jenjang ?? '' }}
                · PIC: {{ $madrasahUser->nama_pic }}
            </p>
        </div>
    </div>
    <span class="badge {{ $madrasahUser->is_active ? 'badge-green' : 'badge-gray' }}">{{ $madrasahUser->is_active ? 'Aktif' : 'Nonaktif' }}</span>
</div>

@if($arsip->isEmpty())
<div class="bg-white rounded-xl border border-zinc-100 py-14 text-center fade-in">
    <p class="text-xs text-zinc-400">Madrasah ini belum mengupload arsip apapun.</p>
</div>
@else
<div class="bg-white rounded-xl border border-zinc-100 overflow-hidden fade-in">
    <table class="w-full">
        <thead>
            <tr class="tbl-head">
                <th class="px-4 py-2.5 text-left">Judul Arsip</th>
                <th class="px-4 py-2.5 text-left hidden sm:table-cell">Kategori</th>
                <th class="px-4 py-2.5 text-left hidden md:table-cell">Tahun</th>
                <th class="px-4 py-2.5 text-center w-24">Status</th>
                <th class="px-4 py-2.5 text-center w-28">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($arsip as $a)
            <tr class="tbl-row" style="border-bottom:1px solid #fafafa">
                <td class="px-4 py-3">
                    <a href="{{ $a->link_gdrive }}" target="_blank" class="text-xs font-medium text-zinc-800 hover:text-green-700 truncate max-w-56 block">
                        {{ $a->judul }}
                        <svg class="w-3 h-3 inline-block ml-0.5 text-zinc-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    </a>
                    @if($a->keterangan)<p class="text-2xs text-zinc-400 mt-0.5 truncate max-w-56">{{ $a->keterangan }}</p>@endif
                    @if($a->catatan_admin)
                    <p class="text-2xs text-amber-600 mt-0.5">Catatan: {{ $a->catatan_admin }}</p>
                    @endif
                </td>
                <td class="px-4 py-3 hidden sm:table-cell"><span class="badge badge-gray">{{ $a->kategori->nama ?? '—' }}</span></td>
                <td class="px-4 py-3 hidden md:table-cell"><span class="text-xs text-zinc-500">{{ $a->tahun ?? '—' }}</span></td>
                <td class="px-4 py-3 text-center">
                    <span class="badge {{ $a->is_verified ? 'badge-green' : 'badge-yellow' }}">{{ $a->is_verified ? 'Verified' : 'Pending' }}</span>
                </td>
                <td class="px-4 py-3 text-center">
                    <div class="flex items-center justify-center gap-1">
                        @if(!$a->is_verified)
                        <form action="{{ route('admin.arsip-madrasah.verify', $a->id) }}" method="POST" class="inline">
                            @csrf @method('PATCH')
                            <button type="submit" class="btn-icon success" title="Verifikasi">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            </button>
                        </form>
                        @else
                        <form action="{{ route('admin.arsip-madrasah.unverify', $a->id) }}" method="POST" class="inline">
                            @csrf @method('PATCH')
                            <button type="submit" class="btn-icon warning" title="Batalkan Verifikasi">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </form>
                        @endif

                        {{-- Form catatan inline --}}
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open" class="btn-icon" title="Catatan Admin">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </button>
                            <div x-show="open" @click.outside="open = false" x-transition
                                 class="absolute right-0 top-8 z-20 bg-white rounded-lg border border-zinc-200 p-3 w-64"
                                 style="box-shadow:0 8px 24px rgba(0,0,0,0.1)">
                                <form action="{{ route('admin.arsip-madrasah.catatan', $a->id) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <p class="text-2xs font-medium text-zinc-500 mb-1.5">Catatan Admin</p>
                                    <textarea name="catatan_admin" rows="3"
                                        class="w-full border border-zinc-200 rounded-md px-2.5 py-1.5 text-xs focus:outline-none focus:ring-1 focus:ring-zinc-300 resize-none"
                                        placeholder="Tulis catatan untuk madrasah (opsional)">{{ $a->catatan_admin }}</textarea>
                                    <button type="submit" class="mt-2 w-full bg-zinc-900 text-white text-xs font-medium py-1.5 rounded-md hover:bg-zinc-700 transition">Simpan Catatan</button>
                                </form>
                            </div>
                        </div>

                        <form action="{{ route('admin.arsip-madrasah.destroy', $a->id) }}" method="POST" data-confirm="Hapus arsip ini?" data-confirm-btn="Ya, Hapus" class="inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn-icon danger" title="Hapus">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif
@endsection
