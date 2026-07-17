@extends('layouts.app')
@section('title', 'Ajukan - ' . $jenis->nama)

@section('content')

    {{-- Hero --}}
    <div class="text-white py-10" style="background: linear-gradient(135deg, #052e16, #166534)">
        <div class="max-w-3xl mx-auto px-4">
            <a href="{{ route('home') }}"
                class="text-green-300 hover:text-white text-sm mb-3 inline-flex items-center gap-1.5 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali ke Beranda
            </a>
            <div class="text-center mt-2">
                <h1 class="text-2xl font-extrabold">Formulir Pengajuan</h1>
                <span
                    class="inline-block bg-white/20 text-white text-3xl font-semibold px-4 py-1 rounded-full mt-2">{{ $jenis->nama }}</span>
            </div>
        </div>
    </div>

    <div class="max-w-3xl mx-auto px-4 py-10">

        {{-- Syarat Pengajuan — warna merah sebagai warning --}}
        @if ($jenis->syarat)
            <div class="bg-red-50 border border-red-300 rounded-2xl p-5 mb-6 flex gap-4">
                <div class="w-9 h-9 bg-red-100 rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
                    </svg>
                </div>
                <div class="text-sm text-red-800">
                    <p class="font-bold mb-2">Syarat Pengajuan Dokumen {{ $jenis->nama }}</p>
                    <ul class="space-y-1.5">
                        @foreach (array_filter(array_map('trim', explode(';', $jenis->syarat))) as $i => $syarat)
                            <li class="flex items-start gap-2">
                                <span
                                    class="flex-shrink-0 w-5 h-5 rounded-full bg-red-200 text-red-700 text-xs font-bold flex items-center justify-center mt-0.5">{{ $i + 1 }}</span>
                                <span>{{ $syarat }}</span>
                            </li>
                        @endforeach
                    </ul>
                    <p>Dokumen digabungkan menjadi 1 file dengan format penamaan <strong>Nama_JenisAjuan.pdf</strong>.
                </div>
            </div>
        @endif

        {{-- Info kode ajuan --}}
        <div class="bg-blue-50 border border-blue-200 rounded-2xl p-5 mb-8 flex gap-4">
            <div class="w-9 h-9 bg-blue-100 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div class="text-sm text-blue-800">
                <p class="font-bold mb-1">Perhatian</p>
                <p>Setelah pengajuan berhasil, Anda akan mendapat <strong>Kode Ajuan</strong>. Simpan kode untuk memantau
                    status di menu <strong>Status Ajuan</strong>.</p>
            </div>
        </div>

        {{-- Tentukan apakah token wajib (semua jenis KECUALI S07) --}}
        @php $tokenWajib = false; @endphp

        <form action="{{ route('pengajuan.store', $jenis->id) }}" method="POST" enctype="multipart/form-data"
            x-data="{ fileName: '', fileSize: '', dragging: false }">
            @csrf

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-8 pt-8 pb-8 space-y-5">

                    {{-- Nama Guru --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                            Nama Guru <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="nama_guru" value="{{ old('nama_guru') }}"
                            placeholder="Nama lengkap guru"
                            class="w-full border rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 transition
                                  @error('nama_guru') border-red-400 bg-red-50 @else border-gray-200 @enderror">
                        @error('nama_guru')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- NIP --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                            Nomor Identitas <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="nip" value="{{ old('nip') }}" placeholder="NUPTK/NRG/PEG ID"
                            class="w-full border rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 transition
                                  @error('nip') border-red-400 bg-red-50 @else border-gray-200 @enderror">
                        @error('nip')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Nama Madrasah --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                            Nama Madrasah <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="nama_madrasah" value="{{ old('nama_madrasah') }}"
                            placeholder="Nama lengkap madrasah"
                            class="w-full border rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 transition
                                  @error('nama_madrasah') border-red-400 bg-red-50 @else border-gray-200 @enderror">
                        @error('nama_madrasah')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Token --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                            Kode Ajuan
                            @if ($tokenWajib)
                                <span class="text-red-500">*</span>
                            @else
                                <span class="text-gray-400 font-normal text-xs">(opsional)</span>
                            @endif
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                                </svg>
                            </div>
                            <input type="text" name="token" id="token" value="{{ old('token') }}" maxlength="6"
                                placeholder="{{ $tokenWajib ? 'Isilah sesuai kode ajuan yang ada di form yang anda buat' : 'Kode ajuan tidak wajib diisi' }}"
                                oninput="this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '').slice(0,6)"
                                class="w-full border rounded-xl pl-11 pr-16 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 transition
                                      @error('token') border-red-400 bg-red-50 @else border-gray-200 @enderror">
                            {{-- Counter 6 digit --}}
                            <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                                <span id="tokenCounter" class="text-xs text-gray-400 font-mono">0/6</span>
                            </div>
                        </div>
                        @error('token')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Email & No HP --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                                Email <span class="text-gray-400 font-normal text-xs">(opsional)</span>
                            </label>
                            <input type="email" name="email" value="{{ old('email') }}"
                                placeholder="email@madrasah.id"
                                class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 transition
                                      @error('email') border-red-400 bg-red-50 @else border-gray-200 @enderror">
                            @error('email')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                                No. HP <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="no_hp" value="{{ old('no_hp') }}" placeholder="08xxxxxxxxxx"
                                class="w-full border rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 transition
                                      @error('no_hp') border-red-400 bg-red-50 @else border-gray-200 @enderror">
                            @error('no_hp')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Upload PDF --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                            Upload Dokumen PDF <span class="text-red-500">*</span>
                        </label>
                        <div class="relative border-2 border-dashed rounded-xl p-6 text-center cursor-pointer transition-colors"
                            :class="dragging ? 'border-primary-500 bg-primary-50' : (fileName ? 'border-green-400 bg-green-50' :
                                'border-gray-200 hover:border-primary-400')"
                            @dragover.prevent="dragging = true" @dragleave.prevent="dragging = false"
                            @drop.prevent="dragging=false; const f=$event.dataTransfer.files[0]; if(f&&f.type==='application/pdf'){fileName=f.name;fileSize=(f.size/1024/1024).toFixed(2)+' MB';$refs.fileInput.files=$event.dataTransfer.files;}else{alert('Hanya file PDF!');}"
                            @click="$refs.fileInput.click()">
                            <input type="file" name="file_dokumen" accept="application/pdf,.pdf" class="hidden"
                                x-ref="fileInput"
                                @change="const f=$event.target.files[0];if(f){fileName=f.name;fileSize=(f.size/1024/1024).toFixed(2)+' MB';}">
                            <div x-show="!fileName">
                                <div class="w-12 h-12 bg-red-50 rounded-xl flex items-center justify-center mx-auto mb-3">
                                    <svg class="w-6 h-6 text-red-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <p class="text-sm font-semibold text-gray-700">Drag & drop atau <span
                                        class="text-primary-600">klik browse</span></p>
                                <p class="text-xs text-gray-400 mt-1">Hanya <strong class="text-red-500">PDF</strong> —
                                    Maks. 5MB</p>
                            </div>
                            <div x-show="fileName" class="flex items-center justify-center gap-3">
                                <div
                                    class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div class="text-left">
                                    <p class="font-semibold text-sm text-green-700" x-text="fileName"></p>
                                    <p class="text-xs text-gray-400" x-text="fileSize"></p>
                                </div>
                                <button type="button" @click.stop="fileName='';fileSize='';$refs.fileInput.value='';"
                                    class="w-6 h-6 bg-gray-200 hover:bg-gray-300 rounded-full flex items-center justify-center transition ml-1">
                                    <svg class="w-3 h-3 text-gray-500" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                        @error('file_dokumen')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Submit --}}
                    <div class="pt-2">
                        <button type="submit"
                            class="w-full text-white font-bold py-4 rounded-xl transition text-sm flex items-center justify-center gap-2 hover:opacity-90"
                            style="background:linear-gradient(135deg,#15803d,#22c55e);box-shadow:0 4px 15px rgba(34,197,94,0.3)">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                            </svg>
                            Kirim Pengajuan
                        </button>
                    </div>

                </div>
            </div>
        </form>
    </div>

    {{-- Counter token realtime --}}
    <script>
        const tokenInput = document.getElementById('token');
        const tokenCounter = document.getElementById('tokenCounter');
        if (tokenInput && tokenCounter) {
            const update = () => {
                const len = tokenInput.value.length;
                tokenCounter.textContent = len + '/6';
                tokenCounter.className = 'text-xs font-mono ' +
                    (len === 6 ? 'text-green-600 font-bold' : 'text-gray-400');
            };
            tokenInput.addEventListener('input', update);
            update();
        }
    </script>

    @push('scripts')
<script>
    // Cegah submit ulang saat back button
    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }

    // Disable tombol submit setelah diklik
    document.querySelector('form').addEventListener('submit', function() {
        const btn = this.querySelector('button[type="submit"]');
        btn.disabled = true;
        btn.innerHTML = `
            <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
            </svg>
            Mengirim...
        `;
    });
</script>
@endpush

@endsection
