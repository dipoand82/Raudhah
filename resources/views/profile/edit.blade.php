<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profile Admin') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

           {{-- 1. ALERT BERHASIL (SINKRON DENGAN KEDUA CONTROLLER) --}}
    {{-- Menangkap 'status' dari profil/password atau 'success' jika kamu sudah ubah di controller --}}
    @if (session('status') || session('success'))
        <x-alert-success timeout="5000">
            {{-- Jika status berisi 'profile-updated', tampilkan pesan manual, jika tidak tampilkan isi session-nya --}}
            @if(session('status') === 'profile-updated')
                Profil berhasil diperbarui!
            @else
                {{ session('status') ?? session('success') }}
            @endif
        </x-alert-success>
    @endif

    {{-- 2. ALERT GAGAL (SESSION ERROR MANUAL) --}}
    @if (session('error'))
        <x-alert-danger>
            {{ session('error') }}
        </x-alert-danger>
    @endif

    {{-- 3. ALERT VALIDASI ERROR (SINKRON DENGAN BAG PASSWORD) --}}
    @if ($errors->any() || $errors->updatePassword->any())
        <x-alert-danger timeout="8000">
                {{-- Ambil error umum --}}
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach

                {{-- Ambil error khusus password bag --}}
                @foreach ($errors->updatePassword->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
        </x-alert-danger>
    @endif
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

             <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            {{-- <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>  --}}
        </div>
    </div>
</x-app-layout>
