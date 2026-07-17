@extends('layouts.admin')
@section('title', 'Arsip — ' . $guru->nama)

@section('content')

{{-- Breadcrumb --}}
<div class="flex items-center gap-2 text-xs text-zinc-400 mb-4 fade-in">
    <a href="{{ route('admin.guru-users.index') }}" class="hover:text-zinc-600 transition">Akun Guru</a>
    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
    </svg>
    <span class="text-zinc-600 font-medium">{{ $guru->nama }}</span>
</div>

{{-- Header profil guru --}}
<div class="flex items-center justify-between mb-4 fade-in">
    <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white font-bold text-sm flex-shrink-0"
             style="background:linear-gradient(135deg,#0369a1,#38bdf8)">
            {{ strtoupper(substr($guru->nama, 0, 1)) }}
        </div>
        <div>
            <div class="flex items-center gap-2">
                <p class="text-sm font-semibold text-zinc-900">{{ $guru->nama }}</p>
                @if($guru->is_active)
                <span class="badge badge-green">Aktif</span>
                @else
                <span class="badge badge-red">Non-Aktif</span>
                @endif
            </div>
            <div class="flex items-center gap-1.5 mt-0.5">
                <code class="text-2xs bg-zinc-100 text-zinc-500 px-1.5 py-0.5 rounded font-mono">{{ $guru->pegid }}</code>
                @if($guru->madrasah)
                <span class="text-2xs text-zinc-400">· {{ $guru->madrasah->nama_madrasah }}</span>
                @endif
            </div>
        </div>
    </div>
    <a href="{{ route('admin.guru-users.edit', $guru->id) }}"
       class="inline-flex items-center gap-1.5 text-xs font-medium px-3 py-1.5 rounded-lg border border-zinc-200 text-zinc-600 hover:bg-zinc-50 transition">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
        </svg>
        Edit Akun
    </a>
</div>

{{-- Stat mini --}}
<div class="grid grid-cols-3 gap-3 mb-4 fade-in">
    <div class="stat-card">
        <p class="stat-val">{{ $totalArsip }}</p>
        <p class="stat-lbl">Total Arsip</p>
    </div>
    <div class="stat-card accent-green">
        <p class="stat-val" style="color:#15803d">{{ $totalVerified }}</p>
        <p class="stat-lbl">Terverifikasi</p>
    </div>
    <div class="stat-card accent-amber">
        <p class="stat-val" style="color:#d97706">{{ $totalArsip - $totalVerified }}</p>
        <p class="stat-lbl">Pending</p>
    </div>
</div>

{{-- Filter Kategori (pill tab) --}}
<div class="flex gap-1.5 flex-wrap mb-3 fade-in">
    <a href="{{ route('admin.arsip-guru.show', $guru->id) }}"
       class="text-2xs font-semibold px-3 py-1.5 rounded-full transition
              {{ !request('kategori_id') ? 'text-white' : 'bg-white border border-zinc-200 text-zinc-500 hover:border-zinc-300' }}"
       style="{{ !request('kategori_id') ? 'background:#18181b' : '' }}">
        Semua
    </a>
    @foreach($kategoriList as $kat)
    <a href="{{ route('admin.arsip-guru.show', [$guru->id, 'kategori_id' => $kat->id]) }}"
       class="text-2xs font-semibold px-3 py-1.5 rounded-full transition
              {{ request('kategori_id') == $kat->id ? 'text-white' : 'bg-white border border-zinc-200 text-zinc-500 hover:border-zinc-300' }}"
       style="{{ request('kategori_id') == $kat->id ? 'background:#18181b' : '' }}">
        {{ $kat->nama }}
    </a>
    @endforeach
</div>

{{-- Tabel Arsip --}}
<div class="bg-white rounded-xl border border-zinc-100 overflow-hidden fade-in">
    <table class="w-full">
        <thead>
            <tr class="tbl-head">
                <th class="px-4 py-2.5 text-left">Dokumen</th>
                <th class="px-4 py-2.5 text-center hidden sm:table-cell w-16">Tahun</th>
                <th class="px-4 py-2.5 text-center w-24">Status</th>
                <th class="px-4 py-2.5 text-left hidden md:table-cell">Catatan Admin</th>
                <th class="px-4 py-2.5 text-center w-24">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($arsip as $item)
            <tr class="tbl-row" style="border-bottom:1px solid #fafafa"
                x-data="{ catatanModal: false, catatan: '{{ addslashes($item->catatan_admin ?? '') }}' }">

                {{-- Dokumen --}}
                <td class="px-4 py-3">
                    <div class="flex items-center gap-2.5">
                        <div class="w-7 h-7 rounded-lg flex items-center justify-center text-white text-2xs font-bold flex-shrink-0"
                             style="background:linear-gradient(135deg,#0ea5e9,#38bdf8)">
                            {{ strtoupper(substr($item->kategori->nama ?? 'A', 0, 1)) }}
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-medium text-zinc-800 truncate max-w-56">{{ $item->judul }}</p>
                            <p class="text-2xs text-zinc-400 mt-0.5">{{ $item->kategori->nama ?? '—' }} · {{ $item->created_at->format('d M Y') }}</p>
                        </div>
                    </div>
                </td>

                {{-- Tahun --}}
                <td class="px-4 py-3 text-center hidden sm:table-cell">
                    <span class="text-2xs text-zinc-400 font-mono">{{ $item->tahun ?? '—' }}</span>
                </td>

                {{-- Status --}}
                <td class="px-4 py-3 text-center">
                    @if($item->is_verified)
                    <span class="badge badge-green">✓ Verified</span>
                    @else
                    <span class="badge badge-yellow">Pending</span>
                    @endif
                </td>

                {{-- Catatan --}}
                <td class="px-4 py-3 hidden md:table-cell">
                    @if($item->catatan_admin)
                    <p class="text-xs text-zinc-500 truncate max-w-xs">{{ $item->catatan_admin }}</p>
                    @else
                    <span class="text-zinc-300 text-2xs">—</span>
                    @endif
                </td>

                {{-- Aksi --}}
                <td class="px-4 py-3">
                    <div class="flex items-center justify-center gap-1">
                        <a href="{{ $item->link_gdrive }}" target="_blank" class="btn-icon blue" title="Buka File">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                            </svg>
                        </a>

                        @if(!$item->is_verified)
                        <form method="POST" action="{{ route('admin.arsip-guru.verify', $item->id) }}">
                            @csrf @method('PATCH')
                            <button type="submit" class="btn-icon success" title="Verifikasi">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            </button>
                        </form>
                        @else
                        <form method="POST" action="{{ route('admin.arsip-guru.unverify', $item->id) }}">
                            @csrf @method('PATCH')
                            <button type="submit" class="btn-icon warning" title="Batalkan Verifikasi">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </form>
                        @endif

                        <button @click="catatanModal = true" class="btn-icon" title="Catatan Admin">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </button>

                        <form method="POST" action="{{ route('admin.arsip-guru.destroy', $item->id) }}"
                              data-confirm="Hapus arsip ini?\n{{ addslashes($item->judul) }}" data-confirm-btn="Ya, Hapus">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn-icon danger" title="Hapus">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </form>
                    </div>

                    {{-- Modal Catatan --}}
                    <div x-show="catatanModal" x-cloak x-transition
                         class="fixed inset-0 z-50 flex items-center justify-center p-4"
                         style="background:rgba(0,0,0,0.4);backdrop-filter:blur(2px)">
                        <div class="bg-white rounded-2xl w-full max-w-sm p-5" style="box-shadow:0 20px 60px rgba(0,0,0,0.2)" @click.stop>
                            <p class="text-sm font-semibold text-zinc-800 mb-0.5">Catatan Admin</p>
                            <p class="text-2xs text-zinc-400 mb-3 truncate">{{ $item->judul }}</p>
                            <form method="POST" action="{{ route('admin.arsip-guru.catatan', $item->id) }}">
                                @csrf @method('PATCH')
                                <textarea name="catatan_admin" x-model="catatan" rows="3"
                                    placeholder="Tulis catatan untuk guru (opsional)..."
                                    class="filter-input w-full resize-none mb-3"></textarea>
                                <div class="flex gap-2">
                                    <button type="submit" class="flex-1 py-2 rounded-lg text-xs font-bold text-white transition" style="background:#18181b">Simpan</button>
                                    <button type="button" @click="catatanModal=false" class="flex-1 py-2 rounded-lg border border-zinc-200 text-xs font-semibold text-zinc-600 hover:bg-zinc-50 transition">Batal</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="py-14 text-center">
                    <svg class="w-10 h-10 mx-auto text-zinc-200 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                    </svg>
                    <p class="text-xs text-zinc-400">
                        Belum ada arsip {{ request('kategori_id') ? 'untuk kategori ini' : 'dari guru ini' }}
                    </p>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-4 py-3 flex items-center justify-between" style="border-top:1px solid #f4f4f5">
        <p class="text-2xs text-zinc-400">{{ $arsip->firstItem()??0 }}–{{ $arsip->lastItem()??0 }} dari {{ $arsip->total() }}</p>
        {{ $arsip->withQueryString()->links() }}
    </div>
</div>

@endsection
