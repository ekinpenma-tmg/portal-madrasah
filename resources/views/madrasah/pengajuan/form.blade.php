@extends('madrasah.layouts.app')
@section('title', 'Ajukan Dokumen')

@section('content')
<div class="max-w-2xl mx-auto">

    <a href="{{ route('madrasah.pengajuan.index') }}" class="inline-flex items-center gap-1 text-xs text-gray-400 hover:text-gray-600 mb-3">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Pilih jenis dokumen lain
    </a>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <p class="text-xs font-medium text-primary-600 uppercase tracking-wide mb-0.5">Ajukan Dokumen</p>
            <h1 class="text-base font-semibold text-gray-900">{{ $jenis->nama }}</h1>
            @if($jenis->deskripsi)<p class="text-sm text-gray-500 mt-1">{{ $jenis->deskripsi }}</p>@endif
        </div>

        {{-- Syarat Pengajuan --}}
        @if($jenis->syarat)
        <div class="px-5 pt-4">
            <div class="bg-red-50 border border-red-100 rounded-lg p-4 flex gap-3">
                <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                    </svg>
                </div>
                <div class="text-sm text-red-800">
                    <p class="font-semibold mb-1.5">Syarat Pengajuan Dokumen {{ $jenis->nama }}</p>
                    <ul class="space-y-1">
                        @foreach(array_filter(array_map('trim', explode(';', $jenis->syarat))) as $i => $syarat)
                        <li class="flex items-start gap-2">
                            <span class="flex-shrink-0 w-[18px] h-[18px] rounded-full bg-red-200 text-red-700 text-2xs font-bold flex items-center justify-center mt-0.5">{{ $i + 1 }}</span>
                            <span class="text-sm">{{ $syarat }}</span>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        @endif

        @if(session('error'))
        <div class="px-5 pt-4">
            <div class="bg-red-50 border border-red-100 text-red-700 rounded-lg px-4 py-3 text-sm">{{ session('error') }}</div>
        </div>
        @endif

        <form action="{{ route('madrasah.pengajuan.store', $jenis->id) }}" method="POST" enctype="multipart/form-data" id="form-ajuan-madrasah" class="p-5 space-y-4">
            @csrf

            {{-- Data madrasah — auto-fill --}}
            <div class="bg-gray-50 rounded-lg p-3.5 space-y-2.5">
                <p class="text-2xs font-medium text-gray-400 uppercase tracking-wide">Data Pemohon (otomatis dari akun madrasah)</p>
                <div class="grid sm:grid-cols-2 gap-2.5 text-sm">
                    <div>
                        <p class="text-xs text-gray-400">Nama Madrasah</p>
                        <p class="font-medium text-gray-800">{{ $madrasah->nama_madrasah }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">NSM</p>
                        <p class="font-medium text-gray-800 font-mono">{{ $madrasah->nsm }}</p>
                    </div>
                    <div class="sm:col-span-2">
                        <p class="text-xs text-gray-400">Penanggung Jawab</p>
                        <p class="font-medium text-gray-800">{{ $madrasah->nama_pic }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Nomor HP / WhatsApp</p>
                        <p class="font-medium text-gray-800">{{ $madrasah->no_hp }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Email</p>
                        <p class="font-medium text-gray-800">{{ $madrasah->hasEmailValid() ? $madrasah->email : '—' }}</p>
                    </div>
                </div>
                <a href="{{ route('madrasah.profil.form') }}" class="inline-flex items-center gap-1 text-2xs text-primary-700 hover:text-primary-800 font-medium">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    Data salah? Perbarui di Edit Profil
                </a>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1.5">Token <span class="text-gray-400">(opsional, isi jika diminta)</span></label>
                <input type="text" name="token" value="{{ old('token') }}" maxlength="6"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm uppercase tracking-wider font-mono focus:outline-none focus:ring-2 focus:ring-primary-500/30 focus:border-primary-400"
                    placeholder="ABCDEF">
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1.5">File Dokumen (PDF, maks. 5MB) <span class="text-red-500">*</span></label>
                <label for="file_dokumen" class="flex items-center gap-3 border border-dashed border-gray-300 rounded-lg px-3.5 py-3 cursor-pointer hover:border-primary-400 hover:bg-primary-50/40 transition">
                    <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm text-gray-700" id="file-label">Klik untuk pilih file PDF</p>
                        <p class="text-2xs text-gray-400">Format PDF, maksimal 5MB</p>
                    </div>
                    <input id="file_dokumen" type="file" name="file_dokumen" accept="application/pdf" class="hidden">
                </label>
                @error('file_dokumen')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <button type="submit" id="btn-submit"
                class="w-full bg-primary-700 hover:bg-primary-800 text-white text-sm font-medium py-2.5 rounded-lg transition flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                Kirim Pengajuan
            </button>
        </form>
    </div>
</div>

<script>
document.getElementById('file_dokumen')?.addEventListener('change', function(e) {
    if (e.target.files.length > 0) document.getElementById('file-label').textContent = e.target.files[0].name;
});
document.getElementById('form-ajuan-madrasah')?.addEventListener('submit', function() {
    const btn = document.getElementById('btn-submit');
    btn.disabled = true; btn.style.opacity = '0.7';
    btn.innerHTML = '<svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg> Mengirim...';
    setTimeout(() => { btn.disabled = false; btn.style.opacity = '1'; btn.innerHTML = 'Kirim Pengajuan'; }, 10000);
});
</script>
@endsection
