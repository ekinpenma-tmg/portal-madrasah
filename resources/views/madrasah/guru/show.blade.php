@extends('madrasah.layouts.app')
@section('title', 'Detail Guru')
@section('content')

<div class="flex items-center gap-2 text-sm text-gray-400 mb-4 fade-in">
    <a href="{{ route('madrasah.guru.index') }}" class="hover:text-primary-600 transition">Guru Saya</a>
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    <span class="text-gray-600 font-medium">{{ $guru->nama }}</span>
</div>

{{-- Header guru --}}
<div class="bg-white rounded-xl border border-gray-200 p-5 mb-4 fade-in">
    <div class="flex items-start justify-between gap-4 flex-wrap">
        <div class="flex items-center gap-3">
            <div class="w-11 h-11 rounded-full bg-primary-700 flex items-center justify-center text-white text-sm font-semibold flex-shrink-0">
                {{ strtoupper(substr($guru->nama, 0, 1)) }}
            </div>
            <div>
                <h1 class="text-base font-semibold text-gray-900">{{ $guru->nama }}</h1>
                <p class="text-xs text-gray-400 font-mono">PegID: {{ $guru->pegid }}</p>
            </div>
        </div>
        <span class="badge {{ $guru->is_active ? 'badge-green' : 'badge-gray' }}">
            {{ $guru->is_active ? 'Akun aktif' : 'Akun nonaktif' }}
        </span>
    </div>

    <div class="grid sm:grid-cols-3 gap-3 mt-4 pt-4 text-sm" style="border-top:1px solid #f5f5f5">
        <div>
            <p class="text-xs text-gray-400">Nomor HP / WhatsApp</p>
            <p class="font-medium text-gray-800">{{ $guru->hasNoHpValid() ? $guru->no_hp : '—' }}</p>
        </div>
        <div>
            <p class="text-xs text-gray-400">Email</p>
            <p class="font-medium text-gray-800">{{ $guru->hasEmailValid() ? $guru->email : '—' }}</p>
        </div>
        <div>
            <p class="text-xs text-gray-400">Status Password</p>
            <p class="font-medium {{ $guru->password_changed ? 'text-green-600' : 'text-amber-600' }}">
                {{ $guru->password_changed ? 'Sudah diubah' : 'Masih default' }}
            </p>
        </div>
    </div>
</div>

{{-- Arsip guru --}}
<div class="bg-white rounded-xl border border-gray-200 overflow-hidden mb-4 fade-in">
    <div class="px-4 py-3 flex items-center justify-between" style="border-bottom:1px solid #f0f0f0">
        <h3 class="text-sm font-semibold text-gray-800">Arsip Digital ({{ $arsipList->count() }})</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Judul</th>
                    <th>Kategori</th>
                    <th>Tahun</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($arsipList as $arsip)
                <tr>
                    <td class="font-medium text-gray-800">{{ $arsip->judul }}</td>
                    <td>{{ $arsip->kategori->nama ?? '—' }}</td>
                    <td>{{ $arsip->tahun ?? '—' }}</td>
                    <td>
                        <span class="badge {{ $arsip->is_verified ? 'badge-green' : 'badge-gray' }}">
                            {{ $arsip->is_verified ? 'Terverifikasi' : 'Belum diverifikasi' }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="text-center text-gray-400 py-8">Guru ini belum mengunggah arsip.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Pengajuan guru --}}
<div class="bg-white rounded-xl border border-gray-200 overflow-hidden fade-in">
    <div class="px-4 py-3 flex items-center justify-between" style="border-bottom:1px solid #f0f0f0">
        <h3 class="text-sm font-semibold text-gray-800">Riwayat Pengajuan ({{ $pengajuanList->count() }})</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Kode Ajuan</th>
                    <th>Jenis Dokumen</th>
                    <th>Tanggal</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pengajuanList as $p)
                <tr>
                    <td class="font-mono text-xs">{{ $p->kode_ajuan }}</td>
                    <td class="font-medium text-gray-800">{{ $p->jenisDokumen->nama ?? '—' }}</td>
                    <td class="text-gray-500 text-xs">{{ $p->created_at->format('d M Y') }}</td>
                    <td>
                        @php
                            $badgeClass = $p->status === 'diterima' ? 'badge-green' : ($p->status === 'ditolak' ? 'badge-red' : 'badge-yellow');
                        @endphp
                        <span class="badge {{ $badgeClass }}">{{ $p->status_label }}</span>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="text-center text-gray-400 py-8">Guru ini belum mengajukan dokumen.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
