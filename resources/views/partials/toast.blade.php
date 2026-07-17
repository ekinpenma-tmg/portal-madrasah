{{--
    Toast Notification Global.

    Cukup di-include SEKALI per layout (admin / guru / madrasah), lalu
    panggil dari mana saja:
        toast('Berhasil disimpan!', 'success')
        toast('Gagal menghapus data.', 'error')
        toast('Masih ada data yang menunggu.', 'warning')
        toast('Info tambahan.', 'info')

    Aman dipanggil SEBELUM Alpine selesai load (misal dari script inline
    paling atas <body> buat nerusin flash message dari session) — toast
    yang dipanggil sebelum komponen ini siap otomatis di-antrekan lalu
    ditampilkan begitu komponen ini jalan.
--}}
<div x-data="toastManager()" x-init="init()"
     class="fixed top-4 right-4 z-[9999] flex flex-col gap-2 w-[calc(100%-2rem)] max-w-sm pointer-events-none">
    <template x-for="t in toasts" :key="t.id">
        <div x-show="t.show"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-x-4"
             x-transition:enter-end="opacity-100 translate-x-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-x-0"
             x-transition:leave-end="opacity-0 translate-x-4"
             class="pointer-events-auto bg-white rounded-xl overflow-hidden"
             style="box-shadow:0 10px 40px rgba(0,0,0,0.12)">
            <div class="flex items-start gap-2.5 px-4 py-3">
                <div class="w-6 h-6 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5" :class="iconBgClass(t.type)">
                    <svg class="w-3.5 h-3.5" :class="iconColor(t.type)" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" :d="iconPath(t.type)"/>
                    </svg>
                </div>
                <p class="text-xs text-zinc-700 leading-relaxed flex-1 pt-0.5" style="white-space:pre-line" x-text="t.msg"></p>
                <button type="button" @click="dismiss(t.id)" class="text-zinc-300 hover:text-zinc-500 flex-shrink-0 mt-0.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="h-0.5 w-full" :class="accentClass(t.type)"></div>
        </div>
    </template>
</div>

<script>
    function toastManager() {
        return {
            toasts: [],
            init() {
                var self = this;
                window.toast = function (msg, type) { self.push(msg, type || 'success'); };
                (window.__toastQueue || []).forEach(function (q) { self.push(q.msg, q.type); });
                window.__toastQueue = [];
            },
            push(msg, type) {
                if (!msg) return;
                var id = Date.now() + Math.random();
                this.toasts.push({ id: id, msg: msg, type: type, show: true });
                var self = this;
                setTimeout(function () { self.dismiss(id); }, 5000);
            },
            dismiss(id) {
                var t = this.toasts.find(function (x) { return x.id === id; });
                if (t) t.show = false;
                var self = this;
                setTimeout(function () { self.toasts = self.toasts.filter(function (x) { return x.id !== id; }); }, 300);
            },
            accentClass(type) {
                return { success: 'bg-green-500', error: 'bg-red-500', warning: 'bg-amber-400', info: 'bg-blue-500' }[type] || 'bg-zinc-400';
            },
            iconBgClass(type) {
                return { success: 'bg-green-100', error: 'bg-red-100', warning: 'bg-amber-100', info: 'bg-blue-100' }[type] || 'bg-zinc-100';
            },
            iconColor(type) {
                return { success: 'text-green-600', error: 'text-red-600', warning: 'text-amber-500', info: 'text-blue-500' }[type] || 'text-zinc-400';
            },
            iconPath(type) {
                return {
                    success: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
                    error:   'M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                    warning: 'M12 9v3.75m0 3.75h.008v.008H12v-.008zM3 12a9 9 0 1118 0 9 9 0 01-18 0z',
                    info:    'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                }[type] || 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z';
            },
        };
    }

    // Shim antrean: kalau ada kode yang manggil toast(...) SEBELUM Alpine
    // sempat init komponen di atas (misal script flash paling atas <body>),
    // pesannya ditampung dulu di sini, nanti langsung disemprot begitu
    // toastManager().init() jalan.
    window.toast = window.toast || function (msg, type) {
        (window.__toastQueue = window.__toastQueue || []).push({ msg: msg, type: type || 'success' });
    };
</script>
