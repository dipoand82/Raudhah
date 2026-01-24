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
                <div class="mb-4 bg-green-100 text-green-700 px-4 py-3 rounded shadow-sm border border-green-200 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-gray-100 p-6 rounded-lg shadow-sm border border-gray-200">
                
                {{-- TAB NAVIGATION & SEARCH --}}
                <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200 mb-6 flex flex-col md:flex-row justify-between items-center gap-4">
                    <div class="flex space-x-6">
                        <button @click="activeTab = 'siswa'" :class="{ 'border-b-2 border-[#3B3E42] text-[#3B3E42] font-bold': activeTab === 'siswa' }" class="pb-2 text-gray-500 hover:text-[#3B3E42] transition duration-150 font-medium">Siswa</button>
                        <button @click="activeTab = 'guru'" :class="{ 'border-b-2 border-[#3B3E42] text-[#3B3E42] font-bold': activeTab === 'guru' }" class="pb-2 text-gray-500 hover:text-[#3B3E42] transition duration-150 font-medium">Guru</button>
                        <button @click="activeTab = 'password'" :class="{ 'border-b-2 border-[#3B3E42] text-[#3B3E42] font-bold': activeTab === 'password' }" class="pb-2 text-gray-500 hover:text-[#3B3E42] transition duration-150 font-medium">Ubah Password</button>
                    </div>
                    <div x-show="activeTab !== 'password'" class="w-full md:w-1/3 relative">
                        <form method="GET">
                            <input type="text" name="search" placeholder="Cari Nama/Email/NISN..." class="w-full rounded-full border-gray-300 pl-5 pr-10 py-2 focus:border-[#3B3E42] focus:ring-[#3B3E42] shadow-sm text-sm">
                            <button type="submit" class="absolute right-3 top-2.5 text-gray-400 hover:text-[#3B3E42]">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                            </button>
                        </form>
                    </div>
                </div>

                <div class="min-h-[400px]">

                    {{-- ================= KONTEN TAB SISWA ================= --}}
                    <div x-show="activeTab === 'siswa'" x-transition>
                        <div class="flex justify-end gap-3 mb-4">
                            <button x-data="" x-on:click="$dispatch('open-modal', 'import-siswa')" class="flex items-center gap-2 bg-green-600 text-white px-4 py-2 rounded-md text-sm font-semibold hover:bg-green-700 transition shadow">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                Import Akun
                            </button>
                            <x-primary-button x-data="" x-on:click="$dispatch('open-modal', 'add-siswa')" class="flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                Tambah Siswa
                            </x-primary-button>
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

                                {{-- PENTING: ALERT SYARAT IMPORT --}}
                                <div class="mb-4 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-md">
                                    <div class="flex">
                                        <div class="flex-shrink-0"><svg class="h-5 w-5 text-red-500" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" /></svg></div>
                                        <div class="ml-3">
                                            <h3 class="text-sm font-bold text-red-800">Syarat Wajib Import:</h3>
                                            <ul class="mt-1 list-disc list-inside text-xs text-red-700 font-medium">
                                                <li>Pastikan <strong>Tahun Ajaran Aktif</strong> sudah diset.</li>
                                                <li>Pastikan data <strong>Kelas</strong> sudah ada.</li>
                                                <li>Penulisan nama kelas harus <strong>SAMA PERSIS</strong>.</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                {{-- LANGKAH 1: DOWNLOAD --}}
                                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6 flex justify-between items-center">
                                    <div>
                                        <h3 class="text-sm font-bold text-blue-800 mb-1">Langkah 1: Siapkan File Excel</h3>
                                        <p class="text-xs text-blue-600">Gunakan template resmi.</p>
                                    </div>
                                    <a href="{{ route('admin.manajemen-user.siswa.template')}}" class="bg-white text-blue-700 hover:bg-blue-100 border border-blue-300 px-3 py-2 rounded text-sm font-bold shadow-sm transition flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                        Download Template
                                    </a>
                                </div>

                                {{-- LANGKAH 2: UPLOAD --}}
                                <div class="mb-6">
                                    <h3 class="text-sm font-bold text-gray-700 mb-2">Langkah 2: Upload File</h3>
                                    <div class="relative border-2 border-dashed border-gray-300 rounded-lg p-8 hover:border-[#3B3E42] transition bg-gray-50 text-center group">
                                        <input type="file" name="file" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" required accept=".xlsx, .xls, .csv" @change="fileName = $event.target.files[0].name">
                                        <div class="pointer-events-none space-y-2">
                                            <div class="mx-auto h-12 w-12 text-gray-400 group-hover:text-gray-600">
                                                <svg class="w-full h-full" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                                            </div>
                                            <p class="text-sm text-gray-600 font-medium" x-text="fileName ? 'File terpilih: ' + fileName : 'Klik area ini untuk memilih file'"></p>
                                            <p class="text-xs text-gray-500" x-show="!fileName">Format: .xlsx, .xls</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex justify-end gap-3 pt-4 border-t">
                                    <x-secondary-button x-on:click="$dispatch('close')" type="button">Batal</x-secondary-button>
                                    <x-primary-button class="bg-[#3B3E42] hover:bg-gray-700" ::disabled="isLoading">
                                        <span x-text="isLoading ? 'Memproses...' : 'Proses Import Sekarang'"></span>
                                    </x-primary-button>
                                </div>
                            </form>
                        </x-modal>

                        {{-- TABEL SISWA --}}
                        <div class="overflow-x-auto bg-white rounded-xl shadow border border-gray-200">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-[#3B3E42]"> 
                                    <tr>
                                        <th class="px-4 py-4 text-center text-xs font-bold text-white uppercase w-12">No</th>
                                        <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase">Nama Lengkap</th>
                                        <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase">Email / Username</th>
                                        <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase">NISN</th>
                                        <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase">Role</th>
                                        <th class="px-6 py-4 text-right text-xs font-bold text-white uppercase">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-100">
                                    @foreach($userSiswa as $u)
                                    <tr class="hover:bg-indigo-50/50 transition even:bg-gray-50">
                                        <td class="px-4 py-4 text-center text-sm font-medium text-gray-500">{{ $userSiswa->firstItem() + $loop->index }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">{{ $u->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $u->email }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            @if($u->dataSiswa)
                                                <span class="bg-indigo-50 text-indigo-700 px-2 py-1 rounded text-xs font-bold border border-indigo-100">{{ $u->dataSiswa->nisn }}</span>
                                            @else
                                                <span class="text-red-500 text-xs italic">Belum Linked</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full font-semibold">Siswa</span>
                                        </td>
                                        
                                        {{-- ========================================================== --}}
                                        {{-- KOLOM AKSI (EDIT, RESET PASSWORD, HAPUS) --}}
                                        {{-- ========================================================== --}}
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium flex justify-end items-center gap-3">
                                            @if($u->dataSiswa)
                                    <form action="{{ route('admin.manajemen-user.siswa.reset', $u->dataSiswa->id) }}" method="POST" onsubmit="return confirm('Yakin reset password siswa ini kembali ke NISN?');" class="inline-flex">
                                        @csrf
                                        <button type="submit" class="text-yellow-600 hover:text-yellow-700 transition transform hover:scale-110 p-1" title="Reset Password ke NISN">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="Height 15V9a6 6 0 00-6-6 6 6 0 00-6 6v6a6 6 0 006 6h7.5m-.5-5l-4 4m0 0l4 4m-4-4H15" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                                            </svg>
                                        </button>
                                    </form>
                                                {{-- 1. TOMBOL EDIT --}}
                                                <a href="{{ route('admin.siswas.edit', $u->dataSiswa->id) }}" class="text-indigo-600 hover:text-indigo-900 font-semibold">Edit</a>



                                                {{-- 3. TOMBOL HAPUS --}}
                                                <form action="{{ route('admin.siswas.destroy', $u->dataSiswa->id) }}" method="POST" class="inline-flex" onsubmit="return confirm('Hapus User & Data Siswa ini secara permanen?');">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900 font-semibold">Hapus</button>
                                                </form>
                                            @else
                                                <form action="{{ route('admin.manajemen-user.destroy', $u->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus User ini?');">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900 font-semibold">Hapus User</button>
                                                </form>
                                            @endif
                                        </td>
                                        {{-- ========================================================== --}}
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="p-4 bg-gray-50 border-t">{{ $userSiswa->links() }}</div>
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
                            <h3 class="font-bold text-lg mb-6 text-[#3B3E42] flex items-center gap-2 border-b pb-4">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                Ubah Password Admin
                            </h3>
                            <form action="{{ route('admin.manajemen-user.password.update') }}" method="POST">
                                @csrf @method('PUT')
                                <div class="mb-4"><x-input-label :value="__('Password Saat Ini')" /><x-text-input type="password" name="current_password" class="w-full mt-1 focus:border-[#3B3E42] focus:ring-[#3B3E42]" required /></div>
                                <div class="mb-4"><x-input-label :value="__('Password Baru')" /><x-text-input type="password" name="password" class="w-full mt-1 focus:border-[#3B3E42] focus:ring-[#3B3E42]" required /></div>
                                <div class="mb-6"><x-input-label :value="__('Konfirmasi Password Baru')" /><x-text-input type="password" name="password_confirmation" class="w-full mt-1 focus:border-[#3B3E42] focus:ring-[#3B3E42]" required /></div>
                                <x-primary-button class="w-full mt-1 py-3 justify-center">Simpan Password Baru</x-primary-button>
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

</x-app-layout>