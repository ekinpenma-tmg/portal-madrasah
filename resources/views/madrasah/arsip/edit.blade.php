@extends('madrasah.layouts.app')
@section('title', 'Edit Arsip')

@section('content')
<div class="max-w-2xl mx-auto fade-in">

    <a href="{{ route('madrasah.arsip.index') }}" class="inline-flex items-center gap-1 text-xs text-gray-400 hover:text-gray-600 mb-3">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Kembali ke Arsip
    </a>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h1 class="text-sm font-semibold text-gray-900">Edit Arsip</h1>
            <p class="text-xs text-gray-400 mt-0.5">{{ $arsip->judul }}</p>
        </div>

        @if($arsip->is_verified)
        <div class="px-5 pt-4">
            <div class="bg-amber-50 border border-amber-100 rounded-lg px-4 py-2.5 flex items-center gap-2 text-xs text-amber-700">
                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Arsip ini sudah diverifikasi. Mengubah link Google Drive akan mereset status verifikasi.
            </div>
        </div>
        @endif

        <form action="{{ route('madrasah.arsip.update', $arsip->id) }}" method="POST" class="p-5 space-y-4">
            @csrf @method('PUT')

            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1.5">Kategori Dokumen <span class="text-red-500">*</span></label>
                <select name="kategori_id" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-primary-300">
                    <option value="">— Pilih Kategori —</option>
                    @foreach($kategoriList as $k)
                    <option value="{{ $k->id }}" {{ old('kategori_id', $arsip->kategori_id) == $k->id ? 'selected' : '' }}>{{ $k->nama }}</option>
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
                <input type="url" name="link_gdrive" value="{{ old('link_gdrive', $arsip->link_gdrive) }}" required
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-primary-300">
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
                <a href="{{ route('madrasah.arsip.index') }}" class="btn-xs btn-ghost-xs">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
