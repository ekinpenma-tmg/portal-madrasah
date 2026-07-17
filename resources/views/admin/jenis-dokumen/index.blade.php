@extends('layouts.admin')
@section('title', 'Jenis Dokumen')

@section('content')

<div class="flex items-center justify-between mb-4 fade-in">
    <div>
        <h1 class="text-sm font-semibold text-zinc-900">Jenis Dokumen</h1>
        <p class="text-xs text-zinc-400 mt-0.5">Kelola jenis dokumen yang dapat diajukan</p>
    </div>
    <button onclick="bukaModalTambah()"
        class="inline-flex items-center gap-1.5 text-xs font-medium px-3 py-1.5 rounded-lg bg-zinc-900 text-white hover:bg-zinc-700 transition">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Tambah Jenis
    </button>
</div>

{{-- Info kolom untuk --}}
<div class="bg-blue-50 border border-blue-100 rounded-lg px-4 py-3 mb-4 fade-in">
    <p class="text-xs font-medium text-blue-800 mb-1">Keterangan kolom "Untuk"</p>
    <div class="flex flex-wrap gap-3 text-2xs text-blue-700">
        <span class="flex items-center gap-1"><span class="badge badge-green">Guru</span> → hanya muncul di form ajuan portal guru</span>
        <span class="flex items-center gap-1"><span class="badge badge-blue">Madrasah</span> → hanya muncul di form ajuan portal madrasah</span>
        <span class="flex items-center gap-1"><span class="badge badge-gray">Semua</span> → muncul di keduanya dan form publik</span>
    </div>
</div>

{{-- Tabel --}}
<div class="bg-white rounded-xl border border-zinc-100 overflow-hidden fade-in">
    <table class="w-full">
        <thead>
            <tr class="tbl-head">
                <th class="px-4 py-2.5 text-left">Nama Dokumen</th>
                <th class="px-4 py-2.5 text-left hidden sm:table-cell">Deskripsi</th>
                <th class="px-4 py-2.5 text-center w-24">Untuk</th>
                <th class="px-4 py-2.5 text-center w-20 hidden md:table-cell">Ajuan</th>
                <th class="px-4 py-2.5 text-center w-20">Status</th>
                <th class="px-4 py-2.5 text-center w-28">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($jenis as $j)
            <tr class="tbl-row" style="border-bottom:1px solid #fafafa">
                <td class="px-4 py-3">
                    <p class="text-xs font-medium text-zinc-800">{{ $j->nama }}</p>
                    @if($j->syarat)<p class="text-2xs text-zinc-400 mt-0.5">Syarat: {{ Str::limit($j->syarat, 40) }}</p>@endif
                </td>
                <td class="px-4 py-3 hidden sm:table-cell">
                    <span class="text-xs text-zinc-500">{{ $j->deskripsi ? Str::limit($j->deskripsi, 50) : '—' }}</span>
                </td>
                <td class="px-4 py-3 text-center">
                    @if(($j->untuk ?? 'semua') === 'guru')
                        <span class="badge badge-green">Guru</span>
                    @elseif(($j->untuk ?? 'semua') === 'madrasah')
                        <span class="badge badge-blue">Madrasah</span>
                    @else
                        <span class="badge badge-gray">Semua</span>
                    @endif
                </td>
                <td class="px-4 py-3 text-center hidden md:table-cell">
                    <span class="text-xs font-medium text-zinc-600">{{ $j->pengajuan_count }}</span>
                </td>
                <td class="px-4 py-3 text-center">
                    <span class="badge {{ $j->aktif ? 'badge-green' : 'badge-gray' }}">
                        {{ $j->aktif ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </td>
                <td class="px-4 py-3 text-center">
                    <div class="flex items-center justify-center gap-1">
                        <button onclick="bukaEdit(
                            {{ $j->id }},
                            '{{ addslashes($j->nama) }}',
                            '{{ addslashes($j->deskripsi ?? '') }}',
                            '{{ addslashes($j->syarat ?? '') }}',
                            '{{ $j->icon ?? 'document' }}',
                            '{{ $j->untuk ?? 'semua' }}'
                        )" class="btn-icon blue" title="Edit">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </button>
                        <form action="{{ route('admin.jenis-dokumen.toggle', $j->id) }}" method="POST" class="inline">
                            @csrf @method('PATCH')
                            <button type="submit" class="btn-icon {{ $j->aktif ? 'warning' : 'success' }}"
                                title="{{ $j->aktif ? 'Nonaktifkan' : 'Aktifkan' }}">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="{{ $j->aktif ? 'M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636' : 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z' }}"/>
                                </svg>
                            </button>
                        </form>
                        <form action="{{ route('admin.jenis-dokumen.destroy', $j->id) }}" method="POST"
                            data-confirm="Hapus jenis dokumen '{{ addslashes($j->nama) }}'?" data-confirm-btn="Ya, Hapus" class="inline">
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
                    <svg class="w-10 h-10 mx-auto text-zinc-200 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <p class="text-xs text-zinc-400">Belum ada jenis dokumen</p>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- ══ MODAL TAMBAH ══ --}}
<div id="modal-tambah" class="fixed inset-0 z-50 hidden items-center justify-center p-4"
     style="background:rgba(0,0,0,0.4);backdrop-filter:blur(2px)">
    <div class="bg-white rounded-2xl w-full max-w-md overflow-hidden"
         style="box-shadow:0 20px 60px rgba(0,0,0,0.15); max-height:90vh; overflow-y:auto">
        <div class="h-0.5 w-full bg-zinc-900 sticky top-0"></div>
        <div class="p-5">
            <div class="flex items-center justify-between mb-4">
                <p class="text-sm font-semibold text-zinc-800">Tambah Jenis Dokumen</p>
                <button onclick="tutupModal('modal-tambah')" class="text-zinc-400 hover:text-zinc-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form action="{{ route('admin.jenis-dokumen.store') }}" method="POST" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-xs font-medium text-zinc-600 mb-1.5">Nama Dokumen <span class="text-red-500">*</span></label>
                    <input type="text" name="nama" required autofocus
                        class="w-full border border-zinc-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:ring-1 focus:ring-zinc-300"
                        placeholder="Contoh: Surat Keterangan Aktif Mengajar">
                </div>
                <div>
                    <label class="block text-xs font-medium text-zinc-600 mb-1.5">Untuk <span class="text-red-500">*</span></label>
                    <div class="grid grid-cols-3 gap-2">
                        @foreach([['guru','Guru','badge-green','border-green-500','bg-green-50'],['madrasah','Madrasah','badge-blue','border-blue-500','bg-blue-50'],['semua','Semua','badge-gray','border-zinc-500','bg-zinc-50']] as [$val,$label,$badge,$border,$bg])
                        <label>
                            <input type="radio" name="untuk" value="{{ $val }}" class="hidden peer" {{ $val == 'semua' ? 'checked' : '' }}>
                            <div class="border border-zinc-200 rounded-lg px-2 py-2.5 text-center cursor-pointer peer-checked:{{ $border }} peer-checked:{{ $bg }} transition">
                                <p class="text-xs font-medium text-zinc-700">{{ $label }}</p>
                            </div>
                        </label>
                        @endforeach
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-zinc-600 mb-1.5">Deskripsi</label>
                    <textarea name="deskripsi" rows="2"
                        class="w-full border border-zinc-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:ring-1 focus:ring-zinc-300 resize-none"
                        placeholder="Penjelasan singkat tentang jenis dokumen ini"></textarea>
                </div>
                <div>
                    <label class="block text-xs font-medium text-zinc-600 mb-1.5">Syarat / Ketentuan</label>
                    <input type="text" name="syarat"
                        class="w-full border border-zinc-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:ring-1 focus:ring-zinc-300"
                        placeholder="Contoh: Format PDF, maks 2MB">
                </div>
                <div>
                    <label class="block text-xs font-medium text-zinc-600 mb-2">Ikon</label>
                    <div class="grid grid-cols-4 gap-2">
                        @foreach($icons as $key => $icon)
                        <label>
                            <input type="radio" name="icon" value="{{ $key }}" class="hidden peer" {{ $key === 'document' ? 'checked' : '' }}>
                            <div class="border border-zinc-200 rounded-lg p-2 text-center cursor-pointer peer-checked:border-zinc-800 peer-checked:bg-zinc-50 transition">
                                <svg class="w-4 h-4 mx-auto text-zinc-500 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="{{ $icon['path'] }}"/>
                                </svg>
                                <p class="text-2xs text-zinc-500">{{ $icon['label'] }}</p>
                            </div>
                        </label>
                        @endforeach
                    </div>
                </div>
                <div class="flex gap-2 pt-2" style="border-top:1px solid #f4f4f5">
                    <button type="submit" class="bg-zinc-900 hover:bg-zinc-700 text-white text-xs font-semibold px-4 py-2 rounded-lg transition">
                        Simpan
                    </button>
                    <button type="button" onclick="tutupModal('modal-tambah')"
                        class="text-xs font-medium text-zinc-500 hover:text-zinc-700 px-4 py-2 rounded-lg hover:bg-zinc-100 transition">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ══ MODAL EDIT ══ --}}
<div id="modal-edit" class="fixed inset-0 z-50 hidden items-center justify-center p-4"
     style="background:rgba(0,0,0,0.4);backdrop-filter:blur(2px)">
    <div class="bg-white rounded-2xl w-full max-w-md overflow-hidden"
         style="box-shadow:0 20px 60px rgba(0,0,0,0.15); max-height:90vh; overflow-y:auto">
        <div class="h-0.5 w-full bg-blue-500 sticky top-0"></div>
        <div class="p-5">
            <div class="flex items-center justify-between mb-4">
                <p class="text-sm font-semibold text-zinc-800">Edit Jenis Dokumen</p>
                <button onclick="tutupModal('modal-edit')" class="text-zinc-400 hover:text-zinc-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form id="form-edit" action="" method="POST" class="space-y-3">
                @csrf @method('PUT')
                <div>
                    <label class="block text-xs font-medium text-zinc-600 mb-1.5">Nama Dokumen <span class="text-red-500">*</span></label>
                    <input type="text" id="edit-nama" name="nama" required
                        class="w-full border border-zinc-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:ring-1 focus:ring-zinc-300">
                </div>
                <div>
                    <label class="block text-xs font-medium text-zinc-600 mb-1.5">Untuk <span class="text-red-500">*</span></label>
                    <div class="grid grid-cols-3 gap-2">
                        @foreach([['guru','Guru','border-green-500','bg-green-50'],['madrasah','Madrasah','border-blue-500','bg-blue-50'],['semua','Semua','border-zinc-500','bg-zinc-50']] as [$val,$label,$border,$bg])
                        <label>
                            <input type="radio" id="edit-untuk-{{ $val }}" name="untuk" value="{{ $val }}" class="hidden peer">
                            <div class="border border-zinc-200 rounded-lg px-2 py-2.5 text-center cursor-pointer peer-checked:{{ $border }} peer-checked:{{ $bg }} transition">
                                <p class="text-xs font-medium text-zinc-700">{{ $label }}</p>
                            </div>
                        </label>
                        @endforeach
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-zinc-600 mb-1.5">Deskripsi</label>
                    <textarea id="edit-deskripsi" name="deskripsi" rows="2"
                        class="w-full border border-zinc-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:ring-1 focus:ring-zinc-300 resize-none"></textarea>
                </div>
                <div>
                    <label class="block text-xs font-medium text-zinc-600 mb-1.5">Syarat / Ketentuan</label>
                    <input type="text" id="edit-syarat" name="syarat"
                        class="w-full border border-zinc-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:ring-1 focus:ring-zinc-300">
                </div>
                <div>
                    <label class="block text-xs font-medium text-zinc-600 mb-2">Ikon</label>
                    <div class="grid grid-cols-4 gap-2" id="edit-icons">
                        @foreach($icons as $key => $icon)
                        <label>
                            <input type="radio" id="edit-icon-{{ $key }}" name="icon" value="{{ $key }}" class="hidden peer">
                            <div class="border border-zinc-200 rounded-lg p-2 text-center cursor-pointer peer-checked:border-zinc-800 peer-checked:bg-zinc-50 transition">
                                <svg class="w-4 h-4 mx-auto text-zinc-500 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="{{ $icon['path'] }}"/>
                                </svg>
                                <p class="text-2xs text-zinc-500">{{ $icon['label'] }}</p>
                            </div>
                        </label>
                        @endforeach
                    </div>
                </div>
                <div class="flex gap-2 pt-2" style="border-top:1px solid #f4f4f5">
                    <button type="submit" class="inline-flex items-center gap-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold px-4 py-2 rounded-lg transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Simpan Perubahan
                    </button>
                    <button type="button" onclick="tutupModal('modal-edit')"
                        class="text-xs font-medium text-zinc-500 hover:text-zinc-700 px-4 py-2 rounded-lg hover:bg-zinc-100 transition">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function tutupModal(id) {
    const m = document.getElementById(id);
    m.classList.add('hidden');
    m.classList.remove('flex');
}

function bukaModalTambah() {
    const m = document.getElementById('modal-tambah');
    m.classList.remove('hidden');
    m.classList.add('flex');
}

['modal-tambah', 'modal-edit'].forEach(id => {
    document.getElementById(id).addEventListener('click', function(e) {
        if (e.target === this) tutupModal(id);
    });
});

document.addEventListener('keydown', e => {
    if (e.key === 'Escape') { tutupModal('modal-tambah'); tutupModal('modal-edit'); }
});

function bukaEdit(id, nama, deskripsi, syarat, icon, untuk) {
    document.getElementById('edit-nama').value      = nama;
    document.getElementById('edit-deskripsi').value = deskripsi;
    document.getElementById('edit-syarat').value    = syarat;

    // Set radio untuk
    ['guru','madrasah','semua'].forEach(v => {
        document.getElementById('edit-untuk-' + v).checked = (v === untuk);
    });

    // Set radio icon
    const iconEl = document.getElementById('edit-icon-' + icon);
    if (iconEl) iconEl.checked = true;
    else document.getElementById('edit-icon-document').checked = true;

    document.getElementById('form-edit').action = '/admin/jenis-dokumen/' + id;

    const m = document.getElementById('modal-edit');
    m.classList.remove('hidden');
    m.classList.add('flex');
    document.getElementById('edit-nama').focus();
}
</script>
@endpush
@endsection