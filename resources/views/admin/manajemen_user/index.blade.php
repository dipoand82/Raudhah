<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Manajemen User') }}</h2>
    </x-slot>

    <div class="py-12" x-data="{ activeTab: 'siswa' }"> <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
             @if ($errors->any())
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                    <strong class="font-bold">Ada Kesalahan!</strong>
                    <ul class="mt-2 list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if(session('success'))
                <div class="mb-4 bg-green-100 text-green-700 p-4 rounded">{{ session('success') }}</div>
            @endif

            <div class="bg-white p-4 rounded-t-lg shadow-sm border-b flex flex-col md:flex-row justify-between items-center gap-4">
                
                <div class="flex space-x-4">
                    <button @click="activeTab = 'siswa'" :class="{ 'border-b-2 border-indigo-500 text-indigo-600 font-bold': activeTab === 'siswa' }" class="pb-2 text-gray-500 hover:text-gray-700">
                        Siswa
                    </button>
                    <button @click="activeTab = 'guru'" :class="{ 'border-b-2 border-indigo-500 text-indigo-600 font-bold': activeTab === 'guru' }" class="pb-2 text-gray-500 hover:text-gray-700">
                        Guru
                    </button>
                    <button @click="activeTab = 'password'" :class="{ 'border-b-2 border-indigo-500 text-indigo-600 font-bold': activeTab === 'password' }" class="pb-2 text-gray-500 hover:text-gray-700">
                        Ubah Password
                    </button>
                </div>

                <div x-show="activeTab !== 'password'" class="w-full md:w-1/3 relative">
                    <form method="GET">
                        <input type="text" name="search" placeholder="Cari Nama/Email..." class="w-full rounded-full border-gray-300 pl-10 focus:border-indigo-500 focus:ring-indigo-500">
                        <span class="absolute left-3 top-2.5 text-gray-400">🔍</span>
                    </form>
                </div>
            </div>

            <div class="bg-gray-50 p-6 rounded-b-lg shadow-inner min-h-[400px]">

                <div x-show="activeTab === 'siswa'">
                    <div class="flex justify-end gap-3 mb-4">
                        <form action="{{ route('admin.manajemen-user.siswa.import') }}" method="POST" enctype="multipart/form-data" class="flex items-center gap-2">
                            @csrf
                            <input type="file" name="file" class="text-sm w-48" required>
                            <button type="submit" class="bg-gray-600 text-white px-3 py-2 rounded text-sm hover:bg-gray-700">+ Import Akun Massal</button>
                        </form>
                        
                        <button x-data="" x-on:click="$dispatch('open-modal', 'add-siswa')" class="bg-indigo-600 text-white px-4 py-2 rounded text-sm hover:bg-indigo-700">
                            + Tambah Akun Siswa
                        </button>
                    </div>

                <div class="overflow-x-auto bg-white rounded shadow">

                    <table class="min-w-full bg-white rounded shadow">
                        <thead class="bg-gray-200">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold uppercase">Nama</th>
                                <th class="px-6 py-3 text-left text-xs font-bold uppercase">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-bold uppercase">Role</th>
                                <th class="px-6 py-3 text-left text-xs font-bold uppercase">NISN</th>
                                <th class="px-6 py-3 text-right text-xs font-bold uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($userSiswa as $u)
                            <tr>
                                <td class="px-6 py-4">{{ $u->name }}</td>
                                <td class="px-6 py-4 text-gray-500">{{ $u->email }}</td>
                                <td class="px-6 py-4"><span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded">Siswa</span></td>
                                <td class="px-6 py-4 text-gray-500">{{ $u->dataSiswa->nisn ?? '-' }}</td>
                                <td class="px-6 py-4 text-right text-sm font-medium">
                                    {{-- Tombol Edit --}}
                                    {{-- Pastikan user punya dataSiswa (id) sebelum membuat link edit --}}
                                    @if($u->dataSiswa)
                                        <a href="{{ route('admin.siswas.edit', $u->dataSiswa->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-3 font-semibold">
                                            Edit
                                        </a>
                                    @else
                                        <span class="text-gray-400 text-xs mr-3 cursor-not-allowed" title="Data profil siswa belum lengkap">Edit</span>
                                    @endif
                                    
                                    {{-- Tombol Hapus --}}
                                    {{-- Menggunakan route destroy user, karena menghapus User otomatis menghapus Siswa (Cascade) --}}
                                    {{-- ATAU bisa pakai route admin.siswas.destroy jika ingin spesifik lewat controller siswa --}}
                                    {{-- Di sini kita pakai route hapus siswa agar konsisten dengan menu Data Siswa --}}
                                    
                                    @if($u->dataSiswa)
                                        <form action="{{ route('admin.siswas.destroy', $u->dataSiswa->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin hapus User & Data Siswa ini secara permanen?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900 font-semibold">
                                                Hapus
                                            </button>
                                        </form>
                                    @else
                                        {{-- Jika dataSiswa tidak ada, hapus User-nya saja --}}
                                        <form action="{{ route('admin.manajemen-user.destroy', $u->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus User ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900 font-semibold">
                                                Hapus
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="mt-4">{{ $userSiswa->links() }}</div>
                </div>

                <div x-show="activeTab === 'guru'" style="display: none;">
                    <div class="flex justify-end gap-3 mb-4">
                        <form action="{{ route('admin.manajemen-user.guru.import') }}" method="POST" enctype="multipart/form-data" class="flex items-center gap-2">
                            @csrf
                            <input type="file" name="file" class="text-sm w-48" required>
                            <button type="submit" class="bg-gray-600 text-white px-3 py-2 rounded text-sm hover:bg-gray-700">+ Import Guru</button>
                        </form>
                        <button x-data="" x-on:click="$dispatch('open-modal', 'add-guru')" class="bg-indigo-600 text-white px-4 py-2 rounded text-sm hover:bg-indigo-700">
                            + Tambah Akun Guru
                        </button>
                    </div>
                    
                    <div class="overflow-x-auto bg-white rounded shadow">
                        <table class="min-w-full bg-white rounded shadow">
                            <thead class="bg-gray-200">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-bold uppercase">Nama</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold uppercase">Email</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold uppercase">Role</th>
                                    <th class="px-6 py-3 text-right text-xs font-bold uppercase">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($userGuru as $g)
                                <tr>
                                    <td class="px-6 py-4">{{ $g->name }}</td>
                                    <td class="px-6 py-4 text-gray-500">{{ $g->email }}</td>
                                    <td class="px-6 py-4"><span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded">Guru</span></td>
                                    <td class="px-6 py-4 text-right">Hapus via Data Guru</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                </div>

                <div x-show="activeTab === 'password'" style="display: none;">
                    <div class="max-w-md mx-auto bg-white p-6 rounded shadow">
                        <h3 class="font-bold text-lg mb-4">Ubah Password Admin</h3>
                        <form action="{{ route('admin.manajemen-user.password.update') }}" method="POST">
                            @csrf @method('PUT')
                            <div class="mb-4">
                                <x-input-label :value="__('Password Saat Ini')" />
                                <x-text-input type="password" name="current_password" class="w-full mt-1" required />
                            </div>
                            <div class="mb-4">
                                <x-input-label :value="__('Password Baru')" />
                                <x-text-input type="password" name="password" class="w-full mt-1" required />
                            </div>
                            <div class="mb-4">
                                <x-input-label :value="__('Konfirmasi Password Baru')" />
                                <x-text-input type="password" name="password_confirmation" class="w-full mt-1" required />
                            </div>
                            <button class="w-full bg-indigo-600 text-white py-2 rounded hover:bg-indigo-700">Simpan Password</button>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <x-modal name="add-siswa" focusable>
        <form method="POST" action="{{ route('admin.manajemen-user.siswa.store') }}" class="p-6">
            @csrf
            
            <h2 class="text-lg font-bold text-gray-900 mb-4 border-b pb-2">Tambah Siswa Baru</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                
                <div class="col-span-2">
                    <x-input-label value="Nama Lengkap" />
                    <x-text-input name="name" class="w-full mt-1" required placeholder="Contoh: Budi Santoso" />
                </div>

                <div class="col-span-2">
                    <x-input-label value="Email Login" />
                    <x-text-input name="email" type="email" class="w-full mt-1" required placeholder="email@sekolah.com" />
                </div>

                <div>
                    <x-input-label value="NISN" />
                    <x-text-input name="nisn" type="number" class="w-full mt-1" placeholder="00123456" />
                </div>

                <div>
                    <x-input-label value="Jenis Kelamin" />
                    <select name="jenis_kelamin" class="w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="L">Laki-laki</option>
                        <option value="P">Perempuan</option>
                    </select>
                </div>

                <div class="col-span-2">
                    <x-input-label value="Masuk Kelas" />
                    <select name="kelas_id" class="w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">-- Pilih Kelas --</option>
                        @foreach($kelas as $k)
                            <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-500 mt-1">*Boleh dikosongkan jika belum ada kelas.</p>
                </div>

            </div>

            <div class="mt-6 flex justify-end gap-3">
                <x-secondary-button x-on:click="$dispatch('close')">Batal</x-secondary-button>
                <x-primary-button>Simpan Siswa</x-primary-button>
            </div>
        </form>
    </x-modal>

    <x-modal name="add-guru" focusable>
        <form method="POST" action="{{ route('admin.manajemen-user.guru.store') }}" class="p-6">
            @csrf
            <h2 class="text-lg font-medium text-gray-900 mb-4">Tambah Akun Guru</h2>
            <div class="mb-3">
                <x-input-label value="Nama Guru" />
                <x-text-input name="name" class="w-full" required />
            </div>
            <div class="mb-3">
                <x-input-label value="Email" />
                <x-text-input name="email" type="email" class="w-full" required />
            </div>
            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">Batal</x-secondary-button>
                <x-primary-button class="ml-3">Simpan</x-primary-button>
            </div>
        </form>
    </x-modal>

</x-app-layout>