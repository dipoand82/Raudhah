<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manajemen Data Kelas') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 ">

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


            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 ">

                {{-- FORM TAMBAH (KIRI) --}}
                <div class="md:col-span-1">
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 sticky top-6">
                        <h3 class="text-lg font-bold mb-4 flex items-center gap-2 text-[#1072B8]">
                            {{-- Icon disesuaikan warnanya --}}
                            <svg class="w-5 h-5 text-[#1072B8]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
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
                {{-- TABEL DATA (KANAN) --}}
<div class="md:col-span-2">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-[#3B3E42]">
                    <tr>
                        <th class="px-4 py-4 text-center text-xs font-bold text-white uppercase w-12">No</th>
                        <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase">Tingkat</th>
                        <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase">Nama Kelas</th>
                        <th class="px-4 py-4 text-center text-xs font-bold text-white uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($kelas as $k)
                    <tr class="hover:bg-gray-50 transition even:bg-gray-50/50">
                        <td class="px-4 py-4 text-center text-sm font-medium text-gray-500">
                            {{ $loop->iteration + ($kelas->currentPage() - 1) * $kelas->perPage() }}
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap text-sm">
                            <span class="bg-gray-100 text-gray-800 px-3 py-1 rounded-full font-bold border border-gray-200 text-xs">
                                Kelas {{ $k->tingkat }}
                            </span>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap">
                            <div class="text-sm font-bold text-[#3B3E42]">
                                {{ $k->tingkat }} {{ $k->nama_kelas }}
                            </div>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap text-center text-sm font-medium">
                            <div class="flex justify-center items-center gap-3">
                                {{-- <button x-data x-on:click="$dispatch('open-modal', 'edit-kelas-{{ $k->id }}')"
                                    class="text-indigo-600 hover:text-indigo-900 font-semibold transition">
                                    Edit
                                </button> --}}
                                <x-modal-delete-global
                                    :trigger="'delete-kelas-' . $k->id"
                                    :action="route('admin.kelas.destroy', $k->id)"
                                    :message="'Kelas ' . $k->tingkat . ' ' . $k->nama_kelas"
                                />
                            </div>
                        </td>
                    </tr>
                    {{-- Modal Edit tetap di sini --}}
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-10 text-center text-gray-400 italic">Belum ada data kelas.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION --}}
        <div class="px-4 py-3 bg-white border-t border-gray-100">
            {{ $kelas->links() }}
        </div>

        {{-- FOOTER NOTE --}}
        <div class="bg-gray-50 border-t border-gray-200 p-4 text-center">
            <p class="text-xs text-red-500 font-medium">
                * Pastikan membuat semua daftar kelas yang ada terlebih dahulu.
            </p>
        </div>
    </div>
</div>
            </div>
        </div>
    </div>
</x-app-layout>
