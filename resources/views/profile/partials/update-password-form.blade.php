<section class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
    {{-- 1. NOTIFIKASI SUKSES --}}
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Ubah Password') }}
        </h2>
        <p class="mt-1 text-sm text-gray-600">
            {{ __('Pastikan akun Anda menggunakan password yang panjang dan acak untuk tetap aman.') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6" autocomplete="off">
        @csrf
        @method('put')

        {{-- Input palsu untuk mengelabui auto-fill browser --}}
        <input type="text" style="display:none;" aria-hidden="true">
        <input type="password" style="display:none;" aria-hidden="true">

        {{-- PASSWORD SAAT INI --}}
        <div>
            <x-input-label for="update_password_current_password" :value="__('Password Saat Ini')" />
            <x-text-input id="update_password_current_password" name="current_password" type="password"
                class="mt-1 block w-full" readonly onfocus="this.removeAttribute('readonly');" required />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
        </div>

        {{-- PASSWORD BARU --}}
        <div>
            <x-input-label for="update_password_password" :value="__('Password Baru')" />
            <x-text-input id="update_password_password" name="password" type="password" class="mt-1 block w-full"
                readonly onfocus="this.removeAttribute('readonly');" required />
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
        </div>

        {{-- KONFIRMASI PASSWORD --}}
        <div>
            <x-input-label for="update_password_password_confirmation" :value="__('Konfirmasi Password Baru')" />
            <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password"
                class="mt-1 block w-full" readonly onfocus="this.removeAttribute('readonly');" required />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Simpan Password Baru') }}</x-primary-button>

            {{-- Feedback kecil di samping tombol (Opsional jika sudah ada alert di atas) --}}
            @if (session('status') === 'password-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600 italic">{{ __('Tersimpan.') }}</p>
            @endif
        </div>
    </form>
</section>
