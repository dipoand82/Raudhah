<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Kelola Konten Profil') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="{ activeTab: '{{ request()->query('tab', default: 'profil') }}' }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- ALERT SUCCESS --}}
            @if (session('success'))
                <x-alert-success>
                    {{ session('success') }}
                </x-alert-success>
            @endif
            {{-- Tampilkan Alert Gagal (Misal dari Session Error) --}}
            @if (session('error'))
                <x-alert-danger>
                    {{ session('error') }}
                </x-alert-danger>
            @endif

            @if ($errors->any())
                @foreach ($errors->all() as $error)
                    <x-alert-danger timeout="8000"> {{-- Waktu 8 detik agar sempat dibaca --}}
                        {{ $error }}
                    </x-alert-danger>
                @endforeach
            @endif
            {{-- TAB NAVIGATION (Clean Style) --}}
            <div class="bg-white px-6 pt-4 rounded-t-2xl border border-gray-200 border-b-0">
                <div class="flex space-x-8">
                    <button @click="activeTab = 'profil'"
                        :class="{ 'border-b-2 border-[#1072B8] text-[#1072B8] font-bold': activeTab === 'profil', 'text-gray-400 hover:text-gray-600': activeTab !== 'profil' }"
                        class="pb-4 transition duration-150 text-sm tracking-wide capitalize">
                        Profil
                    </button>
                    <button @click="activeTab = 'info'"
                        :class="{ 'border-b-2 border-[#1072B8] text-[#1072B8] font-bold': activeTab === 'info', 'text-gray-400 hover:text-gray-600': activeTab !== 'info' }"
                        class="pb-4 transition duration-150 text-sm tracking-wide capitalize">
                        Info
                    </button>
                    <button @click="activeTab = 'galeri'"
                        :class="{
                            'border-b-2 border-[#1072B8] text-[#1072B8] font-bold': activeTab === 'galeri',
                            'text-gray-400 hover:text-gray-600': activeTab !== 'galeri'
                        }"
                        class="pb-4 transition duration-150 text-sm tracking-wide capitalize">
                        Galeri
                    </button>
                </div>
            </div>

            {{-- KOTAK KONTEN --}}
            <div class="bg-white overflow-hidden shadow-sm rounded-b-2xl border border-gray-200">
                <div class="p-8 text-gray-900">


                    <div x-show="activeTab === 'profil'">
                        <form method="POST" action="{{ route('admin.profil.update') }}" enctype="multipart/form-data">
                            @csrf
                            @method('PATCH')
<input type="hidden" name="current_tab" value="profil">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-10">

                                {{-- KOLOM KIRI: IDENTITAS --}}
                                <div class="space-y-6">
                                    <div>
                                        <x-input-label for="banner" :value="__('Gambar Profil Depan Sekolah')"
                                            class="text-md font-bold text-gray-600 capitalize" />

                                        {{-- Ubah $profil->banner menjadi $profil->banner_path --}}
                                        @if ($profil->banner_path)
                                            <img src="{{ asset('storage/' . $profil->banner_path) }}"
                                                class="w-40 h-auto object-contain mb-3 rounded-lg border p-2 bg-gray-50">
                                        @endif

                                        <input id="banner" type="file" name="banner"
                                            class="block mt-1 w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-[#1072B8] hover:file:bg-blue-100" />
                                    </div>
                                    <div>
                                        <x-input-label for="nama_sekolah" :value="__('Nama Sekolah')"
                                            class="text-md font-bold text-gray-600 capitalize" />
                                        <x-text-input id="nama_sekolah"
                                            class="block mt-1 w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white transition"
                                            type="text" name="nama_sekolah" :value="old('nama_sekolah', $profil->nama_sekolah)" required />
                                    </div>

                                    <div>
                                        <x-input-label for="deskripsi_singkat" :value="__('Deskripsi Singkat')"
                                            class="text-md font-bold text-gray-600 capitalize" />
                                        <textarea id="deskripsi_singkat" name="deskripsi_singkat"
                                            class="block mt-1 w-full border-gray-300 rounded-lg bg-gray-50 focus:bg-white transition" rows="3">{{ old('deskripsi_singkat', $profil->deskripsi_singkat) }}</textarea>
                                    </div>

                                    <div>
                                        <x-input-label for="program_unggulan" :value="__('Program Unggulan')" />
                                        <p class="text-xs text-gray-500 mb-1">*Masukkan satu program unggulan per baris
                                            (tekan Enter
                                            untuk baris baru)</p>
                                        <textarea id="program_unggulan" name="program_unggulan"
                                            class="block mt-1 w-full border-gray-300 rounded-lg bg-gray-50 focus:bg-white transition" rows="3">{{ old('program_unggulan', $profil->program_unggulan) }}</textarea>
                                    </div>

                                    <div>
                                        <x-input-label for="alasan_memilih" :value="__('Alasan Memilih')" />
                                        <p class="text-xs text-gray-500 mb-1">*Masukkan satu alasan memilih per baris
                                            (tekan Enter
                                            untuk baris baru)</p>
                                        <textarea id="alasan_memilih" name="alasan_memilih"
                                            class="block mt-1 w-full border-gray-300 rounded-lg bg-gray-50 focus:bg-white transition" rows="3">{{ old('alasan_memilih', $profil->alasan_memilih) }}</textarea>
                                    </div>

                                    {{-- <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">


                                    </div> --}}
                                </div>

                                <div class="space-y-6">
                                    <div>
                                        <x-input-label for="visi" :value="__('Visi')" />
                                        <textarea id="visi" name="visi"
                                            class="block mt-1 w-full border-gray-300 rounded-lg bg-gray-50 focus:bg-white transition" rows="3">{{ old('visi', $profil->visi) }}</textarea>
                                    </div>

                                    <div>
                                        <x-input-label for="misi" :value="__('Misi')" />
                                        <p class="text-xs text-gray-500 mb-1">*Masukkan satu misi per baris (tekan Enter
                                            untuk baris baru)</p>
                                        <textarea id="misi" name="misi"
                                            class="block mt-1 w-full border-gray-300 rounded-lg bg-gray-50 focus:bg-white transition" rows="5"
                                            placeholder="Contoh:&#10;Menyelenggarakan Program Pembinaan...&#10;Menumbuhkan penghayatan... ">{{ old('misi', $profil->misi) }}</textarea>
                                    </div>

                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <div>
                                            <x-input-label for="alamat" :value="__('Alamat Sekolah')"
                                                class="text-md font-bold text-gray-600 capitalize" />
                                            <x-text-input id="alamat" class="block mt-1 w-full rounded-lg bg-gray-50"
                                                type="text" name="alamat" :value="old('alamat', $profil->alamat)" />
                                        </div>
                                        <div>
                                            <x-input-label for="telepon" :value="__('Telepon')"
                                                class="text-md font-bold text-gray-600 capitalize" />
                                            <x-text-input id="telepon" class="block mt-1 w-full rounded-lg bg-gray-50"
                                                type="text" name="telepon" placeholder="Contoh: 081234567890"
                                                :value="old('telepon', $profil->telepon)" />
                                        </div>
                                        <div>
                                            <x-input-label for="email" :value="__('Email')"
                                                class="text-md font-bold text-gray-600 capitalize" />
                                            <x-text-input id="email" class="block mt-1 w-full rounded-lg bg-gray-50"
                                                type="email" name="email" :value="old('email', $profil->email)" />
                                        </div>

                                        <div>
                                            <x-input-label for="instagram" :value="__('Link Instagram')"
                                                class="text-md font-bold text-gray-600 capitalize" />
                                            <x-text-input id="instagram" class="block mt-1 w-full rounded-lg bg-gray-50"
                                                type="text" name="instagram" :value="old('instagram', $profil->instagram)" />
                                        </div>
                                        <div>
                                            <x-input-label for="tiktok" :value="__('Link TikTok')"
                                                class="text-md font-bold text-gray-600 capitalize" />
                                            <x-text-input id="tiktok"
                                                class="block mt-1 w-full rounded-lg bg-gray-50" type="text"
                                                name="tiktok" :value="old('tiktok', $profil->tiktok)" />
                                        </div>
                                        <div>
                                            <x-input-label for="info_footer" :value="__('Info Tambahan Footer')"
                                                class="text-md font-bold text-gray-600 capitalize" />
                                            <x-text-input id="info_footer"
                                                class="block mt-1 w-full rounded-lg bg-gray-50" type="text"
                                                name="info_footer" :value="old('info_footer', $profil->info_footer)" />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center justify-end mt-10 pt-6 border-t border-gray-100">
                                <x-primary-button class="bg-[#1072B8] hover:bg-blue-800 rounded-lg">
                                    {{ __('Simpan Perubahan') }}
                                </x-primary-button>
                            </div>
                        </form>
                    </div>
                    <div x-show="activeTab === 'info'" x-transition>
                        <form method="POST" action="{{ route('admin.profil.update') }}"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PATCH')
<input type="hidden" name="current_tab" value="info">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                                <div>
                                    <x-input-label for="info_penting" :value="__('Info Penting')"
                                        class="text-md font-bold text-gray-600 capitalize" />
                                    <x-text-input id="info_penting" class="block mt-1 w-full rounded-lg bg-gray-50"
                                        type="text" name="info_penting" :value="old('info_penting', $profil->info_penting)" />
                                </div>
                                <div>
                                    <x-input-label for="brosur_info" :value="__('Gambar Brosur Info Penting')"
                                        class="text-md font-bold text-gray-600 capitalize" />

                                    {{-- Ubah $profil->banner menjadi $profil->banner_path --}}
                                    @if ($profil->brosur_info)
                                        <img src="{{ asset('storage/' . $profil->brosur_info) }}"
                                            class="w-40 h-auto object-contain mb-3 rounded-lg border p-2 bg-gray-50">
                                    @endif

                                    <input id="brosur_info" type="file" name="brosur_info"
                                        class="block mt-1 w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-[#1072B8] hover:file:bg-blue-100" />
                                </div>


                            </div>
                            {{-- <div
                                class="px-4 py-2 hover:bg-blue-800 transition-colors {{ request()->routeIs('admin.galeri.*') ? 'bg-blue-900 border-l-4 border-yellow-400' : '' }}">
                                <a href="{{ route('admin.galeri.index') }}"
                                    class="flex items-center gap-3 text-white">
                                    <i class="fas fa-images w-5 text-center"></i>
                                    <span class="font-medium">Galeri Kegiatan</span>
                                </a>
                            </div> --}}
                            <div class="flex items-center justify-end mt-10 pt-6 border-t border-gray-100">
<a href="{{ route('admin.profil.edit', ['tab' => 'info']) }}">
                                <x-primary-button class="bg-[#1072B8] hover:bg-blue-800 rounded-lg">
                                    {{ __('Simpan Perubahan') }}
                                </x-primary-button>
                            </a>
                            </div>
                        </form>
                    </div>

                    <div x-show="activeTab === 'galeri'" x-transition class="space-y-6"> {{-- TAMBAHKAN BARIS INI --}}
                        <div class="flex justify-between items-center pb-4 border-b border-gray-100">
                            <div>
                                <h3 class="text-lg font-bold text-gray-800">Kumpulan Galeri Kegiatan</h3>
                                <p class="text-sm text-gray-500">Kelola dokumentasi foto yang tampil di halaman depan.
                                </p>
                            </div>
                            <a href="{{ route('admin.galeri.create') }}">

                                <x-primary-button> Tambah Galeri Baru
                                </x-primary-button>
                            </a>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                            @forelse($galeri as $item)
                                <div
                                    class="group relative bg-gray-50 rounded-xl overflow-hidden border border-gray-200 shadow-sm">
                                    <div class="aspect-video overflow-hidden">
                                        <img src="{{ asset('storage/' . $item->gambar) }}" alt="{{ $item->judul }}"
                                            class="w-full h-full object-cover">
                                    </div>

                                    <div class="p-3">
                                        <h4 class="font-bold text-gray-800 text-sm truncate">{{ $item->judul }}</h4>
                                        <div class="flex justify-between items-center mt-3">
                                            {{-- Tombol Edit --}}
                                            <a href="{{ route('admin.galeri.edit', $item->id) }}"
                                                class="text-indigo-600 hover:text-indigo-900 font-semibold transition">
                                               Edit
                                            </a>

                                            {{-- PEMANGGILAN MODAL GLOBAL --}}
                                            <x-modal-delete-global trigger="confirm-delete-galeri-{{ $item->id }}"
                                                :action="route('admin.galeri.destroy', $item->id)" title=""
                                                message="Menghapus Galeri {{ $item->judul }}"
                                                submitText="Ya, Hapus Permanen" class="text-xs px-3 py-1"
                                                {{-- Mengecilkan tombol agar serasi dengan tombol edit --}} />
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div
                                    class="col-span-full py-10 text-center bg-gray-50 rounded-xl border-2 border-dashed border-gray-200 text-gray-400">
                                    <i class="fas fa-images text-4xl mb-3"></i>
                                    <p>Belum ada foto di galeri.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> {{-- TAMBAHKAN PENUTUP INI UNTUK x-show galeri --}}
    {{-- Penutup p-8 --}}
</x-app-layout>
