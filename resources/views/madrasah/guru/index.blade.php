@extends('madrasah.layouts.app')
@section('title', 'Guru Saya')
@section('content')

<div class="flex items-center justify-between mb-4 fade-in">
    <div>
        <h1 class="text-lg font-semibold text-gray-900">Guru Saya</h1>
        <p class="text-sm text-gray-500 mt-0.5">Pantau arsip &amp; pengajuan dokumen guru di madrasah Anda.</p>
    </div>
</div>

{{-- Stat ringkas --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-4 fade-in">
    <div class="stat-card">
        <div class="stat-value">{{ $stats['total_guru'] }}</div>
        <div class="stat-sub">Total guru</div>
    </div>
    <div class="stat-card">
        <div class="stat-value">{{ $stats['total_arsip'] }}</div>
        <div class="stat-sub">Total arsip semua guru</div>
    </div>
    <div class="stat-card amber">
        <div class="stat-value">{{ $stats['total_pending'] }}</div>
        <div class="stat-sub">Pengajuan menunggu (guru)</div>
    </div>
</div>

{{-- Search --}}
<div class="bg-white rounded-xl border border-gray-200 px-4 py-3 mb-4 fade-in">
    <form method="GET" class="flex gap-2">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau PegID..."
               class="w-full sm:w-72 border border-gray-200 rounded-lg px-3 py-1.5 text-xs focus:outline-none focus:ring-2 focus:ring-primary-500/30 focus:border-primary-400">
        <button type="submit" class="btn-xs btn-primary-xs">Cari</button>
        @if(request('search'))
        <a href="{{ route('madrasah.guru.index') }}" class="text-xs text-gray-400 hover:text-gray-600 self-center">Reset</a>
        @endif
    </form>
</div>

<div class="bg-white rounded-xl border border-gray-200 overflow-hidden fade-in">
    {{-- Desktop: tabel --}}
    <div class="hidden md:block overflow-x-auto">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Nama Guru</th>
                    <th>PegID</th>
                    <th>Status Akun</th>
                    <th>Arsip</th>
                    <th>Pengajuan Pending</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($guruList as $guru)
                <tr>
                    <td>
                        <div class="flex items-center gap-2.5">
                            <div class="w-7 h-7 rounded-lg flex items-center justify-center text-white text-xs font-semibold flex-shrink-0"
                                 style="background:linear-gradient(135deg,#0d3a7c,#c9a163)">
                                {{ strtoupper(substr($guru->nama, 0, 1)) }}
                            </div>
                            <span class="font-medium text-gray-800">{{ $guru->nama }}</span>
                        </div>
                    </td>
                    <td class="font-mono text-xs">{{ $guru->pegid }}</td>
                    <td>
                        <span class="badge {{ $guru->is_active ? 'badge-green' : 'badge-gray' }}">
                            {{ $guru->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </td>
                    <td>
                        <span class="text-gray-700">{{ $guru->total_verified }}</span>
                        <span class="text-gray-400">/ {{ $guru->total_arsip }} terverifikasi</span>
                    </td>
                    <td>
                        @if($guru->total_pending > 0)
                        <span class="badge badge-yellow">{{ $guru->total_pending }} menunggu</span>
                        @else
                        <span class="text-gray-400 text-xs">—</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('madrasah.guru.show', $guru->id) }}" class="btn-xs btn-ghost-xs">Lihat Detail</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-gray-400 py-8">
                        @if(request('search'))
                            Tidak ada guru yang cocok dengan pencarian "{{ request('search') }}".
                        @else
                            Belum ada akun guru terdaftar di madrasah ini.
                        @endif
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Mobile: card list --}}
    <div class="md:hidden divide-y divide-gray-50">
        @forelse($guruList as $guru)
        <div class="p-4">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center text-white text-sm font-semibold flex-shrink-0"
                     style="background:linear-gradient(135deg,#0d3a7c,#c9a163)">
                    {{ strtoupper(substr($guru->nama, 0, 1)) }}
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-medium text-gray-800 truncate">{{ $guru->nama }}</p>
                    <p class="text-xs text-gray-400 font-mono">{{ $guru->pegid }}</p>
                </div>
                <span class="badge {{ $guru->is_active ? 'badge-green' : 'badge-gray' }} flex-shrink-0">
                    {{ $guru->is_active ? 'Aktif' : 'Nonaktif' }}
                </span>
            </div>
            <div class="grid grid-cols-1 gap-2.5 mb-3">
                <div class="rounded-lg bg-gray-50 px-3 py-2">
                    <p class="text-2xs text-gray-400 mb-0.5">Arsip</p>
                    <p class="text-xs text-gray-700 font-medium">{{ $guru->total_verified }}<span class="text-gray-400 font-normal"> / {{ $guru->total_arsip }} verified</span></p>
                </div>
            </div>
            <div class="flex items-center gap-2 mb-3">
                @if($guru->total_pending > 0)
                <span class="badge badge-yellow">{{ $guru->total_pending }} pengajuan menunggu</span>
                @endif
            </div>
            <a href="{{ route('madrasah.guru.show', $guru->id) }}"
               class="block text-center py-2.5 rounded-lg border border-gray-200 text-gray-700 text-xs font-medium active:bg-gray-50">
                Lihat Detail
            </a>
        </div>
        @empty
        <div class="text-center text-gray-400 py-10 px-4 text-sm">
            @if(request('search'))
                Tidak ada guru yang cocok dengan pencarian "{{ request('search') }}".
            @else
                Belum ada akun guru terdaftar di madrasah ini.
            @endif
        </div>
        @endforelse
    </div>
</div>

@if($guruList->hasPages())
<div class="mt-4 fade-in">
    {{ $guruList->links() }}
</div>
@endif

@endsection
