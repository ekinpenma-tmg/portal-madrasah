@extends('madrasah.layouts.app')
@section('title', 'Ajukan Dokumen')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="mb-5">
        <h1 class="text-lg font-semibold text-gray-900">Ajukan Dokumen</h1>
        <p class="text-sm text-gray-500 mt-0.5">Pilih jenis dokumen yang ingin diajukan ke Penma.</p>
    </div>

    @if($jenisDokumen->isEmpty())
    <div class="bg-white rounded-xl border border-gray-200 p-10 text-center">
        <p class="text-sm font-medium text-gray-700">Belum ada jenis dokumen tersedia untuk madrasah</p>
        <p class="text-xs text-gray-400 mt-1">Hubungi admin Penma untuk informasi lebih lanjut.</p>
    </div>
    @else
    <div class="grid sm:grid-cols-2 gap-3">
        @foreach($jenisDokumen as $jenis)
        <a href="{{ route('madrasah.pengajuan.form', $jenis->id) }}"
           class="group bg-white rounded-xl border border-gray-200 p-4 flex items-center gap-3 hover:border-primary-300 hover:shadow-sm transition">
            <div class="w-9 h-9 rounded-lg bg-primary-50 text-primary-700 flex items-center justify-center flex-shrink-0 group-hover:bg-primary-100 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <div class="min-w-0">
                <p class="text-sm font-medium text-gray-900 truncate">{{ $jenis->nama }}</p>
                @if($jenis->deskripsi)<p class="text-xs text-gray-400 truncate">{{ $jenis->deskripsi }}</p>@endif
            </div>
            <svg class="w-4 h-4 text-gray-300 ml-auto flex-shrink-0 group-hover:text-primary-500 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5l7 7-7 7"/></svg>
        </a>
        @endforeach
    </div>
    @endif
</div>
@endsection
