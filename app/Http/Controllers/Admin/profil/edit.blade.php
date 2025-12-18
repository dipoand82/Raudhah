<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Profil & Halaman Depan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="mb-4 bg-green-100 text-green-700 p-4 rounded shadow-sm">
                    {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('admin.profil.update') }}" enctype="multipart/form-data">
                @csrf
                @method('PATCH')

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6">
                    <h3 class="text-lg font-bold text-gray-700 border-b pb-2 mb-4">1. Identitas & Kontak</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="nama_sekolah" :value="__('Nama Sekolah')" />
                            <x-text-input id="nama_sekolah" class="block mt-1 w-full" type="text" name="nama_sekolah" :value="old('nama_sekolah', $profil->nama_sekolah)" required />
                        </div>
                        <div>
                            <x-input-label for="telepon" :value="__('No. Telepon')" />
                            <x-text-input id="telepon" class="block mt-1 w-full" type="text" name="telepon" :value="old('telepon', $profil->telepon)" />
                        </div>
                        <div class="md:col-span-2">
                            <x-input-label for="alamat" :value="__('Alamat Lengkap')" />
                            <textarea id="alamat" name="alamat" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" rows="2">{{ old('alamat', $profil->alamat) }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6">
                    <h3 class="text-lg font-bold text-indigo-700 border-b pb-2 mb-4">2. Konten Halaman Depan</h3>
                    
                    <div class="grid grid-cols-1 gap-6">
                        
                        <div>
                            <x-input-label for="banner" :value="__('Banner Utama (Gambar Besar)')" />
                            @if($profil->banner_path)
                                <img src="{{ asset('storage/' . $profil->banner_path) }}" class="my-2 h-40 w-full object-cover rounded-lg border">
                            @endif
                            <input type="file" name="banner" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" />
                            <p class="text-xs text-gray-500 mt-1">Disarankan ukuran 1920x600 px.</p>
                        </div>

                        <div>
                            <x-input-label for="deskripsi_singkat" :value="__('Deskripsi Singkat (Intro)')" />
                            <textarea id="deskripsi_singkat" name="deskripsi_singkat" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" rows="3" placeholder="Contoh: SMP IT Raudhah adalah sekolah berbasis karakter...">{{ old('deskripsi_singkat', $profil->deskripsi_singkat) }}</textarea>
                        </div>

                        <div>
                            <x-input-label for="alasan_memilih" :value="__('Poin Alasan Memilih Sekolah')" />
                            <textarea id="alasan_memilih" name="alasan_memilih" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" rows="4" placeholder="- Guru Kompeten&#10;- Fasilitas Lengkap&#10;- Lingkungan Asri">{{ old('alasan_memilih', $profil->alasan_memilih) }}</textarea>
                            <p class="text-xs text-gray-500">Gunakan tanda strip (-) untuk membuat poin baru.</p>
                        </div>

                        <div>
                            <x-input-label for="program_unggulan" :value="__('Daftar Program Unggulan')" />
                            <textarea id="program_unggulan" name="program_unggulan" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" rows="4" placeholder="- Tahfidz Quran&#10;- Coding Class&#10;- Robotic">{{ old('program_unggulan', $profil->program_unggulan) }}</textarea>
                        </div>

                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6">
                    <h3 class="text-lg font-bold text-gray-700 border-b pb-2 mb-4">3. Visi, Misi & Logo</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="logo" :value="__('Logo Sekolah')" />
                            @if($profil->logo_path)
                                <img src="{{ asset('storage/' . $profil->logo_path) }}" class="h-20 w-auto my-2">
                            @endif
                            <input type="file" name="logo" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" />
                        </div>
                        
                        <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="visi" :value="__('Visi')" />
                                <textarea id="visi" name="visi" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" rows="4">{{ old('visi', $profil->visi) }}</textarea>
                            </div>
                            <div>
                                <x-input-label for="misi" :value="__('Misi')" />
                                <textarea id="misi" name="misi" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" rows="4">{{ old('misi', $profil->misi) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="fixed bottom-0 left-0 w-full bg-white border-t p-4 flex justify-end z-50 shadow-lg">
                    <div class="max-w-7xl mx-auto w-full flex justify-end px-4">
                        <x-primary-button class="px-8 py-3 text-lg">
                            {{ __('Simpan Semua Perubahan') }}
                        </x-primary-button>
                    </div>
                </div>
                <div class="h-20"></div>

            </form>
        </div>
    </div>
</x-app-layout>