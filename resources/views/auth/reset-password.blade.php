<x-guest-layout>
    {{-- HEADER --}}
    <div class="w-full max-w-sm text-center mb-6">
        <div class="flex justify-center mb-4">
            <a href="{{ url('/') }}" class="p-2 bg-white rounded-2xl shadow-sm border border-gray-100">
                <img src="{{ asset('storage/logos/logo_smp_raudhah.png') }}" alt="Logo Raudhah"
                    class="h-16 w-auto object-contain flex items-center gap-3 z-10 hover:opacity-80 transition-opacity min-w-max">
            </a>
        </div>
        <h1 class="text-2xl font-extrabold text-gray-900 leading-tight mb-1">SMP IT Raudhah</h1>
        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-[0.2em]">Reset Kata Sandi</p>
    </div>

    <div class="w-full max-w-sm bg-white rounded-[2rem] shadow-xl border border-gray-100 p-6">
        <form method="POST" action="{{ route('password.store') }}" class="space-y-5">
            @csrf

            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <div class="w-full text-left">
                <label
                    class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1 px-1">Email</label>
                <input id="email"
                    class="block w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#1072B8] outline-none text-sm font-medium"
                    type="email" name="email" value="{{ old('email', $request->email) }}" required autofocus
                    autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div class="w-full text-left">
                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1 px-1">Password
                    Baru</label>
                <input id="password"
                    class="block w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#1072B8] outline-none text-sm font-medium"
                    type="password" name="password" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div class="w-full text-left">
                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1 px-1">Konfirmasi
                    Password</label>
                <input id="password_confirmation"
                    class="block w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#1072B8] outline-none text-sm font-medium"
                    type="password" name="password_confirmation" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>

            <button type="submit"
                class="w-full py-3.5 bg-[#1072B8] text-white rounded-xl font-bold shadow-lg hover:bg-[#0d5e98] active:scale-95 transition-all duration-200 uppercase tracking-widest text-xs">
                Reset Password
            </button>
        </form>

        <div class="mt-6 text-center pt-4 border-t border-gray-50">
            <a href="{{ route('login') }}"
                class="text-xs font-bold text-gray-400 hover:text-[#1072B8] inline-flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali ke Login
            </a>
        </div>
    </div>
</x-guest-layout>
