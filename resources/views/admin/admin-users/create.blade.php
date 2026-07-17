@extends('layouts.admin')
@section('title', 'Tambah Akun Admin')
@section('content')

<div class="max-w-md">
    <a href="{{ route('admin.admin-users.index') }}" class="inline-flex items-center gap-1.5 text-xs text-zinc-400 hover:text-zinc-600 mb-4 transition">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Kembali
    </a>

    <div class="bg-white rounded-xl border border-zinc-100 overflow-hidden fade-in">
        <div class="px-5 py-4 flex items-center gap-3" style="border-bottom:1px solid #f4f4f5">
            <div class="w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0 bg-zinc-900">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                </svg>
            </div>
            <div>
                <p class="text-sm font-semibold text-zinc-900">Tambah Akun Admin</p>
                <p class="text-2xs text-zinc-400 mt-0.5">Akun baru dapat langsung login ke dashboard</p>
            </div>
        </div>

        <form action="{{ route('admin.admin-users.store') }}" method="POST" class="p-5 space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-medium text-zinc-600 mb-1.5">Nama Lengkap <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}"
                       placeholder="Masukkan nama lengkap"
                       class="w-full border rounded-lg px-3 py-2 text-xs focus:outline-none focus:ring-1 focus:ring-zinc-300 transition
                              @error('name') border-red-300 @else border-zinc-200 @enderror">
                @error('name')<p class="text-red-500 text-2xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-xs font-medium text-zinc-600 mb-1.5">Email <span class="text-red-500">*</span></label>
                <input type="email" name="email" value="{{ old('email') }}"
                       placeholder="email@contoh.com"
                       class="w-full border rounded-lg px-3 py-2 text-xs focus:outline-none focus:ring-1 focus:ring-zinc-300 transition
                              @error('email') border-red-300 @else border-zinc-200 @enderror">
                @error('email')<p class="text-red-500 text-2xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-xs font-medium text-zinc-600 mb-1.5">Peran Akun <span class="text-red-500">*</span></label>
                <select name="role" class="w-full border rounded-lg px-3 py-2 text-xs focus:outline-none focus:ring-1 focus:ring-zinc-300 transition
                              @error('role') border-red-300 @else border-zinc-200 @enderror">
                    <option value="staff" {{ old('role', 'staff') === 'staff' ? 'selected' : '' }}>Staff (tidak bisa kelola akun admin/reset data massal)</option>
                    <option value="super_admin" {{ old('role') === 'super_admin' ? 'selected' : '' }}>Super Admin (hak penuh)</option>
                </select>
                @error('role')<p class="text-red-500 text-2xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div x-data="{ show: false }">
                <label class="block text-xs font-medium text-zinc-600 mb-1.5">Password <span class="text-red-500">*</span></label>
                <div class="relative">
                    <input :type="show ? 'text' : 'password'" name="password"
                           placeholder="Minimal 8 karakter"
                           class="w-full border rounded-lg px-3 py-2 pr-9 text-xs focus:outline-none focus:ring-1 focus:ring-zinc-300 transition
                                  @error('password') border-red-300 @else border-zinc-200 @enderror">
                    <button type="button" @click="show = !show" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-zinc-400 hover:text-zinc-600 transition">
                        <svg x-show="!show" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        <svg x-show="show" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                        </svg>
                    </button>
                </div>
                @error('password')<p class="text-red-500 text-2xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div x-data="{ show: false }">
                <label class="block text-xs font-medium text-zinc-600 mb-1.5">Konfirmasi Password <span class="text-red-500">*</span></label>
                <div class="relative">
                    <input :type="show ? 'text' : 'password'" name="password_confirmation"
                           placeholder="Ulangi password"
                           class="w-full border border-zinc-200 rounded-lg px-3 py-2 pr-9 text-xs focus:outline-none focus:ring-1 focus:ring-zinc-300 transition">
                    <button type="button" @click="show = !show" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-zinc-400 hover:text-zinc-600 transition">
                        <svg x-show="!show" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        <svg x-show="show" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="pt-2">
                <button type="submit" class="w-full bg-zinc-900 hover:bg-zinc-700 text-white text-xs font-semibold py-2.5 rounded-lg transition">
                    Tambah Akun Admin
                </button>
            </div>
        </form>
    </div>
</div>

@endsection