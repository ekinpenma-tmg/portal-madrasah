@extends('madrasah.layouts.app')
@section('title', 'Ganti Password')
@section('content')
<div class="max-w-md mx-auto">
    <div class="mb-4">
        <h1 class="text-lg font-semibold text-gray-900">Ganti Password</h1>
        <p class="text-sm text-gray-500 mt-0.5">Ubah password akun madrasah Anda.</p>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 p-5 fade-in"
         x-data="{ showOld: false, showNew: false, showConfirm: false, newPw: '', confirmPw: '' }">
        <form action="{{ route('madrasah.password.update') }}" method="POST" class="space-y-4">
            @csrf @method('PUT')

            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1.5">Password Saat Ini <span class="text-red-500">*</span></label>
                <div class="relative">
                    <input :type="showOld ? 'text' : 'password'" name="password_lama" required
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 pr-9 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500/30 focus:border-primary-400 @error('password_lama') border-red-300 @enderror">
                    <button type="button" @click="showOld = !showOld" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                        <svg x-show="!showOld" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        <svg x-show="showOld" x-cloak class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                    </button>
                </div>
                @error('password_lama')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1.5">Password Baru <span class="text-red-500">*</span></label>
                <div class="relative">
                    <input :type="showNew ? 'text' : 'password'" name="password_baru" x-model="newPw" required
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 pr-9 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500/30 focus:border-primary-400 @error('password_baru') border-red-300 @enderror">
                    <button type="button" @click="showNew = !showNew" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                        <svg x-show="!showNew" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        <svg x-show="showNew" x-cloak class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                    </button>
                </div>
                <div class="mt-1.5 h-1 bg-gray-100 rounded-full overflow-hidden" x-show="newPw">
                    <div class="h-full rounded-full transition-all duration-300"
                         :class="{ 'bg-red-400': newPw.length > 0 && newPw.length < 6, 'bg-yellow-400': newPw.length >= 6 && newPw.length < 10, 'bg-green-500': newPw.length >= 10 }"
                         :style="`width: ${Math.min((newPw.length / 12) * 100, 100)}%`"></div>
                </div>
                <p class="text-2xs text-gray-400 mt-1">Minimal 8 karakter.</p>
                @error('password_baru')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1.5">Konfirmasi Password Baru <span class="text-red-500">*</span></label>
                <div class="relative">
                    <input :type="showConfirm ? 'text' : 'password'" name="password_baru_confirmation" x-model="confirmPw" required
                        class="w-full border rounded-lg px-3 py-2 pr-9 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500/30 focus:border-primary-400"
                        :class="confirmPw && newPw !== confirmPw ? 'border-red-300' : (confirmPw && newPw === confirmPw ? 'border-green-300' : 'border-gray-200')">
                    <button type="button" @click="showConfirm = !showConfirm" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                        <svg x-show="!showConfirm" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        <svg x-show="showConfirm" x-cloak class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                    </button>
                </div>
                <p x-show="confirmPw && newPw !== confirmPw" x-cloak class="text-red-500 text-xs mt-1">Password tidak cocok.</p>
            </div>

            <div class="flex gap-2 pt-2" style="border-top:1px solid #f5f5f5">
                <button type="submit" :disabled="confirmPw && newPw !== confirmPw" class="btn-xs btn-primary-xs disabled:opacity-40 disabled:cursor-not-allowed">Simpan Password</button>
                <a href="{{ route('madrasah.dashboard') }}" class="btn-xs btn-ghost-xs">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
