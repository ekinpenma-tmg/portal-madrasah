@extends('guru.layouts.app')
@section('title', 'Tambah Arsip')

@section('content')
<div class="max-w-2xl mx-auto fade-in">

    <a href="{{ route('guru.arsip.index') }}" class="inline-flex items-center gap-1 text-xs text-gray-400 hover:text-gray-600 mb-3">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Kembali ke Arsip
    </a>

    {{-- Panduan GDrive --}}
    <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 mb-4">
        <p class="text-xs font-semibold text-blue-800 mb-2">Cara mendapatkan link Google Drive</p>
        <ol class="text-xs text-blue-700 space-y-1">
            <li>1. Upload file ke Google Drive Anda</li>
            <li>2. Klik kanan file → <strong>Share / Bagikan</strong></li>
            <li>3. Ubah akses menjadi <strong>"Siapa saja yang memiliki link"</strong></li>
            <li>4. Klik <strong>Copy link</strong> lalu tempel di kolom di bawah</li>
        </ol>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h1 class="text-sm font-semibold text-gray-900">Tambah Arsip Baru</h1>
        </div>

        <form action="{{ route('guru.arsip.store') }}" method="POST" class="p-5 space-y-4"
              x-data="{ linkVal: '{{ old('link_gdrive') }}', linkValid: null }">
            @csrf

            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1.5">Kategori Dokumen <span class="text-red-500">*</span></label>
                <select name="kategori_id" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-primary-300 @error('kategori_id') border-red-300 @enderror">
                    <option value="">— Pilih Kategori —</option>
                    @foreach($kategoriList as $kat)
                    <option value="{{ $kat->id }}" {{ old('kategori_id') == $kat->id ? 'selected' : '' }}>{{ $kat->nama }}</option>
                    @endforeach
                </select>
                <p class="text-xs text-gray-400 mt-1">1 kategori hanya boleh 1 dokumen per tahun — isi tahun yang beda kalau ini dokumen tahun lain.</p>
                @error('kategori_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1.5">Judul Dokumen <span class="text-red-500">*</span></label>
                <input type="text" name="judul" value="{{ old('judul') }}" required
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-primary-300 @error('judul') border-red-300 @enderror"
                    placeholder="Contoh: Ijazah S1 Pendidikan Agama Islam">
                @error('judul')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1.5">Link Google Drive <span class="text-red-500">*</span></label>
                <div class="relative">
                    <input type="url" name="link_gdrive" x-model="linkVal" required
                        placeholder="https://drive.google.com/file/d/..."
                        @input="linkValid = linkVal.includes('drive.google.com') || linkVal.includes('docs.google.com')"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 pr-9 text-sm focus:outline-none focus:ring-1 focus:ring-primary-300 @error('link_gdrive') border-red-300 @enderror"
                        :class="linkVal && !linkValid ? 'border-red-300' : (linkVal && linkValid ? 'border-green-300' : '')">
                    <div class="absolute right-2.5 top-1/2 -translate-y-1/2">
                        <svg x-show="linkVal && linkValid" x-cloak class="w-3.5 h-3.5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <svg x-show="linkVal && !linkValid" x-cloak class="w-3.5 h-3.5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </div>
                </div>
                <p x-show="linkVal && !linkValid" x-cloak class="text-red-500 text-xs mt-1">Link harus dari Google Drive atau Google Docs.</p>
                <p class="text-xs text-gray-400 mt-1">Pastikan file sudah di-share dengan akses "Anyone with the link".</p>
                @error('link_gdrive')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Tahun Dokumen</label>
                    <input type="number" name="tahun" value="{{ old('tahun', date('Y')) }}" min="1990" max="{{ date('Y') + 1 }}"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-primary-300">
                    @error('tahun')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Keterangan</label>
                    <input type="text" name="keterangan" value="{{ old('keterangan') }}"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-primary-300"
                        placeholder="Deskripsi singkat (opsional)">
                </div>
            </div>

            <div class="flex gap-2 pt-2" style="border-top:1px solid #f5f5f5">
                <button type="submit" class="btn-xs btn-primary-xs">Simpan Arsip</button>
                <a href="{{ route('guru.arsip.index') }}" class="btn-xs btn-ghost-xs">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
