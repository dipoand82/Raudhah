<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Kelola Master Biaya') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- NOTIFIKASI ALERTS --}}
            <div class="space-y-2">
                @if (session('success'))
                    <x-alert-success>{{ session('success') }}</x-alert-success>
                @endif

                @if (session('error'))
                    <x-alert-danger>{{ session('error') }}</x-alert-danger>
                @endif

                @if ($errors->any())
                    @foreach ($errors->all() as $error)
                        <x-alert-danger timeout="8000">{{ $error }}</x-alert-danger>
                    @endforeach
                @endif
            </div>

            {{-- 1. HEADER & TOMBOL TAMBAH --}}
            <div class="bg-white p-5 sm:p-6 rounded-xl shadow-sm border border-gray-200">
                <div class="mb-5">
                    <h3 class="text-lg font-bold text-gray-900">Master Biaya</h3>
                    <p class="text-sm text-gray-500 leading-relaxed">Penambahan tagihan untuk generate kesemua siswa
                        nantinya.</p>
                    <p class="text-xs text-red-500 mt-2 font-medium leading-relaxed italic">* Pastikan <strong>Nama
                            Tagihan</strong> dan <strong>Nominal</strong> sudah benar.</p>
                </div>

                <x-primary-button x-data=""
                    x-on:click.prevent="$dispatch('open-modal', 'add-master-biaya')"
                    class="bg-[#1072B8] hover:bg-[#0d5a91] w-full md:w-auto justify-center py-3 md:py-2">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Master Biaya
                </x-primary-button>
            </div>

            {{-- 2. TABEL DATA --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-[#3B3E42]">
                            <tr>
                                <th
                                    class="px-4 py-4 text-center text-xs font-bold text-white uppercase tracking-wider w-12">
                                    No</th>
                                <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">
                                    Nama Tagihan</th>
                                <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">
                                    Nominal</th>
                                <th class="px-4 py-4 text-center text-xs font-bold text-white uppercase tracking-wider">
                                    Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @forelse($masterTagihans as $index => $master)
                                <tr class="hover:bg-indigo-50/30 transition even:bg-gray-50">
                                    <td class="px-4 py-4 text-sm text-gray-600 font-medium text-center">
                                        {{ $index + 1 }}
                                    </td>
                                    <td class="px-4 py-4">
                                        <div class="text-sm font-bold text-gray-900 leading-tight">
                                            {{ $master->nama_tagihan }}</div>
                                        <div class="text-[10px] sm:text-xs text-gray-400 mt-0.5 line-clamp-1 italic">
                                            {{ $master->deskripsi ?? 'Tidak ada deskripsi' }}</div>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <span class="text-sm font-bold text-[#1072B8]">
                                            Rp {{ number_format($master->nominal, 0, ',', '.') }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 text-center">
                                        <div class="flex justify-center items-center gap-2 sm:gap-3">
                                            {{-- TOMBOL EDIT --}}
                                            <button type="button" x-data=""
                                                x-on:click.prevent="$dispatch('open-modal', 'edit-master-{{ $master->id }}')"
                                                class="text-indigo-600 hover:text-indigo-900 font-semibold transition">
                                                Edit
                                            </button>
                                            <x-siswa.delete-modal trigger="delete-master-{{ $master->id }}"
                                                action="{{ route('admin.keuangan.master.destroy', $master->id) }}"
                                                message="Hapus Master: {{ $master->nama_tagihan }}?"
                                                title="Hapus Master Biaya" />
                                        </div>
                                    </td>
                                </tr>

                                {{-- MODAL EDIT (HARUS DI DALAM LOOP AGAR ID SESUAI) --}}
                                <x-modal name="edit-master-{{ $master->id }}" focusable>
                                    {{-- Tambahkan padding p-6 atau p-8 dan pastikan container memiliki max-width yang pas --}}
                                    <div class="p-6 sm:p-8">
                                        <form method="post"
                                            action="{{ route('admin.keuangan.master.update', $master->id) }}"
                                            class="text-left">
                                            @csrf
                                            @method('PUT')

                                            {{-- Header Modal --}}
                                            <div class="border-b pb-4 mb-6">
                                                <h2 class="text-xl font-bold text-gray-900 text-[#1072B8]">
                                                    Edit Master Biaya: <span
                                                        class="text-gray-600">{{ $master->nama_tagihan }}</span>
                                                </h2>
                                                <p class="text-sm text-gray-500 mt-1">Perbarui informasi tagihan di
                                                    bawah ini.</p>
                                            </div>

                                            <div class="space-y-6">
                                                {{-- Input Nama Tagihan --}}
                                                <div>
                                                    <x-input-label for="edit_nama_tagihan_{{ $master->id }}"
                                                        value="Nama Tagihan" class="font-bold text-gray-700" />
                                                    <x-text-input id="edit_nama_tagihan_{{ $master->id }}"
                                                        name="nama_tagihan" type="text"
                                                        class="mt-2 block w-full focus:ring-[#1072B8] focus:border-[#1072B8] shadow-sm"
                                                        value="{{ $master->nama_tagihan }}" required />
                                                </div>

                                                {{-- Input Nominal --}}
                                                <div>
                                                    <x-input-label for="edit_nominal_{{ $master->id }}"
                                                        value="Nominal (Rp)" class="font-bold text-gray-700" />
                                                    <div class="relative mt-2">
                                                        <div
                                                            class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                            <span class="text-gray-500 sm:text-sm font-medium">Rp</span>
                                                        </div>
                                                        <x-text-input id="edit_nominal_{{ $master->id }}"
                                                            name="nominal" type="text"
                                                            class="pl-10 block w-full focus:ring-[#1072B8] focus:border-[#1072B8] shadow-sm"
                                                            value="{{ (int) $master->nominal }}"
                                                            oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                                            required />
                                                    </div>
                                                    <p class="text-[11px] text-red-500 mt-1 italic">* Masukkan angka
                                                        saja tanpa titik atau koma.</p>
                                                </div>

                                                {{-- Input Deskripsi --}}
                                                <div>
                                                    <x-input-label for="edit_deskripsi_{{ $master->id }}"
                                                        value="Deskripsi (Opsional)" class="font-bold text-gray-700" />
                                                    <textarea id="edit_deskripsi_{{ $master->id }}" name="deskripsi" rows="3"
                                                        class="mt-2 block w-full border-gray-300 focus:ring-[#1072B8] focus:border-[#1072B8] rounded-md shadow-sm text-sm"
                                                        placeholder="Catatan tambahan jika ada">{{ $master->deskripsi }}</textarea>
                                                </div>
                                            </div>

                                            {{-- Action Buttons --}}
                                            <div
                                                class="mt-8 pt-6 border-t flex flex-col-reverse sm:flex-row justify-end gap-3">
                                                <x-secondary-button x-on:click="$dispatch('close')"
                                                    class="w-full sm:w-auto justify-center px-6 py-2.5">
                                                    Batal
                                                </x-secondary-button>
                                                <x-primary-button
                                                    class="bg-[#1072B8] hover:bg-[#0d5a91] w-full sm:w-auto justify-center px-6 py-2.5 shadow-md">
                                                    {{ __('Update Master Biaya') }}
                                                </x-primary-button>
                                            </div>
                                        </form>
                                    </div>
                                </x-modal>

                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-10 text-center text-gray-400 italic text-sm">
                                        Belum ada data master biaya.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL TAMBAH --}}
    <x-modal name="add-master-biaya" focusable>
        <form method="post" action="{{ route('admin.keuangan.master.store') }}" class="p-8 text-left">
            @csrf
            <h2 class="text-xl font-bold text-gray-900 mb-6 pb-3 border-b text-[#1072B8]">
                Tambah Master Biaya Baru
            </h2>
            <div class="space-y-5">
                <div>
                    <x-input-label for="nama_tagihan" value="Nama Tagihan" class="font-semibold" />
                    <x-text-input id="nama_tagihan" name="nama_tagihan" type="text"
                        class="mt-2 block w-full focus:border-[#3B3E42]" placeholder="Contoh: SPP Bulanan" required />
                </div>
                <div>
                    <x-input-label for="nominal" value="Nominal (Rp)" class="font-semibold" />
                    <div class="relative mt-2">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 sm:text-sm">Rp</span>
                        </div>
                        <x-text-input id="nominal" name="nominal" type="text"
                            class="pl-10 block w-full focus:border-[#3B3E42]" placeholder="500000"
                            oninput="this.value = this.value.replace(/[^0-9]/g, '')" required />
                    </div>
                    <p class="text-[10px] text-red-500 mt-2 font-bold italic">*Hanya angka tanpa titik/koma.</p>
                </div>
                <div>
                    <x-input-label for="deskripsi" value="Deskripsi (Opsional)" class="font-semibold" />
                    <textarea id="deskripsi" name="deskripsi" rows="3" placeholder="Catatan tambahan jika ada"
                        class="mt-2 block w-full border-gray-300 focus:border-[#3B3E42] focus:ring-[#3B3E42] rounded-md shadow-sm"></textarea>
                </div>
            </div>
            <div class="mt-8 flex justify-end gap-3 border-t pt-5">
                <x-secondary-button x-on:click="$dispatch('close')" class="px-5">Batal</x-secondary-button>
                <x-primary-button class="bg-[#1072B8] px-5">
                    {{ __('Simpan Master Biaya') }}
                </x-primary-button>
            </div>
        </form>
    </x-modal>
</x-app-layout>
