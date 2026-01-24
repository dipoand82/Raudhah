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
                <div x-data="{ show: true }" x-show="show" class="mb-4 flex items-center justify-between bg-green-100 border border-green-200 text-green-700 px-4 py-3 rounded shadow-sm">
                    <span>{{ session('success') }}</span>
                    <button @click="show = false" class="text-green-500 hover:text-green-700">&times;</button>
                </div>
            @endif

           <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                
                {{-- [BAGIAN 1: TOOLBAR ATAS] --}}
                <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-6 gap-4">
                    
                    {{-- FORM PENCARIAN & FILTER --}}
                    <form method="GET" action="{{ route('admin.siswas.index') }}" class="w-full lg:w-2/3 flex flex-col md:flex-row gap-3">
                        
                        {{-- [PERUBAHAN 1]: Tambahkan Hidden Input per_page disini --}}
                        {{-- Supaya kalau user cari nama, pilihan "50 data" tidak reset jadi 10 lagi --}}
                        <input type="hidden" name="per_page" value="{{ request('per_page', 10) }}">

                        <div class="relative w-full md:w-1/2">
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="Cari Nama / NISN..."
                                class="w-full rounded-full border-gray-300 pl-5 pr-10 py-2 focus:border-[#3B3E42] focus:ring-[#3B3E42] shadow-sm">
                            <button type="submit" class="absolute right-3 top-2.5 text-gray-400 hover:text-[#3B3E42]">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                            </button>
                        </div>
                        <div class="w-full md:w-1/3">
                            <select name="kelas_id" onchange="this.form.submit()" 
                                class="w-full rounded-full border-gray-300 py-2 pl-4 pr-8 shadow-sm focus:border-[#3B3E42] focus:ring-[#3B3E42] cursor-pointer text-gray-700">
                                <option value="">-- Semua Kelas --</option>
                                @foreach($kelas_list as $k) 
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
                                @foreach(['Aktif', 'Lulus', 'Pindah', 'Keluar'] as $st)
                                    <option value="{{ $st }}" {{ request('status') == $st ? 'selected' : '' }}>
                                        {{ $st }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </form>

                    {{-- TOMBOL AKSI --}}
                    {{-- <div class="flex items-center gap-3 w-full lg:w-auto justify-end">
                        <a href="{{ route('admin.siswas.export', ['kelas_id' => request('kelas_id'), 'search' => request('search'), 'status' => request('status')]) }}" 
                           class="flex items-center justify-center gap-2 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-semibold text-sm transition shadow-sm ">
                            <svg class="w-4 h-4 " fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                            Export Data Siswa
                        </a>
                        <a href="{{ route('admin.manajemen-user.index') }}"
                        class="inline-flex items-center justify-center px-4 py-2 bg-[#1072B8] border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-[#0d5a91] transition ease-in-out duration-150 gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Tambah Siswa
                        </a>
                    </div> --}}
                    {{-- TOMBOL AKSI --}}
                    {{-- Tombol Aksi Kanan --}}
                    <div class="flex items-center gap-3 w-full lg:w-auto justify-end">
                        
                        {{-- Tombol Import --}}
                        <button x-data="" x-on:click="$dispatch('open-modal', 'import-siswa')" 
                            class="inline-flex items-center justify-center gap-2 bg-green-600 text-white px-6 py-2 rounded-md text-sm font-semibold hover:bg-green-700 transition shadow whitespace-nowrap">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                            Import Akun
                        </button>

                        {{-- Tombol Tambah --}}
                        <x-primary-button x-data="" x-on:click="$dispatch('open-modal', 'add-siswa')" 
                            class="flex items-center justify-center gap-2 px-6 whitespace-nowrap">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            Tambah Siswa
                        </x-primary-button>
                    </div>
                </div>

                {{-- [BAGIAN 2: TABEL DATA] --}}
                <form action="{{ route('admin.siswas.bulk_delete') }}" method="POST" id="bulkDeleteForm" onsubmit="return confirm('Yakin hapus data terpilih? Data user terkait juga akan dihapus!')">
                    @csrf
                    @method('DELETE')

                    <div id="bulkDeleteContainer" class="hidden mb-3 bg-red-50 p-2 rounded flex justify-between items-center border border-red-200">
                        <span class="text-red-700 text-sm font-semibold ml-2">
                            <span id="selectedCount">0</span> Siswa dipilih
                        </span>
                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-xs font-bold transition">
                            Hapus Terpilih
                        </button>
                    </div>

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
                                    <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase">Kelas</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase">Thn Ajaran</th>
                                    <th class="px-6 py-4 text-center text-xs font-bold text-white uppercase">Status</th>
                                    <th class="px-6 py-4 text-right text-xs font-bold text-white uppercase">Aksi</th>
                                </tr>
                            </thead>

                            <tbody class="bg-white divide-y divide-gray-100">
                                @forelse($siswas as $siswa)
                                    <tr class="hover:bg-indigo-50/50 transition even:bg-gray-50">
                                        <td class="px-4 py-4 text-center">
                                            <input type="checkbox" name="ids[]" value="{{ $siswa->id }}" class="select-item rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 cursor-pointer">
                                        </td>
                                        <td class="px-4 py-4 text-center text-sm text-gray-500 font-medium">
                                            {{ $siswas->firstItem() + $loop->index }}
                                        </td>
                                        <td class="px-6 py-4 text-sm font-mono text-gray-600">
                                            {{ $siswa->nisn ?? '-' }}
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-bold text-gray-900">{{ $siswa->user->name ?? 'User Terhapus' }}</div>
                                            <div class="text-xs text-gray-400">{{ $siswa->user->email ?? '' }}</div>
                                        </td>
                                        <td class="px-6 py-4">
                                            @if($siswa->kelas)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                                    {{ $siswa->kelas->tingkat }} {{ $siswa->kelas->nama_kelas }}
                                                </span>
                                            @else
                                                <span class="text-xs text-red-400 font font-semibold">No Class</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500">
                                            {{ $siswa->tahunAjaran->tahun ?? '-' }}
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            @php
                                                $statusClass = match($siswa->status) {
                                                    'Aktif' => 'bg-green-100 text-green-800 border-green-200',
                                                    'Lulus' => 'bg-blue-100 text-blue-800 border-blue-200',
                                                    'Keluar' => 'bg-red-100 text-red-800 border-red-200',
                                                    'Pindah' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                                    default => 'bg-gray-100 text-gray-800 border-gray-200',
                                                };
                                            @endphp
                                            <span class="px-3 py-1 inline-flex text-[10px] font-bold border rounded-full {{ $statusClass }}">
                                                {{ strtoupper($siswa->status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-right text-sm font-medium">
                                            <a href="{{ route('admin.siswas.edit', $siswa->id) }}"
                                            class="text-indigo-600 hover:text-indigo-900 mr-3 font-semibold">Edit</a>

                                            <button type="button" onclick="confirmDelete('{{ route('admin.siswas.destroy', $siswa->id) }}', '{{ $siswa->user->name ?? '' }}')" 
                                                class="text-red-600 hover:text-red-900 font-semibold bg-transparent border-0 cursor-pointer">
                                                Hapus
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="px-6 py-12 text-center text-gray-400 italic flex-col">
                                            <span class="block mb-2 text-xl"></span>
                                            Data siswa tidak ditemukan untuk pencarian/filter ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </form> 

                {{-- [BAGIAN 3: PAGINATION & PILIHAN LIMIT (MODIFIKASI)] --}}
                {{-- [PERUBAHAN 2]: Ubah Div Biasa menjadi Flex agar bisa Kiri-Kanan --}}
                <div class="mt-6 flex flex-col sm:flex-row justify-between items-center gap-4">
                    
                    {{-- [PERUBAHAN 3]: Tambahkan Form Pilihan "Show" di Sebelah Kiri --}}
                    <form method="GET" action="{{ route('admin.siswas.index') }}">
                        {{-- Penting: Sertakan hidden input agar filter Search/Kelas tidak hilang saat ganti limit --}}
                        <input type="hidden" name="search" value="{{ request('search') }}">
                        <input type="hidden" name="kelas_id" value="{{ request('kelas_id') }}">
                        <input type="hidden" name="status" value="{{ request('status') }}">
                        
                        <div class="flex items-center gap-2">
                            <span class="text-sm text-gray-500">Show:</span>
                            <select name="per_page" onchange="this.form.submit()" 
                                class="text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-1 pl-2 pr-8">
                                <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                                <option value="20" {{ request('per_page') == 20 ? 'selected' : '' }}>20</option>
                                <option value="30" {{ request('per_page') == 30 ? 'selected' : '' }}>30</option>
                                <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                            </select>
                        </div>
                    </form>

                    {{-- [PERUBAHAN 4]: Pagination Links (Halaman 1,2,3) di Sebelah Kanan --}}
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

    <form id="deleteForm" method="POST" class="hidden">
        @csrf @method('DELETE')
    </form>

    <script>
        // ... (Script JS tetap sama) ...
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

        selectAll.addEventListener('change', function() {
            items.forEach(item => {
                item.checked = this.checked;
            });
            toggleBulkDeleteButton();
        });

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

        function confirmDelete(url, name) {
            if (confirm('Yakin ingin menghapus siswa ' + name + '?')) {
                const form = document.getElementById('deleteForm');
                form.action = url;
                form.submit();
            }
        }
    </script>
</x-app-layout>