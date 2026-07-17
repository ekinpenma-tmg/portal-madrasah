@extends('guru.layouts.app')
@section('title', 'Edit Arsip')

@section('content')
<div class="max-w-2xl mx-auto fade-in">

    <a href="{{ route('guru.arsip.index') }}" class="inline-flex items-center gap-1 text-xs text-gray-400 hover:text-gray-600 mb-3">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Kembali ke Arsip
    </a>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between gap-3">
            <div class="min-w-0">
                <h1 class="text-sm font-semibold text-gray-900">Edit Arsip</h1>
                <p class="text-xs text-gray-400 mt-0.5 truncate">{{ $arsip->judul }}</p>
            </div>
            <span class="badge {{ $arsip->is_verified ? 'badge-green' : 'badge-yellow' }} flex-shrink-0">
                {{ $arsip->is_verified ? 'Terverifikasi' : 'Menunggu Verifikasi' }}
            </span>
        </div>

        @if($arsip->is_verified)
        <div class="px-5 pt-4">
            <div class="bg-amber-50 border border-amber-100 rounded-lg px-4 py-2.5 flex items-center gap-2 text-xs text-amber-700">
                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Arsip ini sudah diverifikasi. Mengubah link Google Drive akan mereset status verifikasi.
            </div>
        </div>
        @elseif($arsip->catatan_admin)
        <div class="px-5 pt-4">
            <div class="bg-gray-50 border border-gray-100 rounded-lg px-4 py-2.5 text-xs text-gray-600">
                <strong class="text-gray-700">Catatan Admin:</strong> {{ $arsip->catatan_admin }}
            </div>
        </div>
        @endif

        <form action="{{ route('guru.arsip.update', $arsip->id) }}" method="POST" class="p-5 space-y-4"
              x-data="{ linkVal: '{{ old('link_gdrive', $arsip->link_gdrive) }}', linkValid: true }">
            @csrf @method('PUT')

            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1.5">Kategori Dokumen <span class="text-red-500">*</span></label>
                <select name="kategori_id" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-primary-300">
                    <option value="">— Pilih Kategori —</option>
                    @foreach($kategoriList as $kat)
                    <option value="{{ $kat->id }}" {{ old('kategori_id', $arsip->kategori_id) == $kat->id ? 'selected' : '' }}>{{ $kat->nama }}</option>
                    @endforeach
                </select>
                <p class="text-xs text-gray-400 mt-1">1 kategori hanya boleh 1 dokumen per tahun.</p>
                @error('kategori_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1.5">Judul Dokumen <span class="text-red-500">*</span></label>
                <input type="text" name="judul" value="{{ old('judul', $arsip->judul) }}" required
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-primary-300">
                @error('judul')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1.5">Link Google Drive <span class="text-red-500">*</span></label>
                <div class="relative">
                    <input type="url" name="link_gdrive" x-model="linkVal" required
                        @input="linkValid = linkVal.includes('drive.google.com') || linkVal.includes('docs.google.com')"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 pr-9 text-sm focus:outline-none focus:ring-1 focus:ring-primary-300">
                    <div class="absolute right-2.5 top-1/2 -translate-y-1/2">
                        <svg x-show="linkVal && linkValid" x-cloak class="w-3.5 h-3.5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </div>
                </div>
                <div class="flex items-center justify-between mt-1">
                    <p class="text-xs text-gray-400">Pastikan link sudah di-share publik di Google Drive.</p>
                    <a :href="linkVal" target="_blank" x-show="linkVal && linkValid" x-cloak class="text-xs text-blue-600 hover:underline font-medium">Buka file &rarr;</a>
                </div>
                @error('link_gdrive')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Tahun Dokumen</label>
                    <input type="number" name="tahun" value="{{ old('tahun', $arsip->tahun) }}" min="1990" max="{{ date('Y') + 1 }}"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-primary-300">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Keterangan</label>
                    <input type="text" name="keterangan" value="{{ old('keterangan', $arsip->keterangan) }}"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-primary-300"
                        placeholder="Deskripsi singkat (opsional)">
                </div>
            </div>

            <div class="flex gap-2 pt-2" style="border-top:1px solid #f5f5f5">
                <button type="submit" class="btn-xs btn-primary-xs">Simpan Perubahan</button>
                <a href="{{ route('guru.arsip.index') }}" class="btn-xs btn-ghost-xs">Batal</a>
            </div>
        </form>
    </div>

    {{-- Zona hapus --}}
    <div class="mt-3 bg-white rounded-xl border border-gray-200 p-5">
        <h3 class="text-xs font-semibold text-gray-700 mb-1">Hapus Arsip Ini</h3>
        <p class="text-xs text-gray-400 mb-3">Arsip yang dihapus tidak bisa dikembalikan.</p>
        <form method="POST" action="{{ route('guru.arsip.destroy', $arsip->id) }}"
              data-confirm="Hapus arsip ini?&#10;&#10;{{ $arsip->judul }}&#10;&#10;Tidak bisa dikembalikan." data-confirm-btn="Ya, Hapus">
            @csrf @method('DELETE')
            <button type="submit" class="btn-xs" style="background:#fef2f2; border-color:#fecaca; color:#dc2626;">Hapus Arsip</button>
        </form>
    </div>

</div>
@endsection
