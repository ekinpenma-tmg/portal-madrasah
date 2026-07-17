@extends('guru.layouts.app')
@section('title', 'Detail Ajuan')

@section('content')
<div class="max-w-2xl mx-auto">

    <a href="{{ route('guru.pengajuan.riwayat') }}" class="inline-flex items-center gap-1 text-xs text-gray-400 hover:text-gray-600 mb-3">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Kembali ke riwayat
    </a>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        @php
            $isD = $pengajuan->status === 'diterima';
            $isT = $pengajuan->status === 'ditolak';
            $bg  = $isD ? 'bg-green-700' : ($isT ? 'bg-red-600' : 'bg-amber-500');
        @endphp
        <div class="px-5 py-4 {{ $bg }} text-center">
            <p class="text-white font-semibold text-base">{{ $pengajuan->status_label }}</p>
            <p class="text-white/70 text-xs font-mono mt-0.5">{{ $pengajuan->kode_ajuan }}</p>
        </div>

        <div class="p-5 space-y-2.5 text-sm">
            <div class="flex justify-between border-b border-gray-50 pb-2"><span class="text-gray-400">Jenis Dokumen</span><span class="font-medium text-gray-800">{{ $pengajuan->jenisDokumen->nama }}</span></div>
            <div class="flex justify-between border-b border-gray-50 pb-2"><span class="text-gray-400">Tanggal Ajuan</span><span class="font-medium text-gray-800">{{ $pengajuan->created_at->format('d M Y, H:i') }}</span></div>
            @if($pengajuan->tanggal_proses)
            <div class="flex justify-between border-b border-gray-50 pb-2"><span class="text-gray-400">Tanggal Diproses</span><span class="font-medium text-gray-800">{{ $pengajuan->tanggal_proses->format('d M Y, H:i') }}</span></div>
            @endif
            @if($pengajuan->token)
            <div class="flex justify-between border-b border-gray-50 pb-2"><span class="text-gray-400">Token</span><span class="font-medium text-gray-800 font-mono">{{ $pengajuan->token }}</span></div>
            @endif

            @if($pengajuan->catatan)
            <div class="mt-3 rounded-lg px-4 py-3 {{ $isD ? 'bg-green-50 border border-green-100' : 'bg-red-50 border border-red-100' }}">
                <p class="text-2xs font-medium {{ $isD ? 'text-green-700' : 'text-red-700' }} mb-1 uppercase tracking-wide">Catatan Admin</p>
                <p class="text-sm {{ $isD ? 'text-green-800' : 'text-red-800' }}">{{ $pengajuan->catatan }}</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
