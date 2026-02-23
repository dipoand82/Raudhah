<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Kelola Master Biaya') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Form Tambah Master Biaya --}}
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg border border-gray-100">
                <section>
                    <header>
                        <h3 class="text-lg font-bold text-gray-900">Tambah Master Biaya</h3>
                        <p class="mt-1 text-sm text-gray-600">Gunakan form ini untuk membuat template tagihan (Contoh: SPP, Uang Buku).</p>
                    </header>

                    {{-- PERBAIKAN: Gunakan route admin.keuangan.master.store --}}
                    <form method="post" action="{{ route('admin.keuangan.master.store') }}" class="mt-6 space-y-4">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="nama_tagihan" value="Nama Tagihan" />
                                <x-text-input id="nama_tagihan" name="nama_tagihan" type="text" class="mt-1 block w-full" placeholder="Contoh: SPP Juli 2025" required />
                            </div>
                            <div>
                                <x-input-label for="nominal" value="Nominal (Rp)" />
                                <x-text-input id="nominal" name="nominal" type="text" class="mt-1 block w-full" placeholder="Contoh: 500000" oninput="this.value = this.value.replace(/[^0-9]/g, '')" required />
                                <p class="text-[10px] text-gray-500 mt-1">*Input angka murni tanpa titik/koma.</p>
                            </div>
                        </div>
                        <div>
                            <x-input-label for="deskripsi" value="Deskripsi (Opsional)" />
                            <textarea id="deskripsi" name="deskripsi" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"></textarea>
                        </div>
                        <div class="flex items-center gap-4">
                            <x-primary-button>{{ __('Simpan Master') }}</x-primary-button>
                        </div>
                    </form>
                </section>
            </div>

            {{-- Tabel Daftar Master Tagihan --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Nama Tagihan</th>
                            <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase">Nominal</th>
                            <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($masterTagihans as $master)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $master->nama_tagihan }}</td>
                            <td class="px-6 py-4 text-sm text-right font-bold text-indigo-600">Rp {{ number_format($master->nominal, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-center">
                                {{-- PERBAIKAN: Gunakan route admin.keuangan.master.destroy --}}
                                <form action="{{ route('admin.keuangan.master.destroy', $master->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus? Pastikan data belum dipakai siswa.')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900 font-bold text-xs uppercase">Hapus</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
