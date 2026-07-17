<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ArsipGuru;
use App\Models\GuruUser;
use App\Models\KategoriArsip;
use App\Models\Madrasah;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\{Alignment, Fill, Border};

class ArsipGuruAdminController extends Controller
{
    // ─────────────────────────────────────────
    // INDEX — semua arsip dengan filter lengkap
    // ─────────────────────────────────────────
    public function index(Request $request)
    {
        $query = ArsipGuru::with(['guru.madrasah', 'kategori'])->latest();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('judul', 'like', "%$s%")
                  ->orWhereHas('guru', fn($g) => $g->where('nama', 'like', "%$s%")
                                                    ->orWhere('pegid', 'like', "%$s%"));
            });
        }

        if ($request->filled('kategori_id')) {
            $query->where('kategori_id', $request->kategori_id);
        }

        if ($request->filled('madrasah_id')) {
            $query->whereHas('guru', fn($g) => $g->where('madrasah_id', $request->madrasah_id));
        }

        if ($request->filled('status')) {
            $query->where('is_verified', $request->status === 'verified');
        }

        if ($request->filled('tahun')) {
            $query->where('tahun', $request->tahun);
        }

        $arsip        = $query->paginate(20)->withQueryString();
        $kategoriList = KategoriArsip::orderBy('urutan')->get();
        $madrasahs    = Madrasah::aktif()->orderBy('nama_madrasah')->get();
        $tahunList    = ArsipGuru::whereNotNull('tahun')->distinct()->orderByDesc('tahun')->pluck('tahun');

        // Stat untuk header
        $totalArsip    = ArsipGuru::count();
        $totalVerified = ArsipGuru::where('is_verified', true)->count();
        $totalPending  = ArsipGuru::where('is_verified', false)->count();
        $totalGuru     = GuruUser::whereHas('arsip')->count();

        return view('admin.arsip-guru.index', compact(
            'arsip', 'kategoriList', 'madrasahs', 'tahunList',
            'totalArsip', 'totalVerified', 'totalPending', 'totalGuru'
        ));
    }

    // ─────────────────────────────────────────
    // SHOW — semua arsip milik 1 guru
    // ─────────────────────────────────────────
    public function show(Request $request, $guruId)
    {
        $guru  = GuruUser::with('madrasah')->findOrFail($guruId);
        $query = $guru->arsip()->with('kategori')->latest();

        if ($request->filled('kategori_id')) {
            $query->where('kategori_id', $request->kategori_id);
        }

        $arsip        = $query->paginate(15)->withQueryString();
        $kategoriList = KategoriArsip::orderBy('urutan')->get();
        $totalArsip   = $guru->arsip()->count();
        $totalVerified = $guru->arsip()->where('is_verified', true)->count();

        return view('admin.arsip-guru.show', compact(
            'guru', 'arsip', 'kategoriList', 'totalArsip', 'totalVerified'
        ));
    }

    // ─────────────────────────────────────────
    // VERIFY — tandai terverifikasi
    // ─────────────────────────────────────────
    public function verify(Request $request, $id)
    {
        $arsip = ArsipGuru::with('guru')->findOrFail($id);

        $request->validate([
            'catatan_admin' => 'nullable|string|max:500',
        ]);

        $arsip->update([
            'is_verified'   => true,
            'catatan_admin' => $request->catatan_admin ?? null,
        ]);

        return back()->with('success',
            "Arsip \"{$arsip->judul}\" milik {$arsip->guru->nama} berhasil diverifikasi."
        );
    }

    // ─────────────────────────────────────────
    // UNVERIFY — batalkan verifikasi
    // ─────────────────────────────────────────
    public function unverify(Request $request, $id)
    {
        $arsip = ArsipGuru::with('guru')->findOrFail($id);

        $request->validate([
            'catatan_admin' => 'nullable|string|max:500',
        ]);

        $arsip->update([
            'is_verified'   => false,
            'catatan_admin' => $request->catatan_admin ?? null,
        ]);

        return back()->with('success',
            "Verifikasi arsip \"{$arsip->judul}\" berhasil dibatalkan."
        );
    }

    // ─────────────────────────────────────────
    // BULK VERIFY — verifikasi banyak sekaligus
    // ─────────────────────────────────────────
    public function bulkVerify(Request $request)
    {
        $request->validate([
            'ids'   => 'required|array|min:1',
            'ids.*' => 'integer|exists:arsip_guru,id',
        ]);

        $jumlah = ArsipGuru::whereIn('id', $request->ids)
            ->update(['is_verified' => true]);

        return back()->with('success', "{$jumlah} arsip berhasil diverifikasi sekaligus.");
    }

    // ─────────────────────────────────────────
    // CATATAN — tambah/ubah catatan admin saja
    // ─────────────────────────────────────────
    public function updateCatatan(Request $request, $id)
    {
        $arsip = ArsipGuru::findOrFail($id);

        $request->validate([
            'catatan_admin' => 'nullable|string|max:500',
        ]);

        $arsip->update(['catatan_admin' => $request->catatan_admin]);

        return back()->with('success', 'Catatan berhasil disimpan.');
    }

    // ─────────────────────────────────────────
    // HAPUS — admin hapus arsip (dengan konfirmasi)
    // ─────────────────────────────────────────
    public function destroy($id)
    {
        $arsip = ArsipGuru::with('guru')->findOrFail($id);
        $info  = "\"{$arsip->judul}\" milik {$arsip->guru->nama}";
        $arsip->delete();

        return back()->with('success', "Arsip {$info} berhasil dihapus.");
    }

    // ─────────────────────────────────────────
    // EXPORT EXCEL — semua arsip sesuai filter
    // ─────────────────────────────────────────
    public function export(Request $request)
    {
        $query = ArsipGuru::with(['guru.madrasah', 'kategori'])->latest();

        // Terapkan filter yang sama dengan index
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('judul', 'like', "%$s%")
                  ->orWhereHas('guru', fn($g) => $g->where('nama', 'like', "%$s%")
                                                    ->orWhere('pegid', 'like', "%$s%"));
            });
        }
        if ($request->filled('kategori_id'))  $query->where('kategori_id', $request->kategori_id);
        if ($request->filled('madrasah_id'))  $query->whereHas('guru', fn($g) => $g->where('madrasah_id', $request->madrasah_id));
        if ($request->filled('status'))       $query->where('is_verified', $request->status === 'verified');
        if ($request->filled('tahun'))        $query->where('tahun', $request->tahun);

        $data = $query->get();

        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Arsip Digital Guru');

        // Header
        $headers = ['No', 'Nama Guru', 'PegID', 'Madrasah', 'Kategori', 'Judul Dokumen', 'Tahun', 'Link GDrive', 'Status', 'Catatan Admin', 'Tanggal Upload'];
        foreach ($headers as $i => $h) {
            $cell = chr(65 + $i) . '1';
            $sheet->setCellValue($cell, $h);
        }
        $sheet->getStyle('A1:K1')->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 10],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '15803D']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(20);

        // Data
        foreach ($data as $i => $item) {
            $row = $i + 2;
            $sheet->setCellValue("A{$row}", $i + 1);
            $sheet->setCellValue("B{$row}", $item->guru->nama ?? '—');
            $sheet->setCellValue("C{$row}", $item->guru->pegid ?? '—');
            $sheet->setCellValue("D{$row}", $item->guru->madrasah?->nama_madrasah ?? '—');
            $sheet->setCellValue("E{$row}", $item->kategori->nama ?? '—');
            $sheet->setCellValue("F{$row}", $item->judul);
            $sheet->setCellValue("G{$row}", $item->tahun ?? '—');
            $sheet->setCellValue("H{$row}", $item->link_gdrive);
            $sheet->setCellValue("I{$row}", $item->is_verified ? 'Terverifikasi' : 'Pending');
            $sheet->setCellValue("J{$row}", $item->catatan_admin ?? '');
            $sheet->setCellValue("K{$row}", $item->created_at->format('d/m/Y'));

            $bg = $i % 2 === 0 ? 'F0FDF4' : 'FFFFFF';
            $sheet->getStyle("A{$row}:K{$row}")->applyFromArray([
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bg]],
            ]);
        }

        // Lebar kolom
        foreach (['A'=>5,'B'=>28,'C'=>16,'D'=>34,'E'=>22,'F'=>36,'G'=>8,'H'=>50,'I'=>16,'J'=>28,'K'=>14] as $col => $w) {
            $sheet->getColumnDimension($col)->setWidth($w);
        }

        $filename = 'Arsip_Digital_Guru_' . now()->format('Ymd_His') . '.xlsx';
        $writer   = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
