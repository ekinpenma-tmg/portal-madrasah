@extends('layouts.admin')
@section('title', 'Tambah Staff')
@section('content')
<div class="max-w-xl">

    <div class="flex items-center gap-2.5 mb-4 fade-in">
        <a href="{{ route('admin.staff.index') }}" class="btn-icon">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <h1 class="text-sm font-semibold text-zinc-900">Tambah Staff</h1>
            <p class="text-xs text-zinc-400 mt-0.5">Tambahkan data staff baru untuk ditampilkan di halaman publik</p>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-zinc-100 p-5 fade-in">
        <form action="{{ route('admin.staff.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-medium text-zinc-600 mb-1.5">Nama Lengkap <span class="text-red-500">*</span></label>
                <input type="text" name="nama" value="{{ old('nama') }}" required
                       class="w-full border border-zinc-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:ring-1 focus:ring-zinc-300">
                @error('nama')<p class="text-red-500 text-2xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-xs font-medium text-zinc-600 mb-1.5">Jabatan <span class="text-red-500">*</span></label>
                <input type="text" name="jabatan" value="{{ old('jabatan') }}" required
                       class="w-full border border-zinc-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:ring-1 focus:ring-zinc-300">
                @error('jabatan')<p class="text-red-500 text-2xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-xs font-medium text-zinc-600 mb-1.5">Urutan Tampil</label>
                <input type="number" name="urutan" value="{{ old('urutan', 0) }}" min="0"
                       class="w-28 border border-zinc-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:ring-1 focus:ring-zinc-300">
                <p class="text-2xs text-zinc-400 mt-1">Angka kecil tampil lebih dulu</p>
            </div>
            <div>
                <label class="block text-xs font-medium text-zinc-600 mb-1.5">Foto <span class="text-zinc-400 font-normal">(opsional)</span></label>
                <input type="file" name="foto" accept="image/*"
                       class="w-full text-xs text-zinc-500 file:mr-3 file:py-1 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-medium file:bg-zinc-100 file:text-zinc-600 hover:file:bg-zinc-200">
                <p class="text-zinc-400 text-2xs mt-1">Format JPG/PNG, maks 2MB</p>
            </div>
            <div class="flex gap-2 pt-2" style="border-top:1px solid #f4f4f5">
                <button type="submit" class="bg-zinc-900 hover:bg-zinc-700 text-white text-xs font-semibold px-4 py-2 rounded-lg transition">
                    Simpan Staff
                </button>
                <a href="{{ route('admin.staff.index') }}" class="text-xs font-medium text-zinc-500 hover:text-zinc-700 px-4 py-2 rounded-lg hover:bg-zinc-100 transition">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection