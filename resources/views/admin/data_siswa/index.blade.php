<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Data Siswa') }}
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

           <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                
                {{-- [BAGIAN 1: TOOLBAR ATAS] --}}
                <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-6 gap-4">
                    
                    {{-- FORM PENCARIAN & FILTER --}}
                    <form method="GET" action="{{ route('admin.siswas.index') }}" class="w-full lg:w-2/3 flex flex-col md:flex-row gap-3">
                        <input type="hidden" name="per_page" value="{{ request('per_page', 30) }}">

                        <div class="relative w-full md:w-1/3">
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="Cari Nama / NISN"
                                class="w-full rounded-full border-gray-300 pl-5 pr-10 py-2 focus:border-[#3B3E42] focus:ring-[#3B3E42] shadow-sm">
                            <button type="submit" class="absolute right-3 top-2.5 text-gray-400 hover:text-[#3B3E42]">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                            </button>
                        </div>
                        <div class="w-full md:w-1/4">
                            <select name="kelas_id" onchange="this.form.submit()" 
                                class="w-full rounded-full border-gray-300 py-2 pl-4 pr-8 shadow-sm focus:border-[#3B3E42] focus:ring-[#3B3E42] cursor-pointer text-gray-700">
                                <option value="">-- Semua Kelas --</option>
                                @foreach($kelas as $k) 
                                    <option value="{{ $k->id }}" {{ request('kelas_id') == $k->id ? 'selected' : '' }}>
                                        Kelas {{ $k->tingkat }} {{ $k->nama_kelas }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="w-full md:w-1/4">
                            <select name="status" onchange="this.form.submit()" 
                                class="w-full rounded-full border-gray-300 py-2 pl-4 pr-8 shadow-sm focus:border-[#3B3E42] focus:ring-[#3B3E42] cursor-pointer text-gray-700">
                                <option value="">-- Semua Status --</option>
                                @foreach(['Aktif','Cuti', 'Lulus', 'Pindah', 'Keluar'] as $st)
                                    <option value="{{ $st }}" {{ request('status') == $st ? 'selected' : '' }}>
                                        {{ $st }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </form>

                    {{-- TOMBOL AKSI KANAN --}}
                    <div class="flex flex-col sm:flex-row items-center gap-3 w-full lg:w-auto justify-end">
                        <a href="{{ route('admin.siswas.export', ['kelas_id' => request('kelas_id'), 'search' => request('search'), 'status' => request('status')]) }}" 
                        class="inline-flex w-full items-center justify-center gap-2 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-semibold text-sm transition shadow-sm whitespace-nowrap">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" /></svg>
                            Export Data Siswa
                        </a>

                        {{-- Tombol Tambah (Trigger Modal) --}}
                        <button x-data=""
                                x-on:click.prevent="$dispatch('open-modal', 'add-siswa')"
                                type="button"
                                class="inline-flex w-full items-center justify-center gap-2 bg-[#1072B8] hover:bg-[#0d5a91] text-white px-4 py-2 rounded-lg font-semibold text-sm transition shadow-sm whitespace-nowrap">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Tambah Siswa
                        </button>
                    </div>
                </div>

                {{-- [BAGIAN 2: TABEL DATA] --}}
                
                {{-- A. FORM BULK DELETE (Tanpa Onsubmit Confirm lagi, karena sudah pakai modal) --}}
                <form action="{{ route('admin.siswas.bulk_delete') }}" method="POST" id="bulkDeleteForm">
                    @csrf
                    @method('DELETE')
                </form>

                {{-- B. TOMBOL TRIGGER BULK DELETE --}}
                <div id="bulkDeleteContainer" class="hidden mb-3 bg-red-50 p-2 rounded flex justify-between items-center border border-red-200">
                    <span class="text-red-700 text-sm font-semibold ml-2">
                        <span id="selectedCount">0</span> Siswa dipilih
                    </span>
                    {{-- Tombol ini membuka Modal Konfirmasi --}}
                    <x-danger-button type="button" x-data="" x-on:click="$dispatch('open-modal', 'bulk-delete-confirm')" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-xs font-bold transition">
                        Hapus Terpilih
                    </x-danger-button>
                </div>

                {{-- C. MODAL KONFIRMASI BULK DELETE --}}
                <x-modal name="bulk-delete-confirm" focusable>
                    <div class="p-6">
                        <h2 class="text-lg font-bold text-gray-900">Konfirmasi Hapus Massal</h2>
                        <p class="mt-1 text-sm text-gray-600">
                            Apakah Anda yakin ingin menghapus <strong>semua data siswa yang dipilih</strong>?
                            <br>
                            <span class="text-red-500 font-bold text-xs">Peringatan: Data akun user, nilai, dan tagihan terkait juga akan dihapus permanen.</span>
                        </p>
                        <div class="mt-6 flex justify-end gap-3">
                            <x-secondary-button x-on:click="$dispatch('close')">Batal</x-secondary-button>
                            
                            {{-- Tombol ini menekan form ID 'bulkDeleteForm' di atas --}}
                            <x-danger-button type="submit" form="bulkDeleteForm">
                                Ya, Hapus Semua
                            </x-danger-button>
                        </div>
                    </div>
                </x-modal>

                <div class="overflow-x-auto bg-white rounded-xl shadow border border-gray-200">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-[#3B3E42]"> 
                            <tr>
                                <th class="px-4 py-4 w-10 text-center">
                                    <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 cursor-pointer">
                                </th>
                                <th class="px-4 py-4 text-center text-xs font-bold text-white uppercase w-12">No</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase">NISN</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase">Nama Siswa</th>
                                <th class="px-6 py-4 text-center text-xs font-bold text-white uppercase">Kelas</th>
                                <th class="px-6 py-4 text-center text-xs font-bold text-white uppercase">Tahun Ajaran</th>
                                <th class="px-6 py-4 text-center text-xs font-bold text-white uppercase">Status</th>
                                <th class="px-6 py-4 text-center text-xs font-bold text-white uppercase">Aksi</th>
                            </tr>
                        </thead>

                        <tbody class="bg-white divide-y divide-gray-100">
                            @forelse($siswas as $siswa)
                                <tr class="hover:bg-indigo-50/50 transition even:bg-gray-50">
                                    <td class="px-4 py-4 text-center">
                                        {{-- Checkbox terhubung ke form bulk delete --}}
                                        <input type="checkbox" name="ids[]" form="bulkDeleteForm" value="{{ $siswa->id }}" class="select-item rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 cursor-pointer">
                                    </td>
                                    <td class="px-4 py-4 text-center text-sm text-gray-500 font-medium">
                                        {{ $siswas->firstItem() + $loop->index }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-left font-mono text-gray-600">
                                        {{ $siswa->nisn ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-bold text-gray-900">{{ $siswa->user->name ?? 'User Terhapus' }}</div>
                                        <div class="text-xs text-gray-400">{{ $siswa->user->email ?? '' }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @if($siswa->kelas)
                                            <span class="inline-flex items-center whitespace-nowrap px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 ">
                                                {{ $siswa->kelas->tingkat }} {{ $siswa->kelas->nama_kelas }}
                                            </span>
                                        @else
                                            <span class="text-xs text-red-400 font font-semibold whitespace-nowrap">No Class</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm text-center text-gray-500">
                                        {{ $siswa->tahunAjaran->tahun ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @php
                                            $statusClass = match($siswa->status) {
                                                'Aktif' => 'bg-green-100 text-green-800 border-green-200',
                                                'Cuti' => 'bg-gray-100 text-gray-800 border-gray-200',
                                                'Lulus' => 'bg-blue-100 text-blue-800 border-blue-200',
                                                'Pindah' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                                'Keluar' => 'bg-red-100 text-red-800 border-red-200',
                                                default => 'bg-gray-100 text-gray-800 border-gray-200',
                                            };
                                        @endphp
                                        <span class="px-3 py-1 inline-flex text-[10px] font-bold border rounded-full {{ $statusClass }}">
                                            {{ strtoupper($siswa->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right text-sm font-medium flex justify-end items-center gap-2">
    
                                    {{-- 1. TOMBOL PEMICU MODAL --}}
                                    <button type="button" 
                                            x-data 
                                            x-on:click="$dispatch('open-modal', 'edit-siswa-{{ $siswa->id }}')" 
                                            class="text-indigo-600 hover:text-indigo-900 font-semibold transition">
                                        Edit
                                    </button>

                                    {{-- 2. STRUKTUR MODAL EDIT --}}
                                    <x-modal name="edit-siswa-{{ $siswa->id }}" focusable>
                                        {{-- Pastikan ada pembungkus div dengan p-6 agar tidak mepet ke pinggir --}}
                                        <div class="p-6 text-left">
                                            <h2 class="text-lg font-bold mb-4 border-b pb-2 text-gray-900">
                                                Edit Data Siswa: {{ $siswa->user->name ?? $siswa->nama_lengkap }}
                                            </h2>

                                            <form method="POST" action="{{ route('admin.siswas.update', $siswa->id) }}">
                                                @csrf 
                                                @method('PUT')
                                                
                                                {{-- Memanggil Komponen Form --}}
                                                <x-siswa.edit-form :siswa="$siswa" :kelas="$kelas" :tahunAjaran="$tahunAjaranList" />

                                                <div class="mt-6 flex justify-end gap-3 border-t pt-4">
                                                    <x-secondary-button x-on:click="$dispatch('close')">Batal</x-secondary-button>
                                                    <x-primary-button class="bg-[#3B3E42]">Simpan Perubahan</x-primary-button>
                                                </div>
                                            </form>
                                        </div>
                                    </x-modal>

                                    {{-- 3. TOMBOL HAPUS (Tetap) --}}
                                    <x-siswa.delete-modal 
                                        trigger="delete-siswa-{{ $siswa->id }}" 
                                        action="{{ route('admin.siswas.destroy', $siswa->id) }}"
                                        message="{{ $siswa->user->name ?? $siswa->nama_lengkap }}"
                                    />
                                </td>
                                    {{-- <td class="px-6 py-4 text-right text-sm font-medium flex justify-end items-center gap-2">
                                        <a href="{{ route('admin.siswas.edit', $siswa->id) }}"
                                        class="text-indigo-600 hover:text-indigo-900 font-semibold">Edit</a>
                                         --}}
                                        {{-- TOMBOL HAPUS SATUAN (Menggunakan Component) --}}
                                        {{-- <x-siswa.delete-modal 
                                            trigger="delete-siswa-{{ $siswa->id }}" 
                                            action="{{ route('admin.siswas.destroy', $siswa->id) }}"
                                            message="{{ $siswa->user->name ?? $siswa->nama_lengkap }}"
                                        /> --}}
                                    {{-- </td> --}}
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-12 text-center text-gray-400 italic flex-col">
                                        Data siswa tidak ditemukan untuk pencarian/filter ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- [BAGIAN 3: PAGINATION & PILIHAN LIMIT] --}}
                <div class="mt-6 flex flex-col sm:flex-row justify-between items-center gap-4">
                    <form method="GET" action="{{ route('admin.siswas.index') }}">
                        <input type="hidden" name="search" value="{{ request('search') }}">
                        <input type="hidden" name="kelas_id" value="{{ request('kelas_id') }}">
                        <input type="hidden" name="status" value="{{ request('status') }}">
                        
                        <div class="flex items-center gap-2">
                            <span class="text-sm text-gray-500">Show:</span>
                            <select name="per_page" onchange="this.form.submit()" 
                                class="text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-1 pl-2 pr-8">
                                <option value="30" {{ request('per_page') == 30 ? 'selected' : '' }}>30</option>
                                <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                                <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                            </select>
                        </div>
                    </form>

                    <div>
                        {{ $siswas->appends([
                            'search' => request('search'), 
                            'kelas_id' => request('kelas_id'), 
                            'status' => request('status'),
                            'per_page' => request('per_page') 
                        ])->links() }}
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- MODAL TAMBAH SISWA (Component) --}}
    <x-siswa.add-student-modal :kelas="$kelas" />

    {{-- SCRIPT JS HANYA UNTUK CHECKBOX (Script hapus lama sudah dibuang) --}}
    <script>
        const selectAll = document.getElementById('selectAll');
        const items = document.querySelectorAll('.select-item');
        const bulkDeleteContainer = document.getElementById('bulkDeleteContainer');
        const selectedCountSpan = document.getElementById('selectedCount');

        function toggleBulkDeleteButton() {
            const checkedCount = document.querySelectorAll('.select-item:checked').length;
            selectedCountSpan.innerText = checkedCount;
            
            if (checkedCount > 0) {
                bulkDeleteContainer.classList.remove('hidden');
            } else {
                bulkDeleteContainer.classList.add('hidden');
            }
        }

        if(selectAll) {
            selectAll.addEventListener('change', function() {
                items.forEach(item => {
                    item.checked = this.checked;
                });
                toggleBulkDeleteButton();
            });
        }

        items.forEach(item => {
            item.addEventListener('change', function() {
                if (!this.checked) {
                    selectAll.checked = false;
                }
                const allChecked = document.querySelectorAll('.select-item:checked').length === items.length;
                if(items.length > 0 && allChecked) {
                    selectAll.checked = true;
                }
                toggleBulkDeleteButton();
            });
        });
    </script>
</x-app-layout>