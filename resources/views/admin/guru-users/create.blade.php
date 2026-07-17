@extends('layouts.admin')
@section('title', 'Tambah Akun Guru')

@section('content')
{{-- Diubah ke max-w-xl biar lebar box form-nya sama persis dan proposional seperti Madrasah --}}
<div class="max-w-xl fade-in">

    {{-- Header Halaman --}}
    <div class="flex items-center gap-2.5 mb-5 fade-in">
        <a href="{{ route('admin.guru-users.index') }}" class="btn-icon">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <h1 class="text-sm font-semibold text-zinc-900">Tambah Akun Guru</h1>
            <p class="text-xs text-zinc-400 mt-0.5">Buat akun login baru untuk guru</p>
        </div>
    </div>

    {{-- Alert Info Password Default --}}
    <div class="bg-amber-50 border border-amber-100 rounded-lg px-4 py-3 mb-5 fade-in">
        <p class="text-xs font-medium text-amber-800 mb-0.5">Informasi</p>
        <p class="text-2xs text-amber-700">Password default akun adalah PegID.</p>
    </div>

    {{-- Box Kontainer Form --}}
    <div class="bg-white rounded-xl border border-zinc-100 overflow-hidden" style="box-shadow:0 1px 4px rgba(0,0,0,0.04)">

        {{-- Padding disesuaikan jadi p-6 agar terasa lega di dalam box --}}
        <form method="POST" action="{{ route('admin.guru-users.store') }}" class="p-6 space-y-5">
            @csrf

            {{-- PegID --}}
            <div>
                <label class="block text-xs font-semibold text-zinc-700 mb-1.5">
                    Nomor PegID <span class="text-red-500">*</span>
                </label>
                <input type="text" name="pegid" value="{{ old('pegid') }}" required
                    placeholder="Masukkan nomor PegID"
                    class="w-full border border-zinc-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:ring-1 focus:ring-zinc-300">
                <p class="text-xs text-zinc-400 mt-1.5">Dijadikan username login dan password default.</p>
                @error('pegid') <p class="text-red-500 text-2xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Nama --}}
            <div>
                <label class="block text-xs font-semibold text-zinc-700 mb-1.5">
                    Nama Lengkap <span class="text-red-500">*</span>
                </label>
                <input type="text" name="nama" value="{{ old('nama') }}" required
                    placeholder="Masukkan nama lengkap"
                    class="w-full border border-zinc-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:ring-1 focus:ring-zinc-300">
                @error('nama') <p class="text-red-500 text-2xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Madrasah --}}
            <div>
                <label class="block text-xs font-semibold text-zinc-700 mb-1.5">Madrasah</label>
                <select name="madrasah_id"
                    class="w-full border border-zinc-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:ring-1 focus:ring-zinc-300">
                    <option value="">— Pilih Madrasah (opsional) —</option>
                    @foreach($madrasahs as $m)
                    <option value="{{ $m->id }}" {{ old('madrasah_id') == $m->id ? 'selected' : '' }}>
                        {{ $m->label_lengkap }}
                    </option>
                    @endforeach
                </select>
                <p class="text-xs text-zinc-400 mt-1.5">Bisa dikosongkan dan dilengkapi nanti.</p>
                @error('madrasah_id') <p class="text-red-500 text-2xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Baris Email & No HP (Grid 2 Kolom) --}}
            <div class="grid grid-cols-2 gap-4">
                {{-- Email --}}
                <div>
                    <label class="block text-xs font-semibold text-zinc-700 mb-1.5">Email (opsional)</label>
                    <input type="email" name="email" value="{{ old('email') }}"
                        placeholder="guru@email.com"
                        class="w-full border border-zinc-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:ring-1 focus:ring-zinc-300">
                    @error('email') <p class="text-red-500 text-2xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- No HP --}}
                <div>
                    <label class="block text-xs font-semibold text-zinc-700 mb-1.5">No HP (opsional)</label>
                    <input type="text" name="no_hp" value="{{ old('no_hp') }}"
                        placeholder="08xxxxxxxxxx"
                        class="w-full border border-zinc-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:ring-1 focus:ring-zinc-300">
                </div>
            </div>

            {{-- Tombol Aksi (Gaya Inline + Tombol Batal) --}}
            <div class="pt-5 border-t border-zinc-100 flex items-center gap-4">
                <button type="submit"
                    class="bg-zinc-900 hover:bg-zinc-700 text-white text-xs font-semibold px-4 py-2 rounded-lg transition">
                    Buat Akun
                </button>
                <a href="{{ route('admin.guru-users.index') }}" 
                    class="text-xs font-medium text-zinc-500 hover:text-zinc-700 px-4 py-2 rounded-lg hover:bg-zinc-100 transition">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection