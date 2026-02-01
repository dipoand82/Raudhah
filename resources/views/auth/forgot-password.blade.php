<x-guest-layout>
    {{-- HEADER --}}
    <div class="w-full max-w-sm text-center mb-6">
        <div class="flex justify-center mb-4">
            <div class="p-2 bg-white rounded-2xl shadow-sm border border-gray-100">
                <img src="{{ asset('storage/logos/logo_smp_raudhah.png') }}" 
                     alt="Logo Raudhah" 
                     class="h-16 w-auto object-contain">
            </div>
        </div>
        <h1 class="text-2xl font-extrabold text-gray-900 leading-tight mb-1">Lupa Password</h1>
        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-[0.2em]">SMP IT Raudhah</p>
    </div>

    {{-- CARD FORGOT PASSWORD --}}
    <div class="w-full max-w-sm bg-white rounded-[2rem] shadow-xl border border-gray-100 p-8">
        <div class="mb-6 text-center">
            <p class="text-xs text-gray-500 leading-relaxed">
                {{ __('Masukkan alamat email Anda untuk reset password.') }}
            </p>
        </div>

        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
            @csrf

            {{-- Input Email --}}
            <div class="w-full text-left">
                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1 px-1">Alamat Email</label>
                <input id="email" 
                       class="block w-full px-4 py-3 bg-gray-50 border-gray-200 border rounded-xl focus:ring-2 focus:ring-[#1072B8] focus:border-transparent outline-none text-sm font-medium transition-all" 
                       type="email" 
                       name="email" 
                       value="{{ old('email') }}" 
                       placeholder=" "
                       required 
                       autofocus />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            {{-- Tombol Submit --}}
            <button type="submit" class="w-full py-3.5 bg-[#1072B8] text-white rounded-xl font-bold shadow-lg hover:bg-[#0d5e98] active:scale-95 transition-all duration-200 uppercase tracking-widest text-xs">
                {{ __('Kirim Link Reset') }}
            </button>
        </form>

        <div class="mt-8 text-center pt-4 border-t border-gray-50 space-y-3">
            <a href="{{ route('login') }}" class="text-xs font-bold text-[#1072B8] hover:underline block">
                Kembali ke Login
            </a>
            <a href="{{ url('/') }}" class="text-xs font-bold text-gray-400 hover:text-[#1072B8] inline-flex items-center gap-2 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali ke Beranda
            </a>
        </div>
    </div>
</x-guest-layout>