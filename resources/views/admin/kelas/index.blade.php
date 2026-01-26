<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manajemen Data Kelas') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- ALERT SUCCESS --}}
            @if(session('success'))
                <x-alert-success>
                    {{ session('success') }}
                </x-alert-success>
            @endif
            {{-- Tampilkan Alert Gagal (Misal dari Session Error) --}}
            @if(session('error'))
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

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                
                {{-- FORM TAMBAH (KIRI) --}}
                <div class="md:col-span-1">
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 sticky top-6">
                        <h3 class="text-lg font-bold mb-4 flex items-center gap-2 text-gray-800">
                            {{-- Icon disesuaikan warnanya --}}
                            <svg class="w-5 h-5 text-[#3B3E42]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                            Tambah Kelas Baru
                        </h3>
                        
                        <form action="{{ route('admin.kelas.store') }}" method="POST">
                            @csrf
                            <div class="mb-4">
                                <x-input-label for="tingkat" :value="__('Tingkat')" />
                                {{-- Input Focus Color disesuaikan --}}
                                <select name="tingkat" id="tingkat" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-[#3B3E42] focus:border-[#3B3E42]" required>
                                    <option value="">-- Pilih Tingkat --</option>
                                    <option value="7">Kelas 7</option>
                                    <option value="8">Kelas 8</option>
                                    <option value="9">Kelas 9</option>
                                </select>
                            </div>

                            <div class="mb-4">
                                <x-input-label for="nama_kelas" :value="__('Label Kelas')" />
                                <select name="nama_kelas" id="nama_kelas" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-[#3B3E42] focus:border-[#3B3E42]" required>
                                    <option value="">-- Pilih Label --</option>
                                    @foreach(['A','B','C'] as $label)
                                        <option value="{{ $label }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                                <p class="text-xs text-gray-500 mt-1">*Contoh: Jika dipilih 'A', hasil: 7A</p>
                            </div>

                            <x-primary-button class="w-full justify-center">
                                {{ __('Simpan Kelas') }}
                            </x-primary-button>
                        </form>
                    </div>
                </div>

                {{-- TABEL DATA (KANAN) --}}
                <div class="md:col-span-2 ">
                    <div class="overflow-x-auto bg-white rounded shadow overflow-hidden bg-white rounded-xl shadow border border-gray-200">
                        <table class="min-w-full divide-y divide-gray-200">
                            {{-- HEADER GELAP --}}
                            <thead class="bg-[#3B3E42]">
                                <tr>
                                    <th class="px-6 py-4 text-center text-xs font-bold text-white uppercase w-12">No</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase">Tingkat</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase">Nama Kelas</th>
                                    <th class="px-6 py-4 text-right text-xs font-bold text-white uppercase">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                @forelse($kelas as $k)
                                <tr class="hover:bg-indigo-50/50 transition even:bg-gray-50">
                                    {{-- Penomoran Loop dengan Pagination --}}
                                    <td class="px-6 py-4 text-center text-sm font-medium text-gray-500">
                                        {{ $loop->iteration + ($kelas->currentPage() - 1) * $kelas->perPage() }}
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <span class="bg-gray-100 text-gray-800 px-3 py-1 rounded-full font-bold border border-gray-200">
                                            Kelas {{ $k->tingkat }}
                                        </span>
                                    </td>
                                    
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-bold text-[#3B3E42] text-lg">
                                            {{ $k->tingkat }} {{ $k->nama_kelas }}
                                        </div>
                                    </td>
                                    
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        {{-- Tombol Edit --}}
                                        <button x-data x-on:click="$dispatch('open-modal', 'edit-kelas-{{ $k->id }}')"
                                            class="text-indigo-600 hover:text-indigo-900 mr-3 font-semibold transition">
                                            Edit
                                        </button>

                                        {{-- Tombol Hapus --}}
                                        <form action="{{ route('admin.kelas.destroy', $k->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus kelas {{ $k->tingkat }} {{ $k->nama_kelas }}?');">
                                            @csrf @method('DELETE')
                                            <x-danger-button>Hapus</x-danger-button>
                                        </form>
                                    </td>
                                </tr>

                                {{-- MODAL EDIT --}}
                                <x-modal name="edit-kelas-{{ $k->id }}" focusable>
                                    <div class="p-6">
                                        <div class="flex justify-between items-center mb-5 border-b pb-3">
                                            <h2 class="text-lg font-bold text-gray-900">Edit Data Kelas</h2>
                                            <button x-on:click="$dispatch('close')" class="text-gray-400 hover:text-gray-600">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                            </button>
                                        </div>

                                        <form method="POST" action="{{ route('admin.kelas.update', $k->id) }}">
                                            @csrf @method('PUT')
                                            
                                            <div class="space-y-4">
                                                <div>
                                                    <x-input-label value="Tingkat" />
                                                    <select name="tingkat" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-[#3B3E42] focus:border-[#3B3E42]">
                                                        @foreach([7,8,9] as $t)
                                                            <option value="{{ $t }}" {{ $k->tingkat == $t ? 'selected' : '' }}>Kelas {{ $t }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div>
                                                    <x-input-label value="Label Kelas" />
                                                    <select name="nama_kelas" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-[#3B3E42] focus:border-[#3B3E42]">
                                                        @foreach(['A','B','C'] as $h)
                                                            <option value="{{ $h }}" {{ $k->nama_kelas == $h ? 'selected' : '' }}>{{ $h }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="mt-6 flex justify-end gap-3 pt-4 border-t">
                                                <x-secondary-button x-on:click="$dispatch('close')">Batal</x-secondary-button>
                                                <x-primary-button >Simpan Perubahan</x-primary-button>
                                            </div>
                                        </form>
                                    </div>
                                </x-modal>
                                @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-10 text-center text-gray-400 italic">Belum ada data kelas.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                        
                        {{-- PAGINATION --}}
                        <div class="p-4 bg-white border-t">
                            {{ $kelas->links() }}
                        </div>

                        {{-- FOOTER NOTE (RATA TENGAH) --}}
                        <div class="bg-gray-50 border-t border-gray-200 p-4 text-center">
                            <p class="text-sm text-red-500 font-medium inline-block">
                                * Pastikan membuat semua daftar kelas yang ada terlebih dahulu.
                            </p>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>