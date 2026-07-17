@extends('layouts.admin')
@section('title', 'Kategori Arsip')

@section('content')

<div class="flex items-center justify-between mb-4 fade-in">
    <div>
        <h1 class="text-sm font-semibold text-zinc-900">Kategori Arsip</h1>
        <p class="text-xs text-zinc-400 mt-0.5">Kelola kategori dokumen untuk arsip guru dan madrasah</p>
    </div>
    <button onclick="document.getElementById('modal-tambah').classList.remove('hidden'); document.getElementById('modal-tambah').classList.add('flex')"
        class="inline-flex items-center gap-1.5 text-xs font-medium px-3 py-1.5 rounded-lg bg-zinc-900 text-white hover:bg-zinc-700 transition">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Tambah Kategori
    </button>
</div>

{{-- Info kolom untuk --}}
<div class="bg-blue-50 border border-blue-100 rounded-lg px-4 py-3 mb-4 fade-in">
    <p class="text-xs font-medium text-blue-800 mb-1">Keterangan kolom "Untuk"</p>
    <div class="flex flex-wrap gap-3 text-2xs text-blue-700">
        <span class="flex items-center gap-1"><span class="badge badge-green">Guru</span> → hanya tampil di portal guru</span>
        <span class="flex items-center gap-1"><span class="badge badge-blue">Madrasah</span> → hanya tampil di portal madrasah</span>
        <span class="flex items-center gap-1"><span class="badge badge-gray">Semua</span> → tampil di keduanya</span>
    </div>
</div>

{{-- Tabel --}}
<div class="bg-white rounded-xl border border-zinc-100 overflow-hidden fade-in">
    <table class="w-full">
        <thead>
            <tr class="tbl-head">
                <th class="px-4 py-2.5 text-center text-2xs font-semibold text-zinc-600 uppercase tracking-wider">No</th>
                <th class="px-4 py-2.5 text-left text-2xs font-semibold text-zinc-600 uppercase tracking-wider">Nama Kategori</th>
                <th class="px-4 py-2.5 text-left text-2xs font-semibold text-zinc-600 uppercase tracking-wider hidden sm:table-cell">Deskripsi</th>
                <th class="px-4 py-2.5 text-center text-2xs font-semibold text-zinc-600 uppercase tracking-wider">Untuk</th>
                <th class="px-4 py-2.5 text-center text-2xs font-semibold text-zinc-600 uppercase tracking-wider">Status</th>
                <th class="px-4 py-2.5 text-center text-2xs font-semibold text-zinc-600 uppercase tracking-wider">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($kategori as $k)
            <tr class="tbl-row" style="border-bottom:1px solid #fafafa">
                <td class="px-4 py-3 text-center">
                    <span class="text-xs text-zinc-400">{{ $k->urutan ?: '—' }}</span>
                </td>
                <td class="px-4 py-3">
                    <p class="text-xs font-medium text-zinc-800">{{ $k->nama }}</p>
                </td>
                <td class="px-4 py-3 hidden sm:table-cell">
                    <span class="text-xs text-zinc-500">{{ $k->deskripsi ?: '—' }}</span>
                </td>
                <td class="px-4 py-3 text-center">
                    @if($k->untuk === 'guru')
                        <span class="badge badge-green">Guru</span>
                    @elseif($k->untuk === 'madrasah')
                        <span class="badge badge-blue">Madrasah</span>
                    @else
                        <span class="badge badge-gray">Semua</span>
                    @endif
                </td>
                <td class="px-4 py-3 text-center">
                    <span class="badge {{ $k->aktif ? 'badge-green' : 'badge-gray' }}">
                        {{ $k->aktif ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </td>
                <td class="px-4 py-3 text-center">
                    <div class="flex items-center justify-center gap-1">
                        {{-- Edit --}}
                        <button onclick="bukaEdit({{ $k->id }}, '{{ addslashes($k->nama) }}', '{{ addslashes($k->deskripsi ?? '') }}', {{ $k->urutan }}, '{{ $k->untuk }}')"
                            class="btn-icon blue" title="Edit">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </button>
                        {{-- Toggle aktif --}}
                        <form action="{{ route('admin.kategori-arsip.toggle', $k->id) }}" method="POST" class="inline">
                            @csrf @method('PATCH')
                            <button type="submit" class="btn-icon {{ $k->aktif ? 'warning' : 'success' }}"
                                title="{{ $k->aktif ? 'Nonaktifkan' : 'Aktifkan' }}">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="{{ $k->aktif ? 'M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636' : 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z' }}"/>
                                </svg>
                            </button>
                        </form>
                        {{-- Hapus --}}
                        <form action="{{ route('admin.kategori-arsip.destroy', $k->id) }}" method="POST"
                            data-confirm="Hapus kategori '{{ addslashes($k->nama) }}'?" data-confirm-btn="Ya, Hapus" class="inline">
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
                <td colspan="6" class="py-14 text-center">
                    <svg class="w-10 h-10 mx-auto text-zinc-200 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                    </svg>
                    <p class="text-xs text-zinc-400">Belum ada kategori arsip</p>
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
         style="box-shadow:0 20px 60px rgba(0,0,0,0.15)">
        <div class="h-0.5 w-full bg-zinc-900"></div>
        <div class="p-5">
            <div class="flex items-center justify-between mb-4">
                <p class="text-sm font-semibold text-zinc-800">Tambah Kategori Arsip</p>
                <button onclick="tutupModal('modal-tambah')" class="text-zinc-400 hover:text-zinc-600 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form action="{{ route('admin.kategori-arsip.store') }}" method="POST" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-xs font-medium text-zinc-600 mb-1.5">Nama Kategori <span class="text-red-500">*</span></label>
                    <input type="text" name="nama" required autofocus
                        class="w-full border border-zinc-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:ring-1 focus:ring-zinc-300"
                        placeholder="Contoh: Berkas TPG, Ijazah, Akreditasi">
                    @error('nama')<p class="text-red-500 text-2xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-zinc-600 mb-1.5">Deskripsi <span class="text-zinc-400 font-normal">(opsional)</span></label>
                    <input type="text" name="deskripsi"
                        class="w-full border border-zinc-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:ring-1 focus:ring-zinc-300"
                        placeholder="Keterangan singkat tentang kategori ini">
                </div>
                <div>
                    <label class="block text-xs font-medium text-zinc-600 mb-1.5">Untuk <span class="text-red-500">*</span></label>
                    <div class="grid grid-cols-3 gap-2">
                        <label class="untuk-option">
                            <input type="radio" name="untuk" value="guru" class="hidden peer" required>
                            <div class="border border-zinc-200 rounded-lg px-3 py-2.5 text-center cursor-pointer peer-checked:border-green-500 peer-checked:bg-green-50 transition">
                                <p class="text-xs font-medium text-zinc-700 peer-checked:text-green-700">Guru</p>
                                <p class="text-2xs text-zinc-400 mt-0.5">Portal guru</p>
                            </div>
                        </label>
                        <label class="untuk-option">
                            <input type="radio" name="untuk" value="madrasah" class="hidden peer">
                            <div class="border border-zinc-200 rounded-lg px-3 py-2.5 text-center cursor-pointer peer-checked:border-blue-500 peer-checked:bg-blue-50 transition">
                                <p class="text-xs font-medium text-zinc-700">Madrasah</p>
                                <p class="text-2xs text-zinc-400 mt-0.5">Portal madrasah</p>
                            </div>
                        </label>
                        <label class="untuk-option">
                            <input type="radio" name="untuk" value="semua" class="hidden peer">
                            <div class="border border-zinc-200 rounded-lg px-3 py-2.5 text-center cursor-pointer peer-checked:border-zinc-500 peer-checked:bg-zinc-50 transition">
                                <p class="text-xs font-medium text-zinc-700">Semua</p>
                                <p class="text-2xs text-zinc-400 mt-0.5">Keduanya</p>
                            </div>
                        </label>
                    </div>
                    @error('untuk')<p class="text-red-500 text-2xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-zinc-600 mb-1.5">Urutan <span class="text-zinc-400 font-normal">(opsional)</span></label>
                    <input type="number" name="urutan" value="0" min="0"
                        class="w-24 border border-zinc-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:ring-1 focus:ring-zinc-300">
                    <p class="text-2xs text-zinc-400 mt-1">Angka kecil tampil lebih dulu</p>
                </div>
                <div class="flex gap-2 pt-2" style="border-top:1px solid #f4f4f5">
                    <button type="submit" class="bg-zinc-900 hover:bg-zinc-700 text-white text-xs font-semibold px-4 py-2 rounded-lg transition">
                        Simpan Kategori
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
         style="box-shadow:0 20px 60px rgba(0,0,0,0.15)">
        <div class="h-0.5 w-full bg-blue-500"></div>
        <div class="p-5">
            <div class="flex items-center justify-between mb-4">
                <p class="text-sm font-semibold text-zinc-800">Edit Kategori Arsip</p>
                <button onclick="tutupModal('modal-edit')" class="text-zinc-400 hover:text-zinc-600 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form id="form-edit" action="" method="POST" class="space-y-3">
                @csrf @method('PUT')
                <div>
                    <label class="block text-xs font-medium text-zinc-600 mb-1.5">Nama Kategori <span class="text-red-500">*</span></label>
                    <input type="text" id="edit-nama" name="nama" required
                        class="w-full border border-zinc-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:ring-1 focus:ring-zinc-300">
                </div>
                <div>
                    <label class="block text-xs font-medium text-zinc-600 mb-1.5">Deskripsi <span class="text-zinc-400 font-normal">(opsional)</span></label>
                    <input type="text" id="edit-deskripsi" name="deskripsi"
                        class="w-full border border-zinc-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:ring-1 focus:ring-zinc-300">
                </div>
                <div>
                    <label class="block text-xs font-medium text-zinc-600 mb-1.5">Untuk <span class="text-red-500">*</span></label>
                    <div class="grid grid-cols-3 gap-2">
                        <label>
                            <input type="radio" id="edit-untuk-guru" name="untuk" value="guru" class="hidden peer" required>
                            <div class="border border-zinc-200 rounded-lg px-3 py-2.5 text-center cursor-pointer peer-checked:border-green-500 peer-checked:bg-green-50 transition">
                                <p class="text-xs font-medium text-zinc-700">Guru</p>
                                <p class="text-2xs text-zinc-400 mt-0.5">Portal guru</p>
                            </div>
                        </label>
                        <label>
                            <input type="radio" id="edit-untuk-madrasah" name="untuk" value="madrasah" class="hidden peer">
                            <div class="border border-zinc-200 rounded-lg px-3 py-2.5 text-center cursor-pointer peer-checked:border-blue-500 peer-checked:bg-blue-50 transition">
                                <p class="text-xs font-medium text-zinc-700">Madrasah</p>
                                <p class="text-2xs text-zinc-400 mt-0.5">Portal madrasah</p>
                            </div>
                        </label>
                        <label>
                            <input type="radio" id="edit-untuk-semua" name="untuk" value="semua" class="hidden peer">
                            <div class="border border-zinc-200 rounded-lg px-3 py-2.5 text-center cursor-pointer peer-checked:border-zinc-500 peer-checked:bg-zinc-50 transition">
                                <p class="text-xs font-medium text-zinc-700">Semua</p>
                                <p class="text-2xs text-zinc-400 mt-0.5">Keduanya</p>
                            </div>
                        </label>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-zinc-600 mb-1.5">Urutan</label>
                    <input type="number" id="edit-urutan" name="urutan" min="0"
                        class="w-24 border border-zinc-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:ring-1 focus:ring-zinc-300">
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

// Tutup modal saat klik backdrop
['modal-tambah', 'modal-edit'].forEach(id => {
    document.getElementById(id).addEventListener('click', function(e) {
        if (e.target === this) tutupModal(id);
    });
});

// Tutup modal dengan ESC
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        tutupModal('modal-tambah');
        tutupModal('modal-edit');
    }
});

function bukaEdit(id, nama, deskripsi, urutan, untuk) {
    // Isi field
    document.getElementById('edit-nama').value      = nama;
    document.getElementById('edit-deskripsi').value = deskripsi;
    document.getElementById('edit-urutan').value    = urutan;

    // Set radio untuk
    document.getElementById('edit-untuk-guru').checked     = (untuk === 'guru');
    document.getElementById('edit-untuk-madrasah').checked = (untuk === 'madrasah');
    document.getElementById('edit-untuk-semua').checked    = (untuk === 'semua');

    // Set action form
    document.getElementById('form-edit').action = '/admin/kategori-arsip/' + id;

    // Buka modal
    const m = document.getElementById('modal-edit');
    m.classList.remove('hidden');
    m.classList.add('flex');
    document.getElementById('edit-nama').focus();
}

// Buka modal tambah otomatis jika ada validation error dari store
@if($errors->any() && old('_method') === null && old('nama'))
    document.getElementById('modal-tambah').classList.remove('hidden');
    document.getElementById('modal-tambah').classList.add('flex');
@endif
</script>
@endpush
@endsection
