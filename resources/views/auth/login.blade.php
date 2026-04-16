<x-guest-layout>
    {{-- Hapus pembungkus min-h-screen di sini agar tidak double --}}

    {{-- HEADER --}}
    <div class="w-full max-w-sm text-center mb-6">
        <div class="flex justify-center mb-4">
            <a href="{{ url('/') }}"class="p-2 bg-white rounded-2xl shadow-sm border border-gray-100">
                <img src="{{ asset('storage/logos/logo_smp_raudhah.png') }}"
                     alt="Logo Raudhah"
                     class="h-16 w-auto object-contain flex items-center gap-3 z-10 hover:opacity-80 transition-opacity min-w-max">
            </a>
        </div>
        <h1 class="text-2xl font-extrabold text-gray-900 leading-tight mb-1">SMP IT Raudhah</h1>
        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-[0.2em]">Masuk Ke Sistem Pembayaran SPP</p>
    </div>

    {{-- TABEL LOGIN --}}
    <div class="w-full max-w-sm bg-white rounded-[2rem] shadow-xl border border-gray-100 p-6">
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf

            {{-- Input Email --}}
            <div class="w-full text-left"> {{-- Tambahkan text-left --}}
                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1 px-1">Email</label>
                <input id="email" class="block w-full px-4 py-3 bg-gray-50 border-gray-200 border rounded-xl focus:ring-2 focus:ring-[#1072B8] outline-none text-sm font-medium" type="email" name="email" value="{{ old('email') }}" required autofocus />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            {{-- Input Password dengan Icon Mata --}}
            <div class="w-full text-left">
                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1 px-1">Password</label>
                <div class="relative">
                    <input id="password" class="block w-full px-4 py-3 bg-gray-50 border-gray-200 border rounded-xl focus:ring-2 focus:ring-[#1072B8] outline-none text-sm font-medium pr-12" type="password" name="password" required />

                    {{-- Tombol Toggle --}}
                    <button type="button" id="togglePassword" class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-[#1072B8] transition-colors focus:outline-none">

                        {{-- Icon Mata Terbuka (Sembunyi by default) --}}
                        <svg id="eyeOpen" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>

                        {{-- Icon Mata Tertutup (Tampil by default) --}}
                        <svg id="eyeClosed" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                        </svg>

                    </button>
                </div>
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            {{-- Tombol & Fitur Lainnya --}}
            <div class="flex items-center justify-between px-1">
                <label class="inline-flex items-center cursor-pointer">
                    <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-[#1072B8] focus:ring-[#1072B8]" name="remember">
                    <span class="ms-2 text-xs text-gray-500">Ingat saya</span>
                </label>
                @if (Route::has('password.request'))
                    <a class="text-xs text-[#1072B8] hover:underline font-bold" href="{{ route('password.request') }}">Lupa sandi?</a>
                @endif
            </div>

            <button type="submit" class="w-full py-3.5 bg-[#1072B8] text-white rounded-xl font-bold shadow-lg hover:bg-[#0d5e98] active:scale-95 transition-all duration-200 uppercase tracking-widest text-xs">
                MASUK SEKARANG
            </button>
        </form>

        <div class="mt-6 text-center pt-4 border-t border-gray-50">
            <a href="{{ url('/') }}" class="text-xs font-bold text-gray-400 hover:text-[#1072B8] inline-flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Kembali ke Beranda
            </a>
        </div>
    </div>

    {{-- Script untuk Toggle Password --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const togglePassword = document.querySelector('#togglePassword');
            const password = document.querySelector('#password');
            const eyeOpen = document.querySelector('#eyeOpen');
            const eyeClosed = document.querySelector('#eyeClosed');

            togglePassword.addEventListener('click', function () {
                // Toggle tipe atribut (password <-> text)
                const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                password.setAttribute('type', type);

                // Toggle logo mata terbuka/tertutup
                eyeOpen.classList.toggle('hidden');
                eyeClosed.classList.toggle('hidden');
            });
        });
    </script>
</x-guest-layout>
