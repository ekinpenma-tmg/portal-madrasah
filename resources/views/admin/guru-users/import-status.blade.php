@extends('layouts.admin')
@section('title', 'Status Import Akun Guru')

@section('content')
<div class="max-w-xl fade-in"
     x-data="importStatus('{{ route('admin.guru-users.import-status.json', $batchId) }}', '{{ route('admin.guru-users.index') }}')"
     x-init="poll()">

    <div class="flex items-center gap-2 text-xs text-zinc-400 mb-4">
        <a href="{{ route('admin.guru-users.index') }}" class="hover:text-zinc-600 transition">Akun Guru</a>
        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
        <span class="text-zinc-600 font-medium">Status Import</span>
    </div>

    @if(session('info'))
    <div class="bg-blue-50 border border-blue-200 text-blue-700 text-xs rounded-lg px-4 py-2.5 mb-4">{{ session('info') }}</div>
    @endif

    <div class="bg-white rounded-xl border border-zinc-100 p-5">

        {{-- Queued / processing --}}
        <template x-if="status === 'queued' || status === 'processing'">
            <div>
                <div class="flex items-center gap-2 mb-3">
                    <svg class="w-4 h-4 animate-spin text-blue-600" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                    <p class="text-xs font-semibold text-zinc-700" x-text="status === 'queued' ? 'Menunggu giliran diproses…' : 'Sedang diproses…'"></p>
                </div>

                <div class="w-full bg-zinc-100 rounded-full h-2 overflow-hidden mb-2">
                    <div class="bg-blue-600 h-2 rounded-full transition-all duration-300"
                         :style="`width: ${total > 0 ? Math.round((progress/total)*100) : 5}%`"></div>
                </div>
                <p class="text-2xs text-zinc-400" x-text="total > 0 ? `${progress} / ${total} baris` : 'Menyiapkan data…'"></p>

                <p class="text-2xs text-zinc-400 mt-3">Halaman ini otomatis update tiap 2 detik. Aman ditinggal / ditutup — proses tetap lanjut di background, tinggal buka lagi link ini nanti.</p>
            </div>
        </template>

        {{-- Done --}}
        <template x-if="status === 'done'">
            <div>
                <div class="flex items-center gap-2 mb-3 text-green-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <p class="text-sm font-semibold">Import selesai!</p>
                </div>
                <div class="grid grid-cols-3 gap-2 mb-3">
                    <div class="bg-green-50 border border-green-100 rounded-lg px-3 py-2 text-center">
                        <p class="text-lg font-bold text-green-700" x-text="inserted"></p>
                        <p class="text-2xs text-zinc-500">Ditambahkan</p>
                    </div>
                    <div class="bg-blue-50 border border-blue-100 rounded-lg px-3 py-2 text-center">
                        <p class="text-lg font-bold text-blue-700" x-text="updated"></p>
                        <p class="text-2xs text-zinc-500">Diperbarui</p>
                    </div>
                    <div class="bg-zinc-50 border border-zinc-100 rounded-lg px-3 py-2 text-center">
                        <p class="text-lg font-bold text-zinc-600" x-text="skipped"></p>
                        <p class="text-2xs text-zinc-500">Dilewati</p>
                    </div>
                </div>

                <template x-if="errors.length > 0">
                    <div class="bg-amber-50 border border-amber-100 rounded-lg px-3 py-2 mb-3 max-h-40 overflow-y-auto">
                        <p class="text-2xs font-semibold text-amber-700 mb-1">Catatan (<span x-text="errors.length"></span>):</p>
                        <ul class="text-2xs text-amber-700 list-disc list-inside space-y-0.5">
                            <template x-for="err in errors" :key="err">
                                <li x-text="err"></li>
                            </template>
                        </ul>
                    </div>
                </template>

                <a :href="indexUrl" class="inline-flex items-center gap-1.5 text-xs font-bold px-4 py-2 rounded-lg text-white transition" style="background:#2563eb">
                    Lihat Daftar Akun Guru
                </a>
            </div>
        </template>

        {{-- Error --}}
        <template x-if="status === 'error'">
            <div>
                <div class="flex items-center gap-2 mb-2 text-red-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-sm font-semibold">Import gagal</p>
                </div>
                <p class="text-xs text-red-600 mb-3" x-text="message"></p>
                <a href="{{ route('admin.guru-users.import-form') }}" class="bg-zinc-100 hover:bg-zinc-200 text-zinc-600 text-xs font-semibold px-4 py-2 rounded-lg transition">
                    Coba Lagi
                </a>
            </div>
        </template>

        {{-- Not found (batch kadaluarsa / salah link) --}}
        <template x-if="status === 'not_found'">
            <div>
                <p class="text-xs text-zinc-500 mb-3">Data status import ini tidak ditemukan (mungkin sudah lebih dari 6 jam, atau link salah).</p>
                <a href="{{ route('admin.guru-users.import-form') }}" class="bg-zinc-100 hover:bg-zinc-200 text-zinc-600 text-xs font-semibold px-4 py-2 rounded-lg transition">
                    Kembali ke Import
                </a>
            </div>
        </template>
    </div>
</div>

<script>
function importStatus(jsonUrl, indexUrl) {
    return {
        status: 'queued',
        progress: 0,
        total: 0,
        inserted: 0,
        updated: 0,
        skipped: 0,
        errors: [],
        message: '',
        indexUrl: indexUrl,
        timer: null,
        poll() {
            const tick = () => {
                fetch(jsonUrl, { headers: { 'Accept': 'application/json' } })
                    .then(res => res.ok ? res.json() : { status: 'not_found' })
                    .then(data => {
                        this.status   = data.status ?? 'not_found';
                        this.progress = data.progress ?? 0;
                        this.total    = data.total ?? 0;
                        this.inserted = data.inserted ?? 0;
                        this.updated  = data.updated ?? 0;
                        this.skipped  = data.skipped ?? 0;
                        this.errors   = data.errors ?? [];
                        this.message  = data.message ?? '';

                        if (this.status === 'queued' || this.status === 'processing') {
                            this.timer = setTimeout(tick, 2000);
                        }
                    })
                    .catch(() => { this.timer = setTimeout(tick, 3000); });
            };
            tick();
        }
    }
}
</script>
@endsection
