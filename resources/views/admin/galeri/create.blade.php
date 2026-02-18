<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tambah Kegiatan Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-8">

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

                <form action="{{ route('admin.galeri.store') }}" method="POST" enctype="multipart/form-data"
                    class="space-y-6">
                    @csrf

                    <div>
                        <label for="judul" class="block text-sm font-medium text-gray-700">Judul Kegiatan</label>
                        <input type="text" name="judul" id="judul" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            placeholder="Contoh: Wisuda Tahfidz Qur'an 2026">
                    </div>

                    <div>
                        <label for="deskripsi" class="block text-sm font-medium text-gray-700">Deskripsi /
                            Keterangan</label>
                        <textarea name="deskripsi" id="deskripsi" rows="4"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            placeholder="Ceritakan singkat tentang kegiatan ini..."></textarea>
                    </div>

                    <div>
                        <label for="gambar" class="block text-sm font-medium text-gray-700">Foto Kegiatan</label>
                        <input type="file" name="gambar" id="gambar" required accept="image/*"
                            class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        <p class="mt-2 text-xs text-gray-500">Format: JPG, PNG, atau JPEG (Max: 2MB)</p>
                    </div>

                    <div class="flex items-center justify-end gap-4">
                        <a href="{{ route('admin.profil.edit', ['tab' => 'galeri']) }}"><x-secondary-button
                                type="button">
                                Batal
                            </x-secondary-button>
                        </a> <x-primary-button type="submit">
                            Simpan Perubahan
                        </x-primary-button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
