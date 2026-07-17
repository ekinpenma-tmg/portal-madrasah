<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengajuan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class PengajuanAdminController extends Controller
{
    // ─────────────────────────────────────────
    // DAFTAR PENGAJUAN AKTIF
    // ─────────────────────────────────────────
    public function index(Request $request)
    {
        $query = Pengajuan::with('jenisDokumen')->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('kode_ajuan',    'like', "%$search%")
                  ->orWhere('nama_guru',     'like', "%$search%")
                  ->orWhere('nama_madrasah', 'like', "%$search%")
                  ->orWhere('nip',           'like', "%$search%")
                  ->orWhere('token',         'like', "%$search%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('jenis')) {
            $query->where('jenis_dokumen_id', $request->jenis);
        }

        $pengajuan    = $query->paginate(15)->withQueryString();
        $jenisDokumen = \App\Models\JenisDokumen::all();
        $trashCount   = Pengajuan::onlyTrashed()->count();

        return view('admin.pengajuan.index', compact('pengajuan', 'jenisDokumen', 'trashCount'));
    }

    // ─────────────────────────────────────────
    // DETAIL PENGAJUAN
    // ─────────────────────────────────────────
    public function show($id)
    {
        $pengajuan = Pengajuan::with('jenisDokumen')->findOrFail($id);
        return view('admin.pengajuan.show', compact('pengajuan'));
    }

    // ─────────────────────────────────────────
    // TERIMA PENGAJUAN
    // ─────────────────────────────────────────
    public function terima(Request $request, $id)
    {
        $pengajuan = Pengajuan::findOrFail($id);
        $request->validate(['catatan' => 'nullable|string|max:500']);

        $pengajuan->update([
            'status'         => 'diterima',
            'catatan'        => $request->catatan,
            'tanggal_proses' => now(),
        ]);

        return redirect()->route('admin.pengajuan.show', $id)
            ->with('success', 'Pengajuan berhasil diterima.');
    }

    // ─────────────────────────────────────────
    // TOLAK PENGAJUAN
    // ─────────────────────────────────────────
    public function tolak(Request $request, $id)
    {
        $pengajuan = Pengajuan::findOrFail($id);
        $request->validate([
            'catatan' => 'required|string|max:500',
        ], [
            'catatan.required' => 'Alasan penolakan wajib diisi.',
        ]);

        $pengajuan->update([
            'status'         => 'ditolak',
            'catatan'        => $request->catatan,
            'tanggal_proses' => now(),
        ]);

        return redirect()->route('admin.pengajuan.show', $id)
            ->with('success', 'Pengajuan telah ditolak.');
    }

    // ─────────────────────────────────────────
    // SOFT DELETE (pindah ke trash)
    // ─────────────────────────────────────────
    public function destroy($id)
    {
        $pengajuan = Pengajuan::findOrFail($id);
        $pengajuan->delete();

        return redirect()->route('admin.pengajuan.index')
            ->with('success', 'Pengajuan dipindahkan ke tempat sampah.');
    }

    // ─────────────────────────────────────────
    // BULK SOFT DELETE
    // ─────────────────────────────────────────
    public function bulkDelete(Request $request)
    {
        $request->validate(['ids' => 'required|array', 'ids.*' => 'integer']);

        Pengajuan::whereIn('id', $request->ids)->delete();

        return back()->with('success', count($request->ids) . ' pengajuan dipindahkan ke tempat sampah.');
    }

    // ─────────────────────────────────────────
    // HALAMAN TRASH
    // ─────────────────────────────────────────
    public function trash(Request $request)
    {
        $query = Pengajuan::onlyTrashed()->with('jenisDokumen')->latest('deleted_at');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('kode_ajuan',    'like', "%$search%")
                  ->orWhere('nama_guru',     'like', "%$search%")
                  ->orWhere('nama_madrasah', 'like', "%$search%")
                  ->orWhere('nip',           'like', "%$search%");
            });
        }

        $pengajuan  = $query->paginate(15)->withQueryString();
        $trashCount = Pengajuan::onlyTrashed()->count();

        return view('admin.pengajuan.trash', compact('pengajuan', 'trashCount'));
    }

    // ─────────────────────────────────────────
    // RESTORE (kembalikan dari trash)
    // ─────────────────────────────────────────
    public function restore($id)
    {
        $pengajuan = Pengajuan::onlyTrashed()->findOrFail($id);
        $pengajuan->restore();

        return back()->with('success', 'Pengajuan berhasil dipulihkan.');
    }

    // ─────────────────────────────────────────
    // BULK RESTORE
    // ─────────────────────────────────────────
    public function bulkRestore(Request $request)
    {
        $request->validate(['ids' => 'required|array', 'ids.*' => 'integer']);

        Pengajuan::onlyTrashed()->whereIn('id', $request->ids)->restore();

        return back()->with('success', count($request->ids) . ' pengajuan berhasil dipulihkan.');
    }

    // ─────────────────────────────────────────
    // FORCE DELETE (hapus permanen)
    // ─────────────────────────────────────────
    public function forceDelete($id)
    {
        $pengajuan = Pengajuan::onlyTrashed()->findOrFail($id);

        // Hapus file PDF juga
        if ($pengajuan->file_dokumen && Storage::disk('public')->exists($pengajuan->file_dokumen)) {
            Storage::disk('public')->delete($pengajuan->file_dokumen);
        }

        $pengajuan->forceDelete();

        return back()->with('success', 'Pengajuan dihapus permanen.');
    }

    // ─────────────────────────────────────────
    // BULK FORCE DELETE
    // ─────────────────────────────────────────
    public function bulkForceDelete(Request $request)
    {
        $request->validate(['ids' => 'required|array', 'ids.*' => 'integer']);

        $pengajuanList = Pengajuan::onlyTrashed()->whereIn('id', $request->ids)->get();

        foreach ($pengajuanList as $p) {
            if ($p->file_dokumen && Storage::disk('public')->exists($p->file_dokumen)) {
                Storage::disk('public')->delete($p->file_dokumen);
            }
            $p->forceDelete();
        }

        return back()->with('success', count($request->ids) . ' pengajuan dihapus permanen.');
    }

    // ─────────────────────────────────────────
    // EXPORT EXCEL
    // ─────────────────────────────────────────
    public function export(Request $request)
    {
        $query = Pengajuan::with('jenisDokumen')->latest();

        // Ikuti filter yang aktif
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('kode_ajuan',    'like', "%$search%")
                  ->orWhere('nama_guru',     'like', "%$search%")
                  ->orWhere('nama_madrasah', 'like', "%$search%")
                  ->orWhere('nip',           'like', "%$search%")
                  ->orWhere('token',         'like', "%$search%");
            });
        }
        if ($request->filled('status'))  $query->where('status', $request->status);
        if ($request->filled('jenis'))   $query->where('jenis_dokumen_id', $request->jenis);
        if ($request->filled('dari'))    $query->whereDate('created_at', '>=', $request->dari);
        if ($request->filled('sampai'))  $query->whereDate('created_at', '<=', $request->sampai);

        $data = $query->get();

        // ── Buat Spreadsheet ──
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Pengajuan');

        // ── Judul utama ──
        $sheet->mergeCells('A1:J1');
        $sheet->setCellValue('A1', 'REKAP DATA PENGAJUAN DOKUMEN');
        $sheet->getStyle('A1')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '14532D']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(32);

        // ── Sub judul ──
        $sheet->mergeCells('A2:J2');
        $filterLabel = 'Semua Data';
        if ($request->filled('status')) $filterLabel = 'Status: ' . ucfirst($request->status);
        if ($request->filled('dari') || $request->filled('sampai')) {
            $filterLabel .= ' | Periode: ' . ($request->dari ?? '-') . ' s/d ' . ($request->sampai ?? '-');
        }
        $sheet->setCellValue('A2', 'Seksi Pendidikan Madrasah — Kemenag Kab. Temanggung | ' . $filterLabel . ' | Dicetak: ' . now()->format('d/m/Y H:i'));
        $sheet->getStyle('A2')->applyFromArray([
            'font'      => ['size' => 9, 'color' => ['rgb' => 'FFFFFF'], 'italic' => true],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '166534']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $sheet->getRowDimension(2)->setRowHeight(18);

        // ── Header kolom ──
        $headers = ['No', 'Kode Ajuan', 'Jenis Dokumen', 'Nama Guru', 'NIP / NUPTK', 'Nama Madrasah', 'No. HP', 'Email', 'Status', 'Tgl Pengajuan'];
        $cols    = ['A','B','C','D','E','F','G','H','I','J'];

        foreach ($headers as $i => $h) {
            $cell = $cols[$i] . '3';
            $sheet->setCellValue($cell, $h);
        }
        $sheet->getStyle('A3:J3')->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 10],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '15803D']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'FFFFFF']]],
        ]);
        $sheet->getRowDimension(3)->setRowHeight(22);

        // ── Data rows ──
        $statusLabel = ['pending' => 'Menunggu', 'diterima' => 'Diterima', 'ditolak' => 'Ditolak'];
        $statusColor = ['pending' => 'FEF3C7', 'diterima' => 'DCFCE7', 'ditolak' => 'FEE2E2'];
        $statusText  = ['pending' => '92400E', 'diterima' => '166534', 'ditolak' => '991B1B'];

        foreach ($data as $i => $p) {
            $row = $i + 4;
            $bgRow = $i % 2 === 0 ? 'FFFFFF' : 'F0FDF4';

            $sheet->setCellValue('A' . $row, $i + 1);
            $sheet->setCellValue('B' . $row, $p->kode_ajuan);
            $sheet->setCellValue('C' . $row, $p->jenisDokumen->nama ?? '-');
            $sheet->setCellValue('D' . $row, $p->nama_guru);
            $sheet->setCellValue('E' . $row, $p->nip);
            $sheet->setCellValue('F' . $row, $p->nama_madrasah);
            $sheet->setCellValue('G' . $row, $p->no_hp ?? '-');
            $sheet->setCellValue('H' . $row, $p->email ?? '-');
            $sheet->setCellValue('I' . $row, $statusLabel[$p->status] ?? $p->status);
            $sheet->setCellValue('J' . $row, $p->created_at->format('d/m/Y'));

            // Style row
            $sheet->getStyle("A{$row}:J{$row}")->applyFromArray([
                'fill'    => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bgRow]],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E5E7EB']]],
                'font'    => ['size' => 10],
            ]);

            // Style kolom status (warna sesuai status)
            $st = $p->status;
            $sheet->getStyle("I{$row}")->applyFromArray([
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $statusColor[$st] ?? 'F3F4F6']],
                'font'      => ['bold' => true, 'color' => ['rgb' => $statusText[$st] ?? '374151']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ]);

            $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("B{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("J{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getRowDimension($row)->setRowHeight(18);
        }

        // ── Footer summary ──
        $footerRow = $data->count() + 4;
        $sheet->mergeCells("A{$footerRow}:H{$footerRow}");
        $sheet->setCellValue("A{$footerRow}", 'Total: ' . $data->count() . ' data');
        $sheet->getStyle("A{$footerRow}:J{$footerRow}")->applyFromArray([
            'font' => ['bold' => true, 'size' => 10],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F0FDF4']],
        ]);

        // ── Lebar kolom ──
        $widths = ['A'=>6,'B'=>16,'C'=>18,'D'=>28,'E'=>18,'F'=>32,'G'=>16,'H'=>28,'I'=>14,'J'=>14];
        foreach ($widths as $col => $w) {
            $sheet->getColumnDimension($col)->setWidth($w);
        }

        // ── Output ──
        $filename = 'Rekap_Pengajuan_' . now()->format('Ymd_His') . '.xlsx';
        $writer   = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    // ─────────────────────────────────────────
    // LIHAT DOKUMEN PDF
    // ─────────────────────────────────────────
    public function lihatDokumen($id)
    {
        $pengajuan = Pengajuan::findOrFail($id);

        if (!Storage::disk('public')->exists($pengajuan->file_dokumen)) {
            abort(404, 'File tidak ditemukan.');
        }

        return response()->file(Storage::disk('public')->path($pengajuan->file_dokumen));
    }
}
