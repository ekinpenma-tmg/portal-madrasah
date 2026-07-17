@extends('layouts.admin')
@section('title', 'Tindakan Cepat')

@section('content')

{{-- Modal Tolak --}}
<div x-data="modalManager()" x-cloak @open-tolak.window="openTolak($event.detail)">
    <div x-show="show" x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         class="fixed inset-0 z-50 flex items-center justify-center p-4"
         style="background:rgba(0,0,0,0.4);backdrop-filter:blur(2px)" @click.self="close()">
        <div x-show="show" x-transition:enter="transition ease-out duration-150"
             x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
             class="bg-white rounded-2xl w-full max-w-sm overflow-hidden" style="box-shadow:0 20px 60px rgba(0,0,0,0.15)">
            <div class="h-0.5 w-full bg-red-500"></div>
            <div class="p-5">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-9 h-9 bg-red-100 rounded-xl flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-zinc-800">Tolak Pengajuan</p>
                        <p class="text-2xs text-zinc-400 font-mono" x-text="kode"></p>
                    </div>
                </div>
                <p class="text-xs text-zinc-500 bg-zinc-50 rounded-lg px-3 py-2.5 mb-3" x-text="'Pengajuan atas nama ' + nama + ' akan ditolak.'"></p>
                <form :action="formAction" method="POST" @submit.prevent="submitTolak($el)">
                    @csrf
                    <textarea name="catatan" id="catatanTolak" rows="3" placeholder="Alasan penolakan (wajib diisi)"
                        :class="errorCatatan ? 'border-red-400 bg-red-50' : 'border-zinc-200'"
                        class="w-full border rounded-lg px-3 py-2 text-xs focus:outline-none focus:ring-1 focus:ring-red-400 resize-none transition"
                        @input="errorCatatan = false"></textarea>
                    <p x-show="errorCatatan" class="text-red-500 text-2xs mt-1">Alasan penolakan wajib diisi.</p>
                    <div class="flex gap-2 mt-3">
                        <button type="button" @click="close()"
                            class="flex-1 px-3 py-2 rounded-lg text-xs font-semibold text-zinc-600 bg-zinc-100 hover:bg-zinc-200 transition">Batal</button>
                        <button type="submit"
                            class="flex-1 px-3 py-2 rounded-lg text-xs font-bold text-white bg-red-600 hover:bg-red-700 transition">Tolak</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Header --}}
<div class="flex items-center justify-between mb-4 fade-in">
    <div>
        <h1 class="text-sm font-semibold text-zinc-900">Tindakan Cepat</h1>
        <p class="text-xs text-zinc-400 mt-0.5">Proses pengajuan langsung tanpa membuka halaman detail</p>
    </div>
    @if($pendingCount > 0)
    <span class="inline-flex items-center gap-1.5 text-xs font-semibold px-3 py-1.5 rounded-lg text-amber-700" style="background:#fef9c3">
        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 flex-shrink-0"></span>
        {{ $pendingCount }} menunggu diproses
    </span>
    @endif
</div>

{{-- Filter --}}
<div class="bg-white rounded-xl border border-zinc-100 px-4 py-3 mb-3 fade-in">
    <form method="GET" class="flex flex-wrap gap-2 items-center">
        <div class="relative flex-1 min-w-48">
            <svg class="absolute left-2.5 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-zinc-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input type="text" name="search" value="{{ request('search') }}"
                placeholder="Kode, nama, NIP, madrasah, token..."
                class="w-full border border-zinc-200 rounded-lg pl-8 pr-3 py-1.5 text-xs focus:ring-1 focus:ring-zinc-300 focus:outline-none">
        </div>
        <select name="jenis" class="border border-zinc-200 rounded-lg px-3 py-1.5 text-xs focus:outline-none bg-white">
            <option value="">Semua Jenis</option>
            @foreach($jenisDokumen as $j)
            <option value="{{ $j->id }}" {{ request('jenis')==$j->id?'selected':'' }}>{{ $j->nama }}</option>
            @endforeach
        </select>
        <button type="submit" class="px-3 py-1.5 bg-zinc-900 text-white text-xs font-medium rounded-lg hover:bg-zinc-700 transition">Cari</button>
        @if(request('search') || request('jenis'))
        <a href="{{ route('admin.tindakan-cepat.index') }}" class="text-xs text-zinc-400 hover:text-zinc-600 transition">Reset</a>
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
                    <th class="px-4 py-2.5 text-left text-2xs font-semibold text-zinc-600 uppercase tracking-wider hidden md:table-cell">Madrasah</th>
                    <th class="px-4 py-2.5 text-center text-2xs font-semibold text-zinc-600 uppercase tracking-wider">Token</th>
                    <th class="px-4 py-2.5 text-center text-2xs font-semibold text-zinc-600 uppercase tracking-wider">Dokumen</th>
                    <th class="px-4 py-2.5 text-center text-2xs font-semibold text-zinc-600 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pengajuan as $p)
                <tr class="tbl-row {{ $p->status !== 'pending' ? 'opacity-50' : '' }}" style="border-bottom:1px solid #fafafa">
                    <td class="px-4 py-3">
                        <code class="text-xs font-mono font-bold text-primary-700">{{ $p->kode_ajuan }}</code>
                    </td>
                    <td class="px-4 py-3">
                        <p class="text-xs font-semibold text-zinc-800 truncate max-w-36">{{ $p->nama_guru }}</p>
                        <p class="text-2xs text-zinc-400 mt-0.5 truncate max-w-36">{{ $p->nip ?: '—' }}</p>
                    </td>
                    <td class="px-4 py-3 hidden md:table-cell">
                        <p class="text-xs text-zinc-600 truncate max-w-40">{{ $p->nama_madrasah }}</p>
                    </td>
                    <td class="px-4 py-3 text-center hidden sm:table-cell">
                        @if($p->token)
                        <code class="text-2xs font-mono bg-primary-50 text-primary-700 px-1.5 py-0.5 rounded">{{ $p->token }}</code>
                        @else
                        <span class="text-zinc-300 text-2xs">—</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-center hidden lg:table-cell">
                        <a href="{{ route('admin.pengajuan.dokumen', $p->id) }}" target="_blank"
                           class="inline-flex items-center gap-1 text-xs text-primary-600 hover:text-primary-800 hover:underline transition">
                            <span class="truncate max-w-28">{{ $p->jenisDokumen->nama }}</span>
                            <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                            </svg>
                        </a>
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex items-center justify-center gap-1.5">
                            <form action="{{ route('admin.tindakan-cepat.terima', $p->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn-icon success" title="Terima">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </button>
                            </form>
                            <button type="button" class="btn-icon danger" title="Tolak"
                                @click="$dispatch('open-tolak', {
                                    kode: '{{ $p->kode_ajuan }}',
                                    nama: '{{ addslashes($p->nama_guru) }}',
                                    formAction: '{{ route('admin.tindakan-cepat.tolak', $p->id) }}'
                                })">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-14 text-center">
                        <svg class="w-10 h-10 mx-auto text-zinc-200 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        <p class="text-xs text-zinc-400">Tidak ada pengajuan ditemukan</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-4 py-3 flex items-center justify-between" style="border-top:1px solid #f4f4f5">
        <p class="text-2xs text-zinc-400">Total: {{ $pengajuan->total() }}</p>
        {{ $pengajuan->links() }}
    </div>
</div>

<script>
function modalManager(){
    return{
        show:false,mode:null,kode:'',nama:'',formAction:'',errorCatatan:false,
        openTolak(d){Object.assign(this,{mode:'tolak',kode:d.kode,nama:d.nama,formAction:d.formAction,errorCatatan:false,show:true});this.$nextTick(()=>document.getElementById('catatanTolak')?.focus());},
        close(){this.show=false;this.mode=null;},
        submitTolak(form){const c=form.querySelector('[name="catatan"]');if(!c.value.trim()){this.errorCatatan=true;c.focus();return;}form.action=this.formAction;form.submit();}
    }
}
</script>
@endsection
