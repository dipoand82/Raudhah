<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Profil Sekolah') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6">
            
            @if(session('success'))
                <div class="mb-4 bg-green-100 text-green-700 p-4 rounded shadow-sm">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <form method="POST" action="{{ route('admin.profil.update') }}" enctype="multipart/form-data">
                        @csrf
                        @method('PATCH')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            
                            <div class="space-y-4">
                                <h3 class="text-lg font-bold text-gray-700 border-b pb-2">Identitas Sekolah</h3>
                                
                                <div>
                                    <x-input-label for="nama_sekolah" :value="__('Nama Sekolah')" />
                                    <x-text-input id="nama_sekolah" class="block mt-1 w-full" type="text" name="nama_sekolah" :value="old('nama_sekolah', $profil->nama_sekolah)" required />
                                </div>

                                <div>
                                    <x-input-label for="telepon" :value="__('Nomor Telepon')" />
                                    <x-text-input id="telepon" class="block mt-1 w-full" type="text" name="telepon" :value="old('telepon', $profil->telepon)" />
                                </div>

                                <div>
                                    <x-input-label for="email" :value="__('Email Resmi')" />
                                    <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $profil->email)" />
                                </div>

                                <div>
                                    <x-input-label for="alamat" :value="__('Alamat Lengkap')" />
                                    <textarea id="alamat" name="alamat" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" rows="3">{{ old('alamat', $profil->alamat) }}</textarea>
                                </div>
                            </div>

                            <div class="space-y-4">
                                {{-- <h3 class="text-lg font-bold text-gray-700 border-b pb-2">Branding & Tujuan</h3> --}}

                                <div>
                                    {{-- <x-input-label for="logo" :value="__('Logo Sekolah')" /> --}}
                                    
                                    @if($profil->logo_path)
                                        {{-- <div class="mb-2">
                                            <img src="{{ asset('storage/' . $profil->logo_path) }}" alt="Logo" class="h-20 w-auto object-contain border p-1 rounded">
                                        </div> --}}
                                    @endif
                                    <br>
                                    <br>
                                    {{-- <input type="file" name="logo" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" />
                                    <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG. Maks: 2MB.</p> --}}
                                </div>

                                <div>
                                    <x-input-label for="visi" :value="__('Visi')" />
                                    <textarea id="visi" name="visi" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" rows="2">{{ old('visi', $profil->visi) }}</textarea>
                                </div>

                                <div>
                                    <x-input-label for="misi" :value="__('Misi')" />
                                    <textarea id="misi" name="misi" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" rows="3">{{ old('misi', $profil->misi) }}</textarea>
                                </div>
                            </div>

                        </div>

                        <div class="flex items-center justify-end mt-6 pt-4 border-t">
                            <x-primary-button>
                                {{ __('Simpan Perubahan') }}
                            </x-primary-button>
                        </div>

                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>