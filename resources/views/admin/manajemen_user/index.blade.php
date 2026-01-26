<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Manajemen User') }}</h2>
    </x-slot>

    <div class="py-12" x-data="{ activeTab: 'siswa' }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- ALERT ERROR --}}
            @if ($errors->any())
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative shadow-sm">
                    <strong class="font-bold flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Ada Kesalahan!
                    </strong>
                    <ul class="mt-2 list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- ALERT SUCCESS --}}
            @if(session('success'))
                <div x-data="{ show: true }" x-show="show" class="mb-4 flex items-center justify-between bg-green-100 border border-green-200 text-green-700 px-4 py-3 rounded shadow-sm">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button @click="show = false" class="text-green-500 hover:text-green-700">&times;</button>
                </div>
            @endif

            <div class="bg-gray-100 p-6 rounded-lg shadow-sm border border-gray-200">
                
                {{-- TAB NAVIGATION --}}
                <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200 mb-6">
                    <div class="flex space-x-6">
                        <button @click="activeTab = 'siswa'" :class="{ 'border-b-2 border-[#3B3E42] text-[#3B3E42] font-bold': activeTab === 'siswa' }" class="pb-2 text-gray-500 hover:text-[#3B3E42] transition duration-150 font-medium">Siswa</button>
                        <button @click="activeTab = 'guru'" :class="{ 'border-b-2 border-[#3B3E42] text-[#3B3E42] font-bold': activeTab === 'guru' }" class="pb-2 text-gray-500 hover:text-[#3B3E42] transition duration-150 font-medium">Guru</button>
                        <button @click="activeTab = 'password'" :class="{ 'border-b-2 border-[#3B3E42] text-[#3B3E42] font-bold': activeTab === 'password' }" class="pb-2 text-gray-500 hover:text-[#3B3E42] transition duration-150 font-medium">Ubah Password</button>
                    </div>
                </div>

                <div class="min-h-[400px]">

                    {{-- ================= KONTEN TAB SISWA ================= --}}
                    <div x-show="activeTab === 'siswa'" x-transition>
                        
                        {{-- 1. TOOLBAR FILTER & SEARCH --}}
                        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-6 gap-4">
                            {{-- Form Filter --}}
                            <form method="GET" action="{{ route('admin.manajemen-user.index') }}" class="w-full lg:w-3/4 flex flex-col md:flex-row gap-2">
                                <input type="hidden" name="tab" value="siswa"> 
                                <input type="hidden" name="per_page" value="{{ request('per_page', 10) }}">

                                {{-- Search --}}
                                <div class="relative w-full md:w-1/3">
                                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Nama / NISN..." class="w-full rounded-full border-gray-300 pl-5 pr-10 py-2 focus:border-[#3B3E42] focus:ring-[#3B3E42] shadow-sm text-sm">
                                    <button type="submit" class="absolute right-3 top-2.5 text-gray-400 hover:text-[#3B3E42]">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                                    </button>
                                </div>

                                {{-- Filter Kelas --}}
                                <div class="w-full md:w-1/4">
                                    <select name="kelas_id" onchange="this.form.submit()" class="w-full rounded-full border-gray-300 py-2 pl-4 pr-8 shadow-sm focus:border-[#3B3E42] focus:ring-[#3B3E42] cursor-pointer text-gray-700 text-sm">
                                        <option value="">-- Semua Kelas --</option>
                                        @foreach($kelas as $k) 
                                            <option value="{{ $k->id }}" {{ request('kelas_id') == $k->id ? 'selected' : '' }}>
                                                Kelas {{ $k->tingkat }} {{ $k->nama_kelas }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Filter Status --}}
                                <div class="w-full md:w-1/5">
                                    <select name="status" onchange="this.form.submit()" class="w-full rounded-full border-gray-300 py-2 pl-4 pr-8 shadow-sm focus:border-[#3B3E42] focus:ring-[#3B3E42] cursor-pointer text-gray-700 text-sm">
                                        <option value="">-- Status --</option>
                                        @foreach(['Aktif', 'Lulus', 'Pindah', 'Keluar'] as $st)
                                            <option value="{{ $st }}" {{ request('status') == $st ? 'selected' : '' }}>{{ $st }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </form>

                            {{-- Tombol Aksi Kanan --}}
                            <div class="flex items-center gap-3 w-full lg:w-auto justify-end">
                                <button x-data="" x-on:click="$dispatch('open-modal', 'import-siswa')" class="inline-flex items-center justify-center gap-2 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-semibold text-sm transition shadow-sm whitespace-nowrap">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                    Import Akun Siswa
                                </button>
                            <button type="button" x-data="" x-on:click="$dispatch('open-modal', 'add-siswa')" class="inline-flex items-center justify-center gap-2 bg-[#1072B8] hover:bg-[#0d5a91] text-white px-4 py-2 rounded-lg font-semibold text-sm transition shadow-sm whitespace-nowrap capitalize">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                Tambah Siswa
                            </button>
                            </div>
                        </div>

                        {{-- MODAL IMPORT SISWA --}}
                        <x-modal name="import-siswa" focusable>
                            <form method="POST" action="{{ route('admin.manajemen-user.siswa.import') }}" enctype="multipart/form-data" class="p-6" x-data="{ fileName: '', isLoading: false }" @submit="isLoading = true">
                                @csrf
                                <div class="flex justify-between items-start mb-5 border-b pb-3">
                                    <h2 class="text-xl font-bold text-gray-900">Import Data Siswa</h2>
                                    <button type="button" x-on:click="$dispatch('close')" class="text-gray-400 hover:text-gray-600">
                                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </button>
                                </div>
                                {{-- Alert Syarat Import --}}
                                <div class="mb-4 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-md">
                                    <div class="flex">
                                        <div class="flex-shrink-0"><svg class="h-5 w-5 text-red-500" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" /></svg></div>
                                        <div class="ml-3">
                                            <h3 class="text-sm font-bold text-red-800">Syarat Wajib Import:</h3>
                                            <ul class="mt-1 list-disc list-inside text-xs text-red-700 font-medium">
                                                <li>Pastikan <strong>Tahun Ajaran Aktif</strong> sudah diset.</li>
                                                <li>Pastikan data <strong>Kelas</strong> sudah tersedia.</li>
                                                <li>Penulisan nama kelas harus <strong>SAMA PERSIS</strong>.</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6 flex justify-between items-center">
                                    <div><h3 class="text-sm font-bold text-blue-800 mb-1">Langkah 1: Siapkan File</h3><p class="text-xs text-blue-600">Gunakan template resmi.</p></div>
                                    <a href="{{ route('admin.manajemen-user.siswa.template')}}" class="bg-white text-blue-700 hover:bg-blue-100 border border-blue-300 px-3 py-2 rounded text-sm font-bold shadow-sm transition flex items-center gap-2"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>Template</a>
                                </div>
                                <div class="mb-6">
                                    <h3 class="text-sm font-bold text-gray-700 mb-2">Langkah 2: Upload File</h3>
                                    <div class="relative border-2 border-dashed border-gray-300 rounded-lg p-8 hover:border-[#3B3E42] transition bg-gray-50 text-center group">
                                        <input type="file" name="file" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" required accept=".xlsx, .xls, .csv" @change="fileName = $event.target.files[0].name">
                                        <div class="pointer-events-none space-y-2">
                                            <p class="text-sm text-gray-600 font-medium" x-text="fileName ? 'File: ' + fileName : 'Klik untuk pilih file'"></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex justify-end gap-3 pt-4 border-t">
                                    <x-secondary-button x-on:click="$dispatch('close')" type="button">Batal</x-secondary-button>
                                    <x-primary-button class="bg-[#3B3E42] hover:bg-gray-700" ::disabled="isLoading"><span x-text="isLoading ? 'Memproses...' : 'Proses Import'"></span></x-primary-button>
                                </div>
                            </form>
                        </x-modal>

                        {{-- 2. TABEL SISWA --}}
                        
                        {{-- PERUBAHAN 1: FORM BULK DELETE DIPISAH KELUAR (Standalone) --}}
                        {{-- Form ini kosong, hanya sebagai wadah untuk submit DELETE massal --}}
                        <form id="bulkDeleteForm" action="{{ route('admin.siswas.bulk_delete') }}" method="POST" onsubmit="return confirm('Yakin hapus data terpilih? Data user terkait juga akan dihapus!')">
                            @csrf @method('DELETE')
                        </form>

                        {{-- Tombol Bulk Delete (Muncul via JS) --}}
                        <div id="bulkDeleteContainer" class="hidden mb-3 bg-red-50 p-2 rounded flex justify-between items-center border border-red-200">
                            <span class="text-red-700 text-sm font-semibold ml-2"><span id="selectedCount">0</span> Siswa dipilih</span>
                            
                            {{-- PERUBAHAN 2: Tombol ini dihubungkan ke form di atas pakai attribute form="bulkDeleteForm" --}}
                            <button type="submit" form="bulkDeleteForm" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-xs font-bold transition">Hapus Terpilih</button>
                        </div>

                        {{-- Tabel Tidak Lagi Dibungkus Form --}}
                        <div class="overflow-x-auto bg-white rounded-xl shadow border border-gray-200">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-[#3B3E42]"> 
                                    <tr>
                                        <th class="px-4 py-4 w-10 text-center">
                                            <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 cursor-pointer">
                                        </th>
                                        <th class="px-4 py-4 text-center text-xs font-bold text-white uppercase w-12">No</th>
                                        <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase">Nama Siswa</th>
                                        <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase">NISN</th>
                                        <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase">Kelas</th>
                                        <th class="px-6 py-4 text-center text-xs font-bold text-white uppercase">Status</th>
                                        <th class="px-6 py-4 text-right text-xs font-bold text-white uppercase">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-100">
                                    @foreach($userSiswa as $u)
                                        <tr class="hover:bg-indigo-50/50 transition even:bg-gray-50">
                                            {{-- CHECKBOX --}}
                                            <td class="px-4 py-4 text-center">
                                                @if($u->dataSiswa)
                                                    {{-- PERUBAHAN 3: Checkbox dihubungkan ke form bulk pakai attribute form="bulkDeleteForm" --}}
                                                    <input type="checkbox" name="ids[]" form="bulkDeleteForm" value="{{ $u->dataSiswa->id }}" class="select-item rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 cursor-pointer">
                                                @else
                                                    <span class="text-gray-300">-</span>
                                                @endif
                                            </td>

                                            <td class="px-4 py-4 text-center text-sm font-medium text-gray-500">{{ $userSiswa->firstItem() + $loop->index }}</td>
                                            
                                            <td class="px-6 py-4">
                                                <div class="text-sm font-bold text-gray-900">{{ $u->name }}</div>
                                                <div class="text-xs text-gray-400">{{ $u->email }}</div>
                                            </td>

                                            <td class="px-6 py-4 text-sm font-mono text-gray-600">
                                                @if($u->dataSiswa)
                                                    {{ $u->dataSiswa->nisn }}
                                                @else
                                                    <span class="text-red-400 italic text-xs">Unlinked</span>
                                                @endif
                                            </td>

                                            <td class="px-6 py-4">
                                                @if($u->dataSiswa && $u->dataSiswa->kelas)
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                                        {{ $u->dataSiswa->kelas->tingkat }} {{ $u->dataSiswa->kelas->nama_kelas }}
                                                    </span>
                                                @else
                                                    <span class="text-xs text-red-400 font font-semibold">No Class</span>
                                                @endif
                                            </td>

                                            <td class="px-6 py-4 text-center">
                                                @if($u->dataSiswa)
                                                    @php
                                                        $statusClass = match($u->dataSiswa->status) {
                                                            'Aktif' => 'bg-green-100 text-green-800 border-green-200',
                                                            'Lulus' => 'bg-blue-100 text-blue-800 border-blue-200',
                                                            'Keluar' => 'bg-red-100 text-red-800 border-red-200',
                                                            'Pindah' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                                            default => 'bg-gray-100 text-gray-800 border-gray-200',
                                                        };
                                                    @endphp
                                                    <span class="px-3 py-1 inline-flex text-[10px] font-bold border rounded-full {{ $statusClass }}">
                                                        {{ strtoupper($u->dataSiswa->status) }}
                                                    </span>
                                                @else
                                                    -
                                                @endif
                                            </td>

                                            {{-- AKSI --}}
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium flex justify-end items-center gap-3">
                                                @if($u->dataSiswa)
                                                    {{-- Reset Password (AMAN: Form ini sekarang mandiri, tidak di dalam form lain) --}}
                                                    <form action="{{ route('admin.manajemen-user.siswa.reset', $u->dataSiswa->id) }}" method="POST" onsubmit="return confirm('Yakin reset password siswa ini kembali ke NISN?');" class="inline-flex">
                                                        @csrf
                                                        <button type="submit" class="text-yellow-600 hover:text-yellow-700 transition transform hover:scale-110 p-1" title="Reset Password ke NISN">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" /></svg>
                                                        </button>
                                                    </form>
                                                    
                                                    {{-- Edit --}}
                                                    <a href="{{ route('admin.siswas.edit', $u->dataSiswa->id) }}" class="text-indigo-600 hover:text-indigo-900 font-semibold">Edit</a>
                                                    
                                                    {{-- Hapus (AMAN: Form ini sekarang mandiri) --}}
                                                    <form action="{{ route('admin.siswas.destroy', $u->dataSiswa->id) }}" method="POST" style="display:inline-block;">
                                                        @csrf @method('DELETE')
                                                        
                                                        <x-danger-button type="submit" onclick="return confirm('Apakah Anda yakin ingin menghapus {{ $u->name }}?')">
                                                            Hapus
                                                        </x-danger-button>
                                                    </form>
                                                @else
                                                    {{-- Hapus User Saja (Tanpa Data Siswa) --}}
                                                    <form action="{{ route('admin.manajemen-user.destroy', $u->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus User ini?');">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="text-red-600 hover:text-red-900 font-semibold">Hapus User</button>
                                                    </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- 3. PAGINATION & SHOW ENTRIES --}}
                        <div class="mt-6 flex flex-col sm:flex-row justify-between items-center gap-4">
                            <form method="GET" action="{{ route('admin.manajemen-user.index') }}">
                                <input type="hidden" name="tab" value="siswa">
                                <input type="hidden" name="search" value="{{ request('search') }}">
                                <input type="hidden" name="kelas_id" value="{{ request('kelas_id') }}">
                                <input type="hidden" name="status" value="{{ request('status') }}">
                                
                                <div class="flex items-center gap-2">
                                    <span class="text-sm text-gray-500">Show:</span>
                                    <select name="per_page" onchange="this.form.submit()" class="text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-1 pl-2 pr-8">
                                        <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                                        <option value="20" {{ request('per_page') == 20 ? 'selected' : '' }}>20</option>
                                        <option value="30" {{ request('per_page') == 30 ? 'selected' : '' }}>30</option>
                                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                                    </select>
                                </div>
                            </form>

                            <div>
                                {{ $userSiswa->appends(['search' => request('search'), 'kelas_id' => request('kelas_id'), 'status' => request('status'), 'per_page' => request('per_page'), 'tab' => 'siswa'])->links() }}
                            </div>
                        </div>

                    </div> 

                    {{-- ================= KONTEN TAB GURU ================= --}}
                    <div x-show="activeTab === 'guru'" x-transition>
                        <div class="flex justify-end gap-3 mb-4">
                            <x-primary-button x-data="" x-on:click="$dispatch('open-modal', 'add-guru')" class="flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                Tambah Akun Guru
                            </x-primary-button>
                        </div>
                        
                        <div class="overflow-x-auto bg-white rounded-xl shadow border border-gray-200">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-[#3B3E42]">
                                    <tr>
                                        <th class="px-4 py-4 text-center text-xs font-bold text-white uppercase w-12">No</th>
                                        <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase">Nama</th>
                                        <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase">Email</th>
                                        <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase">Role</th>
                                        <th class="px-6 py-4 text-right text-xs font-bold text-white uppercase">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-100">
                                    @foreach($userGuru as $g)
                                    <tr class="hover:bg-indigo-50/50 transition even:bg-gray-50">
                                        <td class="px-4 py-4 text-center text-sm font-medium text-gray-500">{{ $loop->iteration }}</td>
                                        <td class="px-6 py-4 font-medium text-gray-900">{{ $g->name }}</td>
                                        <td class="px-6 py-4 text-gray-600">{{ $g->email }}</td>
                                        <td class="px-6 py-4"><span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full font-semibold">Guru</span></td>
                                        <td class="px-6 py-4 text-right">
                                            <form action="{{ route('admin.manajemen-user.destroy', $g->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus akun Guru ini?');">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900 font-semibold text-sm">Hapus</button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- ================= KONTEN TAB PASSWORD ================= --}}
                    <div x-show="activeTab === 'password'" x-transition>
                        <div class="max-w-md mx-auto bg-white p-8 rounded-lg shadow-sm border border-gray-200 mt-6">
                            <h3 class="font-bold text-lg mb-6 text-[#3B3E42] flex items-center gap-2 border-b pb-4">Ubah Password Admin</h3>
                            
                            <form action="{{ route('admin.manajemen-user.password.update') }}" method="POST" autocomplete="off">
                                @csrf @method('PUT')
                                
                                <input type="text" style="display:none;" aria-hidden="true">
                                <input type="password" style="display:none;" aria-hidden="true">

                                <div class="mb-4">
                                    <x-input-label :value="__('Password Saat Ini')" />
                                    <x-text-input type="password" 
                                                name="current_password" 
                                                class="w-full mt-1 focus:border-[#3B3E42]" 
                                                required 
                                                readonly 
                                                onfocus="this.removeAttribute('readonly');"
                                                autocomplete="one-time-code" />
                                </div>

                                <div class="mb-4">
                                    <x-input-label :value="__('Password Baru')" />
                                    <x-text-input type="password" 
                                                name="password" 
                                                class="w-full mt-1 focus:border-[#3B3E42]" 
                                                required 
                                                readonly
                                                onfocus="this.removeAttribute('readonly');"
                                                autocomplete="new-password" />
                                </div>

                                <div class="mb-6">
                                    <x-input-label :value="__('Konfirmasi Password Baru')" />
                                    <x-text-input type="password" 
                                                name="password_confirmation" 
                                                class="w-full mt-1 focus:border-[#3B3E42]" 
                                                required 
                                                readonly
                                                onfocus="this.removeAttribute('readonly');"
                                                autocomplete="new-password" />
                                </div>

                                <x-primary-button class="w-full py-3 justify-center">Simpan Password Baru</x-primary-button>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- MODAL TAMBAH SISWA MANUAL --}}
    <x-modal name="add-siswa" focusable>
        <form method="POST" action="{{ route('admin.manajemen-user.siswa.store') }}" class="p-6">
            @csrf
            <div class="flex justify-between items-center mb-4 border-b pb-2"><h2 class="text-lg font-bold text-gray-900">Tambah Siswa</h2><button type="button" x-on:click="$dispatch('close')" class="text-gray-400 hover:text-gray-500"><span class="sr-only">Tutup</span><svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button></div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="col-span-2"><x-input-label value="Nama Lengkap" /><x-text-input name="name" class="w-full mt-1 focus:border-[#3B3E42] focus:ring-[#3B3E42]" required placeholder="Contoh: Budi Santoso" /></div>
                <div class="col-span-2"><x-input-label value="Email Login (Opsional)" /><x-text-input name="email" type="email" class="w-full mt-1 focus:border-[#3B3E42] focus:ring-[#3B3E42]" placeholder="Jika kosong, akan pakai nama.nisn@sekolah.id" /></div>
                <div><x-input-label value="NISN (Wajib)" /><x-text-input name="nisn" type="number" class="w-full mt-1 focus:border-[#3B3E42] focus:ring-[#3B3E42]" required placeholder="00123456" min="0" oninput="validity.valid||(value='');"/></div>
                <div><x-input-label value="Jenis Kelamin" /><select name="jenis_kelamin" class="w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-[#3B3E42] focus:ring-[#3B3E42]"><option value="L">Laki-laki</option><option value="P">Perempuan</option></select></div>
                <div class="col-span-2"><x-input-label value="Masuk Kelas" /><select name="kelas_id" class="w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-[#3B3E42] focus:ring-[#3B3E42]"><option value="">-- Belum Ada Kelas --</option>@foreach($kelas as $k)<option value="{{ $k->id }}">Kelas {{ $k->tingkat }} {{ $k->nama_kelas }}</option>@endforeach</select></div>
            </div>
            <div class="mt-6 flex justify-end gap-3"><x-secondary-button x-on:click="$dispatch('close')">Batal</x-secondary-button><x-primary-button>Simpan Siswa</x-primary-button></div>
        </form>
    </x-modal>

    {{-- MODAL TAMBAH GURU MANUAL --}}
    <x-modal name="add-guru" focusable>
        <form method="POST" action="{{ route('admin.manajemen-user.guru.store') }}" class="p-6">
            @csrf
            <h2 class="text-lg font-bold text-gray-900 mb-4 pb-2 border-b">Tambah Akun Guru</h2>
            <div class="mb-3"><x-input-label value="Nama Guru" /><x-text-input name="name" class="w-full focus:border-[#3B3E42] focus:ring-[#3B3E42]" required /></div>
            <div class="mb-3"><x-input-label value="Email" /><x-text-input name="email" type="email" class="w-full focus:border-[#3B3E42] focus:ring-[#3B3E42]" required /></div>
            <div class="mt-6 flex justify-end gap-3"><x-secondary-button x-on:click="$dispatch('close')">Batal</x-secondary-button><x-primary-button class="bg-[#3B3E42] hover:bg-gray-700">Simpan</x-primary-button></div>
        </form>
    </x-modal>

    {{-- Form Cadangan untuk delete satuan pakai JS (Jika masih digunakan oleh bagian Hapus User tanpa siswa) --}}
    <form id="deleteForm" method="POST" class="hidden">@csrf @method('DELETE')</form>

    <script>
        // SCRIPT BULK DELETE & CHECKBOX
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
                items.forEach(item => { item.checked = this.checked; });
                toggleBulkDeleteButton();
            });
        }

        items.forEach(item => {
            item.addEventListener('change', function() {
                if (!this.checked) { if(selectAll) selectAll.checked = false; }
                const allChecked = document.querySelectorAll('.select-item:checked').length === items.length;
                if(items.length > 0 && allChecked) { if(selectAll) selectAll.checked = true; }
                toggleBulkDeleteButton();
            });
        });

        // SCRIPT HAPUS SATUAN (Cadangan jika tidak pakai form inline)
        function confirmDelete(url, name) {
            if (confirm('Yakin ingin menghapus data siswa ' + name + ' secara permanen?')) {
                const form = document.getElementById('deleteForm');
                form.action = url;
                form.submit();
            }
        }
    </script>
</x-app-layout>