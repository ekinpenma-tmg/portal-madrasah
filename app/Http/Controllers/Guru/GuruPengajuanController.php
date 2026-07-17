<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\JenisDokumen;
use App\Models\Pengajuan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GuruPengajuanController extends Controller
{
    private function guru()
    {
        return Auth::guard('guru')->user();
    }

    // ─────────────────────────────────────────
    // PILIH JENIS DOKUMEN
    // ─────────────────────────────────────────
    public function index()
    {
        $jenisDokumen = JenisDokumen::where('aktif', true)
            ->whereIn('untuk', ['guru', 'semua'])
            ->get();

        return view('guru.pengajuan.index', compact('jenisDokumen'));
    }

    // ─────────────────────────────────────────
    // FORM AJUAN
    // ─────────────────────────────────────────
    public function form($jenisId)
    {
        $guru  = $this->guru();
        $jenis = JenisDokumen::where('aktif', true)
            ->whereIn('untuk', ['guru', 'semua'])
            ->findOrFail($jenisId);

        if (! $guru->isKontakLengkap()) {
            return redirect()->route('guru.profil.form')
                ->with('error', 'Lengkapi Nomor HP/WhatsApp Anda terlebih dahulu sebelum mengajukan dokumen.');
        }

        return view('guru.pengajuan.form', compact('guru', 'jenis'));
    }

    // ─────────────────────────────────────────
    // SIMPAN AJUAN
    // ─────────────────────────────────────────
    public function store(Request $request, $jenisId)
    {
        $guru  = $this->guru();
        $jenis = JenisDokumen::where('aktif', true)
            ->whereIn('untuk', ['guru', 'semua'])
            ->findOrFail($jenisId);

        // Cegah double submit dalam 5 menit
        $duplikat = Pengajuan::where('guru_user_id', $guru->id)
            ->where('jenis_dokumen_id', $jenis->id)
            ->where('created_at', '>=', now()->subMinutes(5))
            ->exists();

        if ($duplikat) {
            return back()
                ->with('error', 'Pengajuan yang sama sudah dikirim dalam 5 menit terakhir. Silakan cek riwayat ajuan Anda.')
                ->withInput();
        }

        if (! $guru->isKontakLengkap()) {
            return redirect()->route('guru.profil.form')
                ->with('error', 'Lengkapi Nomor HP/WhatsApp Anda terlebih dahulu sebelum mengajukan dokumen.');
        }

        $tokenRule = 'nullable|string|size:6|regex:/^[A-Z0-9]{6}$/';

        $validated = $request->validate([
            'token'        => $tokenRule,
            'file_dokumen' => 'required|file|mimes:pdf|max:5120',
        ], [
            'token.size'             => 'Token harus tepat 6 karakter.',
            'token.regex'            => 'Token hanya boleh berisi huruf besar (A-Z) dan angka (0-9).',
            'file_dokumen.required'  => 'File dokumen wajib diupload.',
            'file_dokumen.mimes'     => 'File harus berformat PDF.',
            'file_dokumen.max'       => 'Ukuran file maksimal 5MB.',
        ]);

        $file         = $request->file('file_dokumen');
        $namaFileAsli = $file->getClientOriginalName();
        $filePath     = $file->store('dokumen-pengajuan', 'public');
        $kodeAjuan    = Pengajuan::generateKode();

        Pengajuan::create([
            'kode_ajuan'       => $kodeAjuan,
            'guru_user_id'     => $guru->id,
            'jenis_dokumen_id' => $jenis->id,
            'nama_guru'        => $guru->nama,
            'nip'              => $guru->pegid,
            'nama_madrasah'    => $guru->nama_madrasah,
            'token'            => $validated['token'] ?? null,
            'email'            => $guru->hasEmailValid() ? $guru->email : null,
            'no_hp'            => $guru->no_hp,
            'file_dokumen'     => $filePath,
            'nama_file_asli'   => $namaFileAsli,
            'status'           => 'pending',
        ]);

        return redirect()
            ->route('guru.pengajuan.sukses', $kodeAjuan)
            ->with('success', 'Pengajuan berhasil dikirim.');
    }

    // ─────────────────────────────────────────
    // HALAMAN SUKSES
    // ─────────────────────────────────────────
    public function sukses($kode)
    {
        $guru      = $this->guru();
        $pengajuan = Pengajuan::with('jenisDokumen')
            ->where('guru_user_id', $guru->id)
            ->where('kode_ajuan', $kode)
            ->firstOrFail();

        return view('guru.pengajuan.sukses', compact('pengajuan'));
    }

    // ─────────────────────────────────────────
    // RIWAYAT AJUAN
    // ─────────────────────────────────────────
    public function riwayat(Request $request)
    {
        $guru  = $this->guru();
        $query = Pengajuan::with('jenisDokumen')
            ->where('guru_user_id', $guru->id)
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $riwayat = $query->paginate(10)->withQueryString();

        return view('guru.pengajuan.riwayat', compact('riwayat'));
    }

    // ─────────────────────────────────────────
    // DETAIL AJUAN
    // ─────────────────────────────────────────
    public function show($id)
    {
        $guru      = $this->guru();
        $pengajuan = Pengajuan::with('jenisDokumen')
            ->where('guru_user_id', $guru->id)
            ->findOrFail($id);

        return view('guru.pengajuan.show', compact('pengajuan'));
    }

    // ─────────────────────────────────────────
    // BATALKAN AJUAN (hanya jika masih menunggu)
    // ─────────────────────────────────────────
    public function batalkan($id)
    {
        $guru      = $this->guru();
        $pengajuan = Pengajuan::where('guru_user_id', $guru->id)->findOrFail($id);

        if ($pengajuan->status !== 'pending') {
            return back()->with('error', 'Pengajuan yang sudah diproses admin tidak bisa dibatalkan.');
        }

        $pengajuan->delete();

        return back()->with('success', 'Pengajuan berhasil dibatalkan.');
    }
}
