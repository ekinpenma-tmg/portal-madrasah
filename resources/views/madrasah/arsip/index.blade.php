@extends('madrasah.layouts.app')
@section('title', 'Arsip Madrasah')

@section('content')
<div class="flex items-center justify-between mb-5 fade-in">
    <div>
        <h1 class="text-lg font-semibold text-gray-900">Arsip Madrasah</h1>
        <p class="text-sm text-gray-500 mt-0.5">Kelola dokumen digital madrasah Anda</p>
    </div>
    <a href="{{ route('madrasah.arsip.create') }}" class="btn-xs btn-primary-xs">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Tambah Arsip
    </a>
</div>

{{-- Filter --}}
<div class="bg-white rounded-xl border border-gray-200 px-4 py-3 mb-4 fade-in">
    <form method="GET" class="flex flex-wrap gap-2 items-center">
        <div class="relative flex-1 min-w-48">
            <svg class="absolute left-2.5 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul atau keterangan..."
                class="w-full border border-gray-200 rounded-lg pl-8 pr-3 py-1.5 text-xs focus:outline-none focus:ring-1 focus:ring-primary-300">
        </div>
        <select name="kategori_id" onchange="this.form.submit()" class="border border-gray-200 rounded-lg px-3 py-1.5 text-xs focus:outline-none bg-white">
            <option value="">Semua Kategori</option>
            @foreach($kategoriList as $kat)
            <option value="{{ $kat->id }}" {{ request('kategori_id') == $kat->id ? 'selected' : '' }}>{{ $kat->nama }}</option>
            @endforeach
        </select>
        @if($tahunList->count())
        <select name="tahun" onchange="this.form.submit()" class="border border-gray-200 rounded-lg px-3 py-1.5 text-xs focus:outline-none bg-white">
            <option value="">Semua Tahun</option>
            @foreach($tahunList as $t)
            <option value="{{ $t }}" {{ request('tahun') == $t ? 'selected' : '' }}>{{ $t }}</option>
            @endforeach
        </select>
        @endif
        <select name="verified" onchange="this.form.submit()" class="border border-gray-200 rounded-lg px-3 py-1.5 text-xs focus:outline-none bg-white">
            <option value="">Semua Status</option>
            <option value="1" {{ request('verified') === '1' ? 'selected' : '' }}>Terverifikasi</option>
            <option value="0" {{ request('verified') === '0' ? 'selected' : '' }}>Belum Verifikasi</option>
        </select>
        @if(request()->hasAny(['search','kategori_id','tahun','verified']))
        <a href="{{ route('madrasah.arsip.index') }}" class="text-xs text-gray-400 hover:text-gray-600 self-center">Reset</a>
        @endif
    </form>
</div>

<div class="bg-white rounded-xl border border-gray-200 overflow-hidden fade-in">
    @if($arsip->isEmpty())
    <div class="py-14 text-center">
        <svg class="w-10 h-10 mx-auto text-gray-200 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
        @if(request()->hasAny(['search','kategori_id','tahun','verified']))
        <p class="text-sm font-medium text-gray-500 mb-1">Tidak ada arsip yang cocok</p>
        <p class="text-xs text-gray-400 mb-3">Coba ubah kata kunci atau filter kategori/tahun/status</p>
        <a href="{{ route('madrasah.arsip.index') }}" class="text-xs text-primary-700 hover:underline">Reset filter</a>
        @else
        <p class="text-sm font-medium text-gray-500 mb-1">Belum ada arsip</p>
        <p class="text-xs text-gray-400 mb-3">Dokumen yang madrasah unggah akan muncul di sini</p>
        <a href="{{ route('madrasah.arsip.create') }}" class="inline-flex items-center gap-1.5 btn-xs btn-primary-xs">Tambah Arsip Pertama</a>
        @endif
    </div>
    @else
    {{-- Desktop: tabel --}}
    <div class="hidden md:block">
    <table class="data-table">
        <thead>
            <tr>
                <th>Judul Dokumen</th>
                <th class="hidden sm:table-cell">Kategori</th>
                <th class="hidden md:table-cell">Tahun</th>
                <th>Status</th>
                <th style="text-align:right">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($arsip as $a)
            <tr>
                <td>
                    <div class="flex items-center gap-2.5">
                        <div class="w-7 h-7 rounded-lg flex items-center justify-center text-white text-xs font-semibold flex-shrink-0"
                             style="background:linear-gradient(135deg,#0d3a7c,#c9a163)">
                            {{ strtoupper(substr($a->kategori->nama ?? 'A', 0, 1)) }}
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-gray-800 truncate max-w-48" title="{{ $a->judul }}">{{ $a->judul }}</p>
                            <p class="text-xs text-gray-400 truncate max-w-48 sm:hidden">{{ $a->kategori->nama ?? '—' }}</p>
                            @if($a->keterangan)<p class="text-xs text-gray-400 truncate max-w-48" title="{{ $a->keterangan }}">{{ $a->keterangan }}</p>@endif
                        </div>
                    </div>
                </td>
                <td class="hidden sm:table-cell"><span class="text-xs text-gray-600">{{ $a->kategori->nama ?? '—' }}</span></td>
                <td class="hidden md:table-cell"><span class="text-xs text-gray-500">{{ $a->tahun ?? '—' }}</span></td>
                <td><span class="badge {{ $a->is_verified ? 'badge-green' : 'badge-yellow' }}">{{ $a->is_verified ? 'Verified' : 'Pending' }}</span></td>
                <td style="text-align:right">
                    <div class="flex items-center justify-end gap-1">
                        <a href="{{ $a->link_gdrive }}" target="_blank" class="btn-icon blue" title="Buka File">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        </a>
                        <a href="{{ route('madrasah.arsip.edit', $a->id) }}" class="btn-icon" title="Edit">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </a>
                        <form action="{{ route('madrasah.arsip.destroy', $a->id) }}" method="POST" data-confirm="Hapus arsip ini?" data-confirm-btn="Ya, Hapus" class="inline">
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
    <div class="px-4 py-3" style="border-top:1px solid #f4f4f5">{{ $arsip->links() }}</div>
    </div>

    {{-- Mobile: card list --}}
    <div class="md:hidden divide-y divide-gray-50">
        @foreach($arsip as $a)
        <div class="p-4">
            <div class="flex items-start gap-3">
                <div class="w-9 h-9 rounded-lg flex items-center justify-center text-white text-xs font-semibold flex-shrink-0"
                     style="background:linear-gradient(135deg,#0d3a7c,#c9a163)">
                    {{ strtoupper(substr($a->kategori->nama ?? 'A', 0, 1)) }}
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-medium text-gray-800">{{ $a->judul }}</p>
                    <p class="text-xs text-gray-400 mt-0.5">
                        {{ $a->kategori->nama ?? '—' }}
                        @if($a->tahun) · {{ $a->tahun }} @endif
                    </p>
                    @if($a->keterangan)<p class="text-xs text-gray-400 mt-0.5">{{ $a->keterangan }}</p>@endif
                </div>
                <span class="badge {{ $a->is_verified ? 'badge-green' : 'badge-yellow' }} flex-shrink-0">{{ $a->is_verified ? 'Verified' : 'Pending' }}</span>
            </div>
            <div class="flex items-center gap-2 mt-3">
                <a href="{{ $a->link_gdrive }}" target="_blank"
                   class="flex-1 inline-flex items-center justify-center gap-1.5 text-xs font-medium py-2.5 rounded-lg border border-gray-200 text-gray-700 active:bg-gray-50">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    Buka File
                </a>
                <a href="{{ route('madrasah.arsip.edit', $a->id) }}"
                   class="inline-flex items-center justify-center w-11 h-11 rounded-lg border border-gray-200 text-gray-600 active:bg-gray-50 flex-shrink-0" title="Edit">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                </a>
                <form action="{{ route('madrasah.arsip.destroy', $a->id) }}" method="POST" data-confirm="Hapus arsip ini?" data-confirm-btn="Ya, Hapus" class="flex-shrink-0">
                    @csrf @method('DELETE')
                    <button type="submit" class="inline-flex items-center justify-center w-11 h-11 rounded-lg border border-red-200 text-red-500 active:bg-red-50" title="Hapus">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </form>
            </div>
        </div>
        @endforeach
        <div class="px-4 py-3" style="border-top:1px solid #f4f4f5">{{ $arsip->links() }}</div>
    </div>
    @endif
</div>
@endsection
