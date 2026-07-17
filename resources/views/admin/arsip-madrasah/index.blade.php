@extends('layouts.admin')
@section('title', 'Arsip Madrasah')
@section('content')

{{-- Header --}}
<div class="flex items-center justify-between mb-5 fade-in">
    <div>
        <h1 class="text-base font-semibold text-zinc-900">Arsip Madrasah</h1>
        <p class="text-xs text-zinc-400 mt-0.5">Kelola dan verifikasi arsip digital dari seluruh madrasah</p>
    </div>
    <div class="flex items-center gap-2">
        <a href="{{ route('admin.arsip-madrasah.export', request()->query()) }}"
           class="inline-flex items-center gap-1.5 text-xs font-medium px-3 py-1.5 rounded-lg border border-zinc-200 text-zinc-600 hover:bg-zinc-50 transition">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
            </svg>
            Export Excel
        </a>
    </div>
</div>

{{-- Stat Cards --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-5 fade-in">
    <div class="bg-white rounded-xl border border-zinc-100 px-4 py-4">
        <p class="text-2xs font-semibold text-zinc-400 uppercase tracking-wider mb-2">Total Arsip</p>
        <div class="flex items-end justify-between">
            <p class="text-3xl font-bold text-zinc-900 leading-none">{{ number_format($totalArsip) }}</p>
            <div class="w-8 h-8 rounded-full flex items-center justify-center" style="background:#f4f4f5">
                <svg class="w-4 h-4 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-xl border border-zinc-100 px-4 py-4">
        <p class="text-2xs font-semibold text-zinc-400 uppercase tracking-wider mb-2">Terverifikasi</p>
        <div class="flex items-end justify-between">
            <p class="text-3xl font-bold text-zinc-900 leading-none">{{ number_format($totalVerified) }}</p>
            <div class="w-8 h-8 rounded-full flex items-center justify-center" style="background:#dcfce7">
                <svg class="w-4 h-4 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-xl border border-zinc-100 px-4 py-4">
        <p class="text-2xs font-semibold text-zinc-400 uppercase tracking-wider mb-2">Pending</p>
        <div class="flex items-end justify-between">
            <p class="text-3xl font-bold text-zinc-900 leading-none">{{ number_format($totalPending) }}</p>
            <div class="w-8 h-8 rounded-full flex items-center justify-center" style="background:#fef9c3">
                <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-xl border border-zinc-100 px-4 py-4">
        <p class="text-2xs font-semibold text-zinc-400 uppercase tracking-wider mb-2">Madrasah Upload</p>
        <div class="flex items-end justify-between">
            <p class="text-3xl font-bold text-zinc-900 leading-none">{{ number_format($totalMadrasah) }}</p>
            <div class="w-8 h-8 rounded-full flex items-center justify-center" style="background:#ede9fe">
                <svg class="w-4 h-4 text-violet-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
        </div>
    </div>
</div>

{{-- Filter --}}
<div class="bg-white rounded-xl border border-zinc-100 px-4 py-3 mb-4 fade-in">
    <form method="GET" class="flex flex-wrap gap-2 items-center">
        <div class="relative flex-1 min-w-44">
            <svg class="absolute left-2.5 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-zinc-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Judul, NSM, nama madrasah..."
                class="w-full border border-zinc-200 rounded-lg pl-8 pr-3 py-1.5 text-xs focus:ring-1 focus:ring-zinc-300 focus:outline-none">
        </div>
        <select name="kategori_id" class="border border-zinc-200 rounded-lg px-3 py-1.5 text-xs focus:ring-1 focus:ring-zinc-300 focus:outline-none bg-white w-40">
            <option value="">Semua Kategori</option>
            @foreach($kategoriList as $k)
            <option value="{{ $k->id }}" {{ request('kategori_id') == $k->id ? 'selected' : '' }}>{{ $k->nama }}</option>
            @endforeach
        </select>
        <select name="verified" class="border border-zinc-200 rounded-lg px-3 py-1.5 text-xs focus:ring-1 focus:ring-zinc-300 focus:outline-none bg-white">
            <option value="">Semua Status</option>
            <option value="1" {{ request('verified') === '1' ? 'selected' : '' }}>Terverifikasi</option>
            <option value="0" {{ request('verified') === '0' ? 'selected' : '' }}>Belum Verifikasi</option>
        </select>
        <select name="tahun" class="border border-zinc-200 rounded-lg px-3 py-1.5 text-xs focus:ring-1 focus:ring-zinc-300 focus:outline-none bg-white w-24">
            <option value="">Semua Tahun</option>
            @foreach($tahunList as $t)
            <option value="{{ $t }}" {{ request('tahun') == $t ? 'selected' : '' }}>{{ $t }}</option>
            @endforeach
        </select>
        <button type="submit" class="px-3 py-1.5 bg-zinc-900 text-white text-xs font-medium rounded-lg hover:bg-zinc-700 transition">Filter</button>
        @if(request()->hasAny(['search','kategori_id','verified','tahun']))
        <a href="{{ route('admin.arsip-madrasah.index') }}" class="text-xs text-zinc-400 hover:text-zinc-600 transition">Reset</a>
        @endif
    </form>
</div>

{{-- Tabel dengan Bulk Verify --}}
<div class="bg-white rounded-xl border border-zinc-100 overflow-hidden fade-in"
     x-data="{ selected: [], allChecked: false }">

    {{-- Bulk action bar --}}
    <div x-show="selected.length > 0" x-cloak x-transition
         class="bg-primary-50 px-4 py-2.5 flex items-center justify-between gap-3" style="border-bottom:1px solid #dcfce7">
        <p class="text-xs font-semibold text-primary-800">
            <span x-text="selected.length"></span> arsip dipilih
        </p>
        <form method="POST" action="{{ route('admin.arsip-madrasah.bulk-verify') }}"
              @submit.prevent="if(selected.length) { $el.submit() }"
              id="form-bulk">
            @csrf
            <template x-for="id in selected" :key="id">
                <input type="hidden" name="ids[]" :value="id">
            </template>
            <button type="submit"
                class="inline-flex items-center gap-1.5 bg-zinc-900 text-white text-xs font-medium px-3 py-1.5 rounded-lg hover:bg-zinc-700 transition">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                </svg>
                Verifikasi Semua yang Dipilih
            </button>
        </form>
    </div>

    <table class="w-full text-sm">
        <thead>
            <tr style="border-bottom:1px solid #f4f4f5">
                <th class="px-4 py-2.5 w-10">
                    <input type="checkbox" class="accent-primary-600 w-3.5 h-3.5 rounded"
                        @change="allChecked = $event.target.checked;
                                 selected = allChecked
                                    ? Array.from(document.querySelectorAll('.row-check')).map(el => el.value)
                                    : []">
                </th>
                <th class="px-4 py-2.5 text-left text-2xs font-semibold text-zinc-600 uppercase tracking-wider">Dokumen</th>
                <th class="px-4 py-2.5 text-left text-2xs font-semibold text-zinc-600 uppercase tracking-wider hidden md:table-cell">Madrasah</th>
                <th class="px-4 py-2.5 text-center text-2xs font-semibold text-zinc-600 uppercase tracking-wider hidden sm:table-cell">Tahun</th>
                <th class="px-4 py-2.5 text-center text-2xs font-semibold text-zinc-600 uppercase tracking-wider">Status</th>
                <th class="px-4 py-2.5 text-center text-2xs font-semibold text-zinc-600 uppercase tracking-wider">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($arsip as $a)
            <tr class="tbl-row" style="border-bottom:1px solid #fafafa" x-bind:class="selected.includes('{{ $a->id }}') ? 'bg-primary-50/40' : ''">

                {{-- Checkbox --}}
                <td class="px-4 py-3">
                    <input type="checkbox" class="row-check accent-primary-600 w-3.5 h-3.5 rounded"
                        value="{{ $a->id }}"
                        @change="selected.includes('{{ $a->id }}')
                            ? selected = selected.filter(i => i !== '{{ $a->id }}')
                            : selected.push('{{ $a->id }}')">
                </td>

                {{-- Dokumen --}}
                <td class="px-4 py-3">
                    <div class="flex items-center gap-2.5">
                        <div class="w-7 h-7 rounded-lg flex items-center justify-center text-white text-xs font-semibold flex-shrink-0"
                             style="background:linear-gradient(135deg,#15803d,#4ade80)">
                            {{ strtoupper(substr($a->kategori->nama ?? 'A', 0, 1)) }}
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-medium text-zinc-800 truncate max-w-48">{{ $a->judul }}</p>
                            <p class="text-2xs text-zinc-400">{{ $a->kategori->nama ?? '—' }}</p>
                        </div>
                    </div>
                </td>

                {{-- Madrasah --}}
                <td class="px-4 py-3 hidden md:table-cell">
                    <p class="text-xs font-medium text-zinc-800">{{ $a->madrasahUser->madrasah->nama_madrasah ?? '—' }}</p>
                    <code class="text-2xs bg-zinc-100 text-zinc-600 px-1.5 py-0.5 rounded font-mono">{{ $a->madrasahUser->nsm ?? '' }}</code>
                </td>

                {{-- Tahun --}}
                <td class="px-4 py-3 text-center hidden sm:table-cell">
                    <span class="text-xs text-zinc-500 font-mono">{{ $a->tahun ?? '—' }}</span>
                </td>

                {{-- Status + catatan --}}
                <td class="px-4 py-3 text-center">
                    @if($a->is_verified)
                    <span class="badge badge-green">Verified</span>
                    @else
                    <span class="badge badge-yellow">Pending</span>
                    @endif
                    @if($a->catatan_admin)
                    <p class="text-2xs text-zinc-400 mt-1 max-w-24 truncate mx-auto" title="{{ $a->catatan_admin }}">
                        {{ $a->catatan_admin }}
                    </p>
                    @endif
                </td>

                {{-- Aksi — icon langsung, tanpa dropdown --}}
                <td class="px-4 py-3" x-data="{ catatanModal: false, catatan: '{{ addslashes($a->catatan_admin ?? '') }}' }">
                    <div class="flex items-center justify-center gap-0.5">
                        {{-- Buka GDrive --}}
                        <a href="{{ $a->link_gdrive }}" target="_blank" class="btn-icon blue" title="Buka File">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        </a>
                        {{-- Detail Madrasah --}}
                        <a href="{{ route('admin.arsip-madrasah.show', $a->madrasah_user_id) }}" class="btn-icon" title="Semua Arsip Madrasah Ini">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </a>
                        {{-- Verifikasi / Batalkan --}}
                        @if(!$a->is_verified)
                        <form action="{{ route('admin.arsip-madrasah.verify', $a->id) }}" method="POST" class="inline">
                            @csrf @method('PATCH')
                            <button type="submit" class="btn-icon success" title="Tandai Terverifikasi">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            </button>
                        </form>
                        @else
                        <form action="{{ route('admin.arsip-madrasah.unverify', $a->id) }}" method="POST" class="inline">
                            @csrf @method('PATCH')
                            <button type="submit" class="btn-icon warning" title="Batalkan Verifikasi">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </button>
                        </form>
                        @endif
                        {{-- Catatan --}}
                        <button type="button" @click="catatanModal = true" class="btn-icon" title="Tambah Catatan">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </button>
                        {{-- Hapus --}}
                        <form action="{{ route('admin.arsip-madrasah.destroy', $a->id) }}" method="POST" data-confirm="Hapus arsip ini?\n\n{{ addslashes($a->judul) }}" data-confirm-btn="Ya, Hapus" class="inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn-icon danger" title="Hapus">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </form>
                    </div>

                    {{-- Modal Catatan --}}
                    <div x-show="catatanModal" x-cloak
                         class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40 px-4"
                         x-transition>
                        <div class="bg-white rounded-xl w-full max-w-sm p-5" style="box-shadow:0 8px 24px rgba(0,0,0,0.15)" @click.stop>
                            <h3 class="text-xs font-semibold text-zinc-900 mb-1">Catatan Admin</h3>
                            <p class="text-2xs text-zinc-400 mb-3">Untuk arsip: <strong>{{ $a->judul }}</strong></p>
                            <form method="POST" action="{{ route('admin.arsip-madrasah.catatan', $a->id) }}">
                                @csrf @method('PATCH')
                                <textarea name="catatan_admin" x-model="catatan" rows="3"
                                    placeholder="Tulis catatan untuk madrasah (opsional)..."
                                    class="w-full border border-zinc-200 rounded-lg px-3 py-2 text-xs focus:ring-1 focus:ring-zinc-300 focus:outline-none resize-none mb-3"></textarea>
                                <div class="flex gap-2">
                                    <button type="submit"
                                        class="text-white text-xs font-medium px-4 py-1.5 rounded-lg transition"
                                        style="background:#18181b">
                                        Simpan Catatan
                                    </button>
                                    <button type="button" @click="catatanModal = false"
                                        class="bg-zinc-100 hover:bg-zinc-200 text-zinc-600 text-xs font-medium px-3 py-1.5 rounded-lg transition">
                                        Batal
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="py-14 text-center">
                    <svg class="w-10 h-10 mx-auto text-zinc-200 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                    </svg>
                    <p class="text-xs text-zinc-400">Belum ada arsip madrasah</p>
                    <p class="text-2xs text-zinc-300 mt-1">Coba ubah filter atau tunggu madrasah mengunggah arsip</p>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Pagination --}}
    <div class="px-4 py-3 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2" style="border-top:1px solid #f4f4f5">
        <p class="text-2xs text-zinc-400">
            {{ $arsip->firstItem() ?? 0 }}–{{ $arsip->lastItem() ?? 0 }} dari {{ number_format($arsip->total()) }} arsip
        </p>
        {{ $arsip->withQueryString()->links() }}
    </div>
</div>

@endsection
