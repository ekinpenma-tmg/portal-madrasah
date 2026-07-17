@extends('layouts.admin')
@section('title', 'Import Data Siswa')

@section('content')
<div class="max-w-xl fade-in">

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-2 text-xs text-zinc-400 mb-4">
        <a href="{{ route('admin.data-madrasah.index') }}" class="hover:text-zinc-600 transition">Data Madrasah</a>
        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
        <span class="text-zinc-600 font-medium">Import Data Siswa</span>
    </div>

    @if(session('error'))
    <div class="bg-red-50 border border-red-200 text-red-700 text-xs rounded-lg px-4 py-2.5 mb-4">{{ session('error') }}</div>
    @endif

    {{-- Penting: import madrasah dulu --}}
    <div class="bg-white rounded-xl border border-zinc-100 p-4 mb-4">
        <div class="flex items-start gap-2">
            <svg class="w-3.5 h-3.5 text-amber-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p class="text-2xs text-zinc-600"><span class="font-semibold text-zinc-800">Penting:</span> pastikan <strong>Data Madrasah sudah diimport terlebih dahulu</strong> sebelum import data siswa. Data siswa dicocokkan berdasarkan NSM — jika NSM tidak ditemukan, baris tersebut akan dilewati.</p>
        </div>
    </div>

    {{-- Panduan format --}}
    <div class="bg-white rounded-xl border border-zinc-100 p-4 mb-4">
        <div class="flex items-start justify-between gap-3 mb-3">
            <div>
                <p class="text-xs font-semibold text-zinc-800">Format Kolom yang Diperlukan</p>
                <p class="text-2xs text-zinc-400 mt-0.5">Nama kolom tidak case-sensitive. Baris pertama harus header.</p>
            </div>
            <a href="{{ route('admin.data-madrasah.import-siswa.template') }}"
               class="flex-shrink-0 inline-flex items-center gap-1.5 bg-zinc-900 hover:bg-zinc-800 text-white text-2xs font-semibold px-3 py-1.5 rounded-lg transition">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Template
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-1.5">
            <div class="flex items-center gap-2 bg-green-50 border border-green-100 rounded-lg px-3 py-2">
                <span class="badge badge-green flex-shrink-0">Wajib</span>
                <div>
                    <p class="text-xs font-semibold text-zinc-700">NSM</p>
                    <p class="text-2xs text-zinc-400">Cocokkan madrasah</p>
                </div>
            </div>
            <div class="flex items-center gap-2 bg-zinc-50 border border-zinc-100 rounded-lg px-3 py-2">
                <span class="badge badge-gray flex-shrink-0">Opsional</span>
                <div>
                    <p class="text-xs font-semibold text-zinc-700">Nama Lembaga</p>
                    <p class="text-2xs text-zinc-400">Hanya untuk referensi</p>
                </div>
            </div>
            <div class="flex items-center gap-2 bg-green-50 border border-green-100 rounded-lg px-3 py-2">
                <span class="badge badge-green flex-shrink-0">Wajib</span>
                <div>
                    <p class="text-xs font-semibold text-zinc-700">Siswa Laki-laki</p>
                    <p class="text-2xs text-zinc-400">Jumlah siswa</p>
                </div>
            </div>
            <div class="flex items-center gap-2 bg-green-50 border border-green-100 rounded-lg px-3 py-2">
                <span class="badge badge-green flex-shrink-0">Wajib</span>
                <div>
                    <p class="text-xs font-semibold text-zinc-700">Siswa Perempuan</p>
                    <p class="text-2xs text-zinc-400">Jumlah siswa</p>
                </div>
            </div>
            <div class="flex items-center gap-2 bg-green-50 border border-green-100 rounded-lg px-3 py-2">
                <span class="badge badge-green flex-shrink-0">Wajib</span>
                <div>
                    <p class="text-xs font-semibold text-zinc-700">Total Siswa</p>
                    <p class="text-2xs text-zinc-400">Total keseluruhan</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Form upload --}}
    <div class="bg-white rounded-xl border border-zinc-100 p-5">
        <form method="POST" action="{{ route('admin.data-madrasah.import-siswa.store') }}" enctype="multipart/form-data"
              x-data="{ fileName: '', loading: false }" @submit="loading = true">
            @csrf

            <div class="mb-4">
                <label class="block text-2xs font-semibold text-zinc-500 mb-2 uppercase tracking-wider">
                    File Excel EMIS Siswa <span class="text-red-500">*</span>
                </label>
                <label class="relative flex flex-col items-center justify-center w-full min-h-28
                              border-2 border-dashed rounded-xl cursor-pointer transition group"
                       :class="fileName ? 'border-zinc-400 bg-zinc-50' : 'border-zinc-200 hover:border-zinc-300 hover:bg-zinc-50/50'">
                    <input type="file" name="file" accept=".xlsx,.xls" required class="absolute inset-0 opacity-0 cursor-pointer"
                           @change="fileName = $event.target.files[0]?.name || ''">

                    <div x-show="!fileName" class="text-center px-5 py-5">
                        <svg class="w-8 h-8 mx-auto text-zinc-300 mb-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                        </svg>
                        <p class="text-xs text-zinc-400">Klik atau seret file Excel ke sini</p>
                        <p class="text-2xs text-zinc-300 mt-0.5">.xlsx atau .xls</p>
                    </div>
                    <div x-show="fileName" class="text-center px-5 py-5">
                        <svg class="w-8 h-8 mx-auto text-zinc-600 mb-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <p class="text-xs font-semibold text-zinc-700" x-text="fileName"></p>
                        <p class="text-2xs text-zinc-400 mt-0.5">Klik untuk ganti file</p>
                    </div>
                </label>
                @error('file') <p class="text-red-500 text-2xs mt-1.5">{{ $message }}</p> @enderror
            </div>

            <div class="mb-5">
                <label class="block text-2xs font-semibold text-zinc-500 mb-2 uppercase tracking-wider">
                    Tahun Pelajaran <span class="text-red-500">*</span>
                </label>
                <input type="text" name="tahun_pelajaran"
                    value="{{ old('tahun_pelajaran', date('Y') . '/' . (date('Y') + 1)) }}"
                    placeholder="Contoh: 2024/2025"
                    class="border border-zinc-200 rounded-lg px-3 py-1.5 text-xs w-36 focus:ring-1 focus:ring-zinc-300 focus:outline-none">
                <p class="text-2xs text-zinc-400 mt-1">Format: YYYY/YYYY — contoh: 2024/2025</p>
                @error('tahun_pelajaran') <p class="text-red-500 text-2xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mb-5">
                <label class="block text-2xs font-semibold text-zinc-500 mb-2 uppercase tracking-wider">Mode Import <span class="text-red-500">*</span></label>
                <div class="space-y-2">
                    <label class="flex items-start gap-2.5 p-3 border border-zinc-200 rounded-lg cursor-pointer hover:bg-zinc-50 transition">
                        <input type="radio" name="mode" value="update" checked class="mt-0.5 accent-zinc-800 flex-shrink-0">
                        <div>
                            <p class="text-xs font-semibold text-zinc-800">Perbarui yang sudah ada</p>
                            <p class="text-2xs text-zinc-500 mt-0.5">Data siswa madrasah untuk tahun pelajaran ini akan ditimpa dari file, data baru tetap ditambahkan.</p>
                        </div>
                    </label>
                    <label class="flex items-start gap-2.5 p-3 border border-zinc-200 rounded-lg cursor-pointer hover:bg-zinc-50 transition">
                        <input type="radio" name="mode" value="skip" class="mt-0.5 accent-zinc-800 flex-shrink-0">
                        <div>
                            <p class="text-xs font-semibold text-zinc-800">Lewati yang sudah ada</p>
                            <p class="text-2xs text-zinc-500 mt-0.5">Madrasah yang sudah punya data siswa untuk tahun pelajaran ini dilewati, hanya data baru yang ditambahkan.</p>
                        </div>
                    </label>
                </div>
                @error('mode') <p class="text-red-500 text-2xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center gap-2 pt-3" style="border-top:1px solid #f0f0f0">
                <button type="submit" :disabled="loading"
                    class="inline-flex items-center gap-1.5 text-xs font-bold px-5 py-2 rounded-lg text-white transition disabled:opacity-60 bg-zinc-900 hover:bg-zinc-800">
                    <svg x-show="!loading" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                    </svg>
                    <svg x-show="loading" class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                    <span x-text="loading ? 'Memproses...' : 'Mulai Import'"></span>
                </button>
                <a href="{{ route('admin.data-madrasah.index') }}" class="bg-zinc-100 hover:bg-zinc-200 text-zinc-600 text-xs font-semibold px-4 py-2 rounded-lg transition">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
