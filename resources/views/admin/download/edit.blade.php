@extends('layouts.admin')
@section('title', 'Edit File Download')
@section('content')

<div class="flex items-center justify-between mb-4 fade-in">
    <div>
        <h1 class="text-sm font-semibold text-zinc-900">Edit File Download</h1>
        <p class="text-xs text-zinc-400 mt-0.5">Perbarui informasi dan file unduhan</p>
    </div>
    <a href="{{ route('admin.download.index') }}" class="inline-flex items-center gap-1.5 text-xs font-medium px-3 py-1.5 rounded-lg border border-zinc-200 text-zinc-600 hover:bg-zinc-50 transition">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Kembali
    </a>
</div>

<div class="bg-white rounded-xl border border-zinc-100 p-5 max-w-xl fade-in">

    @if($errors->any())
        <div class="mb-4 p-3 rounded-lg bg-red-50 border border-red-100 text-xs text-red-700 flex items-start gap-2">
            <svg class="w-3.5 h-3.5 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <ul class="space-y-0.5">
                @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.download.update', $file->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="space-y-4">

            <div>
                <label class="block text-xs font-medium text-zinc-600 mb-1.5">Nama File <span class="text-red-500">*</span></label>
                <input type="text" name="nama" value="{{ old('nama', $file->nama) }}"
                    placeholder="Nama dokumen yang ditampilkan"
                    class="w-full px-3 py-2 border border-zinc-200 rounded-lg text-xs text-zinc-800 focus:outline-none focus:ring-1 focus:ring-zinc-300"
                    required autofocus>
            </div>

            <div>
                <label class="block text-xs font-medium text-zinc-600 mb-1.5">Kategori</label>
                <input type="text" name="kategori" value="{{ old('kategori', $file->kategori) }}"
                    placeholder="Contoh: Surat Edaran, Formulir, Panduan"
                    class="w-full px-3 py-2 border border-zinc-200 rounded-lg text-xs text-zinc-800 focus:outline-none focus:ring-1 focus:ring-zinc-300">
            </div>

            <div>
                <label class="block text-xs font-medium text-zinc-600 mb-1.5">Deskripsi</label>
                <textarea name="deskripsi" rows="3"
                    placeholder="Deskripsi singkat tentang file ini (opsional)"
                    class="w-full px-3 py-2 border border-zinc-200 rounded-lg text-xs text-zinc-800 focus:outline-none focus:ring-1 focus:ring-zinc-300 resize-none">{{ old('deskripsi', $file->deskripsi) }}</textarea>
            </div>

            {{-- File saat ini --}}
            <div>
                <label class="block text-xs font-medium text-zinc-600 mb-1.5">File Saat Ini</label>
                <div class="flex items-center gap-2.5 p-2.5 bg-blue-50 border border-blue-100 rounded-lg">
                    <div class="w-7 h-7 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                        <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-medium text-blue-800 truncate">{{ $file->nama_file_asli }}</p>
                        <p class="text-2xs text-blue-500 mt-0.5">File aktif saat ini</p>
                    </div>
                    <a href="{{ Storage::url($file->file_path) }}" target="_blank"
                       class="text-2xs font-medium text-blue-600 hover:text-blue-800 bg-white border border-blue-200 px-2.5 py-1 rounded-md transition flex-shrink-0">
                        Lihat
                    </a>
                </div>
            </div>

            {{-- Ganti File --}}
            <div>
                <label class="block text-xs font-medium text-zinc-600 mb-1.5">Ganti File <span class="text-zinc-400 font-normal">(opsional)</span></label>
                <label class="flex items-center gap-2.5 cursor-pointer border border-dashed border-zinc-200 hover:border-zinc-400 rounded-lg p-3 transition" id="fileDropzone">
                    <div class="w-8 h-8 bg-zinc-100 rounded-lg flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-xs font-medium text-zinc-700" id="fileLabel">Klik untuk pilih file baru</p>
                        <p class="text-2xs text-zinc-400 mt-0.5">PDF, DOC, DOCX, XLS, dll — max 10MB. Kosongkan jika tidak ingin mengganti.</p>
                    </div>
                    <input type="file" name="file" id="fileInput" class="hidden">
                </label>
            </div>

            <div>
                <label class="block text-xs font-medium text-zinc-600 mb-1.5">Status</label>
                <label class="flex items-center gap-2.5 cursor-pointer bg-zinc-50 border border-zinc-200 rounded-lg px-3 py-2 hover:bg-zinc-100 transition w-fit">
                    <div class="relative">
                        <input type="checkbox" name="aktif" value="1" id="toggleAktif"
                            {{ old('aktif', $file->aktif) ? 'checked' : '' }}
                            class="sr-only peer">
                        <div class="w-8 h-4 bg-zinc-300 peer-checked:bg-green-600 rounded-full transition-colors duration-200"></div>
                        <div class="absolute top-0.5 left-0.5 w-3 h-3 bg-white rounded-full shadow transition-transform duration-200 peer-checked:translate-x-4"></div>
                    </div>
                    <span class="text-xs font-medium text-zinc-700" id="statusLabel">
                        {{ old('aktif', $file->aktif) ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </label>
            </div>

        </div>

        <div class="flex items-center gap-2 pt-4 mt-4" style="border-top:1px solid #f4f4f5">
            <button type="submit" class="inline-flex items-center gap-1.5 bg-zinc-900 hover:bg-zinc-700 text-white text-xs font-semibold px-4 py-2 rounded-lg transition">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Simpan Perubahan
            </button>
            <a href="{{ route('admin.download.index') }}" class="text-xs font-medium text-zinc-500 hover:text-zinc-700 px-3 py-2 rounded-lg hover:bg-zinc-100 transition">
                Batal
            </a>
        </div>

    </form>
</div>

<script>
    document.getElementById('fileInput').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (!file) return;
        const label = document.getElementById('fileLabel');
        label.textContent = '✓ ' + file.name;
        label.classList.add('text-green-700');
    });

    document.getElementById('toggleAktif').addEventListener('change', function() {
        document.getElementById('statusLabel').textContent = this.checked ? 'Aktif' : 'Nonaktif';
    });
</script>

@endsection