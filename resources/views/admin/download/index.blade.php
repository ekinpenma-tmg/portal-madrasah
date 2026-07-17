@extends('layouts.admin')
@section('title', 'Kelola File Download')
@section('content')

<div class="flex items-center justify-between mb-4 fade-in">
    <div>
        <h1 class="text-sm font-semibold text-zinc-900">File Download</h1>
        <p class="text-xs text-zinc-400 mt-0.5">Kelola dokumen yang dapat diunduh publik</p>
    </div>
    <a href="{{ route('admin.download.create') }}" class="inline-flex items-center gap-1.5 text-xs font-medium px-3 py-1.5 rounded-lg bg-zinc-900 text-white hover:bg-zinc-700 transition">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Tambah File
    </a>
</div>

<div class="bg-white rounded-xl border border-zinc-100 overflow-hidden fade-in">
    <table class="w-full">
        <thead>
            <tr class="tbl-head">
                <th class="px-4 py-2.5 text-left text-2xs font-semibold text-zinc-600 uppercase tracking-wider">Nama File</th>
                <th class="px-4 py-2.5 text-center text-2xs font-semibold text-zinc-600 uppercase tracking-wider hidden sm:table-cell">Kategori</th>
                <th class="px-4 py-2.5 text-center text-2xs font-semibold text-zinc-600 uppercase tracking-wider hidden md:table-cell">Download</th>
                <th class="px-4 py-2.5 text-center text-2xs font-semibold text-zinc-600 uppercase tracking-wider">Status</th>
                <th class="px-4 py-2.5 text-center text-2xs font-semibold text-zinc-600 uppercase tracking-wider">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($files as $file)
            <tr class="tbl-row" style="border-bottom:1px solid #fafafa">
                <td class="px-4 py-3">
                    <p class="text-xs font-medium text-zinc-800">{{ $file->nama }}</p>
                    @if($file->deskripsi)<p class="text-2xs text-zinc-400 mt-0.5">{{ Str::limit($file->deskripsi, 50) }}</p>@endif
                </td>
                <td class="px-4 py-3 hidden sm:table-cell"><span class="text-xs text-zinc-500">{{ $file->kategori ?? '—' }}</span></td>
                <td class="px-4 py-3 text-center hidden md:table-cell"><span class="text-xs font-semibold text-zinc-700">{{ $file->jumlah_download }}</span></td>
                <td class="px-4 py-3 text-center">
                    <span class="badge {{ $file->aktif ? 'badge-green' : 'badge-gray' }}">{{ $file->aktif ? 'Aktif' : 'Nonaktif' }}</span>
                </td>
                <td class="px-4 py-3 text-center">
                    <div class="flex items-center justify-center gap-1">
                        <a href="{{ route('admin.download.edit', $file->id) }}" class="btn-icon blue" title="Edit">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </a>
                        <form action="{{ route('admin.download.destroy', $file->id) }}" method="POST" data-confirm="Hapus file ini?" data-confirm-btn="Ya, Hapus" class="inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn-icon danger" title="Hapus">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="py-14 text-center">
                    <svg class="w-10 h-10 mx-auto text-zinc-200 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    <p class="text-xs text-zinc-400">Belum ada file</p>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-4 py-3" style="border-top:1px solid #f4f4f5">{{ $files->links() }}</div>
</div>
@endsection