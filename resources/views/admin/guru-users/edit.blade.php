@extends('layouts.admin')
@section('title', 'Edit Akun Guru')

@section('content')
<div class="max-w-xl fade-in">

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-2 text-xs text-zinc-400 mb-4">
        <a href="{{ route('admin.guru-users.index') }}" class="hover:text-zinc-600 transition">Akun Guru</a>
        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
        <span class="text-zinc-600 font-medium">{{ $guru->nama }}</span>
    </div>

    {{-- Info ringkas akun --}}
    <div class="bg-white rounded-xl border border-zinc-100 px-4 py-3.5 mb-4 flex items-center justify-between gap-4 flex-wrap">
        <div class="flex items-center gap-2.5">
            <div class="w-9 h-9 rounded-lg flex items-center justify-center text-white font-semibold text-sm flex-shrink-0"
                 style="background:linear-gradient(135deg,#0369a1,#38bdf8)">
                {{ strtoupper(substr($guru->nama, 0, 1)) }}
            </div>
            <div>
                <p class="text-xs font-semibold text-zinc-800">{{ $guru->nama }}</p>
                <p class="text-2xs text-zinc-400">Terdaftar {{ $guru->created_at->format('d M Y') }}</p>
            </div>
        </div>
        <div class="flex items-center gap-4 ml-auto">
            <div class="text-center">
                <p class="text-lg font-bold text-blue-600 leading-none">{{ $jumlahArsip }}</p>
                <p class="text-2xs text-zinc-400 mt-0.5">Arsip</p>
            </div>
            @if($guru->password_changed)
            <span class="badge badge-green">Password diubah</span>
            @else
            <span class="badge badge-yellow">Password default</span>
            @endif
        </div>
    </div>

    {{-- Form Edit --}}
    <div class="bg-white rounded-xl border border-zinc-100 p-5">

        <form method="POST" action="{{ route('admin.guru-users.update', $guru->id) }}">
            @csrf @method('PUT')

            <div class="mb-4">
                <label class="block text-2xs font-semibold text-zinc-500 mb-1.5 uppercase tracking-wider">
                    Nomor PegID <span class="text-red-500">*</span>
                </label>
                <input type="text" name="pegid" value="{{ old('pegid', $guru->pegid) }}" required
                    class="filter-input w-full {{ $errors->has('pegid') ? 'border-red-300 bg-red-50' : '' }}">
                <p class="text-2xs text-amber-500 mt-1">Mengubah PegID tidak otomatis mengubah password. Gunakan Reset Password jika perlu.</p>
                @error('pegid') <p class="text-red-500 text-2xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4">
                <label class="block text-2xs font-semibold text-zinc-500 mb-1.5 uppercase tracking-wider">
                    Nama Lengkap <span class="text-red-500">*</span>
                </label>
                <input type="text" name="nama" value="{{ old('nama', $guru->nama) }}" required
                    class="filter-input w-full {{ $errors->has('nama') ? 'border-red-300 bg-red-50' : '' }}">
                @error('nama') <p class="text-red-500 text-2xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4">
                <label class="block text-2xs font-semibold text-zinc-500 mb-1.5 uppercase tracking-wider">Madrasah</label>
                <select name="madrasah_id" class="filter-input w-full bg-white">
                    <option value="">— Tidak ditentukan —</option>
                    @foreach($madrasahs as $m)
                    <option value="{{ $m->id }}" {{ old('madrasah_id', $guru->madrasah_id) == $m->id ? 'selected' : '' }}>
                        {{ $m->label_lengkap }}
                    </option>
                    @endforeach
                </select>
                @error('madrasah_id') <p class="text-red-500 text-2xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-4">
                <div>
                    <label class="block text-2xs font-semibold text-zinc-500 mb-1.5 uppercase tracking-wider">Email</label>
                    <input type="email" name="email" value="{{ old('email', $guru->email) }}"
                        class="filter-input w-full {{ $errors->has('email') ? 'border-red-300 bg-red-50' : '' }}">
                    @error('email') <p class="text-red-500 text-2xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-2xs font-semibold text-zinc-500 mb-1.5 uppercase tracking-wider">No. HP / WhatsApp</label>
                    <input type="text" name="no_hp" value="{{ old('no_hp', $guru->no_hp) }}" class="filter-input w-full">
                </div>
            </div>

            {{-- Toggle Status Aktif --}}
            <div class="flex items-center justify-between bg-zinc-50 rounded-lg px-4 py-3 mb-5">
                <div>
                    <p class="text-xs font-semibold text-zinc-700">Status Akun</p>
                    <p class="text-2xs text-zinc-400 mt-0.5">Akun non-aktif tidak bisa login</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" class="sr-only peer"
                        {{ old('is_active', $guru->is_active) ? 'checked' : '' }}>
                    <div class="w-10 h-5 bg-zinc-300 rounded-full peer
                                peer-checked:after:translate-x-5 after:content-[''] after:absolute after:top-[2px] after:left-[2px]
                                after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all
                                peer-checked:bg-green-500 relative transition-colors">
                    </div>
                </label>
            </div>

            <div class="flex items-center gap-2 pt-3" style="border-top:1px solid #f0f0f0">
                <button type="submit" class="text-xs font-bold px-5 py-2 rounded-lg text-white transition" style="background:#18181b">
                    Simpan Perubahan
                </button>
                <a href="{{ route('admin.guru-users.index') }}" class="bg-zinc-100 hover:bg-zinc-200 text-zinc-600 text-xs font-semibold px-4 py-2 rounded-lg transition">
                    Batal
                </a>
            </div>
        </form>
    </div>

    {{-- Tindakan lain --}}
    <div class="mt-3 bg-white rounded-xl border border-zinc-100 p-5">
        <p class="text-xs font-semibold text-zinc-700 mb-3">Tindakan Lain</p>
        <div class="flex flex-wrap gap-2">
            <form method="POST" action="{{ route('admin.guru-users.reset-password', $guru->id) }}"
                  data-confirm="Reset password {{ addslashes($guru->nama) }} ke PegID default?\n\nPassword baru: {{ $guru->pegid }}" data-confirm-type="arsip" data-confirm-btn="Ya, Reset">
                @csrf @method('PATCH')
                <button type="submit" class="inline-flex items-center gap-1.5 bg-amber-50 hover:bg-amber-100 border border-amber-200 text-amber-700 text-xs font-semibold px-3 py-1.5 rounded-lg transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                    </svg>
                    Reset Password ke PegID
                </button>
            </form>
            <form method="POST" action="{{ route('admin.guru-users.destroy', $guru->id) }}"
                  data-confirm="Hapus permanen akun {{ addslashes($guru->nama) }}?\nAkun yang memiliki arsip tidak bisa dihapus." data-confirm-btn="Ya, Hapus">
                @csrf @method('DELETE')
                <button type="submit" class="inline-flex items-center gap-1.5 bg-red-50 hover:bg-red-100 border border-red-200 text-red-600 text-xs font-semibold px-3 py-1.5 rounded-lg transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Hapus Akun
                </button>
            </form>
        </div>
        @if($jumlahArsip > 0)
        <p class="text-2xs text-zinc-400 mt-2.5">
            Akun ini memiliki <strong>{{ $jumlahArsip }} arsip</strong>. Hapus semua arsip dulu sebelum menghapus akun.
        </p>
        @endif
    </div>

</div>
@endsection
