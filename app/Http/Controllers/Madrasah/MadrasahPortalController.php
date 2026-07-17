<?php

namespace App\Http\Controllers\Madrasah;

use App\Http\Controllers\Controller;
use App\Models\ArsipMadrasah;
use App\Models\KategoriArsip;
use App\Models\Pengajuan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class MadrasahPortalController extends Controller
{
    private function madrasah()
    {
        return Auth::guard('madrasah')->user();
    }

    // ─────────────────────────────────────────
    // DASHBOARD
    // ─────────────────────────────────────────
    public function dashboard()
    {
        $madrasah      = $this->madrasah();
        $totalArsip    = $madrasah->arsip()->count();
        $totalVerified = $madrasah->arsip()->where('is_verified', true)->count();

        // Kategori tersedia untuk madrasah + checklist terisi/belum
        $kategoriTersedia      = KategoriArsip::untukMadrasah()->get();
        $kategoriIdTerisi      = $madrasah->arsip()->pluck('kategori_id')->toArray();
        $totalKategoriTersedia = $kategoriTersedia->count();
        $totalKategori         = count(array_unique($kategoriIdTerisi));

        $kategoriChecklist = $kategoriTersedia->map(function ($k) use ($kategoriIdTerisi) {
            return [
                'nama'   => $k->nama,
                'terisi' => in_array($k->id, $kategoriIdTerisi),
            ];
        });

        // Pengajuan terakhir (3 terbaru) + hitung yang masih pending
        $pengajuanTerakhir     = Pengajuan::with('jenisDokumen')
            ->where('madrasah_user_id', $madrasah->id)
            ->latest()
            ->take(3)
            ->get();
        $totalPengajuanPending = Pengajuan::where('madrasah_user_id', $madrasah->id)
            ->where('status', 'pending')
            ->count();

        return view('madrasah.dashboard.index', compact(
            'totalArsip', 'totalVerified', 'totalKategori', 'totalKategoriTersedia',
            'kategoriChecklist', 'pengajuanTerakhir', 'totalPengajuanPending'
        ));
    }

    // ─────────────────────────────────────────
    // DAFTAR ARSIP
    // ─────────────────────────────────────────
    public function arsipIndex(Request $request)
    {
        $madrasah = $this->madrasah();
        $query    = $madrasah->arsip()->with('kategori')->latest();

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

        if ($request->filled('verified')) {
            $query->where('is_verified', $request->verified === '1');
        }

        $arsip        = $query->paginate(12)->withQueryString();
        $kategoriList = KategoriArsip::untukMadrasah()->get();
        $tahunList    = $madrasah->arsip()
            ->whereNotNull('tahun')
            ->distinct()->orderByDesc('tahun')
            ->pluck('tahun');

        return view('madrasah.arsip.index', compact('arsip', 'kategoriList', 'tahunList'));
    }

    // ─────────────────────────────────────────
    // FORM TAMBAH ARSIP
    // ─────────────────────────────────────────
    public function arsipCreate()
    {
        $kategoriList = KategoriArsip::untukMadrasah()->get();
        return view('madrasah.arsip.create', compact('kategoriList'));
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
            'tahun'      => 'nullable|integer|min:1990|max:' . (date('Y') + 1),
            'keterangan' => 'nullable|string|max:500',
        ], [
            'kategori_id.required' => 'Kategori dokumen wajib dipilih.',
            'judul.required'       => 'Judul dokumen wajib diisi.',
            'link_gdrive.required' => 'Link Google Drive wajib diisi.',
            'link_gdrive.url'      => 'Format link tidak valid.',
        ]);

        $madrasah = $this->madrasah();

        if ($this->sudahAdaArsipSerupa(ArsipMadrasah::class, 'madrasah_user_id', $madrasah->id, (int) $validated['kategori_id'], $validated['tahun'] ?? null)) {
            return back()->withInput()->withErrors([
                'kategori_id' => 'Madrasah ini sudah punya arsip untuk kategori ini' .
                    (($validated['tahun'] ?? null) ? " di tahun {$validated['tahun']}" : '') .
                    '. Silakan edit arsip yang sudah ada, atau isi tahun yang berbeda kalau ini dokumen tahun lain.',
            ]);
        }

        ArsipMadrasah::create([
            'madrasah_user_id' => $madrasah->id,
            'kategori_id'      => $validated['kategori_id'],
            'judul'            => $validated['judul'],
            'link_gdrive'      => $validated['link_gdrive'],
            'tahun'            => $validated['tahun'] ?? null,
            'keterangan'       => $validated['keterangan'] ?? null,
            'is_verified'      => false,
        ]);

        return redirect()->route('madrasah.arsip.index')
            ->with('success', "Arsip \"{$validated['judul']}\" berhasil disimpan.");
    }

    // ─────────────────────────────────────────
    // FORM EDIT ARSIP
    // ─────────────────────────────────────────
    public function arsipEdit($id)
    {
        $madrasah     = $this->madrasah();
        $arsip        = ArsipMadrasah::where('madrasah_user_id', $madrasah->id)->findOrFail($id);
        $kategoriList = KategoriArsip::untukMadrasah()->get();

        return view('madrasah.arsip.edit', compact('arsip', 'kategoriList'));
    }

    // ─────────────────────────────────────────
    // UPDATE ARSIP
    // ─────────────────────────────────────────
    public function arsipUpdate(Request $request, $id)
    {
        $madrasah = $this->madrasah();
        $arsip    = ArsipMadrasah::where('madrasah_user_id', $madrasah->id)->findOrFail($id);

        $validated = $request->validate([
            'kategori_id'  => 'required|exists:kategori_arsip,id',
            'judul'        => 'required|string|max:255',
            'link_gdrive'  => ['required', 'url', 'max:500', function ($attr, $val, $fail) {
                if (! $this->isValidGoogleDriveUrl($val)) {
                    $fail('Link harus dari Google Drive atau Google Docs.');
                }
            }],
            'tahun'      => 'nullable|integer|min:1990|max:' . (date('Y') + 1),
            'keterangan' => 'nullable|string|max:500',
        ]);

        $resetVerified = $arsip->link_gdrive !== $validated['link_gdrive'];

        if ($this->sudahAdaArsipSerupa(ArsipMadrasah::class, 'madrasah_user_id', $madrasah->id, (int) $validated['kategori_id'], $validated['tahun'] ?? null, $arsip->id)) {
            return back()->withInput()->withErrors([
                'kategori_id' => 'Madrasah ini sudah punya arsip lain untuk kategori ini' .
                    (($validated['tahun'] ?? null) ? " di tahun {$validated['tahun']}" : '') . '.',
            ]);
        }

        $arsip->update([
            'kategori_id'   => $validated['kategori_id'],
            'judul'         => $validated['judul'],
            'link_gdrive'   => $validated['link_gdrive'],
            'tahun'         => $validated['tahun'] ?? null,
            'keterangan'    => $validated['keterangan'] ?? null,
            'is_verified'   => $resetVerified ? false : $arsip->is_verified,
            'catatan_admin' => $resetVerified ? null  : $arsip->catatan_admin,
        ]);

        $msg = "Arsip \"{$arsip->judul}\" berhasil diperbarui.";
        if ($resetVerified) $msg .= ' Status verifikasi direset karena link diubah.';

        return redirect()->route('madrasah.arsip.index')->with('success', $msg);
    }

    // ─────────────────────────────────────────
    // HAPUS ARSIP
    // ─────────────────────────────────────────
    public function arsipDestroy($id)
    {
        $madrasah = $this->madrasah();
        $arsip    = ArsipMadrasah::where('madrasah_user_id', $madrasah->id)->findOrFail($id);
        $judul    = $arsip->judul;
        $arsip->delete();

        return redirect()->route('madrasah.arsip.index')
            ->with('success', "Arsip \"{$judul}\" berhasil dihapus.");
    }

    // ─────────────────────────────────────────
    // FORM EDIT PROFIL (No HP & Email)
    // ─────────────────────────────────────────
    public function profilForm()
    {
        $madrasah = $this->madrasah();
        return view('madrasah.profil', compact('madrasah'));
    }

    // ─────────────────────────────────────────
    // UPDATE PROFIL
    // ─────────────────────────────────────────
    public function profilUpdate(Request $request)
    {
        $madrasah = $this->madrasah();

        $validated = $request->validate([
            'no_hp' => 'required|string|max:20|regex:/^[0-9+\-\s]+$/',
            'email' => 'nullable|email|max:255',
        ], [
            'no_hp.required' => 'Nomor HP/WhatsApp wajib diisi.',
            'no_hp.regex'    => 'Format nomor HP tidak valid.',
            'email.email'    => 'Format email tidak valid.',
        ]);

        $madrasah->update([
            'no_hp' => $validated['no_hp'],
            'email' => $validated['email'] ?? null,
        ]);

        return redirect()->route('madrasah.profil.form')
            ->with('success', 'Profil berhasil diperbarui.');
    }

    // ─────────────────────────────────────────
    // GANTI PASSWORD
    // ─────────────────────────────────────────
    public function passwordForm()
    {
        return view('madrasah.password');
    }

    public function passwordUpdate(Request $request)
    {
        $request->validate([
            'password_lama'           => 'required|string',
            'password_baru'           => 'required|string|min:8|confirmed',
        ], [
            'password_lama.required'  => 'Password saat ini wajib diisi.',
            'password_baru.required'  => 'Password baru wajib diisi.',
            'password_baru.min'       => 'Password baru minimal 8 karakter.',
            'password_baru.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $madrasah = $this->madrasah();

        if (! Hash::check($request->password_lama, $madrasah->password)) {
            return back()->withErrors(['password_lama' => 'Password saat ini tidak sesuai.']);
        }

        if (Hash::check($request->password_baru, $madrasah->password)) {
            return back()->withErrors(['password_baru' => 'Password baru tidak boleh sama dengan password lama.']);
        }

        $madrasah->update([
            'password'         => Hash::make($request->password_baru),
            'password_changed' => true,
        ]);

        return redirect()->route('madrasah.dashboard')
            ->with('success', 'Password berhasil diubah.');
    }
}