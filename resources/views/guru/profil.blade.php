@extends('guru.layouts.app')
@section('title', 'Edit Profil')

@section('content')
<div class="max-w-md mx-auto">
    <div class="mb-4">
        <h1 class="text-lg font-semibold text-gray-900">Edit Profil</h1>
        <p class="text-sm text-gray-500 mt-0.5">Data ini akan otomatis dipakai saat mengajukan dokumen.</p>
    </div>

    @if(!$guru->isKontakLengkap())
    <div class="bg-yellow-50 border border-yellow-200 rounded-xl px-4 py-3 mb-4 flex items-start gap-2.5 fade-in">
        <svg class="w-4 h-4 text-yellow-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
        </svg>
        <p class="text-xs text-yellow-700">Nomor HP/WhatsApp belum diisi. Lengkapi agar bisa langsung mengajukan dokumen.</p>
    </div>
    @endif

    <div class="bg-white rounded-xl border border-gray-200 p-5 fade-in">
        <div class="flex items-center gap-3 bg-gray-50 rounded-lg p-3.5 mb-4">
            <div class="w-11 h-11 rounded-full flex items-center justify-center text-white text-base font-semibold flex-shrink-0"
                 style="background:linear-gradient(135deg,#003d0d,#5ccb5f)">
                {{ strtoupper(substr($guru->nama, 0, 1)) }}
            </div>
            <div class="grid grid-cols-2 gap-2.5 text-sm flex-1 min-w-0">
                <div>
                    <p class="text-xs text-gray-400">Nama</p>
                    <p class="font-medium text-gray-800 truncate">{{ $guru->nama }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400">PegID</p>
                    <p class="font-medium text-gray-800 font-mono">{{ $guru->pegid }}</p>
                </div>
                <div class="col-span-2">
                    <p class="text-xs text-gray-400">Madrasah</p>
                    <p class="font-medium text-gray-800 truncate">{{ $guru->nama_madrasah }}</p>
                </div>
            </div>
        </div>

        <form action="{{ route('guru.profil.update') }}" method="POST" class="space-y-4">
            @csrf @method('PUT')

            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1.5">Nomor HP / WhatsApp <span class="text-red-500">*</span></label>
                <input type="text" name="no_hp"
                    value="{{ old('no_hp', $guru->hasNoHpValid() ? $guru->no_hp : '') }}"
                    placeholder="08xxxxxxxxxx"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500/30 focus:border-primary-400 @error('no_hp') border-red-300 @enderror">
                @error('no_hp')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1.5">Email <span class="text-gray-400">(opsional)</span></label>
                <input type="email" name="email"
                    value="{{ old('email', $guru->hasEmailValid() ? $guru->email : '') }}"
                    placeholder="nama@email.com"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500/30 focus:border-primary-400 @error('email') border-red-300 @enderror">
                @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="flex gap-2 pt-2" style="border-top:1px solid #f5f5f5">
                <button type="submit" class="btn-xs btn-primary-xs">Simpan Profil</button>
                <a href="{{ route('guru.dashboard') }}" class="btn-xs btn-ghost-xs">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
