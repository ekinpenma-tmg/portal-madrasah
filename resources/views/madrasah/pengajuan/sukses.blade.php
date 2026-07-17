{{-- sukses.blade.php --}}
@extends('madrasah.layouts.app')
@section('title', 'Pengajuan Berhasil')
@section('content')
<div class="max-w-md mx-auto">
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-6 pt-6 pb-5 text-center bg-green-700">
            <div class="w-11 h-11 bg-white/15 rounded-xl flex items-center justify-center mx-auto mb-3">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
            </div>
            <p class="text-white font-semibold text-base">Pengajuan Terkirim</p>
            <p class="text-green-100 text-xs mt-1">Dokumen sedang menunggu diproses</p>
        </div>
        <div class="p-5">
            <div class="rounded-lg px-4 py-3 mb-4 text-center" style="background:#f0fdf4; border:1.5px dashed #86efac">
                <p class="text-2xs text-gray-400 font-medium uppercase tracking-wide mb-1">Kode Ajuan</p>
                <p class="text-xl font-bold text-green-700 tracking-widest font-mono">{{ $pengajuan->kode_ajuan }}</p>
            </div>
            <div class="space-y-2 text-sm mb-5">
                <div class="flex justify-between"><span class="text-gray-400">Jenis Dokumen</span><span class="font-medium text-gray-800">{{ $pengajuan->jenisDokumen->nama }}</span></div>
                <div class="flex justify-between"><span class="text-gray-400">Status</span><span class="badge badge-yellow">Menunggu</span></div>
                <div class="flex justify-between"><span class="text-gray-400">Tanggal</span><span class="font-medium text-gray-800">{{ $pengajuan->created_at->format('d M Y, H:i') }}</span></div>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('madrasah.pengajuan.riwayat') }}" class="flex-1 text-center bg-green-700 hover:bg-green-800 text-white text-sm font-medium py-2 rounded-lg transition">Lihat Riwayat</a>
                <a href="{{ route('madrasah.dashboard') }}" class="flex-1 text-center bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-medium py-2 rounded-lg transition">Dashboard</a>
            </div>
        </div>
    </div>
</div>
@endsection
