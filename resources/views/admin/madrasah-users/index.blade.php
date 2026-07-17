@extends('layouts.admin')
@section('title', 'Akun Madrasah')

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
                    <p class="text-sm font-semibold text-zinc-800">Reset Semua Akun Madrasah</p>
                    <p class="text-2xs text-zinc-400">Total {{ number_format($totalMadrasah) }} akun</p>
                </div>
            </div>
            <p class="text-xs text-zinc-500 bg-zinc-50 rounded-lg px-3 py-2.5 mb-4 leading-relaxed">
                Semua akun madrasah akan <strong>dihapus permanen</strong>, beserta <strong>seluruh arsip digital</strong> milik madrasah-madrasah itu. Tindakan ini tidak dapat dibatalkan.
            </p>
            <div class="flex gap-2">
                <button onclick="closeResetModal()" class="flex-1 px-3 py-2 rounded-lg text-xs font-semibold text-zinc-600 bg-zinc-100 hover:bg-zinc-200 transition">
                    Batal
                </button>
                <form method="POST" action="{{ route('admin.madrasah-users.reset') }}" class="flex-1">
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
        <h1 class="text-base font-semibold text-zinc-900">Akun Madrasah</h1>
        <p class="text-xs text-zinc-400 mt-0.5">Kelola akun login untuk portal madrasah</p>
    </div>
    <div class="flex items-center gap-2">
        @if(Auth::user()->isSuperAdmin())
        <button onclick="openResetModal()" class="inline-flex items-center gap-1.5 text-xs font-medium px-3 py-1.5 rounded-lg border border-red-200 text-red-600 hover:bg-red-50 transition">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            Reset
        </button>
        @endif
        <a href="{{ route('admin.madrasah-users.import-form') }}"
           class="inline-flex items-center gap-1.5 text-xs font-medium px-3 py-1.5 rounded-lg border border-zinc-200 text-zinc-600 hover:bg-zinc-50 transition">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
            </svg>
            Import Excel
        </a>
        <a href="{{ route('admin.madrasah-users.create') }}"
           class="inline-flex items-center gap-1.5 text-xs font-medium px-3 py-1.5 rounded-lg text-white transition"
           style="background:#18181b">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Madrasah
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
            <p class="text-3xl font-bold text-zinc-900 leading-none">{{ number_format($totalMadrasah) }}</p>
            <div class="w-8 h-8 rounded-full flex items-center justify-center" style="background:#f4f4f5">
                <svg class="w-4 h-4 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 21h18M5 21V7l8-4v18M19 21V11l-6-4m-4 6h.01M9 12h.01M9 15h.01M9 18h.01"/></svg>
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
            <p class="text-3xl font-bold text-zinc-900 leading-none">{{ number_format($totalMadrasah - $totalAktif) }}</p>
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
            <input type="text" name="search" value="{{ request('search') }}" placeholder="NSM, nama madrasah, nama PIC..."
                class="w-full border border-zinc-200 rounded-lg pl-8 pr-3 py-1.5 text-xs focus:ring-1 focus:ring-zinc-300 focus:outline-none">
        </div>
        <select name="jenjang" class="border border-zinc-200 rounded-lg px-3 py-1.5 text-xs focus:ring-1 focus:ring-zinc-300 focus:outline-none bg-white">
            <option value="">Semua Jenjang</option>
            @foreach(['RA','MI','MTs','MA'] as $j)
            <option value="{{ $j }}" {{ request('jenjang') == $j ? 'selected' : '' }}>{{ $j }}</option>
            @endforeach
        </select>
        <select name="status" class="border border-zinc-200 rounded-lg px-3 py-1.5 text-xs focus:ring-1 focus:ring-zinc-300 focus:outline-none bg-white">
            <option value="">Semua Status</option>
            <option value="aktif"    {{ request('status')==='aktif'?'selected':'' }}>Aktif</option>
            <option value="nonaktif" {{ request('status')==='nonaktif'?'selected':'' }}>Non-Aktif</option>
        </select>
        <button type="submit" class="px-3 py-1.5 bg-zinc-900 text-white text-xs font-medium rounded-lg hover:bg-zinc-700 transition">Filter</button>
        @if(request()->hasAny(['search','jenjang','status']))
        <a href="{{ route('admin.madrasah-users.index') }}" class="text-xs text-zinc-400 hover:text-zinc-600 transition">Reset</a>
        @endif
    </form>
</div>

{{-- Tabel --}}
<div class="bg-white rounded-xl border border-zinc-100 overflow-hidden fade-in">
    <table class="w-full">
        <thead>
            <tr class="tbl-head">
                <th class="px-4 py-2.5 text-left text-2xs font-semibold text-zinc-600 uppercase tracking-wider">Madrasah</th>
                <th class="px-4 py-2.5 text-left text-2xs font-semibold text-zinc-600 uppercase tracking-wider hidden lg:table-cell">NSM</th>
                <th class="px-4 py-2.5 text-left text-2xs font-semibold text-zinc-600 uppercase tracking-wider hidden sm:table-cell">PIC</th>
                <th class="px-4 py-2.5 text-center text-2xs font-semibold text-zinc-600 uppercase tracking-wider hidden md:table-cell">Arsip</th>
                <th class="px-4 py-2.5 text-center text-2xs font-semibold text-zinc-600 uppercase tracking-wider hidden sm:table-cell">Password</th>
                <th class="px-4 py-2.5 text-center text-2xs font-semibold text-zinc-600 uppercase tracking-wider">Status</th>
                <th class="px-4 py-2.5 text-right text-2xs font-semibold text-zinc-600 uppercase tracking-wider">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $u)
            <tr class="tbl-row" style="border-bottom:1px solid #fafafa">
                {{-- Madrasah --}}
                <td class="px-4 py-3">
                    <div class="flex items-center gap-2.5">
                        <div class="w-7 h-7 rounded-lg flex items-center justify-center text-white text-xs font-semibold flex-shrink-0"
                             style="background:linear-gradient(135deg,#15803d,#4ade80)">
                            {{ strtoupper(substr($u->madrasah->nama_madrasah ?? 'M',0,1)) }}
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-medium text-zinc-800 truncate max-w-48">{{ $u->madrasah->nama_madrasah ?? '—' }}</p>
                            <p class="text-2xs text-zinc-400">{{ $u->madrasah->jenjang ?? '' }}</p>
                        </div>
                    </div>
                </td>
                {{-- NSM --}}
                <td class="px-4 py-3 hidden lg:table-cell">
                    <code class="text-2xs bg-zinc-100 text-zinc-600 px-1.5 py-0.5 rounded font-mono">{{ $u->nsm }}</code>
                </td>
                {{-- PIC --}}
                <td class="px-4 py-3 hidden sm:table-cell">
                    <p class="text-xs font-medium text-zinc-800">{{ $u->nama_pic }}</p>
                    @if($u->email)<p class="text-2xs text-zinc-400">{{ $u->email }}</p>@endif
                </td>
                {{-- Arsip --}}
                <td class="px-4 py-3 text-center hidden md:table-cell">
                    <a href="{{ route('admin.arsip-madrasah.show', $u->id) }}"
                       class="badge badge-blue hover:bg-blue-100 transition cursor-pointer">
                        {{ $u->arsip_count ?? 0 }}
                    </a>
                </td>
                {{-- Password --}}
                <td class="px-4 py-3 text-center hidden sm:table-cell">
                    @if($u->password_changed)
                        <span class="badge badge-green">Diubah</span>
                    @else
                        <span class="badge badge-yellow">Default</span>
                    @endif
                </td>
                {{-- Status --}}
                <td class="px-4 py-3 text-center">
                    <span class="badge {{ $u->is_active ? 'badge-green' : 'badge-red' }}">{{ $u->is_active ? 'Aktif' : 'Non-Aktif' }}</span>
                </td>
                {{-- Aksi — icon langsung, tanpa dropdown --}}
                <td class="px-4 py-3">
                    <div class="flex items-center justify-end gap-0.5">
                        {{-- Lihat Arsip --}}
                        <a href="{{ route('admin.arsip-madrasah.show', $u->id) }}"
                           class="btn-icon blue" title="Lihat Arsip">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                        </a>
                        {{-- Edit --}}
                        <a href="{{ route('admin.madrasah-users.edit', $u->id) }}"
                           class="btn-icon" title="Edit">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </a>
                        {{-- Toggle Aktif --}}
                        <form method="POST" action="{{ route('admin.madrasah-users.toggle', $u->id) }}" class="inline">
                            @csrf @method('PATCH')
                            <button type="submit" class="btn-icon {{ $u->is_active ? 'danger' : 'success' }}"
                                title="{{ $u->is_active ? 'Non-Aktifkan' : 'Aktifkan' }}">
                                @if($u->is_active)
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                @else
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                @endif
                            </button>
                        </form>
                        {{-- Reset Password --}}
                        <form method="POST" action="{{ route('admin.madrasah-users.reset-password', $u->id) }}"
                              data-confirm="Reset password akun {{ addslashes($u->madrasah->nama_madrasah ?? $u->nsm) }} ke NSM default ({{ $u->nsm }})?" data-confirm-type="arsip" data-confirm-btn="Ya, Reset" class="inline">
                            @csrf @method('PATCH')
                            <button type="submit" class="btn-icon warning" title="Reset Password">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                            </button>
                        </form>
                        {{-- Hapus --}}
                        <form method="POST" action="{{ route('admin.madrasah-users.destroy', $u->id) }}"
                              data-confirm="Hapus akun {{ addslashes($u->madrasah->nama_madrasah ?? $u->nsm) }}?\nAkun yang punya arsip tidak bisa dihapus." data-confirm-btn="Ya, Hapus" class="inline">
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
                    <svg class="w-10 h-10 mx-auto text-zinc-200 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 21h18M5 21V7l8-4v18M19 21V11l-6-4m-4 6h.01M9 12h.01M9 15h.01M9 18h.01"/></svg>
                    <p class="text-xs text-zinc-400">Belum ada akun madrasah</p>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-4 py-3 flex items-center justify-between" style="border-top:1px solid #f4f4f5">
        <p class="text-2xs text-zinc-400">{{ $users->firstItem() ?? 0 }}–{{ $users->lastItem() ?? 0 }} dari {{ number_format($users->total()) }}</p>
        {{ $users->withQueryString()->links() }}
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
