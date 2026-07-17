@extends('layouts.admin')
@section('title', 'Data Madrasah')

@section('content')

{{-- Modal Konfirmasi Reset --}}
<div id="modal-reset" class="fixed inset-0 z-50 hidden items-center justify-center p-4" style="background:rgba(0,0,0,0.4);backdrop-filter:blur(2px)">
    <div class="bg-white rounded-2xl w-full max-w-sm overflow-hidden" style="box-shadow:0 20px 60px rgba(0,0,0,0.15)">
        <div class="h-0.5 w-full bg-red-500"></div>
        <div class="p-5">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0 bg-red-100">
                    <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-semibold text-zinc-800">Reset Semua Data</p>
                    <p class="text-2xs text-zinc-400">Data Madrasah & Siswa</p>
                </div>
            </div>
            <p class="text-xs text-zinc-500 bg-zinc-50 rounded-lg px-3 py-2.5 mb-4 leading-relaxed">
                Semua data madrasah dan siswa akan <strong>dihapus permanen</strong>. Tindakan ini tidak dapat dibatalkan.
            </p>
            <div class="flex gap-2">
                <button onclick="closeResetModal()" class="flex-1 px-3 py-2 rounded-lg text-xs font-semibold text-zinc-600 bg-zinc-100 hover:bg-zinc-200 transition">
                    Batal
                </button>
                <form method="POST" action="{{ route('admin.data-madrasah.reset') }}" class="flex-1">
                    @csrf @method('DELETE')
                    <button type="submit" class="w-full px-3 py-2 rounded-lg bg-red-600 text-white text-xs font-bold hover:bg-red-700 transition">
                        Ya, Reset
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Header --}}
<div class="flex items-center justify-between mb-4 fade-in">
    <div>
        <h1 class="text-sm font-semibold text-zinc-900">Data Madrasah</h1>
        <p class="text-xs text-zinc-400 mt-0.5">Kelola data madrasah binaan Penma Temanggung</p>
    </div>
    <div class="flex items-center gap-2">
        @if(Auth::user()->isSuperAdmin())
        <button onclick="openResetModal()" class="inline-flex items-center gap-1.5 text-xs font-medium px-3 py-1.5 rounded-lg border border-red-200 text-red-600 hover:bg-red-50 transition">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            Reset
        </button>
        @endif
        <a href="{{ route('admin.data-madrasah.import-madrasah') }}" class="inline-flex items-center gap-1.5 text-xs font-medium px-3 py-1.5 rounded-lg border border-zinc-200 text-zinc-600 hover:bg-zinc-50 transition">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
            Import Madrasah
        </a>
        <a href="{{ route('admin.data-madrasah.import-siswa') }}" class="inline-flex items-center gap-1.5 text-xs font-medium px-3 py-1.5 rounded-lg border border-zinc-200 text-zinc-600 hover:bg-zinc-50 transition">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
            Import Siswa
        </a>
    </div>
</div>

{{-- Rekap Cards --}}
@php
    $jConfig = [
        'RA'  => ['label' => 'RA / BA', 'bg' => '#fef9c3', 'fg' => 'text-amber-500'],
        'MI'  => ['label' => 'MI',      'bg' => '#dbeafe', 'fg' => 'text-blue-500'],
        'MTs' => ['label' => 'MTs',     'bg' => '#ede9fe', 'fg' => 'text-violet-500'],
        'MA'  => ['label' => 'MA',      'bg' => '#dcfce7', 'fg' => 'text-primary-600'],
    ];
@endphp
<div class="grid grid-cols-2 sm:grid-cols-5 gap-3 mb-5 fade-in">
    @foreach($jConfig as $jenjang => $cfg)
    <div class="bg-white rounded-xl border border-zinc-100 px-4 py-4">
        <p class="text-2xs font-semibold text-zinc-400 uppercase tracking-wider mb-2">{{ $cfg['label'] }}</p>
        <div class="flex items-end justify-between">
            <p class="text-3xl font-bold text-zinc-900 leading-none">{{ number_format($totalPerJenjang[$jenjang] ?? 0) }}</p>
            <div class="w-8 h-8 rounded-full flex items-center justify-center" style="background:{{ $cfg['bg'] }}">
                <svg class="w-4 h-4 {{ $cfg['fg'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/>
                </svg>
            </div>
        </div>
    </div>
    @endforeach
    <div class="bg-white rounded-xl border border-zinc-100 px-4 py-4">
        <p class="text-2xs font-semibold text-zinc-400 uppercase tracking-wider mb-2">Total</p>
        <div class="flex items-end justify-between">
            <p class="text-3xl font-bold text-zinc-900 leading-none">{{ number_format($totalSemua) }}</p>
            <div class="w-8 h-8 rounded-full flex items-center justify-center" style="background:#f4f4f5">
                <svg class="w-4 h-4 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </div>
        </div>
    </div>
</div>

{{-- Info tahun --}}
@if($tahunData || $tahunPelajaran)
<div class="flex gap-2 mb-3">
    @if($tahunData)
    <span class="badge badge-blue">Data Madrasah: {{ $tahunData }}</span>
    @endif
    @if($tahunPelajaran)
    <span class="badge badge-green">Data Siswa: {{ $tahunPelajaran }}</span>
    @endif
</div>
@endif

{{-- Filter --}}
<div class="bg-white rounded-xl border border-zinc-100 px-4 py-3 mb-3 fade-in">
    <form method="GET" class="flex flex-wrap gap-2 items-center">
        <div class="relative flex-1 min-w-48">
            <svg class="absolute left-2.5 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-zinc-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama / NSM..."
                class="w-full border border-zinc-200 rounded-lg pl-8 pr-3 py-1.5 text-xs focus:ring-1 focus:ring-zinc-300 focus:outline-none">
        </div>
        <select name="jenjang" class="border border-zinc-200 rounded-lg px-3 py-1.5 text-xs focus:outline-none bg-white">
            <option value="">Semua Jenjang</option>
            @foreach(['RA','MI','MTs','MA'] as $j)
            <option value="{{ $j }}" {{ request('jenjang') == $j ? 'selected' : '' }}>{{ $j }}</option>
            @endforeach
        </select>
        <button type="submit" class="px-3 py-1.5 bg-zinc-900 text-white text-xs font-medium rounded-lg hover:bg-zinc-700 transition">Cari</button>
        @if(request()->hasAny(['search','jenjang']))
        <a href="{{ route('admin.data-madrasah.index') }}" class="text-xs text-zinc-400 hover:text-zinc-600 transition">Reset</a>
        @endif
    </form>
</div>

{{-- Tabel --}}
<div class="bg-white rounded-xl border border-zinc-100 overflow-hidden fade-in">
    @if($madrasah->count() > 0)
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="tbl-head">
                    <th class="px-4 py-2.5 text-left text-2xs font-semibold text-zinc-600 uppercase tracking-wider">NSM</th>
                    <th class="px-4 py-2.5 text-left text-2xs font-semibold text-zinc-600 uppercase tracking-wider">Nama Madrasah</th>
                    <th class="px-4 py-2.5 text-center text-2xs font-semibold text-zinc-600 uppercase tracking-wider">Jenjang</th>
                    <th class="px-4 py-2.5 text-center text-2xs font-semibold text-zinc-600 uppercase tracking-wider hidden md:table-cell">Status</th>
                    <th class="px-4 py-2.5 text-center text-2xs font-semibold text-zinc-600 uppercase tracking-wider hidden lg:table-cell">Kecamatan</th>
                    <th class="px-4 py-2.5 text-center text-2xs font-semibold text-zinc-600 uppercase tracking-wider hidden lg:table-cell">Akreditasi</th>
                    <th class="px-4 py-2.5 text-center text-2xs font-semibold text-zinc-600 uppercase tracking-wider hidden sm:table-cell">Siswa</th>
                    <th class="px-4 py-2.5 text-center text-2xs font-semibold text-zinc-600 uppercase tracking-wider">Aktif</th>
                </tr>
            </thead>
            <tbody>
                @foreach($madrasah as $m)
                @php $jBadge = ['RA'=>'badge-yellow','MI'=>'badge-blue','MTs'=>'badge-purple','MA'=>'badge-green']; @endphp
                <tr class="tbl-row" style="border-bottom:1px solid #fafafa">
                    <td class="px-4 py-3"><span class="text-2xs font-mono text-zinc-400">{{ $m->nsm }}</span></td>
                    <td class="px-4 py-3"><span class="text-xs text-zinc-800">{{ $m->nama_madrasah }}</span></td>
                    <td class="px-4 py-3"><span class="badge {{ $jBadge[$m->jenjang] ?? 'badge-gray' }}">{{ $m->jenjang }}</span></td>
                    <td class="px-4 py-3 hidden md:table-cell"><span class="text-2xs text-zinc-500">{{ $m->status }}</span></td>
                    <td class="px-4 py-3 hidden lg:table-cell"><span class="text-2xs text-zinc-500">{{ $m->kecamatan }}</span></td>
                    <td class="px-4 py-3 hidden lg:table-cell">
                        <span class="text-xs font-semibold" style="color: {{ $m->akreditasi === 'A' ? '#16a34a' : ($m->akreditasi === 'B' ? '#2563eb' : '#a1a1aa') }}">
                            {{ $m->akreditasi ?? '—' }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-right hidden sm:table-cell">
                        <span class="text-xs font-medium text-zinc-700">{{ $m->siswaLatest ? number_format($m->siswaLatest->total_siswa) : '—' }}</span>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <form method="POST" action="{{ route('admin.data-madrasah.toggle', $m->id) }}">
                            @csrf @method('PATCH')
                            <button type="submit"
                                class="w-7 h-4 rounded-full transition-colors relative {{ $m->is_active ? 'bg-green-500' : 'bg-zinc-300' }}"
                                title="{{ $m->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                <span class="absolute top-0.5 {{ $m->is_active ? 'right-0.5' : 'left-0.5' }} w-3 h-3 bg-white rounded-full shadow transition-all"></span>
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="px-4 py-3 flex items-center justify-between" style="border-top:1px solid #f4f4f5">
        <p class="text-2xs text-zinc-400">{{ $madrasah->firstItem() }}–{{ $madrasah->lastItem() }} dari {{ $madrasah->total() }} data</p>
        {{ $madrasah->links() }}
    </div>
    @else
    <div class="py-14 text-center">
        <svg class="w-10 h-10 mx-auto text-zinc-200 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
        </svg>
        <p class="text-xs text-zinc-400 mb-1">Belum ada data madrasah</p>
        <p class="text-2xs text-zinc-300 mb-3">Silakan import data dari file Excel EMIS</p>
        <a href="{{ route('admin.data-madrasah.import-madrasah') }}" class="inline-flex items-center gap-1.5 text-xs font-medium px-3 py-1.5 rounded-lg bg-zinc-900 text-white hover:bg-zinc-700 transition">
            Import Sekarang
        </a>
    </div>
    @endif
</div>

@push('scripts')
<script>
    function openResetModal() {
        const modal = document.getElementById('modal-reset');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
    function closeResetModal() {
        const modal = document.getElementById('modal-reset');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeResetModal();
    });
</script>
@endpush
@endsection