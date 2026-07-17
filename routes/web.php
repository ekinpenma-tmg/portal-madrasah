<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Public\PengajuanPublicController;
use App\Http\Controllers\Public\DownloadPublicController;
use App\Http\Controllers\Public\LayananPublicController;
use App\Http\Controllers\Admin\LayananAdminController;
use App\Http\Controllers\Public\StatusController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PengajuanAdminController;
use App\Http\Controllers\Admin\DownloadAdminController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\Admin\ProfilController;
use App\Http\Controllers\Admin\RiwayatController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\JenisDokumenController;
use App\Http\Controllers\Admin\TindakanCepatController;
use App\Http\Controllers\Public\DataMadrasahController;
use App\Http\Controllers\Admin\DataMadrasahAdminController;
use App\Http\Controllers\Admin\GuruUserController;
use App\Http\Controllers\Admin\KategoriArsipController;
use App\Http\Controllers\Admin\ArsipGuruAdminController;
use App\Http\Controllers\Admin\LaporanArsipAdminController;
use App\Http\Controllers\Guru\GuruAuthController;
use App\Http\Controllers\Guru\GuruPortalController;
use App\Http\Controllers\Guru\GuruPengajuanController;
use App\Http\Controllers\Madrasah\MadrasahAuthController;
use App\Http\Controllers\Madrasah\MadrasahPortalController;
use App\Http\Controllers\Madrasah\MadrasahPengajuanController;
use App\Http\Controllers\Madrasah\MadrasahGuruMonitorController;
use App\Http\Controllers\Madrasah\MadrasahLaporanController;
use App\Http\Controllers\Admin\MadrasahUserController;
use App\Http\Controllers\Admin\ArsipMadrasahAdminController;

// ============================================================
// HALAMAN PUBLIK (Tanpa Login)
// ============================================================

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/profil', [HomeController::class, 'profil'])->name('profil');
Route::get('/data-madrasah', [DataMadrasahController::class, 'index'])->name('data-madrasah.index');
Route::get('/data-madrasah/tabel', [DataMadrasahController::class, 'tabel'])->name('data-madrasah.tabel');

// Pengajuan Dokumen
Route::get('/ajukan/{jenis}', [PengajuanPublicController::class, 'form'])->name('pengajuan.form');
Route::get('/ajukan/{jenis}/sukses/{kode}', [PengajuanPublicController::class, 'sukses'])->name('pengajuan.sukses');
Route::middleware('throttle:5,1')->group(function () {
    Route::post('/ajukan/{jenis}', [PengajuanPublicController::class, 'store'])->name('pengajuan.store');
});

// Pelayanan (SOP & jenis layanan Penma)
Route::get('/pelayanan', [LayananPublicController::class, 'index'])->name('layanan.index');
Route::get('/pelayanan/{layanan:slug}', [LayananPublicController::class, 'show'])->name('layanan.show');
Route::get('/pelayanan/{layanan:slug}/sop', [LayananPublicController::class, 'sop'])->name('layanan.sop');

// Download
Route::get('/download', [DownloadPublicController::class, 'index'])->name('download.index');
Route::get('/download/{id}', [DownloadPublicController::class, 'unduh'])->name('download.unduh');

// Status Ajuan
Route::get('/status', [StatusController::class, 'index'])->name('status.index');
Route::middleware('throttle:10,1')->group(function () {
    Route::post('/status/cari', [StatusController::class, 'cari'])->name('status.cari');
});

// ============================================================
// HALAMAN ADMIN (Harus Login)
// ============================================================

Route::prefix('admin')->middleware(['auth'])->name('admin.')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Kelola Jenis Dokumen
    Route::get('/jenis-dokumen',               [JenisDokumenController::class, 'index'])->name('jenis-dokumen.index');
    Route::post('/jenis-dokumen',              [JenisDokumenController::class, 'store'])->name('jenis-dokumen.store');
    Route::put('/jenis-dokumen/{id}',          [JenisDokumenController::class, 'update'])->name('jenis-dokumen.update');
    Route::delete('/jenis-dokumen/{id}',       [JenisDokumenController::class, 'destroy'])->name('jenis-dokumen.destroy');
    Route::patch('/jenis-dokumen/{id}/toggle', [JenisDokumenController::class, 'toggleAktif'])->name('jenis-dokumen.toggle');

    // Kelola Pengajuan
    Route::get('/pengajuan',                          [PengajuanAdminController::class, 'index'])->name('pengajuan.index');
    Route::get('pengajuan/export',                    [PengajuanAdminController::class, 'export'])->name('pengajuan.export');
    Route::post('/pengajuan/bulk-delete',             [PengajuanAdminController::class, 'bulkDelete'])->name('pengajuan.bulk-delete');
    Route::get('/pengajuan/trash',                    [PengajuanAdminController::class, 'trash'])->name('pengajuan.trash');
    Route::post('/pengajuan/trash/bulk-restore',      [PengajuanAdminController::class, 'bulkRestore'])->name('pengajuan.bulk-restore');
    Route::post('/pengajuan/trash/bulk-force-delete', [PengajuanAdminController::class, 'bulkForceDelete'])->name('pengajuan.bulk-force-delete');
    Route::delete('/pengajuan/trash/{id}/force',      [PengajuanAdminController::class, 'forceDelete'])->name('pengajuan.force-delete');
    Route::post('/pengajuan/trash/{id}/restore',      [PengajuanAdminController::class, 'restore'])->name('pengajuan.restore');
    Route::get('/pengajuan/{id}',                     [PengajuanAdminController::class, 'show'])->name('pengajuan.show');
    Route::post('/pengajuan/{id}/terima',             [PengajuanAdminController::class, 'terima'])->name('pengajuan.terima');
    Route::post('/pengajuan/{id}/tolak',              [PengajuanAdminController::class, 'tolak'])->name('pengajuan.tolak');
    Route::delete('/pengajuan/{id}',                  [PengajuanAdminController::class, 'destroy'])->name('pengajuan.destroy');
    Route::get('/pengajuan/{id}/dokumen',             [PengajuanAdminController::class, 'lihatDokumen'])->name('pengajuan.dokumen');

    // Riwayat
    Route::get('/riwayat', [RiwayatController::class, 'index'])->name('riwayat.index');

    // Tindakan Cepat
    Route::get('/tindakan-cepat',              [TindakanCepatController::class, 'index'])->name('tindakan-cepat.index');
    Route::post('/tindakan-cepat/{id}/terima', [TindakanCepatController::class, 'terima'])->name('tindakan-cepat.terima');
    Route::post('/tindakan-cepat/{id}/tolak',  [TindakanCepatController::class, 'tolak'])->name('tindakan-cepat.tolak');

    // Kelola File Download
    Route::get('/download',           [DownloadAdminController::class, 'index'])->name('download.index');
    Route::get('/download/create',    [DownloadAdminController::class, 'create'])->name('download.create');
    Route::post('/download',          [DownloadAdminController::class, 'store'])->name('download.store');
    Route::get('/download/{id}/edit', [DownloadAdminController::class, 'edit'])->name('download.edit');
    Route::put('/download/{id}',      [DownloadAdminController::class, 'update'])->name('download.update');
    Route::delete('/download/{id}',   [DownloadAdminController::class, 'destroy'])->name('download.destroy');

    // Kelola Pelayanan (SOP / standar pelayanan)
    Route::get('/layanan',               [LayananAdminController::class, 'index'])->name('layanan.index');
    Route::get('/layanan/create',        [LayananAdminController::class, 'create'])->name('layanan.create');
    Route::post('/layanan',              [LayananAdminController::class, 'store'])->name('layanan.store');
    Route::get('/layanan/{id}/edit',     [LayananAdminController::class, 'edit'])->name('layanan.edit');
    Route::put('/layanan/{id}',          [LayananAdminController::class, 'update'])->name('layanan.update');
    Route::delete('/layanan/{id}',       [LayananAdminController::class, 'destroy'])->name('layanan.destroy');
    Route::patch('/layanan/{id}/toggle', [LayananAdminController::class, 'toggleAktif'])->name('layanan.toggle');

    // Kelola Staff
    Route::get('/staff',           [StaffController::class, 'index'])->name('staff.index');
    Route::get('/staff/create',    [StaffController::class, 'create'])->name('staff.create');
    Route::post('/staff',          [StaffController::class, 'store'])->name('staff.store');
    Route::get('/staff/{id}/edit', [StaffController::class, 'edit'])->name('staff.edit');
    Route::put('/staff/{id}',      [StaffController::class, 'update'])->name('staff.update');
    Route::delete('/staff/{id}',   [StaffController::class, 'destroy'])->name('staff.destroy');

    // Kelola Akun Admin — khusus Super Admin
    Route::middleware('super.admin')->group(function () {
        Route::get('/admin-users',           [AdminUserController::class, 'index'])->name('admin-users.index');
        Route::get('/admin-users/create',    [AdminUserController::class, 'create'])->name('admin-users.create');
        Route::post('/admin-users',          [AdminUserController::class, 'store'])->name('admin-users.store');
        Route::get('/admin-users/{id}/edit', [AdminUserController::class, 'edit'])->name('admin-users.edit');
        Route::put('/admin-users/{id}',      [AdminUserController::class, 'update'])->name('admin-users.update');
        Route::delete('/admin-users/{id}',   [AdminUserController::class, 'destroy'])->name('admin-users.destroy');
    });

    // Kelola Data Madrasah
    Route::prefix('data-madrasah')->name('data-madrasah.')->group(function () {
        Route::get('/',                 [DataMadrasahAdminController::class, 'index'])->name('index');
        Route::get('/import-madrasah',  [DataMadrasahAdminController::class, 'importMadrasahForm'])->name('import-madrasah');
        Route::get('/import-madrasah/template', [DataMadrasahAdminController::class, 'downloadTemplateMadrasah'])->name('import-madrasah.template');
        Route::post('/import-madrasah', [DataMadrasahAdminController::class, 'importMadrasah'])->name('import-madrasah.store');
        Route::get('/import-siswa',     [DataMadrasahAdminController::class, 'importSiswaForm'])->name('import-siswa');
        Route::get('/import-siswa/template', [DataMadrasahAdminController::class, 'downloadTemplateSiswa'])->name('import-siswa.template');
        Route::post('/import-siswa',    [DataMadrasahAdminController::class, 'importSiswa'])->name('import-siswa.store');
        Route::patch('/{id}/toggle',    [DataMadrasahAdminController::class, 'toggle'])->name('toggle');
        Route::delete('/reset',         [DataMadrasahAdminController::class, 'reset'])->name('reset')->middleware('super.admin');
    });

    // Kelola Profil Organisasi
    Route::get('/profil',          [ProfilController::class, 'index'])->name('profil.index');
    Route::post('/profil',         [ProfilController::class, 'update'])->name('profil.update');
    Route::delete('/profil/logo',  [ProfilController::class, 'deleteLogo'])->name('profil.logo.delete');

    // ── FITUR ARSIP DIGITAL (Tahap 2) ─────────────────────────

    // Kelola Akun Guru
    Route::prefix('guru-users')->name('guru-users.')->group(function () {
        Route::get('/',                       [GuruUserController::class, 'index'])           ->name('index');
        Route::get('/create',                 [GuruUserController::class, 'create'])          ->name('create');
        Route::post('/',                      [GuruUserController::class, 'store'])           ->name('store');
        Route::get('/import',                 [GuruUserController::class, 'importForm'])      ->name('import-form');
        Route::post('/import',                [GuruUserController::class, 'import'])          ->name('import');
        Route::get('/import-status/{batch}',       [GuruUserController::class, 'importStatus'])    ->name('import-status');
        Route::get('/import-status/{batch}/json',  [GuruUserController::class, 'importStatusJson'])->name('import-status.json');
        Route::get('/download-template',      [GuruUserController::class, 'downloadTemplate'])->name('download-template');
        Route::get('/{id}/edit',             [GuruUserController::class, 'edit'])             ->name('edit');
        Route::put('/{id}',                  [GuruUserController::class, 'update'])           ->name('update');
        Route::patch('/{id}/toggle',         [GuruUserController::class, 'toggle'])           ->name('toggle');
        Route::patch('/{id}/reset-password', [GuruUserController::class, 'resetPassword'])   ->name('reset-password');
        Route::delete('/reset',              [GuruUserController::class, 'reset'])            ->name('reset')->middleware('super.admin');
        Route::delete('/{id}',              [GuruUserController::class, 'destroy'])           ->name('destroy');
    });

    // Kelola Kategori Arsip
    Route::prefix('kategori-arsip')->name('kategori-arsip.')->group(function () {
        Route::get('/',               [KategoriArsipController::class, 'index'])      ->name('index');
        Route::post('/',              [KategoriArsipController::class, 'store'])      ->name('store');
        Route::put('/{id}',          [KategoriArsipController::class, 'update'])     ->name('update');
        Route::patch('/{id}/toggle', [KategoriArsipController::class, 'toggleAktif'])->name('toggle');
        Route::delete('/{id}',       [KategoriArsipController::class, 'destroy'])    ->name('destroy');
    });

    // ── Kelola Akun Madrasah ───────────────────────────────────
    Route::prefix('madrasah-users')->name('madrasah-users.')->group(function () {
        Route::get('/',                       [MadrasahUserController::class, 'index'])           ->name('index');
        Route::get('/create',                 [MadrasahUserController::class, 'create'])          ->name('create');
        Route::post('/',                      [MadrasahUserController::class, 'store'])           ->name('store');
        Route::get('/import',                 [MadrasahUserController::class, 'importForm'])      ->name('import-form');
        Route::post('/import',                [MadrasahUserController::class, 'import'])          ->name('import');
        Route::get('/download-template',      [MadrasahUserController::class, 'downloadTemplate'])->name('download-template');
        Route::get('/{id}/edit',             [MadrasahUserController::class, 'edit'])             ->name('edit');
        Route::put('/{id}',                  [MadrasahUserController::class, 'update'])           ->name('update');
        Route::patch('/{id}/toggle',         [MadrasahUserController::class, 'toggle'])           ->name('toggle');
        Route::patch('/{id}/reset-password', [MadrasahUserController::class, 'resetPassword'])   ->name('reset-password');
        Route::delete('/reset',              [MadrasahUserController::class, 'reset'])            ->name('reset')->middleware('super.admin');
        Route::delete('/{id}',               [MadrasahUserController::class, 'destroy'])          ->name('destroy');
    });

    // ── Arsip Madrasah (Admin) ─────────────────────────────────
    Route::prefix('arsip-madrasah')->name('arsip-madrasah.')->group(function () {
        Route::get('/',                       [ArsipMadrasahAdminController::class, 'index'])        ->name('index');
        Route::get('/export',                 [ArsipMadrasahAdminController::class, 'export'])       ->name('export');
        Route::post('/bulk-verify',           [ArsipMadrasahAdminController::class, 'bulkVerify'])   ->name('bulk-verify');
        Route::get('/madrasah/{madrasahId}',  [ArsipMadrasahAdminController::class, 'show'])         ->name('show');
        Route::patch('/{id}/verify',          [ArsipMadrasahAdminController::class, 'verify'])       ->name('verify');
        Route::patch('/{id}/unverify',        [ArsipMadrasahAdminController::class, 'unverify'])     ->name('unverify');
        Route::patch('/{id}/catatan',         [ArsipMadrasahAdminController::class, 'updateCatatan'])->name('catatan');
        Route::delete('/{id}',                [ArsipMadrasahAdminController::class, 'destroy'])      ->name('destroy');
    });

    // Arsip Digital Guru (Tahap 4)
    Route::prefix('arsip-guru')->name('arsip-guru.')->group(function () {
        Route::get('/',                [ArsipGuruAdminController::class, 'index'])        ->name('index');
        Route::get('/export',          [ArsipGuruAdminController::class, 'export'])       ->name('export');
        Route::post('/bulk-verify',    [ArsipGuruAdminController::class, 'bulkVerify'])   ->name('bulk-verify');
        Route::get('/guru/{guruId}',   [ArsipGuruAdminController::class, 'show'])         ->name('show');
        Route::patch('/{id}/verify',   [ArsipGuruAdminController::class, 'verify'])       ->name('verify');
        Route::patch('/{id}/unverify', [ArsipGuruAdminController::class, 'unverify'])     ->name('unverify');
        Route::patch('/{id}/catatan',  [ArsipGuruAdminController::class, 'updateCatatan'])->name('catatan');
        Route::delete('/{id}',         [ArsipGuruAdminController::class, 'destroy'])      ->name('destroy');
    });

    Route::prefix('laporan-arsip')->name('laporan-arsip.')->group(function () {
        Route::get('/guru',     [LaporanArsipAdminController::class, 'guru'])    ->name('guru');
        Route::get('/madrasah', [LaporanArsipAdminController::class, 'madrasah'])->name('madrasah');
    });

}); // ← tutup grup admin

// ============================================================
// PORTAL GURU
// ============================================================

// Publik (tanpa login guru)
Route::prefix('guru')->name('guru.')->group(function () {
    Route::get('/login',  [GuruAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [GuruAuthController::class, 'login'])->middleware('throttle:5,1')->name('login.post');
    Route::post('/logout',[GuruAuthController::class, 'logout'])->name('logout');
});

// Butuh login guru
Route::prefix('guru')->name('guru.')->middleware(['auth.guru', 'force.password:guru'])->group(function () {
    Route::get('/dashboard',       [GuruPortalController::class, 'dashboard'])->name('dashboard');
    Route::get('/arsip',           [GuruPortalController::class, 'arsipIndex'])->name('arsip.index');
    Route::get('/arsip/tambah',    [GuruPortalController::class, 'arsipCreate'])->name('arsip.create');
    Route::post('/arsip',          [GuruPortalController::class, 'arsipStore'])->name('arsip.store');
    Route::get('/arsip/{id}/edit', [GuruPortalController::class, 'arsipEdit'])->name('arsip.edit');
    Route::put('/arsip/{id}',      [GuruPortalController::class, 'arsipUpdate'])->name('arsip.update');
    Route::delete('/arsip/{id}',   [GuruPortalController::class, 'arsipDestroy'])->name('arsip.destroy');
    Route::get('/password',        [GuruPortalController::class, 'passwordForm'])->name('password.form');
    Route::put('/password',        [GuruPortalController::class, 'passwordUpdate'])->name('password.update');
    Route::get('/profil',          [GuruPortalController::class, 'profilForm'])->name('profil.form');
    Route::put('/profil',          [GuruPortalController::class, 'profilUpdate'])->name('profil.update');

    // Ajuan Dokumen dari Portal Guru
    Route::prefix('pengajuan')->name('pengajuan.')->group(function () {
        Route::get('/',              [GuruPengajuanController::class, 'index'])  ->name('index');
        Route::get('/riwayat',       [GuruPengajuanController::class, 'riwayat'])->name('riwayat');
        Route::get('/{id}/detail',   [GuruPengajuanController::class, 'show'])   ->name('show');
        Route::delete('/{id}/batalkan', [GuruPengajuanController::class, 'batalkan'])->name('batalkan');
        Route::get('/sukses/{kode}', [GuruPengajuanController::class, 'sukses']) ->name('sukses');
        Route::get('/{jenisId}',     [GuruPengajuanController::class, 'form'])   ->name('form');
        Route::post('/{jenisId}',    [GuruPengajuanController::class, 'store'])  ->name('store');
    });
});

// ============================================================
// PORTAL MADRASAH
// ============================================================

// Publik (tanpa login madrasah)
Route::prefix('madrasah')->name('madrasah.')->group(function () {
    Route::get('/login',  [MadrasahAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [MadrasahAuthController::class, 'login'])->middleware('throttle:5,1')->name('login.post');
    Route::post('/logout',[MadrasahAuthController::class, 'logout'])->name('logout');
});

// Butuh login madrasah
Route::prefix('madrasah')->name('madrasah.')->middleware(['auth.madrasah', 'force.password:madrasah'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [MadrasahPortalController::class, 'dashboard'])->name('dashboard');

    // Arsip Madrasah
    Route::get('/arsip',           [MadrasahPortalController::class, 'arsipIndex'])  ->name('arsip.index');
    Route::get('/arsip/tambah',    [MadrasahPortalController::class, 'arsipCreate']) ->name('arsip.create');
    Route::post('/arsip',          [MadrasahPortalController::class, 'arsipStore'])  ->name('arsip.store');
    Route::get('/arsip/{id}/edit', [MadrasahPortalController::class, 'arsipEdit'])   ->name('arsip.edit');
    Route::put('/arsip/{id}',      [MadrasahPortalController::class, 'arsipUpdate']) ->name('arsip.update');
    Route::delete('/arsip/{id}',   [MadrasahPortalController::class, 'arsipDestroy'])->name('arsip.destroy');

    // Ganti Password
    Route::get('/password', [MadrasahPortalController::class, 'passwordForm'])  ->name('password.form');
    Route::put('/password', [MadrasahPortalController::class, 'passwordUpdate'])->name('password.update');

    // Edit Profil (No HP & Email)
    Route::get('/profil', [MadrasahPortalController::class, 'profilForm'])  ->name('profil.form');
    Route::put('/profil', [MadrasahPortalController::class, 'profilUpdate'])->name('profil.update');

    // Guru Saya — monitoring read-only guru di bawah madrasah ini
    Route::prefix('guru-saya')->name('guru.')->group(function () {
        Route::get('/',     [MadrasahGuruMonitorController::class, 'index'])->name('index');
        Route::get('/{id}', [MadrasahGuruMonitorController::class, 'show']) ->name('show');
    });

    // Laporan Kelengkapan Arsip
    Route::prefix('laporan')->name('laporan.')->group(function () {
        Route::get('/arsip',  [MadrasahLaporanController::class, 'arsip']) ->name('arsip');
    });

    // Ajuan Dokumen dari Portal Madrasah
    // ⚠️ /riwayat dan /sukses/{kode} HARUS sebelum /{jenisId}
    Route::prefix('pengajuan')->name('pengajuan.')->group(function () {
        Route::get('/',              [MadrasahPengajuanController::class, 'index'])  ->name('index');
        Route::get('/riwayat',       [MadrasahPengajuanController::class, 'riwayat'])->name('riwayat');
        Route::get('/{id}/detail',   [MadrasahPengajuanController::class, 'show'])   ->name('show');
        Route::delete('/{id}/batalkan', [MadrasahPengajuanController::class, 'batalkan'])->name('batalkan');
        Route::get('/sukses/{kode}', [MadrasahPengajuanController::class, 'sukses']) ->name('sukses');
        Route::get('/{jenisId}',     [MadrasahPengajuanController::class, 'form'])   ->name('form');
        Route::post('/{jenisId}',    [MadrasahPengajuanController::class, 'store'])  ->name('store');
    });
});

// ============================================================
// AUTH (Laravel Breeze)
// ============================================================
require __DIR__ . '/auth.php';
