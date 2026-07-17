<?php

namespace App\Http\Controllers\Madrasah;

use App\Http\Controllers\Controller;
use App\Models\JenisDokumen;
use App\Models\Pengajuan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MadrasahPengajuanController extends Controller
{
    private function madrasah()
    {
        return Auth::guard('madrasah')->user();
    }

    // ─────────────────────────────────────────
    // PILIH JENIS DOKUMEN
    // ─────────────────────────────────────────
    public function index()
    {
        $jenisDokumen = JenisDokumen::where('aktif', true)
            ->whereIn('untuk', ['madrasah', 'semua'])
            ->get();

        return view('madrasah.pengajuan.index', compact('jenisDokumen'));
    }

    // ─────────────────────────────────────────
    // FORM AJUAN
    // ─────────────────────────────────────────
    public function form($jenisId)
    {
        $madrasah = $this->madrasah();
        $jenis    = JenisDokumen::where('aktif', true)
            ->whereIn('untuk', ['madrasah', 'semua'])
            ->findOrFail($jenisId);

        if (! $madrasah->isKontakLengkap()) {
            return redirect()->route('madrasah.profil.form')
                ->with('error', 'Lengkapi Nomor HP/WhatsApp terlebih dahulu sebelum mengajukan dokumen.');
        }

        return view('madrasah.pengajuan.form', compact('madrasah', 'jenis'));
    }

    // ─────────────────────────────────────────
    // SIMPAN AJUAN
    // ─────────────────────────────────────────
    public function store(Request $request, $jenisId)
    {
        $madrasah = $this->madrasah();
        $jenis    = JenisDokumen::where('aktif', true)
            ->whereIn('untuk', ['madrasah', 'semua'])
            ->findOrFail($jenisId);

        // Cegah double submit dalam 5 menit
        $duplikat = Pengajuan::where('madrasah_user_id', $madrasah->id)
            ->where('jenis_dokumen_id', $jenis->id)
            ->where('created_at', '>=', now()->subMinutes(5))
            ->exists();

        if ($duplikat) {
            return back()
                ->with('error', 'Pengajuan yang sama sudah dikirim dalam 5 menit terakhir. Silakan cek riwayat ajuan.')
                ->withInput();
        }

        if (! $madrasah->isKontakLengkap()) {
            return redirect()->route('madrasah.profil.form')
                ->with('error', 'Lengkapi Nomor HP/WhatsApp terlebih dahulu sebelum mengajukan dokumen.');
        }

        $request->validate([
            'token'        => 'nullable|string|size:6|regex:/^[A-Z0-9]{6}$/',
            'file_dokumen' => 'required|file|mimes:pdf|max:5120',
        ], [
            'token.size'            => 'Token harus tepat 6 karakter.',
            'token.regex'           => 'Token hanya boleh huruf besar (A-Z) dan angka (0-9).',
            'file_dokumen.required' => 'File dokumen wajib diupload.',
            'file_dokumen.mimes'    => 'File harus berformat PDF.',
            'file_dokumen.max'      => 'Ukuran file maksimal 5MB.',
        ]);

        $file         = $request->file('file_dokumen');
        $namaFileAsli = $file->getClientOriginalName();
        $filePath     = $file->store('dokumen-pengajuan', 'public');
        $kodeAjuan    = Pengajuan::generateKode();

        Pengajuan::create([
            'kode_ajuan'        => $kodeAjuan,
            'madrasah_user_id'  => $madrasah->id,
            'jenis_dokumen_id'  => $jenis->id,
            'nama_guru'         => $madrasah->nama_pic,
            'nip'               => $madrasah->nsm,
            'nama_madrasah'     => $madrasah->nama_madrasah,
            'token'             => $request->token ?? null,
            'email'             => $madrasah->hasEmailValid() ? $madrasah->email : null,
            'no_hp'             => $madrasah->no_hp,
            'file_dokumen'      => $filePath,
            'nama_file_asli'    => $namaFileAsli,
            'status'            => 'pending',
        ]);

        return redirect()->route('madrasah.pengajuan.sukses', $kodeAjuan);
    }

    // ─────────────────────────────────────────
    // HALAMAN SUKSES
    // ─────────────────────────────────────────
    public function sukses($kode)
    {
        $madrasah  = $this->madrasah();
        $pengajuan = Pengajuan::with('jenisDokumen')
            ->where('madrasah_user_id', $madrasah->id)
            ->where('kode_ajuan', $kode)
            ->firstOrFail();

        return view('madrasah.pengajuan.sukses', compact('pengajuan'));
    }

    // ─────────────────────────────────────────
    // RIWAYAT AJUAN
    // ─────────────────────────────────────────
    public function riwayat(Request $request)
    {
        $madrasah = $this->madrasah();
        $query    = Pengajuan::with('jenisDokumen')
            ->where('madrasah_user_id', $madrasah->id)
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $riwayat = $query->paginate(10)->withQueryString();

        return view('madrasah.pengajuan.riwayat', compact('riwayat'));
    }

    // ─────────────────────────────────────────
    // DETAIL AJUAN
    // ─────────────────────────────────────────
    public function show($id)
    {
        $madrasah  = $this->madrasah();
        $pengajuan = Pengajuan::with('jenisDokumen')
            ->where('madrasah_user_id', $madrasah->id)
            ->findOrFail($id);

        return view('madrasah.pengajuan.show', compact('pengajuan'));
    }

    // ─────────────────────────────────────────
    // BATALKAN AJUAN (hanya jika masih menunggu)
    // ─────────────────────────────────────────
    public function batalkan($id)
    {
        $madrasah  = $this->madrasah();
        $pengajuan = Pengajuan::where('madrasah_user_id', $madrasah->id)->findOrFail($id);

        if ($pengajuan->status !== 'pending') {
            return back()->with('error', 'Pengajuan yang sudah diproses admin tidak bisa dibatalkan.');
        }

        $pengajuan->delete();

        return back()->with('success', 'Pengajuan berhasil dibatalkan.');
    }
}
