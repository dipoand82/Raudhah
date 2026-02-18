<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Galeri Kegiatan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
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
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl p-8 border border-gray-100">
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <h2 class="text-2xl font-bold text-blue-900">Perbarui Galeri</h2>
                        <p class="text-gray-500">Ubah informasi atau ganti foto kegiatan SMP IT Raudhah.</p>
                    </div>
                    <a href="{{ route('admin.profil.edit') }}" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </a>
                </div>

                <form action="{{ route('admin.galeri.update', $galeri->id) }}" method="POST"
                    enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Judul Kegiatan</label>
                        <input type="text" name="judul" value="{{ old('judul', $galeri->judul) }}" required
                            class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Deskripsi Singkat</label>
                        <textarea name="deskripsi" rows="3"
                            class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500">{{ old('deskripsi', $galeri->deskripsi) }}</textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Foto Saat Ini</label>
                            <div class="relative rounded-xl overflow-hidden border border-gray-200 aspect-video">
                                <img src="{{ asset('storage/' . $galeri->gambar) }}" class="w-full h-full object-cover">
                            </div>
                        </div>

                        <div x-data="{ fileName: '' }">
                            <label class="block text-sm font-bold text-gray-700 mb-2">Ganti Foto (Opsional)</label>

                            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-dashed rounded-xl transition-colors h-[calc(100%-2rem)]"
                                :class="fileName ? 'border-green-400 bg-green-50' : 'border-gray-300 hover:border-blue-400'">

                                <div class="space-y-1 text-center self-center">
                                    <div class="mb-3">
                                        <i class="fas fa-cloud-upload-alt text-3xl transition-colors duration-300"
                                            :class="fileName ? 'text-green-500' : 'text-gray-400'"></i>
                                    </div>

                                    <div class="flex flex-col text-sm text-gray-600">
                                        <label
                                            class="relative cursor-pointer bg-transparent rounded-md font-medium transition-colors"
                                            :class="fileName ? 'text-green-700' : 'text-blue-600 hover:text-blue-500'">

                                            <span
                                                x-text="fileName ? 'File terpilih: ' + fileName : 'Klik untuk ganti file'"></span>

                                            <input type="file" name="gambar" class="sr-only" accept="image/*"
                                                @change="fileName = $event.target.files[0].name">
                                        </label>
                                    </div>

                                    <p class="text-xs transition-colors"
                                        :class="fileName ? 'text-green-600' : 'text-gray-500 italic'">
                                        <span x-show="!fileName">Kosongkan jika tidak ingin ganti foto</span>
                                        <span x-show="fileName" class="font-bold">Siap diunggah!</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 pt-6 border-t border-gray-50">
                        <a href="{{ route('admin.profil.edit', ['tab' => 'galeri']) }}"><x-secondary-button
                                type="button">
                                Batal
                            </x-secondary-button>
                        </a>
                        <x-primary-button type="submit">
                            Simpan Perubahan
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    </div>
</x-app-layout>
