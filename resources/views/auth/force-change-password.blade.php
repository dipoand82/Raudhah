<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        <h2 class="text-lg font-bold text-red-600 mb-2">⚠ Keamanan Akun</h2>
        <p>
            {{ __('Ini adalah login pertama Anda (atau akun baru). Demi keamanan data sekolah, Anda WAJIB mengganti password default sebelum bisa mengakses dashboard.') }}
        </p>
    </div>

    <form method="POST" action="{{ route('password.change.update') }}">
        @csrf

        <div class="mt-4">
            <x-input-label for="password" :value="__('Password Baru')" />
            <x-text-input id="password" class="block mt-1 w-full" 
                            type="password" 
                            name="password" 
                            required autocomplete="new-password" 
                            placeholder="Minimal 8 karakter" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Ulangi Password Baru')" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full" 
                            type="password" 
                            name="password_confirmation" 
                            required 
                            placeholder="Ketik ulang password tadi" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-6">
            <x-primary-button>
                {{ __('Simpan & Lanjutkan') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>