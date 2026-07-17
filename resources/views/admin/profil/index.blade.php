@extends('layouts.admin')
@section('title', 'Edit Profil Organisasi')

@section('content')
<div class="max-w-2xl space-y-3">

    <div class="mb-1 fade-in">
        <h1 class="text-sm font-semibold text-zinc-900">Profil Organisasi</h1>
        <p class="text-xs text-zinc-400 mt-0.5">Kelola logo, informasi, dan visi misi yang tampil di halaman publik</p>
    </div>

    {{-- ═══ SECTION LOGO ═══ --}}
    <div class="bg-white rounded-xl border border-zinc-100 p-5 fade-in">
        <h2 class="text-xs font-semibold text-zinc-800 mb-4 flex items-center gap-2">
            <div class="w-6 h-6 rounded-md flex items-center justify-center bg-zinc-100">
                <svg class="w-3.5 h-3.5 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            Logo Organisasi
        </h2>

        {{-- Preview logo saat ini --}}
        <div class="flex items-center gap-4 mb-4">
            <div class="w-14 h-14 rounded-xl border border-dashed border-zinc-200 flex items-center justify-center bg-zinc-50 overflow-hidden flex-shrink-0" id="logoPreview">
                @if(\App\Models\ProfilOrganisasi::getValue('logo_path'))
                    <img src="{{ Storage::url(\App\Models\ProfilOrganisasi::getValue('logo_path')) }}"
                         alt="Logo" class="w-full h-full object-contain p-1">
                @else
                    <svg class="w-6 h-6 text-zinc-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                @endif
            </div>
            <div>
                <p class="text-xs font-medium text-zinc-700">
                    {{ \App\Models\ProfilOrganisasi::getValue('logo_path') ? 'Logo terpasang' : 'Belum ada logo' }}
                </p>
                <p class="text-2xs text-zinc-400 mt-0.5">Format: PNG, JPG, SVG. Maks 2MB.<br>Rekomendasi: transparan (PNG), min 200x200px</p>
            </div>
        </div>

        {{-- Form upload logo --}}
        <form action="{{ route('admin.profil.update') }}" method="POST" enctype="multipart/form-data" class="space-y-2.5">
            @csrf
            <div class="border border-dashed border-zinc-200 rounded-lg p-3.5 hover:border-zinc-400 transition cursor-pointer" onclick="document.getElementById('logoInput').click()">
                <input type="file" name="logo" id="logoInput" accept="image/png,image/jpg,image/jpeg,image/svg+xml"
                       class="hidden" onchange="previewLogo(this)">
                <div class="text-center">
                    <svg class="w-6 h-6 text-zinc-300 mx-auto mb-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                    </svg>
                    <p class="text-xs text-zinc-500 font-medium">Klik untuk pilih file logo</p>
                    <p class="text-2xs text-zinc-400 mt-0.5" id="logoFileName">atau drag &amp; drop di sini</p>
                </div>
            </div>
            <button type="submit" class="w-full py-2 rounded-lg text-xs font-semibold text-white bg-zinc-900 hover:bg-zinc-700 transition">
                Upload Logo
            </button>
        </form>

        {{-- Tombol hapus logo --}}
        @if(\App\Models\ProfilOrganisasi::getValue('logo_path'))
        <form action="{{ route('admin.profil.logo.delete') }}" method="POST" class="mt-2"
              data-confirm="Yakin hapus logo?" data-confirm-btn="Ya, Hapus">
            @csrf @method('DELETE')
            <button type="submit" class="w-full py-2 rounded-lg text-xs font-medium text-red-600 border border-red-200 hover:bg-red-50 transition">
                Hapus Logo
            </button>
        </form>
        @endif
    </div>

    {{-- ═══ SECTION PROFIL ═══ --}}
    <div class="bg-white rounded-xl border border-zinc-100 p-5 fade-in">
        <h2 class="text-xs font-semibold text-zinc-800 mb-4 flex items-center gap-2">
            <div class="w-6 h-6 rounded-md flex items-center justify-center bg-zinc-100">
                <svg class="w-3.5 h-3.5 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </div>
            Informasi Organisasi
        </h2>

        <form action="{{ route('admin.profil.update') }}" method="POST" class="space-y-3">
            @csrf

            @foreach([
                ['name' => 'nama_organisasi', 'label' => 'Nama Organisasi', 'type' => 'input',    'required' => true],
                ['name' => 'alamat',          'label' => 'Alamat',          'type' => 'textarea', 'required' => true],
                ['name' => 'telepon',         'label' => 'Telepon',         'type' => 'input',    'required' => true],
                ['name' => 'email',           'label' => 'Email',           'type' => 'input',    'required' => true],
            ] as $field)
            <div>
                <label class="block text-2xs font-medium text-zinc-500 mb-1.5 uppercase tracking-wide">
                    {{ $field['label'] }} @if($field['required'])<span class="text-red-500">*</span>@endif
                </label>
                @if($field['type'] === 'textarea')
                    <textarea name="{{ $field['name'] }}" rows="2"
                              class="w-full border border-zinc-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:ring-1 focus:ring-zinc-300 resize-none">{{ $profil[$field['name']]->value ?? '' }}</textarea>
                @else
                    <input type="text" name="{{ $field['name'] }}" value="{{ $profil[$field['name']]->value ?? '' }}"
                           class="w-full border border-zinc-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:ring-1 focus:ring-zinc-300">
                @endif
            </div>
            @endforeach

            <button type="submit" class="w-full py-2 rounded-lg text-xs font-semibold text-white bg-zinc-900 hover:bg-zinc-700 transition">
                Simpan Informasi
            </button>
        </form>
    </div>

    {{-- ═══ SECTION VISI MISI ═══ --}}
    <div class="bg-white rounded-xl border border-zinc-100 p-5 fade-in">
        <h2 class="text-xs font-semibold text-zinc-800 mb-4 flex items-center gap-2">
            <div class="w-6 h-6 rounded-md flex items-center justify-center bg-zinc-100">
                <svg class="w-3.5 h-3.5 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            Visi &amp; Misi
        </h2>

        <form action="{{ route('admin.profil.update') }}" method="POST" class="space-y-3">
            @csrf

            <div>
                <label class="block text-2xs font-medium text-zinc-500 mb-1.5 uppercase tracking-wide">Visi <span class="text-red-500">*</span></label>
                <textarea name="visi" rows="3"
                          class="w-full border border-zinc-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:ring-1 focus:ring-zinc-300 resize-none">{{ $profil['visi']->value ?? '' }}</textarea>
            </div>

            @for($i = 1; $i <= 6; $i++)
            <div>
                <label class="block text-2xs font-medium text-zinc-500 mb-1.5 uppercase tracking-wide">
                    Misi {{ $i }} @if($i === 1)<span class="text-red-500">*</span>@endif
                </label>
                <input type="text" name="misi_{{ $i }}" value="{{ $profil['misi_'.$i]->value ?? '' }}"
                       placeholder="{{ $i > 1 ? 'Opsional' : '' }}"
                       class="w-full border border-zinc-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:ring-1 focus:ring-zinc-300">
            </div>
            @endfor

            <button type="submit" class="w-full py-2 rounded-lg text-xs font-semibold text-white bg-zinc-900 hover:bg-zinc-700 transition">
                Simpan Visi &amp; Misi
            </button>
        </form>
    </div>

</div>

@push('scripts')
<script>
function previewLogo(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        document.getElementById('logoFileName').textContent = file.name;
        const reader = new FileReader();
        reader.onload = e => {
            const preview = document.getElementById('logoPreview');
            preview.innerHTML = '<img src="' + e.target.result + '" class="w-full h-full object-contain p-1">';
        };
        reader.readAsDataURL(file);
    }
}
</script>
@endpush

@endsection