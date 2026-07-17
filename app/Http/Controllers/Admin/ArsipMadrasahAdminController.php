<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ArsipMadrasah;
use App\Models\MadrasahUser;
use App\Models\KategoriArsip;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\{Alignment, Fill, Border};

class ArsipMadrasahAdminController extends Controller
{
    // ─────────────────────────────────────────
    // INDEX — semua arsip madrasah
    // ─────────────────────────────────────────
    public function index(Request $request)
    {
        $query = ArsipMadrasah::with(['madrasahUser.madrasah', 'kategori'])->latest();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('judul', 'like', "%$s%")
                  ->orWhereHas('madrasahUser', fn($m) => $m->where('nsm', 'like', "%$s%"))
                  ->orWhereHas('madrasahUser.madrasah', fn($m) => $m->where('nama_madrasah', 'like', "%$s%"));
            });
        }

        if ($request->filled('kategori_id')) {
            $query->where('kategori_id', $request->kategori_id);
        }

        if ($request->filled('verified')) {
            $query->where('is_verified', $request->verified === '1');
        }

        if ($request->filled('tahun')) {
            $query->where('tahun', $request->tahun);
        }

        $arsip        = $query->paginate(20)->withQueryString();
        $kategoriList = KategoriArsip::untukMadrasah()->get();
        $tahunList    = ArsipMadrasah::whereNotNull('tahun')->distinct()->orderByDesc('tahun')->pluck('tahun');

        $totalArsip    = ArsipMadrasah::count();
        $totalVerified = ArsipMadrasah::where('is_verified', true)->count();
        $totalPending  = ArsipMadrasah::where('is_verified', false)->count();
        $totalMadrasah = ArsipMadrasah::distinct('madrasah_user_id')->count('madrasah_user_id');

        return view('admin.arsip-madrasah.index', compact(
            'arsip', 'kategoriList', 'tahunList',
            'totalArsip', 'totalVerified', 'totalPending', 'totalMadrasah'
        ));
    }

    // ─────────────────────────────────────────
    // DETAIL ARSIP PER MADRASAH
    // ─────────────────────────────────────────
    public function show($madrasahUserId)
    {
        $madrasahUser = MadrasahUser::with('madrasah')->findOrFail($madrasahUserId);
        $arsip        = ArsipMadrasah::with('kategori')
            ->where('madrasah_user_id', $madrasahUserId)
            ->latest()
            ->get();

        return view('admin.arsip-madrasah.show', compact('madrasahUser', 'arsip'));
    }

    // ─────────────────────────────────────────
    // VERIFY / UNVERIFY
    // ─────────────────────────────────────────
    public function verify($id)
    {
        $arsip = ArsipMadrasah::findOrFail($id);
        $arsip->update(['is_verified' => true, 'catatan_admin' => null]);
        return back()->with('success', 'Arsip berhasil diverifikasi.');
    }

    public function unverify($id)
    {
        $arsip = ArsipMadrasah::findOrFail($id);
        $arsip->update(['is_verified' => false]);
        return back()->with('success', 'Verifikasi arsip dibatalkan.');
    }

    // ─────────────────────────────────────────
    // BULK VERIFY — centang beberapa arsip sekaligus, verifikasi bareng
    // ─────────────────────────────────────────
    public function bulkVerify(Request $request)
    {
        $request->validate([
            'ids'   => 'required|array|min:1',
            'ids.*' => 'integer|exists:arsip_madrasah,id',
        ]);

        $jumlah = ArsipMadrasah::whereIn('id', $request->ids)
            ->update(['is_verified' => true, 'catatan_admin' => null]);

        return back()->with('success', "{$jumlah} arsip berhasil diverifikasi sekaligus.");
    }

    // ─────────────────────────────────────────
    // CATATAN ADMIN
    // ─────────────────────────────────────────
    public function updateCatatan(Request $request, $id)
    {
        $request->validate(['catatan_admin' => 'nullable|string|max:500']);
        $arsip = ArsipMadrasah::findOrFail($id);
        $arsip->update(['catatan_admin' => $request->catatan_admin]);
        return back()->with('success', 'Catatan berhasil disimpan.');
    }

    // ─────────────────────────────────────────
    // HAPUS
    // ─────────────────────────────────────────
    public function destroy($id)
    {
        $arsip = ArsipMadrasah::findOrFail($id);
        $arsip->delete();
        return back()->with('success', 'Arsip berhasil dihapus.');
    }

    // ─────────────────────────────────────────
    // EXPORT EXCEL — semua arsip madrasah sesuai filter yang aktif
    // ─────────────────────────────────────────
    public function export(Request $request)
    {
        $query = ArsipMadrasah::with(['madrasahUser.madrasah', 'kategori'])->latest();

        // Terapkan filter yang sama dengan index
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('judul', 'like', "%$s%")
                  ->orWhereHas('madrasahUser', fn($m) => $m->where('nsm', 'like', "%$s%"))
                  ->orWhereHas('madrasahUser.madrasah', fn($m) => $m->where('nama_madrasah', 'like', "%$s%"));
            });
        }
        if ($request->filled('kategori_id')) $query->where('kategori_id', $request->kategori_id);
        if ($request->filled('verified'))    $query->where('is_verified', $request->verified === '1');
        if ($request->filled('tahun'))       $query->where('tahun', $request->tahun);

        $data = $query->get();

        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Arsip Digital Madrasah');

        $headers = ['No', 'Nama Madrasah', 'NSM', 'Kategori', 'Judul Dokumen', 'Tahun', 'Link GDrive', 'Status', 'Catatan Admin', 'Tanggal Upload'];
        foreach ($headers as $i => $h) {
            $sheet->setCellValue(chr(65 + $i) . '1', $h);
        }
        $sheet->getStyle('A1:J1')->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 10],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '15803D']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(20);

        foreach ($data as $i => $item) {
            $row = $i + 2;
            $sheet->setCellValue("A{$row}", $i + 1);
            $sheet->setCellValue("B{$row}", $item->madrasahUser->madrasah->nama_madrasah ?? '—');
            $sheet->setCellValue("C{$row}", $item->madrasahUser->nsm ?? '—');
            $sheet->setCellValue("D{$row}", $item->kategori->nama ?? '—');
            $sheet->setCellValue("E{$row}", $item->judul);
            $sheet->setCellValue("F{$row}", $item->tahun ?? '—');
            $sheet->setCellValue("G{$row}", $item->link_gdrive);
            $sheet->setCellValue("H{$row}", $item->is_verified ? 'Terverifikasi' : 'Pending');
            $sheet->setCellValue("I{$row}", $item->catatan_admin ?? '');
            $sheet->setCellValue("J{$row}", $item->created_at->format('d/m/Y'));

            $bg = $i % 2 === 0 ? 'F0FDF4' : 'FFFFFF';
            $sheet->getStyle("A{$row}:J{$row}")->applyFromArray([
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bg]],
            ]);
        }

        foreach (['A'=>5,'B'=>34,'C'=>16,'D'=>22,'E'=>36,'F'=>8,'G'=>50,'H'=>16,'I'=>28,'J'=>14] as $col => $w) {
            $sheet->getColumnDimension($col)->setWidth($w);
        }

        $filename = 'Arsip_Digital_Madrasah_' . now()->format('Ymd_His') . '.xlsx';
        $writer   = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
