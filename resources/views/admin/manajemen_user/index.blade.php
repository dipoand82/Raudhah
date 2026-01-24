<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Manajemen User') }}</h2>
    </x-slot>

    <div class="py-12" x-data="{ activeTab: 'siswa' }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- ALERT ERROR --}}
            @if ($errors->any())
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative shadow-sm">
                    <strong class="font-bold flex items-center gap-2">Ada Kesalahan!</strong>
                    <ul class="mt-2 list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                    </ul>
                </div>
            @endif

            {{-- ALERT SUCCESS --}}
            @if(session('success'))
                <div x-data="{ show: true }" x-show="show" class="mb-4 flex items-center justify-between bg-green-100 border border-green-200 text-green-700 px-4 py-3 rounded shadow-sm">
                    <div class="flex items-center gap-2"><span>{{ session('success') }}</span></div>
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
                        
                        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-6 gap-4">
                            <form method="GET" action="{{ route('admin.manajemen-user.index') }}" class="w-full lg:w-3/4 flex flex-col md:flex-row gap-2">
                                <input type="hidden" name="tab" value="siswa"> 
                                <input type="hidden" name="per_page" value="{{ request('per_page', 10) }}">
                                <div class="relative w-full md:w-1/3">
                                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Nama / NISN..." class="w-full rounded-full border-gray-300 pl-5 pr-10 py-2 focus:border-[#3B3E42] focus:ring-[#3B3E42] shadow-sm text-sm">
                                    <button type="submit" class="absolute right-3 top-2.5 text-gray-400 hover:text-[#3B3E42]">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                                    </button>
                                </div>
                                <div class="w-full md:w-1/4">
                                    <select name="kelas_id" onchange="this.form.submit()" class="w-full rounded-full border-gray-300 py-2 shadow-sm focus:border-[#3B3E42] focus:ring-[#3B3E42] text-sm">
                                        <option value="">-- Semua Kelas --</option>
                                        @foreach($kelas as $k) <option value="{{ $k->id }}" {{ request('kelas_id') == $k->id ? 'selected' : '' }}>Kelas {{ $k->tingkat }} {{ $k->nama_kelas }}</option> @endforeach
                                    </select>
                                </div>
                                <div class="w-full md:w-1/4">
                                    <select name="status" onchange="this.form.submit()" class="w-full rounded-full border-gray-300 py-2 shadow-sm focus:border-[#3B3E42] focus:ring-[#3B3E42] text-sm">
                                        <option value="">-- Status --</option>
                                        @foreach(['Aktif', 'Lulus', 'Pindah', 'Keluar'] as $st) <option value="{{ $st }}" {{ request('status') == $st ? 'selected' : '' }}>{{ $st }}</option> @endforeach
                                    </select>
                                </div>
                            </form>

                            <div class="flex flex-wrap sm:flex-nowrap items-center gap-3 w-full lg:w-auto justify-end">
                                <button x-on:click="$dispatch('open-modal', 'import-siswa')" class="inline-flex items-center justify-center gap-2 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition whitespace-nowrap">Import Akun</button>
                                <button x-on:click="$dispatch('open-modal', 'add-siswa')" class="inline-flex items-center justify-center gap-2 bg-[#1072B8] hover:bg-[#0d5a91] text-white px-4 py-2 rounded-lg text-sm font-semibold transition whitespace-nowrap">Tambah Siswa</button>
                            </div>
                        </div>

                        {{-- Tombol Bulk Delete (Trigger JS) --}}
                        <div id="bulkDeleteContainer" class="hidden mb-3 bg-red-50 p-2 rounded flex justify-between items-center border border-red-200">
                            <span class="text-red-700 text-sm font-semibold ml-2"><span id="selectedCount">0</span> Siswa dipilih</span>
                            <button type="button" onclick="submitBulkDelete()" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-xs font-bold transition">Hapus Terpilih</button>
                        </div>

                        {{-- Form Bulk Delete Terpisah (Hidden) --}}
                        <form id="bulkDeleteForm" action="{{ route('admin.siswas.bulk_delete') }}" method="POST" class="hidden">
                            @csrf @method('DELETE')
                            <div id="idsContainer"></div>
                        </form>

                        <div class="overflow-x-auto bg-white rounded-xl shadow border border-gray-200">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-[#3B3E42]"> 
                                    <tr>
                                        <th class="px-4 py-4 w-10 text-center"><input type="checkbox" id="selectAll" class="rounded border-gray-300 text-indigo-600 cursor-pointer"></th>
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
                                            <td class="px-4 py-4 text-center">
                                                @if($u->dataSiswa) <input type="checkbox" value="{{ $u->dataSiswa->id }}" class="select-item rounded border-gray-300 text-indigo-600 cursor-pointer"> @else - @endif
                                            </td>
                                            <td class="px-4 py-4 text-center text-sm font-medium text-gray-500">{{ $userSiswa->firstItem() + $loop->index }}</td>
                                            <td class="px-6 py-4">
                                                <div class="text-sm font-bold text-gray-900">{{ $u->name }}</div>
                                                <div class="text-xs text-gray-400">{{ $u->email }}</div>
                                            </td>
                                            <td class="px-6 py-4 text-sm font-mono text-gray-600">{{ $u->dataSiswa ? $u->dataSiswa->nisn : 'Unlinked' }}</td>
                                            <td class="px-6 py-4">
                                                @if($u->dataSiswa && $u->dataSiswa->kelas)
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                                        {{ $u->dataSiswa->kelas->tingkat }} {{ $u->dataSiswa->kelas->nama_kelas }}
                                                    </span>
                                                @else <span class="text-xs text-red-400 font-semibold">No Class</span> @endif
                                            </td>
                                            <td class="px-6 py-4 text-center">
                                                @if($u->dataSiswa)
                                                    <span class="px-3 py-1 inline-flex text-[10px] font-bold border rounded-full bg-green-100 text-green-800 border-green-200">{{ strtoupper($u->dataSiswa->status) }}</span>
                                                @else - @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium flex justify-end items-center gap-3">
                                                @if($u->dataSiswa)
                                                    <form action="{{ route('admin.manajemen-user.siswa.reset', $u->dataSiswa->id) }}" method="POST" class="inline m-0 p-0">
                                                        @csrf
                                                        <button type="submit" onclick="return confirm('Reset password {{ $u->name }}?')" class="text-yellow-600 hover:text-yellow-700 p-1">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" /></svg>
                                                        </button>
                                                    </form>
                                                    <a href="{{ route('admin.siswas.edit', $u->dataSiswa->id) }}" class="text-indigo-600 font-bold px-2">Edit</a>
                                                    <button type="button" onclick="confirmDelete('{{ route('admin.siswas.destroy', $u->dataSiswa->id) }}', '{{ $u->name }}')" class="text-red-600 font-bold px-2">Hapus</button>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div> 

                    {{-- ================= KONTEN TAB GURU ================= --}}
                    <div x-show="activeTab === 'guru'" x-transition>
                        <div class="flex justify-end gap-3 mb-4">
                            <x-primary-button x-on:click="$dispatch('open-modal', 'add-guru')" class="flex items-center gap-2">
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
                                            <button type="button" onclick="confirmDelete('{{ route('admin.manajemen-user.destroy', $g->id) }}', '{{ $g->name }}')" class="text-red-600 font-bold text-sm">Hapus</button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- ================= KONTEN TAB PASSWORD ================= --}}
                    {{-- <div x-show="activeTab === 'password'" x-transition>
                        <div class="max-w-md mx-auto bg-white p-8 rounded-lg shadow-sm border border-gray-200 mt-6">
                            <h3 class="font-bold text-lg mb-6 text-[#3B3E42] flex items-center gap-2 border-b pb-4">Ubah Password Admin</h3>
                            <form action="{{ route('admin.manajemen-user.password.update') }}" method="POST">
                                @csrf @method('PUT')
                                <div class="mb-4"><x-input-label :value="__('Password Saat Ini')" /><x-text-input type="password" name="current_password" class="w-full mt-1 focus:border-[#3B3E42]" required /></div>
                                <div class="mb-4"><x-input-label :value="__('Password Baru')" /><x-text-input type="password" name="password" class="w-full mt-1 focus:border-[#3B3E42]" required /></div>
                                <div class="mb-6"><x-input-label :value="__('Konfirmasi Password Baru')" /><x-text-input type="password" name="password_confirmation" class="w-full mt-1 focus:border-[#3B3E42]" required /></div>
                                <x-primary-button class="w-full py-3 justify-center">Simpan Password Baru</x-primary-button>
                            </form>
                        </div>
                    </div> --}}
                    <div x-show="activeTab === 'password'" x-transition>
                        <div class="max-w-md mx-auto bg-white p-8 rounded-lg shadow-sm border border-gray-200 mt-6">
                            <h3 class="font-bold text-lg mb-6 text-[#3B3E42] flex items-center gap-2 border-b pb-4">Ubah Password Admin</h3>
                            
                            {{-- 1. Tambahkan autocomplete="off" di level Form --}}
                            <form action="{{ route('admin.manajemen-user.password.update') }}" method="POST" autocomplete="off">
                                @csrf @method('PUT')
                                
                                {{-- 2. Trik Dummy Input: Browser akan tertipu dan mengisi otomatis ke sini, bukan ke field asli --}}
                                <input type="text" style="display:none;" aria-hidden="true">
                                <input type="password" style="display:none;" aria-hidden="true">

                                {{-- Password Saat Ini --}}
                                <div class="mb-4">
                                    <x-input-label :value="__('Password Saat Ini')" />
                                    <x-text-input type="password" 
                                                name="current_password" 
                                                class="w-full mt-1 focus:border-[#3B3E42]" 
                                                required 
                                                readonly {{-- 3. Lapisan Terakhir: Readonly saat load, berubah jadi writable saat diklik --}}
                                                onfocus="this.removeAttribute('readonly');"
                                                autocomplete="one-time-code" /> {{-- Menggunakan "one-time-code" seringkali lebih ampuh mematikan autosuggest --}}
                                </div>

                                {{-- Password Baru --}}
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

                                {{-- Konfirmasi Password Baru --}}
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

    {{-- MODALS --}}
    <x-modal name="add-guru" focusable>
        <form method="POST" action="{{ route('admin.manajemen-user.guru.store') }}" class="p-6">
            @csrf
            <h2 class="text-lg font-bold text-gray-900 mb-4 pb-2 border-b">Tambah Akun Guru</h2>
            <div class="mb-3"><x-input-label value="Nama Guru" /><x-text-input name="name" class="w-full" required /></div>
            <div class="mb-3"><x-input-label value="Email" /><x-text-input name="email" type="email" class="w-full" required /></div>
            <div class="mt-6 flex justify-end gap-3"><x-secondary-button x-on:click="$dispatch('close')">Batal</x-secondary-button><x-primary-button class="bg-[#3B3E42] hover:bg-gray-700">Simpan</x-primary-button></div>
        </form>
    </x-modal>

    {{-- FORM HAPUS TERSEMBUNYI --}}
    <form id="mainDeleteForm" method="POST" class="hidden">@csrf @method('DELETE')</form>

    <script>
        function confirmDelete(url, name) {
            const form = document.getElementById('mainDeleteForm');
            if (confirm('Yakin ingin menghapus ' + name + '?')) {
                form.action = url;
                form.submit();
            }
        }

        const selectAll = document.getElementById('selectAll');
        const items = document.querySelectorAll('.select-item');
        const bulkDeleteContainer = document.getElementById('bulkDeleteContainer');
        const selectedCountSpan = document.getElementById('selectedCount');

        function toggleBulkDeleteButton() {
            const checkedCount = document.querySelectorAll('.select-item:checked').length;
            if (selectedCountSpan) selectedCountSpan.innerText = checkedCount;
            checkedCount > 0 ? bulkDeleteContainer.classList.remove('hidden') : bulkDeleteContainer.classList.add('hidden');
        }

        function submitBulkDelete() {
            if (confirm('Hapus data terpilih?')) {
                const form = document.getElementById('bulkDeleteForm');
                const container = document.getElementById('idsContainer');
                container.innerHTML = '';
                document.querySelectorAll('.select-item:checked').forEach(item => {
                    let input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'ids[]';
                    input.value = item.value;
                    container.appendChild(input);
                });
                form.submit();
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
                if (!this.checked && selectAll) selectAll.checked = false;
                toggleBulkDeleteButton();
            });
        });
    </script>
</x-app-layout>