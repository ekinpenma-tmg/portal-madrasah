<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Madrasah;
use App\Models\MadrasahUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;

class MadrasahUserController extends Controller
{
    // ─────────────────────────────────────────
    // INDEX
    // ─────────────────────────────────────────
    public function index(Request $request)
    {
        $query = MadrasahUser::with('madrasah')->withCount('arsip')->latest();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('nsm', 'like', "%$s%")
                  ->orWhere('nama_pic', 'like', "%$s%")
                  ->orWhereHas('madrasah', fn($m) => $m->where('nama_madrasah', 'like', "%$s%"));
            });
        }

        if ($request->filled('jenjang')) {
            $query->whereHas('madrasah', fn($q) => $q->where('jenjang', $request->jenjang));
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'aktif');
        }

        $users = $query->paginate(20)->withQueryString();

        $totalMadrasah        = MadrasahUser::count();
        $totalAktif           = MadrasahUser::where('is_active', true)->count();
        $totalPasswordDefault = MadrasahUser::where('password_changed', false)->count();

        return view('admin.madrasah-users.index', compact(
            'users', 'totalMadrasah', 'totalAktif', 'totalPasswordDefault'
        ));
    }

    // ─────────────────────────────────────────
    // CREATE
    // ─────────────────────────────────────────
    public function create()
    {
        // Hanya madrasah yang belum punya akun
        $madrasahList = Madrasah::aktif()
            ->whereDoesntHave('madrasahUser')
            ->orderBy('nama_madrasah')
            ->get();

        return view('admin.madrasah-users.create', compact('madrasahList'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'madrasah_id' => 'required|exists:madrasah,id|unique:madrasah_users,madrasah_id',
            'nama_pic'    => 'required|string|max:255',
            'email'       => 'nullable|email|max:255',
            'no_hp'       => 'nullable|string|max:20',
        ], [
            'madrasah_id.required' => 'Madrasah wajib dipilih.',
            'madrasah_id.unique'   => 'Madrasah ini sudah memiliki akun.',
            'nama_pic.required'    => 'Nama penanggung jawab wajib diisi.',
        ]);

        $madrasah = Madrasah::findOrFail($request->madrasah_id);

        MadrasahUser::create([
            'madrasah_id'      => $madrasah->id,
            'nsm'              => $madrasah->nsm,
            'nama_pic'         => $request->nama_pic,
            'email'            => $request->email,
            'no_hp'            => $request->no_hp,
            'password'         => Hash::make($madrasah->nsm), // default password = NSM
            'is_active'        => true,
            'password_changed' => false,
        ]);

        return redirect()->route('admin.madrasah-users.index')
            ->with('success', "Akun madrasah {$madrasah->nama_madrasah} berhasil dibuat.");
    }

    // ─────────────────────────────────────────
    // EDIT / UPDATE
    // ─────────────────────────────────────────
    public function edit($id)
    {
        $user        = MadrasahUser::with('madrasah')->findOrFail($id);
        $jumlahArsip = $user->arsip()->count();

        return view('admin.madrasah-users.edit', compact('user', 'jumlahArsip'));
    }

    public function update(Request $request, $id)
    {
        $user = MadrasahUser::findOrFail($id);

        $request->validate([
            'nama_pic'  => 'required|string|max:255',
            'email'     => 'nullable|email|max:255',
            'no_hp'     => 'nullable|string|max:20',
            'is_active' => 'boolean',
        ]);

        $user->update([
            'nama_pic'  => $request->nama_pic,
            'email'     => $request->email,
            'no_hp'     => $request->no_hp,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.madrasah-users.index')
            ->with('success', 'Data akun madrasah berhasil diperbarui.');
    }

    // ─────────────────────────────────────────
    // TOGGLE AKTIF
    // ─────────────────────────────────────────
    public function toggle($id)
    {
        $user = MadrasahUser::findOrFail($id);
        $user->update(['is_active' => ! $user->is_active]);

        $status = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "Akun madrasah berhasil {$status}.");
    }

    // ─────────────────────────────────────────
    // RESET PASSWORD
    // ─────────────────────────────────────────
    public function resetPassword($id)
    {
        $user = MadrasahUser::findOrFail($id);
        $user->update([
            'password'         => Hash::make($user->nsm),
            'password_changed' => false,
        ]);

        return back()->with('success', "Password direset ke NSM default ({$user->nsm}).");
    }

    // ─────────────────────────────────────────
    // HAPUS
    // ─────────────────────────────────────────
    public function destroy($id)
    {
        $user = MadrasahUser::findOrFail($id);
        $nama = $user->madrasah->nama_madrasah ?? $user->nsm;

        // Cek apakah punya arsip — jika ada, tolak hapus
        if ($user->arsip()->count() > 0) {
            return back()->with('error', "Akun {$nama} tidak bisa dihapus karena masih memiliki {$user->arsip()->count()} arsip.");
        }

        $user->delete();

        return redirect()->route('admin.madrasah-users.index')
            ->with('success', "Akun madrasah {$nama} berhasil dihapus.");
    }

    // ─────────────────────────────────────────
    // RESET SEMUA AKUN MADRASAH (hapus massal)
    // ─────────────────────────────────────────
    // PENTING: arsip_madrasah di-set CASCADE ke madrasah_users di
    // migration, jadi hapus di sini otomatis ikut menghapus SEMUA arsip
    // digital milik madrasah-madrasah itu juga — bukan cuma akun
    // login-nya. Permanen, tidak ada undo/backup otomatis.
    public function reset()
    {
        $total = MadrasahUser::count();

        MadrasahUser::query()->delete();

        return redirect()->route('admin.madrasah-users.index')
            ->with('success', "Semua akun madrasah berhasil direset ({$total} akun, beserta arsip miliknya).");
    }

    // ─────────────────────────────────────────
    // IMPORT EXCEL
    // ─────────────────────────────────────────
    public function importForm()
    {
        return view('admin.madrasah-users.import');
    }

    public function import(Request $request)
    {
        // Log paling awal SEBELUM validasi apa pun — supaya kalau nanti gagal
        // lagi, kita bisa lihat persis: file-nya beneran nyampe ke server atau
        // tidak, ukurannya berapa, dan mode apa yang dikirim. Ini kejadian yang
        // paling sering luput: kalau ukuran total request kebentur batas
        // `post_max_size`/`upload_max_filesize` di php.ini, PHP diam-diam
        // membuang SELURUH data POST (bukan cuma filenya) SEBELUM request ini
        // sempat dieksekusi sama sekali — dari sisi Laravel kelihatannya cuma
        // seperti "file wajib diunggah" tanpa penyebab yang jelas.
        Log::info('IMPORT AKUN MADRASAH: request masuk. hasFile=' . ($request->hasFile('file') ? 'ya' : 'TIDAK ADA')
            . ', mode=' . ($request->input('mode') ?? '(kosong)')
            . ', ukuran file=' . ($request->hasFile('file') ? $request->file('file')->getSize() . ' bytes' : '-'));

        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls|max:10240',
            'mode' => 'required|in:skip,update',
        ], [
            'file.required' => 'File Excel wajib diunggah.',
            'file.mimes'    => 'Format file harus .xlsx atau .xls.',
            'file.max'      => 'Ukuran file maksimal 10 MB.',
        ]);

        // Import ribuan baris makan waktu lebih dari batas eksekusi PHP default
        // (biasanya 30-60 detik) — dinaikkan khusus untuk request ini saja.
        set_time_limit(0);

        Log::info('IMPORT AKUN MADRASAH: validasi lolos, mulai proses.');

        try {
            Log::info('IMPORT AKUN MADRASAH: mulai load file excel...');
            $spreadsheet = IOFactory::load($request->file('file')->getRealPath());
            Log::info('IMPORT AKUN MADRASAH: file excel berhasil di-load.');

            $sheet = $spreadsheet->getActiveSheet();
            $rows  = $sheet->toArray(null, true, true, false);
            Log::info('IMPORT AKUN MADRASAH: toArray selesai, jumlah baris = ' . count($rows));

            if (empty($rows) || count($rows) < 2) {
                return back()->with('error', 'File kosong atau tidak ada data (minimal 1 baris header + 1 baris data).');
            }

            // ── Mapping header (case-insensitive, TIDAK bergantung urutan kolom) ──
            // Sebelumnya kode ini baca kolom by posisi huruf tetap (A/B/C/D), jadi
            // kalau file Excel yang diupload urutan/nama kolomnya beda sedikit saja
            // dari template (misal ada kolom nomor urut di depan), semua baris gagal
            // dicocokkan secara diam-diam dan hasil akhirnya "0 akun ke-import" tanpa
            // pesan error yang jelas. Sekarang kolom dicari berdasarkan NAMA header,
            // sama seperti cara import akun guru.
            $header = array_map(fn($h) => strtolower(trim($h ?? '')), $rows[0]);

            $col = [
                'nsm'      => $this->findCol($header, ['nsm', 'nomor statistik madrasah']),
                'nama_pic' => $this->findCol($header, ['nama pic', 'nama pic / penanggung jawab', 'penanggung jawab', 'nama penanggung jawab', 'pic']),
                'email'    => $this->findCol($header, ['email', 'e-mail', 'e mail', 'email (opsional)']),
                'no_hp'    => $this->findCol($header, ['no hp', 'no. hp', 'hp', 'telp', 'no telepon', 'handphone', 'no hp (opsional)']),
            ];

            if (is_null($col['nsm'])) {
                return back()->with('error', 'Kolom NSM tidak ditemukan. Pastikan ada kolom bernama "NSM" di baris pertama.');
            }
            if (is_null($col['nama_pic'])) {
                return back()->with('error', 'Kolom Nama PIC tidak ditemukan. Pastikan ada kolom bernama "Nama PIC" di baris pertama.');
            }

            $berhasil   = 0;
            $diperbarui = 0;
            $lewati     = 0;
            $errors     = [];

            DB::transaction(function () use ($rows, $col, $request, &$berhasil, &$diperbarui, &$lewati, &$errors) {

                Log::info('IMPORT AKUN MADRASAH: masuk DB transaction, mulai preload data...');

                // ── Preload semua lookup SEKALI di awal (bukan query per baris) ──
                $madrasahByNsm   = Madrasah::whereNotNull('nsm')->pluck('id', 'nsm');
                Log::info('IMPORT AKUN MADRASAH: preload madrasahByNsm selesai, jumlah = ' . $madrasahByNsm->count());

                $existingNsmUser = MadrasahUser::pluck('id', 'nsm');
                Log::info('IMPORT AKUN MADRASAH: preload existingNsmUser selesai, jumlah = ' . $existingNsmUser->count());

                $now      = now();
                $toInsert = [];

                Log::info('IMPORT AKUN MADRASAH: mulai loop baris, total baris data = ' . (count($rows) - 1));

                foreach (array_slice($rows, 1) as $rowNum => $row) {
                    // NSM di Excel kadang tersimpan sebagai angka (bukan teks), jadi
                    // dibaca PhpSpreadsheet sebagai number/float. Di-cast eksplisit ke
                    // string dulu supaya tidak berubah jadi notasi ilmiah (mis. NSM 12
                    // digit jadi "1.2123312E+11") sebelum di-trim.
                    $nsmRaw = $row[$col['nsm']] ?? '';
                    $nsm    = is_numeric($nsmRaw) ? sprintf('%.0f', $nsmRaw) : trim((string) $nsmRaw);

                    $namaPic = trim($row[$col['nama_pic']] ?? '');
                    $email   = $col['email'] !== null ? (trim($row[$col['email']] ?? '') ?: null) : null;
                    $noHp    = $col['no_hp'] !== null ? (trim($row[$col['no_hp']]  ?? '') ?: null) : null;

                    if (! $nsm || ! $namaPic) {
                        $lewati++;
                        continue;
                    }

                    // Cari madrasah dari peta yang sudah di-preload (bukan query baru)
                    if (! isset($madrasahByNsm[$nsm])) {
                        $errors[] = "Baris " . ($rowNum + 2) . ": NSM {$nsm} tidak ditemukan di data madrasah.";
                        $lewati++;
                        continue;
                    }

                    // Kalau akun sudah ada: mode "update" perbarui data PIC/email/HP
                    // (password & status aktif TIDAK disentuh), mode "skip" lewati
                    // sama sekali seperti perilaku sebelumnya.
                    if (isset($existingNsmUser[$nsm])) {
                        if ($request->mode === 'update') {
                            MadrasahUser::where('nsm', $nsm)->update([
                                'nama_pic'   => $namaPic,
                                'email'      => $email ?? DB::raw('email'),
                                'no_hp'      => $noHp  ?? DB::raw('no_hp'),
                                'updated_at' => $now,
                            ]);
                            $diperbarui++;
                        } else {
                            $lewati++;
                        }
                        continue;
                    }

                    $toInsert[] = [
                        'madrasah_id'      => $madrasahByNsm[$nsm],
                        'nsm'              => $nsm,
                        'nama_pic'         => $namaPic,
                        'email'            => $email,
                        'no_hp'            => $noHp,
                        // Password default = NSM (bukan rahasia asli, sudah publik di
                        // data madrasah), jadi cost bcrypt diturunkan khusus di sini
                        // biar hashing banyak baris gak makan waktu lama. Password akan
                        // pakai cost standar begitu PIC madrasah menggantinya sendiri.
                        'password'         => Hash::make($nsm, ['rounds' => 10]),
                        'is_active'        => true,
                        'password_changed' => false,
                        'created_at'       => $now,
                        'updated_at'       => $now,
                    ];

                    // Tandai supaya baris duplikat NSM dalam file yang sama gak dobel
                    $existingNsmUser[$nsm] = true;
                    $berhasil++;
                }

                // Insert massal per 500 baris, bukan 1 query INSERT per baris
                Log::info('IMPORT AKUN MADRASAH: loop selesai, siap insert. Jumlah toInsert = ' . count($toInsert));
                foreach (array_chunk($toInsert, 500) as $chunk) {
                    MadrasahUser::insert($chunk);
                    Log::info('IMPORT AKUN MADRASAH: 1 chunk insert selesai, ukuran chunk = ' . count($chunk));
                }
                Log::info('IMPORT AKUN MADRASAH: semua insert selesai. berhasil=' . $berhasil . ' diperbarui=' . $diperbarui . ' lewati=' . $lewati);
            });

            Log::info('IMPORT AKUN MADRASAH: DB transaction selesai (commit).');
            if (! empty($errors)) {
                Log::info('IMPORT AKUN MADRASAH: contoh baris gagal cocok NSM: ' . implode(' | ', array_slice($errors, 0, 10)));
            }

            $pesan = "Import selesai: {$berhasil} akun baru dibuat" . ($diperbarui ? ", {$diperbarui} diperbarui" : '') . ", {$lewati} baris dilewati.";

            return redirect()->route('admin.madrasah-users.index')
                ->with('success', $pesan)
                ->with('import_errors', $errors);

        } catch (\Throwable $e) {
            // \Throwable (bukan cuma \Exception) supaya TypeError/Error internal
            // PHP juga ikut ketangkap di sini, bukan jadi error 500 mentah yang
            // gak jelas penyebabnya buat user.
            Log::error('IMPORT AKUN MADRASAH: GAGAL — ' . get_class($e) . ': ' . $e->getMessage()
                . ' (file: ' . $e->getFile() . ':' . $e->getLine() . ')');
            Log::error($e->getTraceAsString());
            return back()->with('error', 'Gagal memproses file Excel: ' . $e->getMessage());
        }
    }

    public function downloadTemplate()
    {
        // Buat template Excel sederhana
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'NSM');
        $sheet->setCellValue('B1', 'Nama PIC / Penanggung Jawab');
        $sheet->setCellValue('C1', 'Email (opsional)');
        $sheet->setCellValue('D1', 'No HP (opsional)');

        // Contoh data
        $sheet->setCellValue('A2', '121233120001');
        $sheet->setCellValue('B2', 'Nama Kepala Madrasah');
        $sheet->setCellValue('C2', 'email@madrasah.sch.id');
        $sheet->setCellValue('D2', '081234567890');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        $filename = 'template_import_akun_madrasah.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment;filename=\"{$filename}\"");
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
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
