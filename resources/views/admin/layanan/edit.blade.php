@extends('layouts.admin')
@section('title', 'Edit Layanan')
@section('content')
<div class="max-w-xl">

    <div class="flex items-center gap-2.5 mb-4 fade-in">
        <a href="{{ route('admin.layanan.index') }}" class="btn-icon">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <h1 class="text-sm font-semibold text-zinc-900">Edit Layanan</h1>
            <p class="text-xs text-zinc-400 mt-0.5">{{ $layanan->nama }}</p>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-zinc-100 p-5 fade-in">
        <form action="{{ route('admin.layanan.update', $layanan->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            @method('PUT')
            @include('admin.layanan._form')

            <div class="flex gap-2 pt-2" style="border-top:1px solid #f4f4f5">
                <button type="submit" class="bg-zinc-900 hover:bg-zinc-700 text-white text-xs font-semibold px-4 py-2 rounded-lg transition">
                    Simpan Perubahan
                </button>
                <a href="{{ route('admin.layanan.index') }}" class="text-xs font-medium text-zinc-500 hover:text-zinc-700 px-4 py-2 rounded-lg hover:bg-zinc-100 transition">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
