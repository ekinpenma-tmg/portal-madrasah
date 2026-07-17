<x-guest-layout>

    {{-- Session Status --}}
    @if (session('status'))
        <div class="mb-3 px-3 py-2 rounded-lg text-xs font-medium text-green-700 bg-green-50 border border-green-100">
            {{ session('status') }}
        </div>
    @endif

    {{-- Error --}}
    @if ($errors->any())
        <div class="mb-3 px-3 py-2 rounded-lg text-xs font-medium text-red-600 bg-red-50 border border-red-100">
            Email atau password yang Anda masukkan salah.
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" id="loginForm">
        @csrf

        {{-- Email --}}
        <div class="mb-3">
            <label for="email" class="sr-only">Email</label>
            <div class="relative">
                <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-[18px] h-[18px] text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9 8.955 8.955 0 004.99-1.507"/>
                </svg>
                <input type="email" id="email" name="email" value="{{ old('email') }}"
                    placeholder="email@madrasah.sch.id" required autofocus autocomplete="username"
                    class="w-full pl-11 pr-4 py-2.5 border border-zinc-200 rounded-full text-sm text-zinc-800 placeholder-zinc-400 focus:outline-none focus:ring-1 focus:ring-zinc-400 focus:border-zinc-400 transition" />
            </div>
            @error('email')
                <p class="mt-1 ml-1 text-2xs text-red-500">{{ $message }}</p>
            @enderror
        </div>

        {{-- Password --}}
        <div class="mb-4">
            <label for="password" class="sr-only">Password</label>
            <div class="relative">
                <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-[18px] h-[18px] text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 10-8 0v4h8z"/>
                </svg>
                <input type="password" id="password" name="password" placeholder="••••••••" required
                    autocomplete="current-password"
                    class="w-full pl-11 pr-11 py-2.5 border border-zinc-200 rounded-full text-sm text-zinc-800 placeholder-zinc-300 focus:outline-none focus:ring-1 focus:ring-zinc-400 focus:border-zinc-400 transition" />
                <button type="button" id="togglePassword"
                    class="absolute right-4 top-1/2 -translate-y-1/2 text-zinc-400 hover:text-zinc-600 transition">
                    <svg id="eyeShow" xmlns="http://www.w3.org/2000/svg" class="w-[18px] h-[18px]" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <svg id="eyeHide" xmlns="http://www.w3.org/2000/svg" class="w-[18px] h-[18px] hidden" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                            d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                    </svg>
                </button>
            </div>
            @error('password')
                <p class="mt-1 ml-1 text-2xs text-red-500">{{ $message }}</p>
            @enderror
        </div>

        {{-- Remember Me --}}
        <div class="flex items-center justify-between mb-4">
            <label for="remember_me" class="flex items-center gap-2 cursor-pointer">
                <input id="remember_me" type="checkbox" name="remember"
                    class="w-3.5 h-3.5 rounded border-zinc-300 text-amber-600 focus:ring-amber-500 cursor-pointer">
                <span class="text-xs text-zinc-500">Ingat saya</span>
            </label>
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}"
                    class="text-xs text-amber-700 hover:text-amber-800 font-medium transition">
                    Lupa password?
                </a>
            @endif
        </div>

        {{-- Submit --}}
        <button type="submit" id="loginBtn"
            class="w-full py-3 rounded-full text-white font-semibold text-sm tracking-wide transition-all duration-200 hover:opacity-90 active:scale-95 relative overflow-hidden"
            style="background: #0c0c0c;">
            <span id="btnText">Masuk</span>
            <span id="btnLoading" class="hidden flex items-center justify-center gap-2">
                <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                        stroke-width="4" />
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z" />
                </svg>
                Memproses...
            </span>
        </button>

        {{-- Info: register dinonaktifkan --}}
        <p class="text-center text-2xs text-zinc-300 mt-3">
            Akses hanya untuk admin yang terdaftar
        </p>
    </form>

    <script>
        // Toggle password
        document.getElementById('togglePassword').addEventListener('click', function() {
            const input = document.getElementById('password');
            const show = document.getElementById('eyeShow');
            const hide = document.getElementById('eyeHide');
            if (input.type === 'password') {
                input.type = 'text';
                show.classList.add('hidden');
                hide.classList.remove('hidden');
            } else {
                input.type = 'password';
                show.classList.remove('hidden');
                hide.classList.add('hidden');
            }
        });

        // Loading state
        document.getElementById('loginForm').addEventListener('submit', function() {
            document.getElementById('btnText').classList.add('hidden');
            document.getElementById('btnLoading').classList.remove('hidden');
            document.getElementById('loginBtn').disabled = true;
        });
    </script>

</x-guest-layout>