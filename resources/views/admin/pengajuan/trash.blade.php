@extends('layouts.admin')
@section('title', 'Arsip Pengajuan')
@section('content')

    {{-- ══ CUSTOM CONFIRM MODAL (sama persis dengan index) ══ --}}
    <div x-data="confirmModal()" @open-confirm.window="open($event.detail)" x-cloak>
        <div x-show="show" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 flex items-center justify-center p-4"
            style="background:rgba(0,0,0,0.45);backdrop-filter:blur(3px);" @click="cancel()">

            <div x-show="show" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="bg-white rounded-2xl shadow-2xl w-full max-w-sm overflow-hidden" @click.stop>

                <div class="h-1 w-full" :class="accentClass"></div>

                <div class="p-6">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-12 h-12 rounded-2xl flex items-center justify-center flex-shrink-0"
                            :class="iconBgClass">
                            <svg x-show="type==='arsip'" class="w-6 h-6" :class="iconColor" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                            </svg>
                            <svg x-show="type==='force'" class="w-6 h-6" :class="iconColor" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            <svg x-show="type==='restore'" class="w-6 h-6" :class="iconColor" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            <svg x-show="type==='bulk'" class="w-6 h-6" :class="iconColor" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-extrabold text-gray-800 text-base leading-tight" x-text="title"></h3>
                            <p class="text-xs text-gray-400 mt-0.5 font-mono" x-text="subtitle"></p>
                        </div>
                    </div>

                    <p class="text-sm text-gray-500 leading-relaxed mb-6 bg-gray-50 rounded-xl px-4 py-3" x-text="message">
                    </p>

                    <div class="flex gap-3">
                        <button @click="cancel()"
                            class="flex-1 px-4 py-2.5 rounded-xl text-sm font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 transition">
                            Batal
                        </button>
                        <button @click="confirm()"
                            class="flex-1 px-4 py-2.5 rounded-xl text-sm font-bold text-white transition"
                            :class="btnClass" x-text="btnLabel">
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Header --}}
    <div class="flex items-center justify-between mb-5">
        <div>
            <a href="{{ route('admin.pengajuan.index') }}"
                class="inline-flex items-center gap-1.5 text-sm font-semibold text-gray-500 hover:text-gray-700 mb-2 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Kembali ke Pengajuan
            </a>
            <h2 class="text-lg font-bold text-gray-800">Arsip Pengajuan</h2>
            <p class="text-xs text-gray-400 mt-0.5">Data diarsipkan — bisa dipulihkan atau dihapus permanen</p>
        </div>
    </div>

    {{-- Info banner --}}
    <div class="flex items-start gap-3 bg-amber-50 border border-amber-200 rounded-2xl px-5 py-4 mb-5">
        <svg class="w-5 h-5 text-amber-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <div>
            <p class="text-xs text-amber-600 mt-0.5">Pulihkan untuk mengembalikan ke daftar aktif. Hapus permanen akan
                menghapus data dan file selamanya.</p>
        </div>
    </div>

    {{-- Search --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 mb-5">
        <form method="GET" class="flex flex-wrap gap-3 items-end">
            <div class="flex-1 min-w-48">
                <label class="block text-xs font-semibold text-gray-500 mb-1.5 uppercase tracking-wider">Cari</label>
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Kode ajuan, nama guru, madrasah..."
                    class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
            </div>
            <button type="submit"
                class="bg-primary-700 hover:bg-primary-800 text-white px-5 py-2 rounded-xl text-sm font-semibold transition">Cari</button>
            @if (request('search'))
                <a href="{{ route('admin.pengajuan.trash') }}"
                    class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-xl text-sm font-medium transition">Reset</a>
            @endif
        </form>
    </div>

    {{-- Bulk action bar --}}
    <div id="bulkBar" class="hidden mb-4">
        <div class="flex items-center gap-3 bg-red-50 border border-red-200 rounded-2xl px-5 py-3">
            <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
            </svg>
            <span class="text-sm font-bold text-red-800"><span id="selectedCount">0</span> data dipilih</span>
            <div class="flex-1"></div>
            <button type="button" id="btnDeselectAll"
                class="text-xs font-semibold text-gray-500 hover:text-gray-700 px-3 py-1.5 rounded-lg hover:bg-white transition">
                Batal
            </button>
            <form id="bulkRestoreForm" action="{{ route('admin.pengajuan.bulk-restore') }}" method="POST"
                class="inline">
                @csrf
                <div id="bulkRestoreIds"></div>
                <button type="button" id="btnBulkRestore"
                    class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white text-xs font-bold px-4 py-2 rounded-xl transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    Pulihkan Dipilih
                </button>
            </form>
            <form id="bulkForceForm" action="{{ route('admin.pengajuan.bulk-force-delete') }}" method="POST"
                class="inline">
                @csrf
                <div id="bulkForceIds"></div>
                <button type="button" id="btnBulkForce"
                    class="inline-flex items-center gap-2 bg-gray-800 hover:bg-gray-900 text-white text-xs font-bold px-4 py-2 rounded-xl transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Hapus Permanen
                </button>
            </form>
        </div>
    </div>

    {{-- Tabel --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 text-gray-400 text-xs font-semibold uppercase tracking-wider">
                    <th class="px-4 py-3 w-10">
                        <input type="checkbox" id="checkAll"
                            class="w-4 h-4 rounded border-gray-300 accent-primary-700 cursor-pointer">
                    </th>
                    <th class="px-4 py-3 text-left">Kode Ajuan</th>
                    <th class="px-4 py-3 text-left">Nama Guru / NIP</th>
                    <th class="px-4 py-3 text-left">Nama Madrasah</th>
                    <th class="px-4 py-3 text-left">Token</th>
                    <th class="px-4 py-3 text-left">Jenis Dokumen</th>
                    <th class="px-4 py-3 text-left">Diarsipkan</th>
                    <th class="px-4 py-3 text-center">Status</th>
                    <th class="px-4 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($pengajuan as $p)
                    @php
                        $sisaHari = now()->diffInDays($p->deleted_at->addDays(30), false);
                        $sisaClass =
                            $sisaHari <= 7 ? 'text-red-500' : ($sisaHari <= 14 ? 'text-orange-500' : 'text-gray-400');
                    @endphp
                    <tr class="hover:bg-gray-50/70 transition opacity-80 hover:opacity-100">
                        <td class="px-4 py-4">
                            <input type="checkbox" value="{{ $p->id }}"
                                class="row-checkbox w-4 h-4 rounded border-gray-300 accent-primary-700 cursor-pointer">
                        </td>
                        <td class="px-4 py-4 font-mono font-bold text-gray-400 text-xs line-through">{{ $p->kode_ajuan }}
                        </td>
                        <td class="px-4 py-4">
                            <p class="font-semibold text-gray-500">{{ $p->nama_guru }}</p>
                            <p class="text-gray-400 text-xs mt-0.5">NIP: {{ $p->nip }}</p>
                        </td>
                        <td class="px-4 py-4 text-gray-500 text-sm">{{ $p->nama_madrasah }}</td>
                        <td class="px-4 py-4">
                            @if ($p->token)
                                <span
                                    class="font-mono text-xs bg-gray-100 text-gray-500 border border-gray-200 px-2 py-1 rounded-lg">{{ $p->token }}</span>
                            @else
                                <span class="text-gray-300 text-xs">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-4 text-gray-400 text-xs">{{ $p->jenisDokumen->nama }}</td>
                        <td class="px-4 py-4 text-gray-400 text-xs">{{ $p->deleted_at->format('d M Y') }}</td>
                        <td class="px-4 py-4 text-center">
                            <span
                                class="px-2 py-1 rounded-full text-xs font-bold {{ $p->status_badge }} opacity-60">{{ $p->status_label }}</span>
                        </td>
                        <td class="px-4 py-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                {{-- Pulihkan --}}
                                <form id="form-restore-{{ $p->id }}"
                                    action="{{ route('admin.pengajuan.restore', $p->id) }}" method="POST"
                                    class="inline">
                                    @csrf
                                    <button type="button"
                                        @click="$dispatch('open-confirm', {
                                    type: 'restore',
                                    title: 'Pulihkan Pengajuan',
                                    subtitle: '{{ $p->kode_ajuan }}',
                                    message: 'Pengajuan atas nama {{ addslashes($p->nama_guru) }} akan dipulihkan ke daftar aktif.',
                                    btnLabel: 'Ya, Pulihkan',
                                    formId: 'form-restore-{{ $p->id }}'
                                })"
                                        class="bg-green-100 hover:bg-green-200 text-green-700 text-xs font-semibold px-3 py-1.5 rounded-lg transition">
                                        Pulihkan
                                    </button>
                                </form>
                                {{-- Hapus Permanen --}}
                                <form id="form-force-{{ $p->id }}"
                                    action="{{ route('admin.pengajuan.force-delete', $p->id) }}" method="POST"
                                    class="inline">
                                    @csrf @method('DELETE')
                                    <button type="button"
                                        @click="$dispatch('open-confirm', {
                                    type: 'force',
                                    title: 'Hapus Permanen',
                                    subtitle: '{{ $p->kode_ajuan }}',
                                    message: 'Data dan file pengajuan {{ addslashes($p->nama_guru) }} akan dihapus selamanya dan tidak bisa dikembalikan.',
                                    btnLabel: 'Hapus Selamanya',
                                    formId: 'form-force-{{ $p->id }}'
                                })"
                                        class="bg-gray-100 hover:bg-red-100 text-gray-500 hover:text-red-600 text-xs font-semibold px-3 py-1.5 rounded-lg transition">
                                        Hapus Permanen
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="px-5 py-12 text-center text-gray-400">
                            <div class="w-12 h-12 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-3">
                                <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </div>
                            Arsip kosong
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-5 py-4 border-t border-gray-100 flex items-center justify-between gap-4">
            <p class="text-xs text-gray-400 flex-shrink-0">Total arsip: {{ $pengajuan->total() }}</p>
            {{ $pengajuan->links() }}
        </div>
    </div>

    <script>
        const checkAll = document.getElementById('checkAll');
        const checkboxes = document.querySelectorAll('.row-checkbox');
        const bulkBar = document.getElementById('bulkBar');
        const selectedCount = document.getElementById('selectedCount');

        function getChecked() {
            return document.querySelectorAll('.row-checkbox:checked');
        }

        function fillIds(containerId, checked) {
            const c = document.getElementById(containerId);
            c.innerHTML = '';
            checked.forEach(cb => {
                const i = document.createElement('input');
                i.type = 'hidden';
                i.name = 'ids[]';
                i.value = cb.value;
                c.appendChild(i);
            });
        }

        function updateBulkBar() {
            const checked = getChecked();
            selectedCount.textContent = checked.length;
            bulkBar.classList.toggle('hidden', checked.length === 0);
            checkAll.indeterminate = checked.length > 0 && checked.length < checkboxes.length;
            checkAll.checked = checkboxes.length > 0 && checked.length === checkboxes.length;
        }
        checkAll.addEventListener('change', function() {
            checkboxes.forEach(cb => cb.checked = this.checked);
            updateBulkBar();
        });
        checkboxes.forEach(cb => cb.addEventListener('change', updateBulkBar));
        document.getElementById('btnDeselectAll').addEventListener('click', () => {
            checkboxes.forEach(cb => cb.checked = false);
            checkAll.checked = false;
            updateBulkBar();
        });

        document.getElementById('btnBulkRestore').addEventListener('click', () => {
            const checked = getChecked();
            if (!checked.length) return;
            window.dispatchEvent(new CustomEvent('open-confirm', {
                detail: {
                    type: 'restore',
                    title: 'Pulihkan Data Terpilih',
                    subtitle: `${checked.length} pengajuan dipilih`,
                    message: `Sebanyak ${checked.length} pengajuan akan dipulihkan ke daftar aktif.`,
                    btnLabel: 'Ya, Pulihkan Semua',
                    callback: () => {
                        fillIds('bulkRestoreIds', checked);
                        document.getElementById('bulkRestoreForm').submit();
                    }
                }
            }));
        });

        document.getElementById('btnBulkForce').addEventListener('click', () => {
            const checked = getChecked();
            if (!checked.length) return;
            window.dispatchEvent(new CustomEvent('open-confirm', {
                detail: {
                    type: 'force',
                    title: 'Hapus Permanen Data Terpilih',
                    subtitle: `${checked.length} pengajuan dipilih`,
                    message: `Sebanyak ${checked.length} pengajuan beserta file-nya akan dihapus selamanya dan tidak bisa dikembalikan.`,
                    btnLabel: 'Hapus Selamanya',
                    callback: () => {
                        fillIds('bulkForceIds', checked);
                        document.getElementById('bulkForceForm').submit();
                    }
                }
            }));
        });

        function confirmModal() {
            return {
                show: false,
                type: 'arsip',
                title: '',
                subtitle: '',
                message: '',
                btnLabel: '',
                formId: null,
                _callback: null,
                open(d) {
                    Object.assign(this, {
                        type: d.type || 'arsip',
                        title: d.title || 'Konfirmasi',
                        subtitle: d.subtitle || '',
                        message: d.message || '',
                        btnLabel: d.btnLabel || 'Ya',
                        formId: d.formId || null,
                        _callback: d.callback || null,
                        show: true
                    });
                },
                confirm() {
                    this.show = false;
                    this.$nextTick(() => {
                        if (this._callback) this._callback();
                        else if (this.formId) document.getElementById(this.formId)?.submit();
                    });
                },
                cancel() {
                    this.show = false;
                },
                get accentClass() {
                    return {
                        arsip: 'bg-gradient-to-r from-orange-400 to-amber-400',
                        force: 'bg-gradient-to-r from-red-500 to-red-600',
                        restore: 'bg-gradient-to-r from-green-500 to-emerald-400',
                        bulk: 'bg-gradient-to-r from-green-500 to-emerald-400'
                    } [this.type] || 'bg-primary-500';
                },
                get iconBgClass() {
                    return {
                        arsip: 'bg-orange-100',
                        force: 'bg-red-100',
                        restore: 'bg-green-100',
                        bulk: 'bg-green-100'
                    } [this.type] || 'bg-gray-100';
                },
                get iconColor() {
                    return {
                        arsip: 'text-orange-500',
                        force: 'text-red-600',
                        restore: 'text-green-600',
                        bulk: 'text-green-600'
                    } [this.type] || 'text-gray-500';
                },
                get btnClass() {
                    return {
                        arsip: 'bg-orange-500 hover:bg-orange-600',
                        force: 'bg-red-600 hover:bg-red-700',
                        restore: 'bg-green-600 hover:bg-green-700',
                        bulk: 'bg-green-600 hover:bg-green-700'
                    } [this.type] || 'bg-primary-700 hover:bg-primary-800';
                },
            }
        }
    </script>

@endsection
