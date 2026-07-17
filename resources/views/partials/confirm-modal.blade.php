{{--
    Modal Konfirmasi Global — pengganti confirm() bawaan browser.

    2 cara pakai:

    1) CARA GAMPANG (buat form hapus/reset yang sebelumnya pakai
       onsubmit="return confirm('...')") — tinggal ganti jadi:
           <form ... data-confirm="Hapus data ini?">
       Atribut opsional: data-confirm-title, data-confirm-btn,
       data-confirm-type ("force" merah / "arsip" & "bulk" kuning /
       "restore" hijau — default "force").

    2) CARA MANUAL (butuh callback custom, misal bulk action / kumpulin
       checkbox dulu sebelum submit) — dispatch event-nya langsung:
           $dispatch('open-confirm', {
               type: 'bulk', title: 'Arsipkan Data Terpilih',
               subtitle: `${checked.length} dipilih`,
               message: 'Penjelasan...', btnLabel: 'Arsipkan Semua',
               callback: () => { ...lakukan sesuatu... }
           })

    Cukup di-include SEKALI per layout (admin / guru / madrasah).
--}}
<div x-data="confirmModal()" @open-confirm.window="open($event.detail)" x-cloak>
    <div x-show="show"
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         class="fixed inset-0 z-[9998] flex items-center justify-center p-4"
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
                                    : (type==='restore'
                                        ? 'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15'
                                        : 'M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16')"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-zinc-800" x-text="title"></p>
                        <p class="text-2xs text-zinc-400 font-mono" x-text="subtitle" x-show="subtitle"></p>
                    </div>
                </div>
                <p class="text-xs text-zinc-500 bg-zinc-50 rounded-lg px-3 py-2.5 mb-4 leading-relaxed" style="white-space:pre-line" x-text="message"></p>
                <div class="flex gap-2">
                    <button type="button" @click="cancel()"
                        class="flex-1 px-3 py-2 rounded-lg text-xs font-semibold text-zinc-600 bg-zinc-100 hover:bg-zinc-200 transition">
                        Batal
                    </button>
                    <button type="button" @click="confirm()"
                        class="flex-1 px-3 py-2 rounded-lg text-xs font-bold text-white transition"
                        :class="btnClass"
                        x-text="btnLabel">
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function confirmModal() {
        return {
            show: false, type: 'force', title: '', subtitle: '', message: '', btnLabel: '',
            formId: null, _callback: null,
            open(d) {
                Object.assign(this, {
                    type: d.type || 'force',
                    title: d.title || 'Konfirmasi',
                    subtitle: d.subtitle || '',
                    message: d.message || '',
                    btnLabel: d.btnLabel || 'Ya, Lanjutkan',
                    formId: d.formId || null,
                    _callback: d.callback || null,
                    show: true,
                });
            },
            confirm() {
                this.show = false;
                this.$nextTick(() => {
                    if (this._callback) this._callback();
                    else if (this.formId) document.getElementById(this.formId)?.submit();
                });
            },
            cancel() { this.show = false; },
            get accentClass() {
                return { arsip: 'bg-amber-400', bulk: 'bg-amber-400', force: 'bg-red-500', restore: 'bg-green-500' }[this.type] || 'bg-zinc-400';
            },
            get iconBgClass() {
                return { arsip: 'bg-amber-100', bulk: 'bg-amber-100', force: 'bg-red-100', restore: 'bg-green-100' }[this.type] || 'bg-zinc-100';
            },
            get iconColor() {
                return { arsip: 'text-amber-500', bulk: 'text-amber-500', force: 'text-red-600', restore: 'text-green-600' }[this.type] || 'text-zinc-400';
            },
            get btnClass() {
                return { arsip: 'bg-amber-500 hover:bg-amber-600', bulk: 'bg-amber-500 hover:bg-amber-600', force: 'bg-red-600 hover:bg-red-700', restore: 'bg-green-600 hover:bg-green-700' }[this.type] || 'bg-zinc-800 hover:bg-zinc-700';
            },
        };
    }

    // ── Auto-wiring: <form data-confirm="..."> otomatis pakai modal ini ──
    // Ini yang bikin migrasi dari onsubmit="return confirm(...)" jadi
    // tinggal ganti atribut, tanpa perlu nulis Alpine/$dispatch manual di
    // tiap tombol satu-satu.
    document.addEventListener('submit', function (e) {
        var form = e.target.closest('form[data-confirm]');
        if (!form) return;
        if (form.dataset.confirmBypass === '1') return; // sudah dikonfirmasi, biarkan submit asli jalan

        e.preventDefault();
        window.dispatchEvent(new CustomEvent('open-confirm', {
            detail: {
                type: form.dataset.confirmType || 'force',
                title: form.dataset.confirmTitle || 'Konfirmasi',
                // \n literal (bukan newline asli, karena ini atribut HTML biasa)
                // di-unescape dulu jadi baris baru beneran biar white-space:pre-line kepakai.
                message: form.dataset.confirm.replace(/\\n/g, '\n'),
                btnLabel: form.dataset.confirmBtn || 'Ya, Lanjutkan',
                callback: function () {
                    form.dataset.confirmBypass = '1';
                    if (form.requestSubmit) form.requestSubmit();
                    else form.submit();
                },
            },
        }));
    });
</script>
