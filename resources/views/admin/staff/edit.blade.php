@extends('layouts.admin')
@section('title', 'Edit Staff')
@section('content')

<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="text-lg font-bold text-gray-800">Edit Staff</h2>
        <p class="text-sm text-gray-400 mt-0.5">Perbarui data staff madrasah</p>
    </div>
    <a href="{{ route('admin.staff.index') }}"
       class="flex items-center gap-2 text-sm font-semibold text-gray-500 hover:text-gray-700 bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-xl transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Kembali@extends('layouts.admin')
@section('title', 'Edit Staff')
@section('content')

<div class="flex items-center justify-between mb-4 fade-in">
    <div>
        <h1 class="text-sm font-semibold text-zinc-900">Edit Staff</h1>
        <p class="text-xs text-zinc-400 mt-0.5">Perbarui data staff madrasah</p>
    </div>
    <a href="{{ route('admin.staff.index') }}" class="inline-flex items-center gap-1.5 text-xs font-medium px-3 py-1.5 rounded-lg border border-zinc-200 text-zinc-600 hover:bg-zinc-50 transition">
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

    <form action="{{ route('admin.staff.update', $staff->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- Foto preview --}}
        <div class="mb-5 flex items-center gap-4">
            <div class="w-14 h-14 rounded-xl overflow-hidden bg-zinc-100 border border-zinc-200 flex items-center justify-center flex-shrink-0" id="fotoPreviewWrap">
                @if($staff->foto)
                    <img src="{{ Storage::url($staff->foto) }}" id="fotoPreview" class="w-full h-full object-cover" alt="">
                @else
                    <svg id="fotoPlaceholder" class="w-6 h-6 text-zinc-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    <img id="fotoPreview" class="w-full h-full object-cover hidden" alt="">
                @endif
            </div>
            <div>
                <label class="block text-2xs font-medium text-zinc-400 mb-1.5 uppercase tracking-wide">Foto Staff</label>
                <label class="cursor-pointer inline-flex items-center gap-1.5 text-xs font-medium text-zinc-700 bg-zinc-100 hover:bg-zinc-200 px-3 py-1.5 rounded-lg transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Ganti Foto
                    <input type="file" name="foto" accept="image/*" class="hidden" id="fotoInput">
                </label>
                <p class="text-zinc-400 text-2xs mt-1.5">JPG, PNG, max 2MB. Kosongkan jika tidak diubah.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5 mb-2">
            <div class="md:col-span-2">
                <label class="block text-xs font-medium text-zinc-600 mb-1.5">Nama Lengkap <span class="text-red-500">*</span></label>
                <input type="text" name="nama" value="{{ old('nama', $staff->nama) }}"
                    placeholder="Masukkan nama lengkap"
                    class="w-full px-3 py-2 border border-zinc-200 rounded-lg text-xs text-zinc-800 focus:outline-none focus:ring-1 focus:ring-zinc-300"
                    required>
            </div>

            <div class="md:col-span-2">
                <label class="block text-xs font-medium text-zinc-600 mb-1.5">Jabatan <span class="text-red-500">*</span></label>
                <input type="text" name="jabatan" value="{{ old('jabatan', $staff->jabatan) }}"
                    placeholder="Contoh: Kepala Seksi"
                    class="w-full px-3 py-2 border border-zinc-200 rounded-lg text-xs text-zinc-800 focus:outline-none focus:ring-1 focus:ring-zinc-300"
                    required>
            </div>

            <div>
                <label class="block text-xs font-medium text-zinc-600 mb-1.5">Urutan Tampil <span class="text-red-500">*</span></label>
                <input type="number" name="urutan" value="{{ old('urutan', $staff->urutan) }}"
                    min="0" placeholder="0"
                    class="w-full px-3 py-2 border border-zinc-200 rounded-lg text-xs text-zinc-800 focus:outline-none focus:ring-1 focus:ring-zinc-300"
                    required>
                <p class="text-zinc-400 text-2xs mt-1">Angka kecil tampil lebih dulu</p>
            </div>

            <div>
                <label class="block text-xs font-medium text-zinc-600 mb-1.5">Status</label>
                <label class="flex items-center gap-2.5 cursor-pointer bg-zinc-50 border border-zinc-200 rounded-lg px-3 py-2 hover:bg-zinc-100 transition w-fit">
                    <div class="relative">
                        <input type="checkbox" name="aktif" value="1" id="toggleAktif"
                            {{ old('aktif', $staff->aktif) ? 'checked' : '' }}
                            class="sr-only peer">
                        <div class="w-8 h-4 bg-zinc-300 peer-checked:bg-green-600 rounded-full transition-colors duration-200"></div>
                        <div class="absolute top-0.5 left-0.5 w-3 h-3 bg-white rounded-full shadow transition-transform duration-200 peer-checked:translate-x-4"></div>
                    </div>
                    <span class="text-xs font-medium text-zinc-700" id="statusLabel">
                        {{ old('aktif', $staff->aktif) ? 'Aktif' : 'Nonaktif' }}
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
            <a href="{{ route('admin.staff.index') }}" class="text-xs font-medium text-zinc-500 hover:text-zinc-700 px-3 py-2 rounded-lg hover:bg-zinc-100 transition">
                Batal
            </a>
        </div>

    </form>
</div>

<script>
    document.getElementById('fotoInput').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = function(ev) {
            const img = document.getElementById('fotoPreview');
            const placeholder = document.getElementById('fotoPlaceholder');
            img.src = ev.target.result;
            img.classList.remove('hidden');
            if (placeholder) placeholder.classList.add('hidden');
        };
        reader.readAsDataURL(file);
    });

    document.getElementById('toggleAktif').addEventListener('change', function() {
        document.getElementById('statusLabel').textContent = this.checked ? 'Aktif' : 'Nonaktif';
    });
</script>

@endsection
    </a>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 max-w-2xl">

    @if($errors->any())
        <div class="mb-5 p-4 rounded-xl bg-red-50 border border-red-200 text-sm text-red-600 font-medium flex items-start gap-2">
            <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <ul class="space-y-0.5">
                @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.staff.update', $staff->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- Foto preview --}}
        <div class="mb-6 flex items-center gap-5">
            <div class="w-20 h-20 rounded-2xl overflow-hidden bg-primary-50 border-2 border-primary-100 flex items-center justify-center flex-shrink-0" id="fotoPreviewWrap">
                @if($staff->foto)
                    <img src="{{ Storage::url($staff->foto) }}" id="fotoPreview" class="w-full h-full object-cover" alt="">
                @else
                    <svg id="fotoPlaceholder" class="w-8 h-8 text-primary-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    <img id="fotoPreview" class="w-full h-full object-cover hidden" alt="">
                @endif
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1.5">Foto Staff</label>
                <label class="cursor-pointer inline-flex items-center gap-2 text-xs font-semibold text-primary-700 bg-primary-50 hover:bg-primary-100 border border-primary-200 px-4 py-2 rounded-lg transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Ganti Foto
                    <input type="file" name="foto" accept="image/*" class="hidden" id="fotoInput">
                </label>
                <p class="text-gray-400 text-xs mt-1.5">JPG, PNG, max 2MB. Kosongkan jika tidak ingin mengubah.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            {{-- Nama --}}
            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Nama Lengkap <span class="text-red-500">*</span></label>
                <input type="text" name="nama" value="{{ old('nama', $staff->nama) }}"
                    placeholder="Masukkan nama lengkap"
                    class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm text-gray-800 placeholder-gray-300 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition bg-gray-50 focus:bg-white"
                    required>
            </div>

            {{-- Jabatan --}}
            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Jabatan <span class="text-red-500">*</span></label>
                <input type="text" name="jabatan" value="{{ old('jabatan', $staff->jabatan) }}"
                    placeholder="Contoh: Kepala Seksi"
                    class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm text-gray-800 placeholder-gray-300 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition bg-gray-50 focus:bg-white"
                    required>
            </div>

            {{-- Urutan --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Urutan Tampil <span class="text-red-500">*</span></label>
                <input type="number" name="urutan" value="{{ old('urutan', $staff->urutan) }}"
                    min="0" placeholder="0"
                    class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm text-gray-800 placeholder-gray-300 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition bg-gray-50 focus:bg-white"
                    required>
                <p class="text-gray-400 text-xs mt-1">Angka kecil tampil lebih dulu</p>
            </div>

            {{-- Status Aktif --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Status</label>
                <label class="flex items-center gap-3 cursor-pointer bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 hover:bg-gray-100 transition w-fit">
                    <div class="relative">
                        <input type="checkbox" name="aktif" value="1" id="toggleAktif"
                            {{ old('aktif', $staff->aktif) ? 'checked' : '' }}
                            class="sr-only peer">
                        <div class="w-10 h-5 bg-gray-300 peer-checked:bg-primary-600 rounded-full transition-colors duration-200"></div>
                        <div class="absolute top-0.5 left-0.5 w-4 h-4 bg-white rounded-full shadow transition-transform duration-200 peer-checked:translate-x-5"></div>
                    </div>
                    <span class="text-sm font-medium text-gray-700" id="statusLabel">
                        {{ old('aktif', $staff->aktif) ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </label>
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex items-center gap-3 pt-4 border-t border-gray-100 mt-6">
            <button type="submit"
                class="flex items-center gap-2 bg-primary-700 hover:bg-primary-800 text-white text-sm font-bold px-6 py-2.5 rounded-xl transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Simpan Perubahan
            </button>
            <a href="{{ route('admin.staff.index') }}"
               class="text-sm font-semibold text-gray-500 hover:text-gray-700 px-4 py-2.5 rounded-xl hover:bg-gray-100 transition">
                Batal
            </a>
        </div>

    </form>
</div>

<script>
    // Preview foto
    document.getElementById('fotoInput').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = function(ev) {
            const img = document.getElementById('fotoPreview');
            const placeholder = document.getElementById('fotoPlaceholder');
            img.src = ev.target.result;
            img.classList.remove('hidden');
            if (placeholder) placeholder.classList.add('hidden');
        };
        reader.readAsDataURL(file);
    });

    // Toggle label status
    document.getElementById('toggleAktif').addEventListener('change', function() {
        document.getElementById('statusLabel').textContent = this.checked ? 'Aktif' : 'Nonaktif';
    });
</script>

@endsection
