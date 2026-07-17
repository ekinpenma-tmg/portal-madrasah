<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\ArsipGuru;
use App\Models\KategoriArsip;
use App\Models\Pengajuan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class GuruPortalController extends Controller
{
    // ─────────────────────────────────────────
    // DASHBOARD
    // ─────────────────────────────────────────
    public function dashboard()
    {
        $guru = Auth::guard('guru')->user();

        $totalArsip   = $guru->arsip()->count();
        $totalVerified = $guru->arsip()->where('is_verified', true)->count();

        // Jumlah kategori unik yang dipakai guru ini
        $totalKategori = $guru->arsip()->distinct('kategori_id')->count('kategori_id');

        // Jumlah kategori yang tersedia untuk dipilih guru (walau belum dipakai)
        $kategoriTersedia      = KategoriArsip::untukGuru()->get();
        $totalKategoriTersedia = $kategoriTersedia->count();

        // Checklist kelengkapan arsip per kategori (mirror dashboard Madrasah)
        $kategoriIdTerisi  = $guru->arsip()->pluck('kategori_id')->toArray();
        $kategoriChecklist = $kategoriTersedia->map(function ($k) use ($kategoriIdTerisi) {
            return [
                'nama'   => $k->nama,
                'terisi' => in_array($k->id, $kategoriIdTerisi),
            ];
        });

        // Jumlah pengajuan dokumen guru ini yang masih menunggu diproses admin
        $totalPengajuanPending = $guru->pengajuan()->where('status', 'pending')->count();

        // Pengajuan terakhir (3 terbaru) — mirror dashboard Madrasah
        $pengajuanTerakhir = Pengajuan::with('jenisDokumen')
            ->where('guru_user_id', $guru->id)
            ->latest()
            ->take(3)
            ->get();

        // Madrasah tempat guru ini mengajar — ditampilkan di dashboard
        $madrasah = $guru->madrasah;

        return view('guru.dashboard.index', compact(
            'totalArsip', 'totalVerified', 'totalKategori',
            'totalKategoriTersedia', 'totalPengajuanPending',
            'kategoriChecklist', 'pengajuanTerakhir',
            'madrasah'
        ));
    }

    // ─────────────────────────────────────────
    // DAFTAR ARSIP
    // ─────────────────────────────────────────
    public function arsipIndex(Request $request)
    {
        $guru  = Auth::guard('guru')->user();
        $query = $guru->arsip()->with('kategori')->latest();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('judul', 'like', "%$s%")
                  ->orWhere('keterangan', 'like', "%$s%");
            });
        }

        if ($request->filled('kategori_id')) {
            $query->where('kategori_id', $request->kategori_id);
        }

        if ($request->filled('tahun')) {
            $query->where('tahun', $request->tahun);
        }

        $arsip       = $query->paginate(12)->withQueryString();
        $kategoriList = KategoriArsip::untukGuru()->get();
        $tahunList    = $guru->arsip()->whereNotNull('tahun')
                            ->distinct()->orderByDesc('tahun')
                            ->pluck('tahun');

        return view('guru.arsip.index', compact('arsip', 'kategoriList', 'tahunList'));
    }

    // ─────────────────────────────────────────
    // FORM TAMBAH ARSIP
    // ─────────────────────────────────────────
    public function arsipCreate()
    {
        $kategoriList = KategoriArsip::untukGuru()->get();
        return view('guru.arsip.create', compact('kategoriList'));
    }

    // ─────────────────────────────────────────
    // SIMPAN ARSIP BARU
    // ─────────────────────────────────────────
    public function arsipStore(Request $request)
    {
        $validated = $request->validate([
            'kategori_id'  => 'required|exists:kategori_arsip,id',
            'judul'        => 'required|string|max:255',
            'link_gdrive'  => ['required', 'url', 'max:500', function ($attr, $val, $fail) {
                if (! $this->isValidGoogleDriveUrl($val)) {
                    $fail('Link harus dari Google Drive atau Google Docs.');
                }
            }],
            'tahun'        => 'nullable|integer|min:1990|max:' . (date('Y') + 1),
            'keterangan'   => 'nullable|string|max:500',
        ], [
            'kategori_id.required' => 'Kategori dokumen wajib dipilih.',
            'judul.required'       => 'Judul dokumen wajib diisi.',
            'link_gdrive.required' => 'Link Google Drive wajib diisi.',
            'link_gdrive.url'      => 'Format link tidak valid.',
        ]);

        $guru = Auth::guard('guru')->user();

        if ($this->sudahAdaArsipSerupa(ArsipGuru::class, 'guru_id', $guru->id, (int) $validated['kategori_id'], $validated['tahun'] ?? null)) {
            return back()->withInput()->withErrors([
                'kategori_id' => 'Anda sudah punya arsip untuk kategori ini' .
                    (($validated['tahun'] ?? null) ? " di tahun {$validated['tahun']}" : '') .
                    '. Silakan edit arsip yang sudah ada, atau isi tahun yang berbeda kalau ini dokumen tahun lain.',
            ]);
        }

        ArsipGuru::create([
            'guru_id'     => $guru->id,
            'kategori_id' => $validated['kategori_id'],
            'judul'       => $validated['judul'],
            'link_gdrive' => $validated['link_gdrive'],
            'tahun'       => $validated['tahun'] ?? null,
            'keterangan'  => $validated['keterangan'] ?? null,
            'is_verified' => false,
        ]);

        return redirect()->route('guru.arsip.index')
            ->with('success', "Arsip \"{$validated['judul']}\" berhasil disimpan.");
    }

    // ─────────────────────────────────────────
    // FORM EDIT ARSIP
    // ─────────────────────────────────────────
    public function arsipEdit($id)
    {
        $guru  = Auth::guard('guru')->user();
        // Pastikan arsip milik guru ini
        $arsip = ArsipGuru::where('guru_id', $guru->id)->findOrFail($id);
        $kategoriList = KategoriArsip::untukGuru()->get();

        return view('guru.arsip.edit', compact('arsip', 'kategoriList'));
    }

    // ─────────────────────────────────────────
    // UPDATE ARSIP
    // ─────────────────────────────────────────
    public function arsipUpdate(Request $request, $id)
    {
        $guru  = Auth::guard('guru')->user();
        $arsip = ArsipGuru::where('guru_id', $guru->id)->findOrFail($id);

        $validated = $request->validate([
            'kategori_id'  => 'required|exists:kategori_arsip,id',
            'judul'        => 'required|string|max:255',
            'link_gdrive'  => ['required', 'url', 'max:500', function ($attr, $val, $fail) {
                if (! $this->isValidGoogleDriveUrl($val)) {
                    $fail('Link harus dari Google Drive atau Google Docs.');
                }
            }],
            'tahun'        => 'nullable|integer|min:1990|max:' . (date('Y') + 1),
            'keterangan'   => 'nullable|string|max:500',
        ]);

        // Jika link gdrive diubah, reset verifikasi
        $resetVerified = $arsip->link_gdrive !== $validated['link_gdrive'];

        if ($this->sudahAdaArsipSerupa(ArsipGuru::class, 'guru_id', $guru->id, (int) $validated['kategori_id'], $validated['tahun'] ?? null, $arsip->id)) {
            return back()->withInput()->withErrors([
                'kategori_id' => 'Anda sudah punya arsip lain untuk kategori ini' .
                    (($validated['tahun'] ?? null) ? " di tahun {$validated['tahun']}" : '') . '.',
            ]);
        }

        $arsip->update([
            'kategori_id' => $validated['kategori_id'],
            'judul'       => $validated['judul'],
            'link_gdrive' => $validated['link_gdrive'],
            'tahun'       => $validated['tahun'] ?? null,
            'keterangan'  => $validated['keterangan'] ?? null,
            'is_verified' => $resetVerified ? false : $arsip->is_verified,
            'catatan_admin' => $resetVerified ? null : $arsip->catatan_admin,
        ]);

        $msg = "Arsip \"{$arsip->judul}\" berhasil diperbarui.";
        if ($resetVerified) $msg .= ' Status verifikasi direset karena link diubah.';

        return redirect()->route('guru.arsip.index')->with('success', $msg);
    }

    // ─────────────────────────────────────────
    // HAPUS ARSIP
    // ─────────────────────────────────────────
    public function arsipDestroy($id)
    {
        $guru  = Auth::guard('guru')->user();
        $arsip = ArsipGuru::where('guru_id', $guru->id)->findOrFail($id);
        $judul = $arsip->judul;
        $arsip->delete();

        return redirect()->route('guru.arsip.index')
            ->with('success', "Arsip \"{$judul}\" berhasil dihapus.");
    }

    // ─────────────────────────────────────────
    // FORM EDIT PROFIL (No HP & Email)
    // ─────────────────────────────────────────
    public function profilForm()
    {
        $guru = Auth::guard('guru')->user();
        return view('guru.profil', compact('guru'));
    }

    // ─────────────────────────────────────────
    // UPDATE PROFIL
    // ─────────────────────────────────────────
    public function profilUpdate(Request $request)
    {
        $guru = Auth::guard('guru')->user();

        $validated = $request->validate([
            'no_hp' => 'required|string|max:20|regex:/^[0-9+\-\s]+$/',
            'email' => 'nullable|email|max:255|unique:guru_users,email,' . $guru->id,
        ], [
            'no_hp.required' => 'Nomor HP/WhatsApp wajib diisi.',
            'no_hp.regex'    => 'Format nomor HP tidak valid.',
            'email.email'    => 'Format email tidak valid.',
            'email.unique'   => 'Email ini sudah digunakan akun lain.',
        ]);

        $guru->update([
            'no_hp' => $validated['no_hp'],
            'email' => $validated['email'] ?? null,
        ]);

        return redirect()->route('guru.profil.form')
            ->with('success', 'Profil berhasil diperbarui.');
    }

    // ─────────────────────────────────────────
    // FORM GANTI PASSWORD
    // ─────────────────────────────────────────
    public function passwordForm()
    {
        return view('guru.password');
    }

    // ─────────────────────────────────────────
    // PROSES GANTI PASSWORD
    // ─────────────────────────────────────────
    public function passwordUpdate(Request $request)
    {
        $request->validate([
            'password_lama'              => 'required|string',
            'password_baru'             => 'required|string|min:8|confirmed',
        ], [
            'password_lama.required'     => 'Password saat ini wajib diisi.',
            'password_baru.required'     => 'Password baru wajib diisi.',
            'password_baru.min'          => 'Password baru minimal 8 karakter.',
            'password_baru.confirmed'    => 'Konfirmasi password tidak cocok.',
        ]);

        $guru = Auth::guard('guru')->user();

        // Verifikasi password lama
        if (! Hash::check($request->password_lama, $guru->password)) {
            return back()->withErrors(['password_lama' => 'Password saat ini tidak sesuai.']);
        }

        // Pastikan password baru berbeda
        if (Hash::check($request->password_baru, $guru->password)) {
            return back()->withErrors(['password_baru' => 'Password baru tidak boleh sama dengan password lama.']);
        }

        $guru->update([
            'password'         => Hash::make($request->password_baru),
            'password_changed' => true,
        ]);

        return redirect()->route('guru.dashboard')
            ->with('success', 'Password berhasil diubah. Gunakan password baru Anda untuk login berikutnya.');
    }
}
