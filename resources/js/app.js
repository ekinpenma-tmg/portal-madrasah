import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';
import { createIcons, icons } from 'lucide';

Alpine.plugin(collapse);
window.Alpine = Alpine;
Alpine.start();

// ── Ikon konsisten (Lucide) ──────────────────────────────────────────
// Elemen <i data-lucide="nama-ikon"> di-scan lalu diganti jadi <svg> asli.
// Class Tailwind (ukuran/warna) yang sudah ditaruh di <i> ikut kebawa ke
// <svg> hasilnya, jadi tinggal pasang class seperti biasa di HTML.
createIcons({ icons });

// Dipanggil ulang kalau ada konten baru yang disisipkan lewat JS (mis.
// toast/modal dinamis yang belum sempat di-scan saat load pertama).
window.refreshIcons = () => createIcons({ icons });
