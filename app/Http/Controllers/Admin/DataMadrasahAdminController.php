<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Madrasah;
use App\Models\SiswaMadrasah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class DataMadrasahAdminController extends Controller
{
    public function index(Request $request)
    {
        $query = Madrasah::with('siswaLatest');

        if ($request->jenjang) $query->where('jenjang', $request->jenjang);
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('nama_madrasah', 'like', '%' . $request->search . '%')
                  ->orWhere('nsm', 'like', '%' . $request->search . '%');
            });
        }

        $madrasah       = $query->orderBy('jenjang')->orderBy('nama_madrasah')->paginate(25)->withQueryString();
        $totalPerJenjang = Madrasah::rekapJenjang();
        $totalSemua     = Madrasah::count();
        $tahunData      = Madrasah::max('tahun_data');
        $tahunPelajaran = SiswaMadrasah::tahunTerbaru();

        return view('admin.data-madrasah.index', compact(
            'madrasah', 'totalPerJenjang', 'totalSemua', 'tahunData', 'tahunPelajaran'
        ));
    }

    // ─── Import Excel Madrasah ────────────────────────────────

    public function importMadrasahForm()
    {
        return view('admin.data-madrasah.import-madrasah');
    }

    // ─────────────────────────────────────────
    // DOWNLOAD TEMPLATE EXCEL — IMPORT DATA MADRASAH
    // ─────────────────────────────────────────
    public function downloadTemplateMadrasah()
    {
        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Data Madrasah');

        $headers = ['NSM', 'NPSN', 'Nama Madrasah', 'Jenjang', 'Status', 'Kecamatan', 'Alamat', 'Akreditasi', 'Nama Kepala'];
        foreach ($headers as $i => $h) {
            $sheet->setCellValue($this->colLetter($i) . '1', $h);
        }

        $lastCol = $this->colLetter(count($headers) - 1);
        $sheet->getStyle("A1:{$lastCol}1")->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '18181B']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'FFFFFF']]],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(28);

        $contoh = ['131235010001', '20123456', 'MI Contoh Teladan', 'MI', 'Swasta', 'Kecamatan Contoh', 'Jl. Contoh No. 1', 'A', 'Nama Kepala Madrasah'];
        foreach ($contoh as $j => $val) {
            $sheet->setCellValue($this->colLetter($j) . '2', $val);
        }

        $catatan = [
            '* Kolom NSM dan Nama Madrasah wajib diisi — dipakai mencocokkan data madrasah.',
            '* Kolom lain (NPSN, Jenjang, Status, Kecamatan, Alamat, Akreditasi, Nama Kepala) bersifat opsional.',
        ];
        foreach ($catatan as $i => $teks) {
            $sheet->setCellValue('A' . (4 + $i), $teks);
        }
        $sheet->getStyle('A4:A5')->applyFromArray([
            'font' => ['italic' => true, 'size' => 9, 'color' => ['rgb' => '6B7280']],
        ]);

        $widths = [16, 12, 28, 10, 10, 18, 28, 10, 22];
        foreach ($widths as $i => $w) {
            $sheet->getColumnDimension($this->colLetter($i))->setWidth($w);
        }

        $filename = 'Template_Import_Data_Madrasah.xlsx';
        $writer   = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function importMadrasah(Request $request)
    {
        $request->validate([
            'file'       => 'required|file|mimes:xlsx,xls|max:10240',
            'tahun_data' => 'required|string|max:10',
            'mode'       => 'required|in:replace,append',
        ], [
            'file.required'       => 'File Excel wajib diunggah.',
            'file.mimes'          => 'Format file harus .xlsx atau .xls.',
            'tahun_data.required' => 'Tahun data wajib diisi.',
        ]);

        set_time_limit(0);

        try {
            $spreadsheet = IOFactory::load($request->file('file')->getPathname());
            $sheet       = $spreadsheet->getActiveSheet();
            $rows        = $sheet->toArray(null, true, true, false);

            if (empty($rows) || count($rows) < 2) {
                return back()->with('error', 'File kosong atau tidak ada data.');
            }

            // Mapping header (baris pertama)
            $header = array_map('trim', $rows[0]);
            $map = [
                'nsm'           => $this->findCol($header, ['NSM']),
                'npsn'          => $this->findCol($header, ['NPSN']),
                'nama_madrasah' => $this->findCol($header, ['Nama Madrasah', 'NAMA MADRASAH']),
                'jenjang'       => $this->findCol($header, ['Jenjang', 'JENJANG']),
                'status'        => $this->findCol($header, ['Status', 'STATUS']),
                'kecamatan'     => $this->findCol($header, ['Kecamatan', 'KECAMATAN']),
                'alamat'        => $this->findCol($header, ['Alamat', 'ALAMAT']),
                'akreditasi'    => $this->findCol($header, ['Akreditasi', 'AKREDITASI']),
                'nama_kepala'   => $this->findCol($header, ['Nama Kepala', 'NAMA KEPALA', 'Kepala Madrasah']),
            ];

            if (is_null($map['nsm']) || is_null($map['nama_madrasah'])) {
                return back()->with('error', 'Kolom NSM atau Nama Madrasah tidak ditemukan. Pastikan format file sesuai template.');
            }

            $inserted      = 0;
            $skipped       = 0;
            $dinonaktifkan = 0;

            // Dibungkus transaction: kalau di tengah proses import ada baris yang
            // gagal, seluruh proses di-rollback, data lama TIDAK ikut berubah /
            // tidak menyisakan data setengah jadi.
            DB::transaction(function () use ($request, $rows, $map, &$inserted, &$skipped, &$dinonaktifkan) {

                $now     = now();
                $batch   = [];
                $nsmSeen = [];

                foreach (array_slice($rows, 1) as $row) {
                    $nsm = trim($row[$map['nsm']] ?? '');
                    if (empty($nsm)) continue;

                    $namaMadrasah = trim($row[$map['nama_madrasah']] ?? '');
                    if (empty($namaMadrasah)) { $skipped++; continue; }

                    // Kalau NSM sama muncul dua kali dalam file yang sama, baris
                    // terakhir yang menang (baris sebelumnya ditimpa di dalam batch).
                    $nsmSeen[$nsm] = true;

                    $batch[$nsm] = [
                        'nsm'           => $nsm,
                        'npsn'          => $map['npsn'] !== null ? trim($row[$map['npsn']] ?? '') : null,
                        'nama_madrasah' => $namaMadrasah,
                        'jenjang'       => $this->normalizeJenjang($map['jenjang'] !== null ? $row[$map['jenjang']] : ''),
                        'status'        => $this->normalizeStatus($map['status'] !== null ? $row[$map['status']] : ''),
                        'kecamatan'     => $map['kecamatan'] !== null ? trim($row[$map['kecamatan']] ?? '') : '',
                        'alamat'        => $map['alamat'] !== null ? trim($row[$map['alamat']] ?? '') : null,
                        'akreditasi'    => $map['akreditasi'] !== null ? trim($row[$map['akreditasi']] ?? '') : null,
                        'nama_kepala'   => $map['nama_kepala'] !== null ? trim($row[$map['nama_kepala']] ?? '') : null,
                        'tahun_data'    => $request->tahun_data,
                        'is_active'     => true,
                        'created_at'    => $now,
                        'updated_at'    => $now,
                    ];
                }

                $inserted = count($batch);

                // Upsert massal per 500 baris — 1 query per 500 baris, bukan 2 query
                // (SELECT + INSERT/UPDATE) per baris seperti updateOrCreate sebelumnya.
                // Dicocokkan lewat kolom 'nsm' (unik): kalau NSM sudah ada, baris
                // ditimpa (update); kalau belum ada, ditambahkan baru (insert).
                foreach (array_chunk(array_values($batch), 500) as $chunk) {
                    Madrasah::upsert(
                        $chunk,
                        ['nsm'],
                        ['npsn', 'nama_madrasah', 'jenjang', 'status', 'kecamatan', 'alamat', 'akreditasi', 'nama_kepala', 'tahun_data', 'is_active', 'updated_at']
                    );
                }

                // Mode "replace": madrasah lama yang NSM-nya TIDAK ada di file baru
                // dianggap sudah tidak berlaku lagi untuk tahun data ini — cukup
                // DINONAKTIFKAN (is_active = false), BUKAN dihapus dari database.
                //
                // PENTING — kenapa bukan Madrasah::query()->delete() seperti
                // sebelumnya: tabel `madrasah` adalah induk dari banyak tabel lain
                // yang di-cascade lewat foreign key (madrasah_users, arsip_madrasah,
                // siswa_madrasah). Menghapus baris madrasah
                // otomatis ikut memusnahkan PERMANEN semua akun PIC madrasah,
                // dan arsip digital sekolah tsb — dan karena
                // baris yang di-upsert ulang dapat id BARU, data yang sudah
                // ke-cascade-delete itu TETAP HILANG walau NSM-nya sama persis.
                // Nonaktifkan jauh lebih aman: madrasah yang memang sudah tidak ada
                // di data terbaru tidak muncul lagi di daftar aktif, tapi seluruh
                // riwayatnya tetap utuh (bisa diaktifkan lagi manual kalau ternyata
                // cuma typo NSM di file impor).
                //
                // Guard `count($batch) > 0`: kalau file ternyata tidak menghasilkan
                // satu pun baris valid (misal semua baris salah format), jangan
                // sampai SEMUA madrasah ikut dinonaktifkan gara-gara whereNotIn
                // kosong dianggap "tidak ada satupun yang cocok".
                if ($request->mode === 'replace' && count($batch) > 0) {
                    $dinonaktifkan = Madrasah::whereNotIn('nsm', array_keys($batch))
                        ->where('is_active', true)
                        ->update(['is_active' => false]);
                }
            });

            $pesan = "Import berhasil! {$inserted} madrasah diproses, {$skipped} baris dilewati";
            $pesan .= $dinonaktifkan
                ? ", {$dinonaktifkan} madrasah lama dinonaktifkan (data & riwayatnya tetap aman, tidak dihapus)."
                : '.';

            return redirect()->route('admin.data-madrasah.index')
                ->with('success', $pesan);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal membaca file: ' . $e->getMessage());
        }
    }

    // ─── Import Excel Siswa ───────────────────────────────────

    public function importSiswaForm()
    {
        return view('admin.data-madrasah.import-siswa');
    }

    // ─────────────────────────────────────────
    // DOWNLOAD TEMPLATE EXCEL — IMPORT DATA SISWA
    // ─────────────────────────────────────────
    public function downloadTemplateSiswa()
    {
        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Data Siswa');

        $headers = ['NSM', 'Nama Lembaga', 'Siswa Laki-laki', 'Siswa Perempuan', 'Total Siswa'];
        foreach ($headers as $i => $h) {
            $sheet->setCellValue($this->colLetter($i) . '1', $h);
        }

        $lastCol = $this->colLetter(count($headers) - 1);
        $sheet->getStyle("A1:{$lastCol}1")->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '18181B']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'FFFFFF']]],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(28);

        $contoh = ['131235010001', 'MI Contoh Teladan', 45, 50, 95];
        foreach ($contoh as $j => $val) {
            $sheet->setCellValue($this->colLetter($j) . '2', $val);
        }

        $catatan = [
            '* Kolom NSM wajib diisi — dipakai mencocokkan data madrasah (harus sudah ada di Data Madrasah).',
            '* Nama Lembaga bersifat opsional, hanya untuk referensi.',
            '* Total Siswa opsional — kalau dikosongkan akan dihitung otomatis dari Laki-laki + Perempuan.',
        ];
        foreach ($catatan as $i => $teks) {
            $sheet->setCellValue('A' . (4 + $i), $teks);
        }
        $sheet->getStyle('A4:A6')->applyFromArray([
            'font' => ['italic' => true, 'size' => 9, 'color' => ['rgb' => '6B7280']],
        ]);

        $widths = [16, 28, 15, 15, 12];
        foreach ($widths as $i => $w) {
            $sheet->getColumnDimension($this->colLetter($i))->setWidth($w);
        }

        $filename = 'Template_Import_Data_Siswa.xlsx';
        $writer   = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function importSiswa(Request $request)
    {
        $request->validate([
            'file'            => 'required|file|mimes:xlsx,xls|max:10240',
            'tahun_pelajaran' => 'required|string|max:10',
            'mode'            => 'required|in:skip,update',
        ], [
            'file.required'            => 'File Excel wajib diunggah.',
            'tahun_pelajaran.required' => 'Tahun pelajaran wajib diisi.',
        ]);

        set_time_limit(0);

        try {
            $spreadsheet = IOFactory::load($request->file('file')->getPathname());
            $sheet       = $spreadsheet->getActiveSheet();
            $rows        = $sheet->toArray(null, true, true, false);

            if (empty($rows) || count($rows) < 2) {
                return back()->with('error', 'File kosong atau tidak ada data.');
            }

            $header = array_map('trim', $rows[0]);
            $map = [
                'nsm'             => $this->findCol($header, ['NSM']),
                'siswa_laki'      => $this->findCol($header, ['Siswa Laki-laki', 'Laki-laki', 'L']),
                'siswa_perempuan' => $this->findCol($header, ['Siswa Perempuan', 'Perempuan', 'P']),
                'total_siswa'     => $this->findCol($header, ['Total Siswa', 'Total', 'Jumlah']),
            ];

            if (is_null($map['nsm'])) {
                return back()->with('error', 'Kolom NSM tidak ditemukan.');
            }

            $inserted = 0;
            $diperbarui = 0;
            $dilewati = 0;
            $notFound = 0;

            DB::transaction(function () use ($request, $rows, $map, &$inserted, &$diperbarui, &$dilewati, &$notFound) {
                // Preload seluruh madrasah sekali di awal (bukan query per baris —
                // pola yang sama seperti import guru/madrasah user).
                $madrasahByNsm = Madrasah::whereNotNull('nsm')->pluck('id', 'nsm');

                // Mode "skip": preload madrasah_id yang sudah punya data siswa untuk
                // tahun_pelajaran ini, supaya pengecekan dilakukan dari memori
                // (bukan query per baris).
                $existingMadrasahId = $request->mode === 'skip'
                    ? SiswaMadrasah::where('tahun_pelajaran', $request->tahun_pelajaran)
                        ->pluck('madrasah_id')->flip()
                    : collect();

                foreach (array_slice($rows, 1) as $row) {
                    $nsm = trim($row[$map['nsm']] ?? '');
                    if (empty($nsm)) continue;

                    if (! isset($madrasahByNsm[$nsm])) { $notFound++; continue; }

                    $madrasahId = $madrasahByNsm[$nsm];

                    if ($request->mode === 'skip' && $existingMadrasahId->has($madrasahId)) {
                        $dilewati++;
                        continue;
                    }

                    $laki       = intval($map['siswa_laki'] !== null ? ($row[$map['siswa_laki']] ?? 0) : 0);
                    $perempuan  = intval($map['siswa_perempuan'] !== null ? ($row[$map['siswa_perempuan']] ?? 0) : 0);
                    $total      = $map['total_siswa'] !== null ? intval($row[$map['total_siswa']] ?? 0) : ($laki + $perempuan);

                    $sudahAda = SiswaMadrasah::where('madrasah_id', $madrasahId)
                        ->where('tahun_pelajaran', $request->tahun_pelajaran)->exists();

                    SiswaMadrasah::updateOrCreate(
                        ['madrasah_id' => $madrasahId, 'tahun_pelajaran' => $request->tahun_pelajaran],
                        [
                            'nsm'             => $nsm,
                            'siswa_laki'      => $laki,
                            'siswa_perempuan' => $perempuan,
                            'total_siswa'     => $total,
                        ]
                    );

                    $sudahAda ? $diperbarui++ : $inserted++;

                    // Tandai supaya baris duplikat NSM dalam file yang sama, waktu
                    // mode "skip", baris keduanya ikut dilewati juga.
                    if ($request->mode === 'skip') {
                        $existingMadrasahId->put($madrasahId, true);
                    }
                }
            });

            $msg = "Import siswa selesai: {$inserted} data baru";
            if ($diperbarui) $msg .= ", {$diperbarui} diperbarui";
            if ($dilewati)   $msg .= ", {$dilewati} dilewati (sudah ada)";
            $msg .= ", {$notFound} NSM tidak ditemukan di data madrasah.";

            return redirect()->route('admin.data-madrasah.index')->with('success', $msg);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal membaca file: ' . $e->getMessage());
        }
    }

    // ─── Toggle aktif/nonaktif ────────────────────────────────

    public function toggle(int $id)
    {
        $madrasah = Madrasah::findOrFail($id);
        $madrasah->update(['is_active' => !$madrasah->is_active]);

        return back()->with('success', 'Status madrasah berhasil diubah.');
    }

    // ─── Hapus semua data (reset) ─────────────────────────────

    public function reset()
    {
        SiswaMadrasah::query()->delete();
        Madrasah::query()->delete();

        return redirect()->route('admin.data-madrasah.index')
            ->with('success', 'Semua data madrasah berhasil dihapus.');
    }

    // ─── Helper ──────────────────────────────────────────────

    private function findCol(array $header, array $candidates): ?int
    {
        foreach ($candidates as $candidate) {
            $idx = array_search(strtolower(trim($candidate)), array_map('strtolower', $header));
            if ($idx !== false) return $idx;
        }
        return null;
    }

    /**
     * Konversi index kolom (0-based) ke huruf kolom Excel (0 => A, 25 => Z, 26 => AA, ...).
     */
    private function colLetter(int $index): string
    {
        $letter = '';
        $index++;
        while ($index > 0) {
            $index--;
            $letter = chr(65 + ($index % 26)) . $letter;
            $index = intdiv($index, 26);
        }
        return $letter;
    }

    private function normalizeJenjang(string $val): string
    {
        $val = strtoupper(trim($val));

        // Singkatan langsung
        $exact = [
            'RA'  => 'RA', 'BA'  => 'RA', 'BA.' => 'RA', 'RA.' => 'RA',
            'MI'  => 'MI', 'MI.' => 'MI',
            'MTS' => 'MTs', 'MTS.' => 'MTs', 'MTSN' => 'MTs',
            'MA'  => 'MA', 'MA.' => 'MA', 'MAN' => 'MA', 'MIN' => 'MI',
        ];
        if (isset($exact[$val])) return $exact[$val];

        // Nama panjang dari EMIS
        if (str_contains($val, 'RAUDHATUL') || str_contains($val, 'RAUDLATUL') || str_contains($val, 'BUSTANUL')) return 'RA';
        if (str_contains($val, 'IBTIDAIYAH')) return 'MI';
        if (str_contains($val, 'TSANAWIYAH')) return 'MTs';
        if (str_contains($val, 'ALIYAH')) return 'MA';

        // Fallback: ambil kata pertama saja
        $first = explode(' ', $val)[0];
        if (isset($exact[$first])) return $exact[$first];

        // Kembalikan nilai asli — biarkan validasi DB yang tangkap
        return $val;
    }

    private function normalizeStatus(string $val): string
    {
        $val = strtolower(trim($val));
        if (str_contains($val, 'negeri') || $val === 'n') return 'Negeri';
        return 'Swasta';
    }
}
