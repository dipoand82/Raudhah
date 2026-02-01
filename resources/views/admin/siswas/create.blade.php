{{-- <x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Tambah Siswa Manual') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-8 shadow-sm rounded-lg border border-gray-200">
                
                <form method="POST" action="{{ route('admin.siswas.store') }}">
                    @csrf

                    <div class="mb-4">
                        <x-input-label for="nisn" :value="__('NISN')" />
                        <x-text-input id="nisn" class="block mt-1 w-full" type="number" name="nisn" :value="old('nisn')" required placeholder="Masukkan NISN Siswa" />
                        <x-input-error :messages="$errors->get('nisn')" class="mt-2" />
                    </div>

                    <div class="mb-4">
                        <x-input-label for="nama" :value="__('Nama Lengkap Siswa')" />
                        <x-text-input id="nama" class="block mt-1 w-full" type="text" name="nama" :value="old('nama')" required placeholder="Nama Lengkap" />
                        <x-input-error :messages="$errors->get('nama')" class="mt-2" />
                    </div>

                    <div class="mb-4">
                        <x-input-label for="jk" :value="__('Jenis Kelamin')" />
                        <div class="flex gap-6 mt-2">
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="radio" name="jenis_kelamin" value="L" class="form-radio text-indigo-600 border-gray-300 focus:ring-indigo-500" checked>
                                <span class="ml-2 text-gray-700">Laki-laki</span>
                            </label>
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="radio" name="jenis_kelamin" value="P" class="form-radio text-indigo-600 border-gray-300 focus:ring-indigo-500">
                                <span class="ml-2 text-gray-700">Perempuan</span>
                            </label>
                        </div>
                        <x-input-error :messages="$errors->get('jenis_kelamin')" class="mt-2" />
                    </div>

                    <div class="mb-6">
                        <x-input-label for="email" :value="__('Email (Untuk Login)')" />
                        <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required placeholder="contoh@gmail.com" />
                        <p class="text-xs text-gray-500 mt-1">*Password default: 12345678</p>
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                        <a href="{{ route('admin.siswas.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 transition font-medium">Batal</a>
                        <x-primary-button>{{ __('Simpan Data Siswa') }}</x-primary-button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout> --}}