{{-- <x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manajemen Galeri Kegiatan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-bold">Daftar Foto</h3>
                    <a href="{{ route('admin.galeri.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm">
                        + Tambah Foto Baru
                    </a>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @forelse($galeri as $item)
                        <div class="border rounded-xl overflow-hidden shadow-sm bg-gray-50">
                            <img src="{{ asset('storage/' . $item->gambar) }}" class="w-full h-48 object-cover">
                            <div class="p-4">
                                <h4 class="font-bold text-gray-800 truncate">{{ $item->judul }}</h4>
                                <p class="text-xs text-gray-500 mt-1">Diunggah: {{ $item->created_at->format('d M Y') }}</p>

                                <div class="mt-4 flex gap-2">
                                    <form action="{{ route('admin.galeri.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus foto ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 text-sm font-semibold">
                                            <i class="fas fa-trash"></i> Hapus
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-3 text-center py-10 text-gray-400">
                            Belum ada foto kegiatan yang diunggah.
                        </div>
                    @endforelse
                </div>

            </div>
        </div>
    </div>
</x-app-layout> --}}
