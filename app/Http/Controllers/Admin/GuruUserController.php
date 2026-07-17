<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GuruUser;
use App\Models\Madrasah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class GuruUserController extends Controller
{
    // ─────────────────────────────────────────
    // DAFTAR AKUN GURU
    // ─────────────────────────────────────────
    public function index(Request $request)
    {
        $query = GuruUser::with('madrasah')->withCount('arsip')->latest();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('nama',  'like', "%$s%")
                    ->orWhere('pegid', 'like', "%$s%")
                    ->orWhere('email', 'like', "%$s%");
            });
        }

        if ($request->filled('madrasah_id')) {
            $query->where('madrasah_id', $request->madrasah_id);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'aktif');
        }

        $guruUsers  = $query->paginate(20)->withQueryString();
        $madrasahs  = Madrasah::aktif()->orderBy('nama_madrasah')->get();
        $totalGuru            = GuruUser::count();
        $totalAktif           = GuruUser::where('is_active', true)->count();
        $totalPasswordDefault = GuruUser::where('password_changed', false)->count(); // ← tambah ini

        return view('admin.guru-users.index', compact(
            'guruUsers',
            'madrasahs',
            'totalGuru',
            'totalAktif',
            'totalPasswordDefault' // ← tambah di sini
        ));
    }

    // ─────────────────────────────────────────
    // FORM TAMBAH MANUAL
    // ─────────────────────────────────────────
    public function create()
    {
        $madrasahs = Madrasah::aktif()->orderBy('nama_madrasah')->get();
        return view('admin.guru-users.create', compact('madrasahs'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'pegid'       => 'required|string|max:30|unique:guru_users,pegid',
            'nama'        => 'required|string|max:255',
            'madrasah_id' => 'nullable|exists:madrasah,id',
            'email'       => 'nullable|email|max:255|unique:guru_users,email',
            'no_hp'       => 'nullable|string|max:20',
        ], [
            'pegid.required' => 'Nomor PegID wajib diisi.',
            'pegid.unique'   => 'PegID ini sudah terdaftar.',
            'nama.required'  => 'Nama guru wajib diisi.',
            'email.unique'   => 'Email ini sudah digunakan.',
        ]);

        GuruUser::create([
            'pegid'            => $validated['pegid'],
            'nama'             => $validated['nama'],
            'madrasah_id'      => $validated['madrasah_id'] ?? null,
            'email'            => $validated['email'] ?? null,
            'no_hp'            => $validated['no_hp'] ?? null,
            'password'         => Hash::make($validated['pegid']),
            'is_active'        => true,
            'password_changed' => false,
        ]);

        return redirect()->route('admin.guru-users.index')
            ->with('success', "Akun guru {$validated['nama']} berhasil ditambahkan. Password default: {$validated['pegid']}");
    }

    // ─────────────────────────────────────────
    // FORM EDIT
    // ─────────────────────────────────────────
    public function edit($id)
    {
        $guru        = GuruUser::findOrFail($id);
        $madrasahs   = Madrasah::aktif()->orderBy('nama_madrasah')->get();
        $jumlahArsip = $guru->arsip()->count(); // ← tambah ini

        return view('admin.guru-users.edit', compact('guru', 'madrasahs', 'jumlahArsip')); // ← tambah di sini
    }

    public function update(Request $request, $id)
    {
        $guru = GuruUser::findOrFail($id);

        $validated = $request->validate([
            'pegid'       => "required|string|max:30|unique:guru_users,pegid,{$id}",
            'nama'        => 'required|string|max:255',
            'madrasah_id' => 'nullable|exists:madrasah,id',
            'email'       => "nullable|email|max:255|unique:guru_users,email,{$id}",
            'no_hp'       => 'nullable|string|max:20',
            'is_active'   => 'boolean',
        ]);

        $guru->update([
            'pegid'       => $validated['pegid'],
            'nama'        => $validated['nama'],
            'madrasah_id' => $validated['madrasah_id'] ?? null,
            'email'       => $validated['email'] ?? null,
            'no_hp'       => $validated['no_hp'] ?? null,
            'is_active'   => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.guru-users.index')
            ->with('success', "Data guru {$guru->nama} berhasil diperbarui.");
    }

    // ─────────────────────────────────────────
    // TOGGLE AKTIF / NON-AKTIF
    // ─────────────────────────────────────────
    public function toggle($id)
    {
        $guru = GuruUser::findOrFail($id);
        $guru->update(['is_active' => ! $guru->is_active]);

        $status = $guru->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "Akun {$guru->nama} berhasil {$status}.");
    }

    // ─────────────────────────────────────────
    // RESET PASSWORD KE DEFAULT (pegid)
    // ─────────────────────────────────────────
    public function resetPassword($id)
    {
        $guru = GuruUser::findOrFail($id);
        $guru->update([
            'password'         => Hash::make($guru->pegid),
            'password_changed' => false,
        ]);

        return back()->with('success', "Password {$guru->nama} berhasil direset ke PegID ({$guru->pegid}).");
    }

    // ─────────────────────────────────────────
    // HAPUS AKUN GURU
    // ─────────────────────────────────────────
    public function destroy($id)
    {
        $guru = GuruUser::findOrFail($id);

        // Cek apakah punya arsip — jika ada, tolak hapus
        if ($guru->arsip()->count() > 0) {
            return back()->with('error', "Akun {$guru->nama} tidak bisa dihapus karena masih memiliki {$guru->arsip()->count()} arsip.");
        }

        $nama = $guru->nama;
        $guru->delete();

        return redirect()->route('admin.guru-users.index')
            ->with('success', "Akun guru {$nama} berhasil dihapus.");
    }

    // ─────────────────────────────────────────
    // RESET SEMUA AKUN GURU (hapus massal)
    // ─────────────────────────────────────────
    // PENTING: berbeda dari destroy() di atas, reset ini TIDAK dicek dulu
    // (memang sengaja mau bersihkan semuanya). Karena arsip_guru
    // di-set CASCADE ke guru_users di migration, hapus di sini otomatis
    // ikut menghapus SEMUA arsip milik guru-guru itu juga — bukan cuma
    // akun login-nya. Ini permanen, tidak ada undo/backup otomatis,
    // makanya di halaman konfirmasi pesannya harus jelas.
    public function reset()
    {
        $total = GuruUser::count();

        GuruUser::query()->delete();

        return redirect()->route('admin.guru-users.index')
            ->with('success', "Semua akun guru berhasil direset ({$total} akun, beserta arsipnya).");
    }

    // ─────────────────────────────────────────
    // FORM IMPORT EXCEL
    // ─────────────────────────────────────────
    public function importForm()
    {
        $madrasahs = Madrasah::aktif()->orderBy('nama_madrasah')->get();
        return view('admin.guru-users.import', compact('madrasahs'));
    }

    // ─────────────────────────────────────────
    // PROSES IMPORT EXCEL
    // ─────────────────────────────────────────
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls|max:10240',
            'mode' => 'required|in:skip,update',
        ], [
            'file.required' => 'File Excel wajib diunggah.',
            'file.mimes'    => 'Format file harus .xlsx atau .xls.',
        ]);

        // Import ribuan baris makan waktu lebih dari batas eksekusi PHP default
        // (biasanya 30-60 detik) — dinaikkan khusus untuk request ini saja.
        set_time_limit(0);

        Log::info('IMPORT GURU: mulai, request diterima.');

        try {
            Log::info('IMPORT GURU: mulai load file excel...');
            $spreadsheet = IOFactory::load($request->file('file')->getPathname());
            Log::info('IMPORT GURU: file excel berhasil di-load.');

            $sheet       = $spreadsheet->getActiveSheet();
            $rows        = $sheet->toArray(null, true, true, false);
            Log::info('IMPORT GURU: toArray selesai, jumlah baris = ' . count($rows));

            if (empty($rows) || count($rows) < 2) {
                return back()->with('error', 'File kosong atau tidak ada data (minimal 1 baris header + 1 baris data).');
            }

            // ── Mapping header (case-insensitive) ──
            $header = array_map(fn($h) => strtolower(trim($h ?? '')), $rows[0]);

            $col = [
                'pegid'       => $this->findCol($header, ['pegid', 'peg id', 'no pegid', 'nomor pegid', 'id guru']),
                'nama'        => $this->findCol($header, ['nama', 'nama guru', 'nama lengkap']),
                'nsm'         => $this->findCol($header, ['nsm', 'nomor statistik madrasah']),
                'nama_madrasah' => $this->findCol($header, ['nama madrasah', 'madrasah', 'satuan pendidikan']),
                'email'       => $this->findCol($header, ['email', 'e-mail', 'e mail']),
                'no_hp'       => $this->findCol($header, ['no hp', 'no. hp', 'hp', 'telp', 'no telepon', 'handphone']),
            ];

            if (is_null($col['pegid'])) {
                return back()->with('error', 'Kolom PegID tidak ditemukan. Pastikan ada kolom bernama "PegID" di baris pertama.');
            }
            if (is_null($col['nama'])) {
                return back()->with('error', 'Kolom Nama tidak ditemukan. Pastikan ada kolom bernama "Nama" di baris pertama.');
            }

            $inserted = 0;
            $updated  = 0;
            $skipped  = 0;
            $errors   = [];

            // Dibungkus transaction: kalau ada baris yang gagal di tengah proses
            // (misal error tak terduga), semua perubahan pada import ini dibatalkan
            // (rollback) alih-alih menyisakan data yang setengah-setengah di DB.
            DB::transaction(function () use ($rows, $col, $request, &$inserted, &$updated, &$skipped, &$errors) {

                Log::info('IMPORT GURU: masuk DB transaction, mulai preload data...');

                // ── Preload semua lookup SEKALI di awal (bukan query per baris) ──
                // Ini yang paling banyak makan waktu sebelumnya: tiap baris nge-query
                // madrasah + cek pegid + cek email satu-satu ke database (bisa sampai
                // 4 query x 3700 baris = ribuan round-trip). Sekarang cukup 3 query
                // total, sisanya dicocokkan di memori.
                $madrasahByNsm  = Madrasah::whereNotNull('nsm')->pluck('id', 'nsm');
                Log::info('IMPORT GURU: preload madrasahByNsm selesai, jumlah = ' . $madrasahByNsm->count());

                $madrasahByNama = Madrasah::pluck('id', 'nama_madrasah');
                Log::info('IMPORT GURU: preload madrasahByNama selesai, jumlah = ' . $madrasahByNama->count());

                $existingPegid  = GuruUser::pluck('id', 'pegid');
                Log::info('IMPORT GURU: preload existingPegid selesai, jumlah = ' . $existingPegid->count());

                $existingEmail  = GuruUser::whereNotNull('email')->pluck('pegid', 'email');
                Log::info('IMPORT GURU: preload existingEmail selesai, jumlah = ' . $existingEmail->count());

                $now      = now();
                $toInsert = [];

                Log::info('IMPORT GURU: mulai loop baris, total baris data = ' . (count($rows) - 1));

                foreach (array_slice($rows, 1) as $rowNum => $row) {
                    $pegid = trim($row[$col['pegid']] ?? '');
                    $nama  = trim($row[$col['nama']]  ?? '');

                    if (empty($pegid) || empty($nama)) {
                        $skipped++;
                        continue;
                    }

                    // Cari madrasah dari peta yang sudah di-preload (bukan query baru)
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
                            // Cocok persis dulu (cepat, dari memori). Kalau gak ketemu,
                            // baru fallback ke pencarian LIKE (jarang kejadian, cuma
                            // baris yang nama madrasahnya ditulis gak persis sama).
                            $madrasahId = $madrasahByNama[$nmdr]
                                ?? Madrasah::where('nama_madrasah', 'like', "%{$nmdr}%")->value('id');
                        }
                    }

                    $email = $col['email'] !== null ? (trim($row[$col['email']] ?? '') ?: null) : null;
                    $noHp  = $col['no_hp'] !== null ? (trim($row[$col['no_hp']]  ?? '') ?: null) : null;

                    // Validasi email unik dari peta yang sudah di-preload
                    if ($email && isset($existingEmail[$email]) && $existingEmail[$email] !== $pegid) {
                        $errors[] = "Baris " . ($rowNum + 2) . ": email {$email} sudah dipakai akun lain, baris dilewati.";
                        $skipped++;
                        continue;
                    }

                    if (isset($existingPegid[$pegid])) {
                        if ($request->mode === 'update') {
                            GuruUser::where('pegid', $pegid)->update([
                                'nama'        => $nama,
                                'madrasah_id' => $madrasahId ?? DB::raw('madrasah_id'),
                                'email'       => $email ?? DB::raw('email'),
                                'no_hp'       => $noHp  ?? DB::raw('no_hp'),
                                'updated_at'  => $now,
                            ]);
                            $updated++;
                        } else {
                            $skipped++; // mode skip: lewati jika sudah ada
                        }
                    } else {
                        $toInsert[] = [
                            'pegid'            => $pegid,
                            'nama'             => $nama,
                            'madrasah_id'      => $madrasahId,
                            'email'            => $email,
                            'no_hp'            => $noHp,
                            // Password default = PegID sendiri, WAJIB diganti pas login
                            // pertama (password_changed=false). Karena ini cuma password
                            // sementara yang nilainya sama dengan data institusi yang
                            // sudah diketahui (bukan rahasia asli), cost bcrypt-nya
                            // sengaja diturunkan (10 → jauh lebih cepat dari default 12)
                            // KHUSUS untuk password bawaan ini saja. Begitu guru ganti
                            // password sendiri, sistem otomatis pakai cost standar lagi
                            // (lihat GuruAuthController — tidak disentuh di sini).
                            'password'         => Hash::make($pegid, ['rounds' => 10]),
                            'is_active'        => true,
                            'password_changed' => false,
                            'created_at'       => $now,
                            'updated_at'       => $now,
                        ];
                        // Tandai di peta supaya baris duplikat DALAM FILE YANG SAMA
                        // gak ke-insert dobel.
                        $existingPegid[$pegid] = true;
                        if ($email) $existingEmail[$email] = $pegid;
                        $inserted++;
                    }
                }

                // Insert massal per 500 baris — jauh lebih cepat daripada 1 query
                // INSERT terpisah untuk tiap baris.
                Log::info('IMPORT GURU: loop selesai, siap insert. Jumlah toInsert = ' . count($toInsert));
                foreach (array_chunk($toInsert, 500) as $chunk) {
                    GuruUser::insert($chunk);
                    Log::info('IMPORT GURU: 1 chunk insert selesai, ukuran chunk = ' . count($chunk));
                }
                Log::info('IMPORT GURU: semua insert selesai. inserted=' . $inserted . ' updated=' . $updated . ' skipped=' . $skipped);
            });

            Log::info('IMPORT GURU: DB transaction selesai (commit).');

            $pesan = "Import selesai! {$inserted} akun baru ditambahkan, {$updated} diperbarui, {$skipped} dilewati.";

            return redirect()->route('admin.guru-users.index')
                ->with('success', $pesan)
                ->with('import_errors', $errors);
        } catch (\Exception $e) {
            Log::error('IMPORT GURU: GAGAL dengan exception: ' . $e->getMessage());
            return back()->with('error', 'Gagal membaca file: ' . $e->getMessage());
        }
    }

    // ─────────────────────────────────────────
    // HALAMAN & JSON STATUS IMPORT (VIA QUEUE)
    // ─────────────────────────────────────────
    // CATATAN: dua method ini disiapkan untuk alur import yang jalan lewat
    // App\Jobs\ImportGuruUsersJob (queue) + halaman polling. Route untuk
    // keduanya sudah lama terdaftar di routes/web.php, dan view
    // 'admin.guru-users.import-status' juga sudah ada, tapi method
    // controller-nya belum pernah dibuat — jadi kalau rute ini pernah
    // diakses (link lama/bookmark/typo url) bakal fatal error "method
    // does not exist".
    //
    // Method import() di atas untuk SEKARANG TETAP JALAN SEPERTI SEBELUMNYA
    // (sinkron, tidak lewat queue) — TIDAK diubah/disentuh sama sekali,
    // supaya alur yang sudah terbukti jalan tidak ikut berubah. Dua method
    // di bawah ini murni menutup rute yang bolong; baru benar-benar dipakai
    // nanti kalau import() disambungkan ke ImportGuruUsersJob::dispatch()
    // (dan sudah dipastikan worker queue aktif di server).
    public function importStatus(string $batch)
    {
        return view('admin.guru-users.import-status', ['batchId' => $batch]);
    }

    public function importStatusJson(string $batch)
    {
        $data = Cache::get("import-guru:{$batch}");

        if (! $data) {
            return response()->json(['status' => 'not_found']);
        }

        return response()->json($data);
    }

    // ─────────────────────────────────────────
    // DOWNLOAD TEMPLATE EXCEL
    // ─────────────────────────────────────────
    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Import Guru');

        // Header
        $headers = ['PegID', 'Nama', 'NSM', 'Nama Madrasah', 'Email', 'No HP'];
        foreach ($headers as $i => $h) {
            $col  = chr(65 + $i); // A, B, C, ...
            $cell = $col . '1';
            $sheet->setCellValue($cell, $h);
        }

        $sheet->getStyle('A1:F1')->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '15803D']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'FFFFFF']]],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(24);

        // Contoh data
        $contoh = [
            ['1234567890', 'Ahmad Fauzi, S.Pd.I', '111233010001', 'MI Miftahul Huda', 'ahmad@email.com', '08123456789'],
            ['0987654321', 'Siti Aminah, S.Ag',   '111233020001', 'MTs Al Hidayah',   '',                '08234567890'],
        ];
        foreach ($contoh as $i => $baris) {
            $row = $i + 2;
            foreach ($baris as $j => $val) {
                $sheet->setCellValue(chr(65 + $j) . $row, $val);
            }
            $sheet->getStyle("A{$row}:F{$row}")->applyFromArray([
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $i % 2 === 0 ? 'F0FDF4' : 'FFFFFF']],
            ]);
        }

        // Catatan
        $sheet->setCellValue('A5', '* Kolom PegID dan Nama wajib diisi.');
        $sheet->setCellValue('A6', '* NSM digunakan untuk mencocokkan data madrasah. Jika tidak ada NSM, isi Nama Madrasah.');
        $sheet->setCellValue('A7', '* Password default = PegID masing-masing guru.');
        $sheet->getStyle('A5:A7')->applyFromArray([
            'font' => ['italic' => true, 'size' => 9, 'color' => ['rgb' => '6B7280']],
        ]);

        // Lebar kolom
        foreach (['A' => 18, 'B' => 32, 'C' => 22, 'D' => 36, 'E' => 28, 'F' => 18] as $col => $w) {
            $sheet->getColumnDimension($col)->setWidth($w);
        }

        $filename = 'Template_Import_Guru.xlsx';
        $writer   = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    // ─────────────────────────────────────────
    // HELPER
    // ─────────────────────────────────────────
    private function findCol(array $header, array $candidates): ?int
    {
        foreach ($candidates as $candidate) {
            $idx = array_search(strtolower(trim($candidate)), $header);
            if ($idx !== false) return $idx;
        }
        return null;
    }
}
