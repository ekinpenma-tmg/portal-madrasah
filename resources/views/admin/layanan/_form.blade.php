{{-- Dipakai oleh create.blade.php & edit.blade.php --}}
@php
    $l = $layanan ?? null;
@endphp

<div>
    <label class="block text-xs font-medium text-zinc-600 mb-1.5">Kategori <span class="text-red-500">*</span></label>
    <input type="text" name="kategori" list="kategori-options" value="{{ old('kategori', $l->kategori ?? '') }}" required
           class="w-full border border-zinc-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:ring-1 focus:ring-zinc-300 @error('kategori') border-red-300 @enderror"
           placeholder="Contoh: Pelayanan Perizinan">
    <datalist id="kategori-options">
        @foreach ($kategoriList as $k)
            <option value="{{ $k }}"></option>
        @endforeach
    </datalist>
    <p class="text-zinc-400 text-2xs mt-1">Pilih dari saran, atau ketik kategori baru. Layanan dengan kategori yang sama otomatis dikelompokkan di halaman publik.</p>
    @error('kategori')<p class="text-red-500 text-2xs mt-1">{{ $message }}</p>@enderror
</div>

<div>
    <label class="block text-xs font-medium text-zinc-600 mb-1.5">Nama Layanan <span class="text-red-500">*</span></label>
    <input type="text" name="nama" value="{{ old('nama', $l->nama ?? '') }}" required
           class="w-full border border-zinc-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:ring-1 focus:ring-zinc-300 @error('nama') border-red-300 @enderror"
           placeholder="Contoh: Standar Pelayanan Izin Pendirian Madrasah">
    @error('nama')<p class="text-red-500 text-2xs mt-1">{{ $message }}</p>@enderror
</div>

<div>
    <label class="block text-xs font-medium text-zinc-600 mb-1.5">Ringkasan Singkat</label>
    <input type="text" name="ringkasan" value="{{ old('ringkasan', $l->ringkasan ?? '') }}" maxlength="255"
           class="w-full border border-zinc-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:ring-1 focus:ring-zinc-300"
           placeholder="1 kalimat singkat, tampil di daftar layanan">
</div>

<div class="grid grid-cols-2 gap-3">
    <div>
        <label class="block text-xs font-medium text-zinc-600 mb-1.5">Ikon</label>
        <select name="icon" class="w-full border border-zinc-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:ring-1 focus:ring-zinc-300">
            @foreach ($iconOptions as $key => $path)
                <option value="{{ $key }}" @selected(old('icon', $l->icon ?? 'dokumen') === $key)>{{ ucfirst($key) }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-xs font-medium text-zinc-600 mb-1.5">Urutan Tampil</label>
        <input type="number" name="urutan" value="{{ old('urutan', $l->urutan ?? 0) }}" min="0"
               class="w-full border border-zinc-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:ring-1 focus:ring-zinc-300">
    </div>
</div>

<div>
    <label class="block text-xs font-medium text-zinc-600 mb-1.5">Deskripsi</label>
    <textarea name="deskripsi" rows="3" class="w-full border border-zinc-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:ring-1 focus:ring-zinc-300 resize-none"
        placeholder="Penjelasan lengkap tentang layanan ini">{{ old('deskripsi', $l->deskripsi ?? '') }}</textarea>
</div>

<div>
    <label class="block text-xs font-medium text-zinc-600 mb-1.5">Dasar Hukum</label>
    <textarea name="dasar_hukum" rows="2" class="w-full border border-zinc-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:ring-1 focus:ring-zinc-300 resize-none"
        placeholder="Contoh: PMA No. 90 Tahun 2013 tentang ...">{{ old('dasar_hukum', $l->dasar_hukum ?? '') }}</textarea>
</div>

<div>
    <label class="block text-xs font-medium text-zinc-600 mb-1.5">Persyaratan <span class="text-zinc-400 font-normal">(1 baris = 1 syarat)</span></label>
    <textarea name="syarat" rows="4" class="w-full border border-zinc-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:ring-1 focus:ring-zinc-300 resize-none font-mono"
        placeholder="Fotokopi ijazah asli&#10;Fotokopi KTP&#10;Surat keterangan dari madrasah">{{ old('syarat', $l->syarat ?? '') }}</textarea>
</div>

<div>
    <label class="block text-xs font-medium text-zinc-600 mb-1.5">Alur / Tahapan <span class="text-zinc-400 font-normal">(1 baris = 1 tahapan, berurutan)</span></label>
    <textarea name="alur" rows="4" class="w-full border border-zinc-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:ring-1 focus:ring-zinc-300 resize-none font-mono"
        placeholder="Datang ke loket Seksi Pendidikan Madrasah&#10;Serahkan berkas persyaratan&#10;Tunggu verifikasi petugas&#10;Ambil dokumen">{{ old('alur', $l->alur ?? '') }}</textarea>
</div>

<div class="grid grid-cols-2 gap-3">
    <div>
        <label class="block text-xs font-medium text-zinc-600 mb-1.5">Waktu Proses</label>
        <input type="text" name="waktu_proses" value="{{ old('waktu_proses', $l->waktu_proses ?? '') }}"
               class="w-full border border-zinc-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:ring-1 focus:ring-zinc-300"
               placeholder="Contoh: 1 hari kerja">
    </div>
    <div>
        <label class="block text-xs font-medium text-zinc-600 mb-1.5">Biaya</label>
        <input type="text" name="biaya" value="{{ old('biaya', $l->biaya ?? '') }}"
               class="w-full border border-zinc-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:ring-1 focus:ring-zinc-300"
               placeholder="Contoh: Gratis">
    </div>
</div>

<div>
    <label class="block text-xs font-medium text-zinc-600 mb-1.5">Lampiran Dokumen Resmi (PDF)</label>
    <input type="file" name="sop_file" accept="application/pdf"
           class="w-full text-xs text-zinc-500 file:mr-3 file:py-1 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-medium file:bg-zinc-100 file:text-zinc-600 hover:file:bg-zinc-200">
    <p class="text-zinc-400 text-2xs mt-1">
        Opsional. Maksimal 10MB, format PDF — untuk SK/Standar Pelayanan resmi yang bertanda tangan.
        @if (! empty($l) && $l->sop_file_path)
            File saat ini: <span class="font-medium text-zinc-600">{{ $l->sop_nama_asli ?? basename($l->sop_file_path) }}</span> — unggah file baru untuk menggantinya.
        @endif
    </p>
    @error('sop_file')<p class="text-red-500 text-2xs mt-1">{{ $message }}</p>@enderror
</div>

@if (! empty($l))
    <div class="flex items-center gap-2 pt-1">
        <input type="hidden" name="aktif" value="0">
        <input type="checkbox" name="aktif" id="aktif" value="1" @checked(old('aktif', $l->aktif))
               class="rounded border-zinc-300 text-zinc-900 focus:ring-zinc-300">
        <label for="aktif" class="text-xs font-medium text-zinc-600">Tampilkan di halaman publik</label>
    </div>
@endif
