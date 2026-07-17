@extends('layouts.admin')
@section('title', 'Riwayat')

@section('content')

{{-- Header --}}
<div class="mb-4 fade-in">
    <h1 class="text-sm font-semibold text-zinc-900">Riwayat Pengajuan</h1>
    <p class="text-xs text-zinc-400 mt-0.5">Daftar pengajuan yang sudah diproses</p>
</div>

{{-- Filter --}}
<div class="bg-white rounded-xl border border-zinc-100 px-4 py-3 mb-3 fade-in">
    <form method="GET" class="flex flex-wrap gap-2 items-center">
        <div class="relative flex-1 min-w-48">
            <svg class="absolute left-2.5 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-zinc-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input type="text" name="search" value="{{ request('search') }}"
                placeholder="Kode, nama, madrasah, token..."
                class="w-full border border-zinc-200 rounded-lg pl-8 pr-3 py-1.5 text-xs focus:ring-1 focus:ring-zinc-300 focus:outline-none">
        </div>
        <select name="status" class="border border-zinc-200 rounded-lg px-3 py-1.5 text-xs focus:outline-none bg-white">
            <option value="">Semua Status</option>
            <option value="diterima" {{ request('status')==='diterima'?'selected':'' }}>Diterima</option>
            <option value="ditolak"  {{ request('status')==='ditolak' ?'selected':'' }}>Ditolak</option>
        </select>
        <button type="submit" class="px-3 py-1.5 bg-zinc-900 text-white text-xs font-medium rounded-lg hover:bg-zinc-700 transition">Cari</button>
        @if(request()->hasAny(['search','status']))
        <a href="{{ route('admin.riwayat.index') }}" class="text-xs text-zinc-400 hover:text-zinc-600 transition">Reset</a>
        @endif
    </form>
</div>

{{-- Tabel --}}
<div class="bg-white rounded-xl border border-zinc-100 overflow-hidden fade-in">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="tbl-head">
                    <th class="px-4 py-2.5 text-left text-2xs font-semibold text-zinc-600 uppercase tracking-wider">Kode</th>
                    <th class="px-4 py-2.5 text-left text-2xs font-semibold text-zinc-600 uppercase tracking-wider">Pemohon</th>
                    <th class="px-4 py-2.5 text-center text-2xs font-semibold text-zinc-600 uppercase tracking-wider hidden sm:table-cell">Token</th>
                    <th class="px-4 py-2.5 text-center text-2xs font-semibold text-zinc-600 uppercase tracking-wider hidden md:table-cell">Dokumen</th>
                    <th class="px-4 py-2.5 text-center text-2xs font-semibold text-zinc-600 uppercase tracking-wider hidden lg:table-cell">Diproses</th>
                    <th class="px-4 py-2.5 text-center text-2xs font-semibold text-zinc-600 uppercase tracking-wider">Status</th>
                    <th class="px-4 py-2.5 text-center text-2xs font-semibold text-zinc-600 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pengajuan as $p)
                <tr class="tbl-row" style="border-bottom:1px solid #fafafa">
                    <td class="px-4 py-3">
                        <code class="text-xs font-mono font-bold text-primary-700">{{ $p->kode_ajuan }}</code>
                    </td>
                    <td class="px-4 py-3">
                        <p class="text-xs font-semibold text-zinc-800 truncate max-w-36">{{ $p->nama_guru }}</p>
                        <p class="text-2xs text-zinc-400 mt-0.5 truncate max-w-36">{{ $p->nama_madrasah }}</p>
                    </td>
                    <td class="px-4 py-3 text-center hidden sm:table-cell">
                        @if($p->token)
                        <code class="text-2xs font-mono font-semibold bg-primary-50 text-primary-700 px-1.5 py-0.5 rounded">{{ $p->token }}</code>
                        @else
                        <span class="text-zinc-300 text-2xs">—</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-center hidden md:table-cell">
                        <span class="badge badge-gray">{{ $p->jenisDokumen->nama }}</span>
                    </td>
                    <td class="px-4 py-3 text-center hidden lg:table-cell">
                        <span class="text-2xs text-zinc-400">{{ $p->tanggal_proses?->format('d/m/y, H:i') ?? '—' }}</span>
                    </td>
                    <td class="px-4 py-3 text-center">
                        @if($p->status === 'pending')
                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-amber-100" title="Menunggu">
                            <svg class="w-3 h-3 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </span>
                        @elseif($p->status === 'diterima')
                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-green-100" title="Diterima">
                            <svg class="w-3 h-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                            </svg>
                        </span>
                        @else
                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-red-100" title="Ditolak">
                            <svg class="w-3 h-3 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-center">
                        <a href="{{ route('admin.pengajuan.show', $p->id) }}" class="btn-icon blue" title="Lihat Detail">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="py-14 text-center">
                        <svg class="w-10 h-10 mx-auto text-zinc-200 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="text-xs text-zinc-400">Belum ada riwayat</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-4 py-3" style="border-top:1px solid #f4f4f5">
        {{ $pengajuan->links() }}
    </div>
</div>
@endsection
