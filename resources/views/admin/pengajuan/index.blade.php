@extends('layouts.admin')
@section('title', 'Pengajuan')

@section('content')

{{-- Modal Konfirmasi --}}
<div x-data="confirmModal()" @open-confirm.window="open($event.detail)" x-cloak>
    <div x-show="show"
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         class="fixed inset-0 z-50 flex items-center justify-center p-4"
         style="background:rgba(0,0,0,0.4);backdrop-filter:blur(2px)"
         @click.self="cancel()">
        <div x-show="show"
             x-transition:enter="transition ease-out duration-150"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             class="bg-white rounded-2xl w-full max-w-sm overflow-hidden"
             style="box-shadow:0 20px 60px rgba(0,0,0,0.15)"
             @click.stop>
            <div class="h-0.5 w-full" :class="accentClass"></div>
            <div class="p-5">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0" :class="iconBgClass">
                        <svg class="w-4 h-4" :class="iconColor" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                x-bind:d="type==='arsip'||type==='bulk'
                                    ? 'M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4'
                                    : 'M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16'"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-zinc-800" x-text="title"></p>
                        <p class="text-2xs text-zinc-400 font-mono" x-text="subtitle"></p>
                    </div>
                </div>
                <p class="text-xs text-zinc-500 bg-zinc-50 rounded-lg px-3 py-2.5 mb-4 leading-relaxed" x-text="message"></p>
                <div class="flex gap-2">
                    <button @click="cancel()"
                        class="flex-1 px-3 py-2 rounded-lg text-xs font-semibold text-zinc-600 bg-zinc-100 hover:bg-zinc-200 transition">
                        Batal
                    </button>
                    <button @click="confirm()"
                        class="flex-1 px-3 py-2 rounded-lg text-xs font-bold text-white transition"
                        :class="btnClass"
                        x-text="btnLabel">
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Header --}}
<div class="flex items-center justify-between mb-4 fade-in">
    <div>
        <h1 class="text-sm font-semibold text-zinc-900">Pengajuan Dokumen</h1>
        <p class="text-xs text-zinc-400 mt-0.5">Data pengajuan yang masuk</p>
    </div>
    <div class="flex items-center gap-2">
        <a href="{{ route('admin.pengajuan.export', request()->query()) }}"
           class="inline-flex items-center gap-1.5 text-xs font-medium px-3 py-1.5 rounded-lg border border-zinc-200 text-zinc-600 hover:bg-zinc-50 transition">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
            </svg>
            Export
        </a>
        @if($trashCount > 0)
        <a href="{{ route('admin.pengajuan.trash') }}"
           class="inline-flex items-center gap-1.5 text-xs font-medium px-3 py-1.5 rounded-lg border border-red-200 text-red-600 hover:bg-red-50 transition">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
            </svg>
            Arsip
            <span class="bg-red-500 text-white text-2xs font-bold px-1.5 py-0.5 rounded-full">{{ $trashCount }}</span>
        </a>
        @endif
    </div>
</div>

{{-- Filter --}}
<div class="bg-white rounded-xl border border-zinc-100 px-4 py-3 mb-3 fade-in">
    <form method="GET" class="flex flex-wrap gap-2 items-center">
        <div class="relative flex-1 min-w-48">
            <svg class="absolute left-2.5 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-zinc-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input type="text" name="search" value="{{ request('search') }}"
                placeholder="Kode, nama, NIP, madrasah..."
                class="w-full border border-zinc-200 rounded-lg pl-8 pr-3 py-1.5 text-xs focus:ring-1 focus:ring-zinc-300 focus:outline-none">
        </div>
        <select name="status" class="border border-zinc-200 rounded-lg px-3 py-1.5 text-xs focus:outline-none bg-white">
            <option value="">Semua Status</option>
            <option value="pending"  {{ request('status')==='pending'?'selected':'' }}>Menunggu</option>
            <option value="diterima" {{ request('status')==='diterima'?'selected':'' }}>Diterima</option>
            <option value="ditolak"  {{ request('status')==='ditolak'?'selected':'' }}>Ditolak</option>
        </select>
        <select name="jenis" class="border border-zinc-200 rounded-lg px-3 py-1.5 text-xs focus:outline-none bg-white">
            <option value="">Semua Jenis</option>
            @foreach($jenisDokumen as $j)
            <option value="{{ $j->id }}" {{ request('jenis')==$j->id?'selected':'' }}>{{ $j->nama }}</option>
            @endforeach
        </select>
        <input type="date" name="dari"   value="{{ request('dari') }}"
               class="border border-zinc-200 rounded-lg px-3 py-1.5 text-xs focus:outline-none">
        <input type="date" name="sampai" value="{{ request('sampai') }}"
               class="border border-zinc-200 rounded-lg px-3 py-1.5 text-xs focus:outline-none">
        <button type="submit" class="px-3 py-1.5 bg-zinc-900 text-white text-xs font-medium rounded-lg hover:bg-zinc-700 transition">Filter</button>
        @if(request()->hasAny(['search','status','jenis','dari','sampai']))
        <a href="{{ route('admin.pengajuan.index') }}" class="text-xs text-zinc-400 hover:text-zinc-600 transition">Reset</a>
        @endif
    </form>
</div>

{{-- Bulk bar --}}
<div id="bulkBar" class="hidden mb-3">
    <div class="flex items-center gap-3 bg-amber-50 border border-amber-200 rounded-xl px-4 py-2.5">
        <span class="text-xs font-semibold text-amber-800"><span id="selectedCount">0</span> dipilih</span>
        <div class="flex-1"></div>
        <button id="btnDeselectAll" class="text-2xs font-medium text-zinc-500 hover:text-zinc-700 transition">Batal</button>
        <form id="bulkDeleteForm" action="{{ route('admin.pengajuan.bulk-delete') }}" method="POST" class="inline">
            @csrf
            <div id="bulkIdsContainer"></div>
            <button type="button" id="btnBulkDelete"
                class="inline-flex items-center gap-1.5 text-xs font-semibold px-3 py-1.5 rounded-lg text-white bg-amber-500 hover:bg-amber-600 transition">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                </svg>
                Arsipkan Dipilih
            </button>
        </form>
    </div>
</div>

{{-- Tabel --}}
<div class="bg-white rounded-xl border border-zinc-100 overflow-hidden fade-in">
    <table class="w-full">
        <thead>
            <tr class="tbl-head">
                <th class="w-8 px-4 py-2.5">
                    <input type="checkbox" id="checkAll" class="w-3.5 h-3.5 rounded accent-zinc-800 cursor-pointer">
                </th>
                <th class="px-4 py-2.5 text-left text-2xs font-semibold text-zinc-600 uppercase tracking-wider">Pemohon</th>
                <th class="px-4 py-2.5 text-left text-2xs font-semibold text-zinc-600 uppercase tracking-wider hidden md:table-cell">Madrasah</th>
                <th class="px-4 py-2.5 text-center text-2xs font-semibold text-zinc-600 uppercase tracking-wider hidden sm:table-cell">Kode</th>
                <th class="px-4 py-2.5 text-left text-2xs font-semibold text-zinc-600 uppercase tracking-wider hidden lg:table-cell">Jenis</th>
                <th class="px-4 py-2.5 text-center text-2xs font-semibold text-zinc-600 uppercase tracking-wider hidden lg:table-cell">Tanggal</th>
                <th class="px-4 py-2.5 text-center text-2xs font-semibold text-zinc-600 uppercase tracking-wider">Status</th>
                <th class="px-4 py-2.5 text-center text-2xs font-semibold text-zinc-600 uppercase tracking-wider">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pengajuan as $p)
            <tr class="tbl-row" style="border-bottom:1px solid #fafafa">
                <td class="px-4 py-3">
                    <input type="checkbox" value="{{ $p->id }}"
                           class="row-checkbox w-3.5 h-3.5 rounded accent-zinc-800 cursor-pointer">
                </td>
                <td class="px-4 py-3">
                    <p class="text-xs font-semibold text-zinc-800 truncate max-w-36">{{ $p->nama_guru }}</p>
                    <p class="text-2xs text-zinc-400 mt-0.5 truncate max-w-36">{{ $p->nip ?: '—' }}</p>
                </td>
                <td class="px-4 py-3 hidden md:table-cell">
                    <p class="text-xs text-zinc-600 truncate max-w-40">{{ $p->nama_madrasah }}</p>
                </td>
                <td class="px-4 py-3 text-center hidden sm:table-cell">
                    <code class="text-2xs font-mono bg-zinc-100 text-zinc-600 px-1.5 py-0.5 rounded">{{ $p->kode_ajuan }}</code>
                    @if($p->token)
                    <br><code class="text-2xs font-mono text-primary-600 bg-primary-50 px-1.5 py-0.5 rounded mt-0.5 inline-block">{{ $p->token }}</code>
                    @endif
                </td>
                <td class="px-4 py-3 hidden lg:table-cell">
                    <span class="badge badge-gray">{{ $p->jenisDokumen->nama }}</span>
                </td>
                <td class="px-4 py-3 text-center hidden lg:table-cell">
                    <span class="text-2xs text-zinc-400">{{ $p->created_at->format('d/m/y') }}</span>
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
                    <div class="flex items-center justify-center gap-1">
                        {{-- Detail --}}
                        <a href="{{ route('admin.pengajuan.show', $p->id) }}" class="btn-icon blue" title="Lihat Detail">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </a>
                        {{-- Arsipkan --}}
                        <form id="form-arsip-{{ $p->id }}" action="{{ route('admin.pengajuan.destroy', $p->id) }}" method="POST" class="inline">
                            @csrf @method('DELETE')
                            <button type="button" class="btn-icon warning" title="Arsipkan"
                                @click="$dispatch('open-confirm', {
                                    type: 'arsip',
                                    title: 'Arsipkan Pengajuan',
                                    subtitle: '{{ $p->kode_ajuan }}',
                                    message: 'Pengajuan {{ addslashes($p->nama_guru) }} akan dipindahkan ke arsip. Data masih bisa dipulihkan.',
                                    btnLabel: 'Arsipkan',
                                    formId: 'form-arsip-{{ $p->id }}'
                                })">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="py-14 text-center">
                    <svg class="w-10 h-10 mx-auto text-zinc-200 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <p class="text-xs text-zinc-400">Tidak ada pengajuan ditemukan</p>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-4 py-3 flex items-center justify-between" style="border-top:1px solid #f4f4f5">
        <p class="text-2xs text-zinc-400">Total: {{ $pengajuan->total() }}</p>
        {{ $pengajuan->links() }}
    </div>
</div>

<script>
const checkAll=document.getElementById('checkAll'),checkboxes=document.querySelectorAll('.row-checkbox'),bulkBar=document.getElementById('bulkBar'),selectedCount=document.getElementById('selectedCount'),bulkIdsContainer=document.getElementById('bulkIdsContainer');
function updateBulkBar(){const c=document.querySelectorAll('.row-checkbox:checked');selectedCount.textContent=c.length;bulkBar.classList.toggle('hidden',c.length===0);checkAll.indeterminate=c.length>0&&c.length<checkboxes.length;checkAll.checked=checkboxes.length>0&&c.length===checkboxes.length;}
checkAll.addEventListener('change',function(){checkboxes.forEach(cb=>cb.checked=this.checked);updateBulkBar();});
checkboxes.forEach(cb=>cb.addEventListener('change',updateBulkBar));
document.getElementById('btnDeselectAll').addEventListener('click',()=>{checkboxes.forEach(cb=>cb.checked=false);checkAll.checked=false;updateBulkBar();});
document.getElementById('btnBulkDelete').addEventListener('click',()=>{
    const checked=document.querySelectorAll('.row-checkbox:checked');
    if(!checked.length)return;
    window.dispatchEvent(new CustomEvent('open-confirm',{detail:{type:'bulk',title:'Arsipkan Data Terpilih',subtitle:`${checked.length} pengajuan dipilih`,message:`${checked.length} pengajuan akan dipindahkan ke arsip. Masih bisa dipulihkan dari menu Arsip.`,btnLabel:'Arsipkan Semua',callback:()=>{bulkIdsContainer.innerHTML='';checked.forEach(cb=>{const inp=document.createElement('input');inp.type='hidden';inp.name='ids[]';inp.value=cb.value;bulkIdsContainer.appendChild(inp);});document.getElementById('bulkDeleteForm').submit();}}}));
});
function confirmModal(){return{show:false,type:'arsip',title:'',subtitle:'',message:'',btnLabel:'',formId:null,_callback:null,open(d){Object.assign(this,{type:d.type||'arsip',title:d.title||'Konfirmasi',subtitle:d.subtitle||'',message:d.message||'',btnLabel:d.btnLabel||'Ya',formId:d.formId||null,_callback:d.callback||null,show:true});},confirm(){this.show=false;this.$nextTick(()=>{if(this._callback)this._callback();else if(this.formId)document.getElementById(this.formId)?.submit();});},cancel(){this.show=false;},get accentClass(){return{arsip:'bg-amber-400',bulk:'bg-amber-400',force:'bg-red-500',restore:'bg-green-500'}[this.type]||'bg-zinc-400';},get iconBgClass(){return{arsip:'bg-amber-100',bulk:'bg-amber-100',force:'bg-red-100',restore:'bg-green-100'}[this.type]||'bg-zinc-100';},get iconColor(){return{arsip:'text-amber-500',bulk:'text-amber-500',force:'text-red-600',restore:'text-green-600'}[this.type]||'text-zinc-400';},get btnClass(){return{arsip:'bg-amber-500 hover:bg-amber-600',bulk:'bg-amber-500 hover:bg-amber-600',force:'bg-red-600 hover:bg-red-700',restore:'bg-green-600 hover:bg-green-700'}[this.type]||'bg-zinc-800';},};}
</script>

@endsection
