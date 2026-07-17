@extends('guru.layouts.app')
@section('title', 'Riwayat Ajuan')

@section('content')
<div class="max-w-4xl mx-auto">

    <div class="flex items-center justify-between mb-5 fade-in">
        <div>
            <h1 class="text-lg font-semibold text-gray-900">Riwayat Ajuan</h1>
            <p class="text-sm text-gray-500 mt-0.5">Daftar seluruh dokumen yang pernah Anda ajukan.</p>
        </div>
        <a href="{{ route('guru.pengajuan.index') }}" class="btn-xs btn-primary-xs">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Ajukan Baru
        </a>
    </div>

    {{-- Filter status --}}
    <div class="bg-white rounded-xl border border-gray-200 px-4 py-3 mb-4 fade-in">
        <form method="GET" class="flex items-center gap-2">
            <select name="status" onchange="this.form.submit()"
                    class="border border-gray-200 rounded-lg px-3 py-1.5 text-xs text-gray-600 focus:outline-none focus:ring-2 focus:ring-primary-500/30">
                <option value="">Semua status</option>
                <option value="pending"  {{ request('status') === 'pending'  ? 'selected' : '' }}>Menunggu</option>
                <option value="diterima" {{ request('status') === 'diterima' ? 'selected' : '' }}>Diterima</option>
                <option value="ditolak"  {{ request('status') === 'ditolak'  ? 'selected' : '' }}>Ditolak</option>
            </select>
            @if(request('status'))
            <a href="{{ route('guru.pengajuan.riwayat') }}" class="text-xs text-gray-400 hover:text-gray-600">Reset</a>
            @endif
        </form>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden fade-in">
        @if($riwayat->isEmpty())
        <div class="p-10 text-center">
            <div class="w-12 h-12 bg-gray-100 rounded-xl flex items-center justify-center mx-auto mb-3">
                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <p class="text-sm font-medium text-gray-700">Belum ada riwayat ajuan</p>
            <p class="text-xs text-gray-400 mt-1">Pengajuan dokumen yang Anda kirim akan muncul di sini.</p>
        </div>
        @else
        {{-- Desktop: tabel (tidak diubah) --}}
        <div class="hidden md:block">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Kode Ajuan</th>
                    <th>Jenis Dokumen</th>
                    <th>Tanggal</th>
                    <th>Status</th>
                    <th style="text-align:right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($riwayat as $p)
                <tr>
                    <td><span class="font-mono text-xs text-gray-600">{{ $p->kode_ajuan }}</span></td>
                    <td><span class="text-sm text-gray-800">{{ $p->jenisDokumen->nama }}</span></td>
                    <td><span class="text-xs text-gray-400">{{ $p->created_at->format('d M Y') }}</span></td>
                    <td>
                        <span class="badge {{ $p->status === 'diterima' ? 'badge-green' : ($p->status === 'ditolak' ? 'badge-red' : 'badge-yellow') }}">
                            {{ $p->status_label }}
                        </span>
                    </td>
                    <td style="text-align:right">
                        <div class="flex items-center justify-end gap-1">
                            <a href="{{ route('guru.pengajuan.show', $p->id) }}" class="btn-icon blue" title="Lihat Detail">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </a>
                            @if($p->status === 'pending')
                            <form action="{{ route('guru.pengajuan.batalkan', $p->id) }}" method="POST"
                                  data-confirm="Batalkan pengajuan {{ $p->kode_ajuan }}?" data-confirm-btn="Ya, Batalkan" class="inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-icon danger" title="Batalkan Ajuan">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="px-4 py-3 border-t border-gray-100">
            {{ $riwayat->links() }}
        </div>
        </div>

        {{-- Mobile: card list --}}
        <div class="md:hidden divide-y divide-gray-50">
            @foreach($riwayat as $p)
            <div class="p-4">
                <div class="flex items-center justify-between gap-2 mb-2">
                    <span class="font-mono text-xs text-gray-500">{{ $p->kode_ajuan }}</span>
                    <span class="badge {{ $p->status === 'diterima' ? 'badge-green' : ($p->status === 'ditolak' ? 'badge-red' : 'badge-yellow') }}">
                        {{ $p->status_label }}
                    </span>
                </div>
                <p class="text-sm font-medium text-gray-800">{{ $p->jenisDokumen->nama }}</p>
                <p class="text-xs text-gray-400 mt-0.5 mb-3">{{ $p->created_at->format('d M Y') }}</p>
                <div class="flex items-center gap-2">
                    <a href="{{ route('guru.pengajuan.show', $p->id) }}"
                       class="flex-1 inline-flex items-center justify-center gap-1.5 text-xs font-medium py-2.5 rounded-lg border border-gray-200 text-gray-700 active:bg-gray-50">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        Lihat Detail
                    </a>
                    @if($p->status === 'pending')
                    <form action="{{ route('guru.pengajuan.batalkan', $p->id) }}" method="POST"
                          data-confirm="Batalkan pengajuan {{ $p->kode_ajuan }}?" data-confirm-btn="Ya, Batalkan" class="flex-shrink-0">
                        @csrf @method('DELETE')
                        <button type="submit" class="inline-flex items-center justify-center w-11 h-11 rounded-lg border border-red-200 text-red-500 active:bg-red-50" title="Batalkan Ajuan">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </form>
                    @endif
                </div>
            </div>
            @endforeach
            <div class="px-4 py-3 border-t border-gray-100">
                {{ $riwayat->links() }}
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
