<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Profil Sekolah') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="mb-4 bg-green-100 text-green-700 p-4 rounded shadow-sm">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <form method="POST" action="{{ route('admin.profil.update') }}" enctype="multipart/form-data">
                        @csrf
                        @method('PATCH')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            
                            <div class="space-y-4">
                                <h3 class="text-lg font-bold text-gray-700 border-b pb-2">Identitas Sekolah</h3>
                                
                                <div>
                                    <x-input-label for="nama_sekolah" :value="__('Nama Sekolah')" />
                                    <x-text-input id="nama_sekolah" class="block mt-1 w-full" type="text" name="nama_sekolah" :value="old('nama_sekolah', $profil->nama_sekolah)" required />
                                </div>

                                <div>
                                    <x-input-label for="telepon" :value="__('Nomor Telepon')" />
                                    <x-text-input id="telepon" class="block mt-1 w-full" type="text" name="telepon" :value="old('telepon', $profil->telepon)" />
                                </div>

                                <div>
                                    <x-input-label for="email" :value="__('Email Resmi')" />
                                    <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $profil->email)" />
                                </div>

                                <div>
                                    <x-input-label for="alamat" :value="__('Alamat Lengkap')" />
                                    <textarea id="alamat" name="alamat" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" rows="3">{{ old('alamat', $profil->alamat) }}</textarea>
                                </div>
                            </div>

                            <div class="space-y-4">
                                <h3 class="text-lg font-bold text-gray-700 border-b pb-2">Branding & Tujuan</h3>

                                <div>
                                    {{-- <x-input-label for="logo" :value="__('Logo Sekolah')" /> --}}
                                    
                                    @if($profil->logo_path)
                                        {{-- <div class="mb-2">
                                            <img src="{{ asset('storage/' . $profil->logo_path) }}" alt="Logo" class="h-20 w-auto object-contain border p-1 rounded">
                                        </div> --}}
                                    @endif
                                    
                                    <input type="file" name="logo" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" />
                                    <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG. Maks: 2MB.</p>
                                </div>

                                <div>
                                    <x-input-label for="visi" :value="__('Visi')" />
                                    <textarea id="visi" name="visi" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" rows="2">{{ old('visi', $profil->visi) }}</textarea>
                                </div>

                                <div>
                                    <x-input-label for="misi" :value="__('Misi')" />
                                    <textarea id="misi" name="misi" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" rows="3">{{ old('misi', $profil->misi) }}</textarea>
                                </div>
                            </div>

                        </div>

                        <div class="flex items-center justify-end mt-6 pt-4 border-t">
                            <x-primary-button clas>
                                {{ __('Simpan Perubahan') }}
                            </x-primary-button>
                        </div>

                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>




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
                        
                        {{-- 1. TOOLBAR FILTER & SEARCH (Sama seperti Data Siswa) --}}
                        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-6 gap-4">
                            {{-- Form Filter --}}
                            <form method="GET" action="{{ route('admin.manajemen-user.index') }}" class="w-full lg:w-3/4 flex flex-col md:flex-row gap-2">
                                {{-- Agar tetap di tab siswa saat reload --}}
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
                            <div class="flex items-center gap-2 w-full lg:w-auto justify-end">
                                <button x-data="" x-on:click="$dispatch('open-modal', 'import-siswa')" class="flex items-center gap-2 bg-green-600 text-white px-4 py-2 rounded-md text-sm font-semibold hover:bg-green-700 transition shadow">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                    Import
                                </button>
                                <x-primary-button x-data="" x-on:click="$dispatch('open-modal', 'add-siswa')" class="flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                    Tambah
                                </x-primary-button>
                            </div>
                        </div>

                        {{-- MODAL IMPORT SISWA (Biarkan Tetap Ada) --}}
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

                        {{-- 2. TABEL SISWA (DENGAN CHECKBOX & GAYA BARU) --}}
                        {{-- Form Bulk Delete --}}
                        <form action="{{ route('admin.siswas.bulk_delete') }}" method="POST" id="bulkDeleteForm" onsubmit="return confirm('Yakin hapus data terpilih? Data user terkait juga akan dihapus!')">
                            @csrf @method('DELETE')

                            {{-- Tombol Bulk Delete (Muncul via JS) --}}
                            <div id="bulkDeleteContainer" class="hidden mb-3 bg-red-50 p-2 rounded flex justify-between items-center border border-red-200">
                                <span class="text-red-700 text-sm font-semibold ml-2"><span id="selectedCount">0</span> Siswa dipilih</span>
                                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-xs font-bold transition">Hapus Terpilih</button>
                            </div>

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
                                                    {{-- Gunakan ID Data Siswa untuk Value Checkbox --}}
                                                    @if($u->dataSiswa)
                                                        <input type="checkbox" name="ids[]" value="{{ $u->dataSiswa->id }}" class="select-item rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 cursor-pointer">
                                                    @else
                                                        <span class="text-gray-300">-</span>
                                                    @endif
                                                </td>

                                                {{-- NO --}}
                                                <td class="px-4 py-4 text-center text-sm font-medium text-gray-500">{{ $userSiswa->firstItem() + $loop->index }}</td>
                                                
                                                {{-- NAMA SISWA (Stack dengan Email) --}}
                                                <td class="px-6 py-4">
                                                    <div class="text-sm font-bold text-gray-900">{{ $u->name }}</div>
                                                    <div class="text-xs text-gray-400">{{ $u->email }}</div>
                                                </td>

                                                {{-- NISN --}}
                                                <td class="px-6 py-4 text-sm font-mono text-gray-600">
                                                    @if($u->dataSiswa)
                                                        {{ $u->dataSiswa->nisn }}
                                                    @else
                                                        <span class="text-red-400 italic text-xs">Unlinked</span>
                                                    @endif
                                                </td>

                                                {{-- KELAS --}}
                                                <td class="px-6 py-4">
                                                    @if($u->dataSiswa && $u->dataSiswa->kelas)
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                                            {{ $u->dataSiswa->kelas->tingkat }} {{ $u->dataSiswa->kelas->nama_kelas }}
                                                        </span>
                                                    @else
                                                        <span class="text-xs text-red-400 italic">Belum Masuk Kelas</span>
                                                    @endif
                                                </td>

                                                {{-- STATUS --}}
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
                                                        {{-- Reset Password --}}
                                                        <form action="{{ route('admin.manajemen-user.siswa.reset', $u->dataSiswa->id) }}" method="POST" onsubmit="return confirm('Yakin reset password siswa ini kembali ke NISN?');" class="inline-flex">
                                                            @csrf
                                                            <button type="submit" class="text-yellow-600 hover:text-yellow-700 transition transform hover:scale-110 p-1" title="Reset Password ke NISN">
                                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" /></svg>
                                                            </button>
                                                        </form>
                                                        {{-- Edit --}}
                                                        <a href="{{ route('admin.siswas.edit', $u->dataSiswa->id) }}" class="text-indigo-600 hover:text-indigo-900 font-semibold">Edit</a>
                                                        {{-- Hapus --}}
                                                        <button type="button" onclick="confirmDelete('{{ route('admin.siswas.destroy', $u->dataSiswa->id) }}', '{{ $u->name }}')" class="text-red-600 hover:text-red-900 font-semibold bg-transparent border-0 cursor-pointer">Hapus</button>
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
                        </form>