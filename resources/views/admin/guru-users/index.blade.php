@extends('layouts.admin')
@section('title', 'Akun Guru')

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
                    <p class="text-sm font-semibold text-zinc-800">Reset Semua Akun Guru</p>
                    <p class="text-2xs text-zinc-400">Total {{ number_format($totalGuru) }} akun</p>
                </div>
            </div>
            <p class="text-xs text-zinc-500 bg-zinc-50 rounded-lg px-3 py-2.5 mb-4 leading-relaxed">
                Semua akun guru akan <strong>dihapus permanen</strong>, beserta <strong>seluruh arsip</strong> milik guru-guru itu. Tindakan ini tidak dapat dibatalkan.
            </p>
            <div class="flex gap-2">
                <button onclick="closeResetModal()" class="flex-1 px-3 py-2 rounded-lg text-xs font-semibold text-zinc-600 bg-zinc-100 hover:bg-zinc-200 transition">
                    Batal
                </button>
                <form method="POST" action="{{ route('admin.guru-users.reset') }}" class="flex-1">
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
<div class="flex items-center justify-between mb-5 fade-in">
    <div>
        <h1 class="text-base font-semibold text-zinc-900">Akun Guru</h1>
        <p class="text-xs text-zinc-400 mt-0.5">Manajemen akun login guru untuk Arsip Digital</p>
    </div>
    <div class="flex items-center gap-2">
        @if(Auth::user()->isSuperAdmin())
        <button onclick="openResetModal()" class="inline-flex items-center gap-1.5 text-xs font-medium px-3 py-1.5 rounded-lg border border-red-200 text-red-600 hover:bg-red-50 transition">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            Reset
        </button>
        @endif
        <a href="{{ route('admin.guru-users.import-form') }}"
           class="inline-flex items-center gap-1.5 text-xs font-medium px-3 py-1.5 rounded-lg border border-zinc-200 text-zinc-600 hover:bg-zinc-50 transition">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
            </svg>
            Import Excel
        </a>
        <a href="{{ route('admin.guru-users.create') }}"
           class="inline-flex items-center gap-1.5 text-xs font-medium px-3 py-1.5 rounded-lg text-white transition"
           style="background:#18181b">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Guru
        </a>
    </div>
</div>

{{-- Flash --}}
@if(session('import_errors') && count(session('import_errors')))
<div class="bg-amber-50 border border-amber-200 text-amber-800 rounded-lg px-4 py-3 mb-4 text-xs">
    <p class="font-semibold mb-1">Beberapa baris dilewati:</p>
    <ul class="list-disc list-inside space-y-0.5">@foreach(session('import_errors') as $e)<li>{{ $e }}</li>@endforeach</ul>
</div>
@endif

{{-- Stat Cards --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-5 fade-in">
    <div class="bg-white rounded-xl border border-zinc-100 px-4 py-4">
        <p class="text-2xs font-semibold text-zinc-400 uppercase tracking-wider mb-2">Total Akun</p>
        <div class="flex items-end justify-between">
            <p class="text-3xl font-bold text-zinc-900 leading-none">{{ number_format($totalGuru) }}</p>
            <div class="w-8 h-8 rounded-full flex items-center justify-center" style="background:#f4f4f5">
                <svg class="w-4 h-4 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-xl border border-zinc-100 px-4 py-4">
        <p class="text-2xs font-semibold text-zinc-400 uppercase tracking-wider mb-2">Aktif</p>
        <div class="flex items-end justify-between">
            <p class="text-3xl font-bold text-zinc-900 leading-none">{{ number_format($totalAktif) }}</p>
            <div class="w-8 h-8 rounded-full flex items-center justify-center" style="background:#dcfce7">
                <svg class="w-4 h-4 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-xl border border-zinc-100 px-4 py-4">
        <p class="text-2xs font-semibold text-zinc-400 uppercase tracking-wider mb-2">Non-Aktif</p>
        <div class="flex items-end justify-between">
            <p class="text-3xl font-bold text-zinc-900 leading-none">{{ number_format($totalGuru - $totalAktif) }}</p>
            <div class="w-8 h-8 rounded-full flex items-center justify-center" style="background:#fee2e2">
                <svg class="w-4 h-4 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-xl border border-zinc-100 px-4 py-4">
        <p class="text-2xs font-semibold text-zinc-400 uppercase tracking-wider mb-2">Password Default</p>
        <div class="flex items-end justify-between">
            <p class="text-3xl font-bold text-zinc-900 leading-none">{{ number_format($totalPasswordDefault) }}</p>
            <div class="w-8 h-8 rounded-full flex items-center justify-center" style="background:#fef9c3">
                <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
            </div>
        </div>
    </div>
</div>

{{-- Filter --}}
<div class="bg-white rounded-xl border border-zinc-100 px-4 py-3 mb-4 fade-in">
    <form method="GET" class="flex flex-wrap gap-2 items-center">
        <div class="relative flex-1 min-w-44">
            <svg class="absolute left-2.5 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-zinc-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau PegID..."
                class="w-full border border-zinc-200 rounded-lg pl-8 pr-3 py-1.5 text-xs focus:ring-1 focus:ring-zinc-300 focus:outline-none">
        </div>
        <select name="madrasah_id" class="border border-zinc-200 rounded-lg px-3 py-1.5 text-xs focus:ring-1 focus:ring-zinc-300 focus:outline-none bg-white w-52">
            <option value="">Semua Madrasah</option>
            @foreach($madrasahs as $m)
            <option value="{{ $m->id }}" {{ request('madrasah_id')==$m->id?'selected':'' }}>{{ $m->label_lengkap }}</option>
            @endforeach
        </select>
        <select name="status" class="border border-zinc-200 rounded-lg px-3 py-1.5 text-xs focus:ring-1 focus:ring-zinc-300 focus:outline-none bg-white">
            <option value="">Semua Status</option>
            <option value="aktif"    {{ request('status')==='aktif'?'selected':'' }}>Aktif</option>
            <option value="nonaktif" {{ request('status')==='nonaktif'?'selected':'' }}>Non-Aktif</option>
        </select>
        <button type="submit" class="px-3 py-1.5 bg-zinc-900 text-white text-xs font-medium rounded-lg hover:bg-zinc-700 transition">Filter</button>
        @if(request()->hasAny(['search','madrasah_id','status']))
        <a href="{{ route('admin.guru-users.index') }}" class="text-xs text-zinc-400 hover:text-zinc-600 transition">Reset</a>
        @endif
    </form>
</div>

{{-- Tabel --}}
<div class="bg-white rounded-xl border border-zinc-100 overflow-hidden fade-in">
    <table class="w-full text-sm">
        <thead>
            <tr style="border-bottom:1px solid #f4f4f5">
                <th class="px-4 py-2.5 text-left text-2xs font-semibold text-zinc-600 uppercase tracking-wider">Guru</th>
                <th class="px-4 py-2.5 text-left text-2xs font-semibold text-zinc-600 uppercase tracking-wider">PegID</th>
                <th class="px-4 py-2.5 text-left text-2xs font-semibold text-zinc-600 uppercase tracking-wider hidden lg:table-cell">Madrasah</th>
                <th class="px-4 py-2.5 text-center text-2xs font-semibold text-zinc-600 uppercase tracking-wider hidden md:table-cell">Arsip</th>
                <th class="px-4 py-2.5 text-center text-2xs font-semibold text-zinc-600 uppercase tracking-wider hidden sm:table-cell">Password</th>
                <th class="px-4 py-2.5 text-center text-2xs font-semibold text-zinc-600 uppercase tracking-wider">Status</th>
                <th class="px-4 py-2.5 text-right text-2xs font-semibold text-zinc-600 uppercase tracking-wider">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($guruUsers as $guru)
            <tr class="tbl-row" style="border-bottom:1px solid #fafafa">
                {{-- Nama --}}
                <td class="px-4 py-3">
                    <div class="flex items-center gap-2.5">
                        <div class="w-7 h-7 rounded-lg flex items-center justify-center text-white text-xs font-semibold flex-shrink-0"
                             style="background:linear-gradient(135deg,#0369a1,#38bdf8)">
                            {{ strtoupper(substr($guru->nama,0,1)) }}
                        </div>
                        <div>
                            <p class="text-xs font-medium text-zinc-800">{{ $guru->nama }}</p>
                            @if($guru->email)<p class="text-2xs text-zinc-400">{{ $guru->email }}</p>@endif
                        </div>
                    </div>
                </td>
                {{-- PegID --}}
                <td class="px-4 py-3">
                    <code class="text-2xs bg-zinc-100 text-zinc-600 px-1.5 py-0.5 rounded font-mono">{{ $guru->pegid }}</code>
                </td>
                {{-- Madrasah --}}
                <td class="px-4 py-3 hidden lg:table-cell">
                    <span class="text-xs text-zinc-500">{{ $guru->madrasah?->nama_madrasah ?? '—' }}</span>
                </td>
                {{-- Arsip --}}
                <td class="px-4 py-3 text-center hidden md:table-cell">
                    <a href="{{ route('admin.arsip-guru.show', $guru->id) }}"
                       class="badge badge-blue hover:bg-blue-100 transition cursor-pointer">
                        {{ $guru->arsip_count ?? 0 }}
                    </a>
                </td>
                {{-- Password --}}
                <td class="px-4 py-3 text-center hidden sm:table-cell">
                    @if($guru->password_changed)
                        <span class="badge badge-green">Diubah</span>
                    @else
                        <span class="badge badge-yellow">Default</span>
                    @endif
                </td>
                {{-- Status --}}
                <td class="px-4 py-3 text-center">
                    @if($guru->is_active)
                        <span class="badge badge-green">Aktif</span>
                    @else
                        <span class="badge badge-red">Non-Aktif</span>
                    @endif
                </td>
                {{-- Aksi — icon langsung, tanpa dropdown --}}
                <td class="px-4 py-3">
                    <div class="flex items-center justify-end gap-0.5">
                        {{-- Lihat Arsip --}}
                        <a href="{{ route('admin.arsip-guru.show', $guru->id) }}"
                           class="btn-icon blue" title="Lihat Arsip">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                        </a>
                        {{-- Edit --}}
                        <a href="{{ route('admin.guru-users.edit', $guru->id) }}"
                           class="btn-icon" title="Edit">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </a>
                        {{-- Toggle Aktif --}}
                        <form method="POST" action="{{ route('admin.guru-users.toggle', $guru->id) }}">
                            @csrf @method('PATCH')
                            <button type="submit" class="btn-icon {{ $guru->is_active ? 'danger' : 'success' }}"
                                title="{{ $guru->is_active ? 'Non-Aktifkan' : 'Aktifkan' }}">
                                @if($guru->is_active)
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                @else
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                @endif
                            </button>
                        </form>
                        {{-- Reset Password --}}
                        <form method="POST" action="{{ route('admin.guru-users.reset-password', $guru->id) }}"
                              data-confirm="Reset password {{ addslashes($guru->nama) }} ke PegID default ({{ $guru->pegid }})?" data-confirm-type="arsip" data-confirm-btn="Ya, Reset">
                            @csrf @method('PATCH')
                            <button type="submit" class="btn-icon warning" title="Reset Password">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                            </button>
                        </form>
                        {{-- Hapus --}}
                        <form method="POST" action="{{ route('admin.guru-users.destroy', $guru->id) }}"
                              data-confirm="Hapus akun {{ addslashes($guru->nama) }}?\nAkun yang punya arsip tidak bisa dihapus." data-confirm-btn="Ya, Hapus">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn-icon danger" title="Hapus">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="py-14 text-center">
                    <svg class="w-10 h-10 mx-auto text-zinc-200 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <p class="text-xs text-zinc-400">Belum ada akun guru</p>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-4 py-3 flex items-center justify-between" style="border-top:1px solid #f4f4f5">
        <p class="text-2xs text-zinc-400">{{ $guruUsers->firstItem() ?? 0 }}–{{ $guruUsers->lastItem() ?? 0 }} dari {{ number_format($guruUsers->total()) }}</p>
        {{ $guruUsers->withQueryString()->links() }}
    </div>
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
