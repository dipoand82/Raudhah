<x-guest-layout>

    <div class="w-full max-w-sm text-center mb-6">
        <div class="flex justify-center mb-4">
            <a href="{{ url('/') }}" class="p-2 bg-white rounded-2xl shadow-sm border border-gray-100">
                <img src="{{ asset('storage/logos/logo_smp_raudhah.png') }}" alt="Logo Raudhah"
                    class="h-16 w-auto object-contain hover:opacity-80 transition-opacity">
            </a>
        </div>
        <h1 class="text-2xl font-extrabold text-gray-900 leading-tight mb-1">
            SMP IT Raudhah
        </h1>
        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-[0.2em]">
            Keamanan Akun
        </p>
    </div>

    <div class="w-full max-w-sm bg-white rounded-[2rem] shadow-xl border border-gray-100 p-6">

        <div
            class="mb-4 text-md text-center text-fontsemibold text-justifycenter text-orange-500 bg-orange-50 border border-orange-100 rounded-xl p-3">
            Ini adalah Login pertama Anda. Silakan ubah password untuk melanjutkan.
        </div>

        <form method="POST" action="{{ route('password.change.update') }}" class="space-y-5">
            @csrf

            <div class="text-left">
                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1 px-1">
                    Password Baru
                </label>
                <input id="password" name="password" type="password" required autocomplete="new-password"
                    class="block w-full px-4 py-3 bg-gray-50 border-gray-200 border rounded-xl focus:ring-2 focus:ring-[#1072B8] outline-none text-sm font-medium"
                    placeholder="Minimal 8 karakter" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div class="text-left">
                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1 px-1">
                    Ulangi Password
                </label>
                <input id="password_confirmation" name="password_confirmation" type="password" required
                    class="block w-full px-4 py-3 bg-gray-50 border-gray-200 border rounded-xl focus:ring-2 focus:ring-[#1072B8] outline-none text-sm font-medium"
                    placeholder="Ketik ulang password" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>

            <button type="submit"
                class="w-full py-3.5 bg-[#1072B8] text-white rounded-xl font-bold shadow-lg hover:bg-[#0d5e98] active:scale-95 transition-all duration-200 uppercase tracking-widest text-xs">
                SIMPAN & LANJUTKAN
            </button>
        </form>

        <div class="mt-6 text-center pt-4 border-t border-gray-50">
            <a href="{{ url('/') }}"
                class="text-xs font-bold text-gray-400 hover:text-[#1072B8] inline-flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali ke Beranda
            </a>
        </div>

    </div>

</x-guest-layout>
