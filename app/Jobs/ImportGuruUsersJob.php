<?php

namespace App\Jobs;

use App\Models\GuruUser;
use App\Models\Madrasah;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;

/**
 * Import akun guru dari Excel — dijalankan di QUEUE (background), bukan
 * langsung di dalam request HTTP.
 *
 * KENAPA DIPINDAH KE JOB:
 * Untuk file kecil (ratusan baris) proses sinkron di controller masih oke.
 * Tapi untuk 3000+ baris, waktu proses (baca file + hash password + insert)
 * bisa sampai beberapa menit. Selama itu request HTTP menggantung, dan di
 * banyak server (Nginx/Apache/PHP-FPM/LiteSpeed) ada batas waktu request
 * sendiri (umumnya 60-120 detik) yang TIDAK bisa dihilangkan lewat
 * set_time_limit(0) di kode PHP — itu cuma menghapus limit di sisi PHP,
 * bukan di sisi web server/proxy. Begitu limit itu tercapai, koneksi
 * diputus paksa, browser tetap "memuat" tanpa pesan jelas, dan admin
 * tidak pernah tahu prosesnya sebenarnya berhasil/gagal/di mana macetnya.
 *
 * Dengan job ini, request upload cuma menyimpan file + mendaftarkan
 * pekerjaan ke antrian (hitungan detik), lalu browser diarahkan ke
 * halaman status yang polling progres. Proses berat berjalan di worker
 * terpisah (`php artisan queue:work`) yang tidak kena batas waktu request.
 *
 * SYARAT DEPLOY: worker queue harus berjalan di server (mis. via Supervisor
 * menjalankan `php artisan queue:work`, atau cron `queue:work --stop-when-empty`
 * tiap menit kalau hosting tidak mendukung proses yang selalu hidup).
 * Kalau QUEUE_CONNECTION di .env = "sync", job ini akan tetap jalan
 * langsung di request (sama seperti sebelumnya) — pastikan diset ke
 * "database" (sudah default di config/queue.php) supaya benar-benar async.
 */
class ImportGuruUsersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Batas waktu job di level queue (detik). Beda dengan batas waktu
     * request HTTP — ini aman dinaikkan tinggi karena jalan di worker.
     */
    public int $timeout = 3600;

    public int $tries = 1;

    public function __construct(
        public string $batchId,
        public string $filePath, // path relatif di disk 'local' (storage/app/private)
        public string $mode,     // 'skip' | 'update'
    ) {
    }

    public function handle(): void
    {
        $cacheKey = $this->cacheKey();
        Cache::put($cacheKey, ['status' => 'processing', 'progress' => 0, 'total' => 0], now()->addHours(6));

        try {
            $fullPath    = Storage::disk('local')->path($this->filePath);
            $spreadsheet = IOFactory::load($fullPath);
            $sheet       = $spreadsheet->getActiveSheet();
            $rows        = $sheet->toArray(null, true, true, false);

            if (empty($rows) || count($rows) < 2) {
                Cache::put($cacheKey, [
                    'status' => 'error',
                    'message' => 'File kosong atau tidak ada data (minimal 1 baris header + 1 baris data).',
                ], now()->addHours(6));
                return;
            }

            $header = array_map(fn ($h) => strtolower(trim($h ?? '')), $rows[0]);
            $col = [
                'pegid'         => $this->findCol($header, ['pegid', 'peg id', 'no pegid', 'nomor pegid', 'id guru']),
                'nama'          => $this->findCol($header, ['nama', 'nama guru', 'nama lengkap']),
                'nsm'           => $this->findCol($header, ['nsm', 'nomor statistik madrasah']),
                'nama_madrasah' => $this->findCol($header, ['nama madrasah', 'madrasah', 'satuan pendidikan']),
                'email'         => $this->findCol($header, ['email', 'e-mail', 'e mail']),
                'no_hp'         => $this->findCol($header, ['no hp', 'no. hp', 'hp', 'telp', 'no telepon', 'handphone']),
            ];

            if (is_null($col['pegid'])) {
                Cache::put($cacheKey, ['status' => 'error', 'message' => 'Kolom PegID tidak ditemukan. Pastikan ada kolom bernama "PegID" di baris pertama.'], now()->addHours(6));
                return;
            }
            if (is_null($col['nama'])) {
                Cache::put($cacheKey, ['status' => 'error', 'message' => 'Kolom Nama tidak ditemukan. Pastikan ada kolom bernama "Nama" di baris pertama.'], now()->addHours(6));
                return;
            }

            $dataRows = array_slice($rows, 1);
            $total    = count($dataRows);

            $inserted = 0;
            $updated  = 0;
            $skipped  = 0;
            $errors   = [];

            Cache::put($cacheKey, ['status' => 'processing', 'progress' => 0, 'total' => $total], now()->addHours(6));

            DB::transaction(function () use ($dataRows, $col, $cacheKey, $total, &$inserted, &$updated, &$skipped, &$errors) {
                // Preload semua lookup SEKALI di awal (bukan query per baris).
                $madrasahByNsm  = Madrasah::whereNotNull('nsm')->pluck('id', 'nsm');
                $madrasahByNama = Madrasah::pluck('id', 'nama_madrasah');
                $existingPegid  = GuruUser::pluck('id', 'pegid');
                $existingEmail  = GuruUser::whereNotNull('email')->pluck('pegid', 'email');

                $now      = now();
                $toInsert = [];

                foreach ($dataRows as $rowNum => $row) {
                    $pegid = trim($row[$col['pegid']] ?? '');
                    $nama  = trim($row[$col['nama']]  ?? '');

                    if (empty($pegid) || empty($nama)) {
                        $skipped++;
                    } else {
                        $madrasahId = null;
                        if ($col['nsm'] !== null) {
                            $nsm = trim($row[$col['nsm']] ?? '');
                            if ($nsm && isset($madrasahByNsm[$nsm])) {
                                $madrasahId = $madrasahByNsm[$nsm];
                            }
                        }
                        if (! $madrasahId && $col['nama_madrasah'] !== null) {
                            $nmdr = trim($row[$col['nama_madrasah']] ?? '');
                            if ($nmdr) {
                                $madrasahId = $madrasahByNama[$nmdr]
                                    ?? Madrasah::where('nama_madrasah', 'like', "%{$nmdr}%")->value('id');
                            }
                        }

                        $email = $col['email'] !== null ? (trim($row[$col['email']] ?? '') ?: null) : null;
                        $noHp  = $col['no_hp'] !== null ? (trim($row[$col['no_hp']]  ?? '') ?: null) : null;

                        if ($email && isset($existingEmail[$email]) && $existingEmail[$email] !== $pegid) {
                            $errors[] = "Baris " . ($rowNum + 2) . ": email {$email} sudah dipakai akun lain, baris dilewati.";
                            $skipped++;
                        } elseif (isset($existingPegid[$pegid])) {
                            if ($this->mode === 'update') {
                                GuruUser::where('pegid', $pegid)->update([
                                    'nama'        => $nama,
                                    'madrasah_id' => $madrasahId ?? DB::raw('madrasah_id'),
                                    'email'       => $email ?? DB::raw('email'),
                                    'no_hp'       => $noHp  ?? DB::raw('no_hp'),
                                    'updated_at'  => $now,
                                ]);
                                $updated++;
                            } else {
                                $skipped++;
                            }
                        } else {
                            $toInsert[] = [
                                'pegid'            => $pegid,
                                'nama'             => $nama,
                                'madrasah_id'      => $madrasahId,
                                'email'            => $email,
                                'no_hp'            => $noHp,
                                // Password default = PegID sendiri, WAJIB diganti pas login
                                // pertama (password_changed=false). Cost bcrypt diturunkan
                                // khusus password bawaan ini (bukan rahasia asli).
                                'password'         => Hash::make($pegid, ['rounds' => 10]),
                                'is_active'        => true,
                                'password_changed' => false,
                                'created_at'       => $now,
                                'updated_at'       => $now,
                            ];
                            $existingPegid[$pegid] = true;
                            if ($email) {
                                $existingEmail[$email] = $pegid;
                            }
                            $inserted++;
                        }
                    }

                    // Update progres tiap 200 baris — cukup sering untuk terasa
                    // "hidup" di halaman status, tapi tidak membebani cache.
                    if (($rowNum + 1) % 200 === 0) {
                        Cache::put($cacheKey, ['status' => 'processing', 'progress' => $rowNum + 1, 'total' => $total], now()->addHours(6));
                    }
                }

                // Insert massal per 500 baris.
                foreach (array_chunk($toInsert, 500) as $chunk) {
                    GuruUser::insert($chunk);
                }
            });

            Cache::put($cacheKey, [
                'status'   => 'done',
                'progress' => $total,
                'total'    => $total,
                'inserted' => $inserted,
                'updated'  => $updated,
                'skipped'  => $skipped,
                'errors'   => array_slice($errors, 0, 50),
            ], now()->addHours(6));
        } catch (\Throwable $e) {
            Cache::put($cacheKey, [
                'status'  => 'error',
                'message' => 'Gagal memproses file: ' . $e->getMessage(),
            ], now()->addHours(6));
        } finally {
            // File sumber sudah tidak dibutuhkan lagi setelah diproses.
            Storage::disk('local')->delete($this->filePath);
        }
    }

    public function cacheKey(): string
    {
        return "import-guru:{$this->batchId}";
    }

    private function findCol(array $header, array $candidates): ?int
    {
        foreach ($candidates as $candidate) {
            $idx = array_search(strtolower(trim($candidate)), $header);
            if ($idx !== false) {
                return $idx;
            }
        }
        return null;
    }
}
