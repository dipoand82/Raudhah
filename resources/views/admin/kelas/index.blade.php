<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manajemen Data Kelas') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div x-data="{ show: true }" x-show="show" class="mb-4 flex items-center justify-between bg-green-100 border border-green-200 text-green-700 px-4 py-3 rounded shadow-sm">
                    <span class="text-sm font-medium">{{ session('success') }}</span>
                    <button @click="show = false" class="text-green-500 hover:text-green-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                
                {{-- FORM TAMBAH --}}
                <div class="md:col-span-1">
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                        <h3 class="text-lg font-bold mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                            Tambah Kelas Baru
                        </h3>
                        
                        <form action="{{ route('admin.kelas.store') }}" method="POST">
                            @csrf
                            <div class="mb-4">
                                <x-input-label for="tingkat" :value="__('Tingkat')" />
                                <select name="tingkat" id="tingkat" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required>
                                    <option value="7">7</option>
                                    <option value="8">8</option>
                                    <option value="9">9</option>
                                </select>
                            </div>

                            <div class="mb-4">
                                <x-input-label for="nama_kelas" :value="__('Label Kelas')" />
                                <select name="nama_kelas" id="nama_kelas" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required>
                                    <option value="A">A</option>
                                    <option value="B">B</option>
                                    <option value="C">C</option>
                                </select>
                                <p class="text-xs text-gray-500 mt-1">*Pilih label kelas (A, B, atau C)</p>
                            </div>

                            <x-primary-button class="w-full justify-center !bg-[#3B3E42] hover:!bg-[#2f3235] focus:!bg-[#2f3235] active:!bg-[#1f2123]">
                                {{ __('Simpan Kelas') }}
                            </x-primary-button>
                        </form>
                    </div>
                </div>

                {{-- TABEL DATA --}}
                <div class="md:col-span-2">
                    <div class="overflow-hidden bg-white rounded-xl shadow-sm border border-gray-100">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-200">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase">Tingkat</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase">Nama Kelas</th>
                                    <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($kelas as $k)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <span class="bg-indigo-50 text-indigo-700 px-2 py-1 rounded-md font-bold">Kelas {{ $k->tingkat }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-bold text-gray-900">
                                            {{-- Menampilkan 7A, 8B, dsb --}}
                                            {{ $k->tingkat }} {{ $k->nama_kelas }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <form action="{{ route('admin.kelas.destroy', $k->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus kelas {{ $k->tingkat }} {{ $k->nama_kelas }}?');">
                                            @csrf @method('DELETE')
                                            <button class="text-red-600 hover:bg-red-50 px-3 py-1 rounded-md transition">Hapus</button>
                                        </form>

                                        <button x-data x-on:click="$dispatch('open-modal', 'edit-kelas-{{ $k->id }}')"
                                            class="ml-2 text-indigo-600 hover:bg-indigo-50 px-3 py-1 rounded-md transition">
                                            Edit
                                        </button>
                                    </td>
                                </tr>

                                {{-- MODAL EDIT --}}
                                <x-modal name="edit-kelas-{{ $k->id }}" focusable>
                                    <div class="p-6">
                                    <form method="POST" action="{{ route('admin.kelas.update', $k->id) }}">
                                        @csrf @method('PUT')
                                        <h2 class="text-lg font-bold text-gray-900 mb-6 border-b pb-3">
                                            Edit Data Kelas
                                        </h2>
                                    <div class="space-y-4">
                                        <div class="mb-4">
                                            <x-input-label value="Tingkat" />
                                            <select name="tingkat" class="block mt-1 w-full border-gray-300 rounded-md">
                                                @foreach([7,8,9] as $t)
                                                    <option value="{{ $t }}" {{ $k->tingkat == $t ? 'selected' : '' }}>Kelas {{ $t }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="mb-4">
                                            <x-input-label value="Label Kelas" />
                                            {{-- Diganti SELECT agar konsisten --}}
                                            <select name="nama_kelas" class="block mt-1 w-full border-gray-300 rounded-md">
                                                @foreach(['A','B','C'] as $h)
                                                    <option value="{{ $h }}" {{ $k->nama_kelas == $h ? 'selected' : '' }}>{{ $h }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="mt-6 flex justify-end gap-3">
                                            <x-secondary-button x-on:click="$dispatch('close')">Batal</x-secondary-button>
                                                <button type="submit" 
                                                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md font-bold text-xs text-white uppercase tracking-widest transition duration-150 ease-in-out 
                                                    !bg-[#3B3E42] hover:!bg-[#2f3235] focus:!bg-[#2f3235] active:!bg-[#1f2123] focus:outline-none focus:ring-2 focus:ring-[#3B3E42] focus:ring-offset-2">
                                                    Simpan Perubahan
                                                </button>
                                            {{-- <x-primary-button>Simpan</x-primary-button> --}}
                                        </div>
                                    </div>
                                    </form>
                                    </div>
                                </x-modal>
                                @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-4 text-center text-gray-400 italic">Belum ada data.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <div class="p-4 bg-gray-50 border-t">{{ $kelas->links() }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>