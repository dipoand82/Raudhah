<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Manajemen User') }}</h2>
    </x-slot>

    <div class="py-12" x-data="{ activeTab: '{{ session('active_tab', request('tab', 'siswa')) }}' }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6">

            @if (session('success'))
                <x-alert-success>
                    {{ session('success') }}
                </x-alert-success>
            @endif
            @if (session('error'))
                <x-alert-danger>
                    {{ session('error') }}
                </x-alert-danger>
            @endif

            @if ($errors->any())
                @foreach ($errors->all() as $error)
                    <x-alert-danger timeout="8000">
                        {{ $error }}
                    </x-alert-danger>
                @endforeach
            @endif

            <div class="bg-gray-100 p-6 rounded-lg shadow-sm border border-gray-200">

                <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200 mb-6">
                    <div class="flex space-x-6">
                        <button @click="activeTab = 'siswa'"
                            :class="{ 'border-b-2 border-[#1072B8] text-[#1072B8] font-bold': activeTab === 'siswa' }"
                            class="pb-2 text-[#1072B8] hover:text-[#0b5A91] transition duration-150 font-medium">Siswa</button>
                        <button @click="activeTab = 'guru'"
                            :class="{ 'border-b-2 border-[#1072B8] text-[#1072B8] font-bold': activeTab === 'guru' }"
                            class="pb-2 text-[#1072B8] hover:text-[#0b3149] transition duration-150 font-medium">Guru</button>
                        <button @click="activeTab = 'panduan'"
                            :class="{ 'border-b-2 border-[#1072B8] text-[#1072B8] font-bold': activeTab === 'panduan' }"
                            class="pb-2 text-[#1072B8] hover:text-[#0b3149] transition duration-150 font-medium">Panduan</button>
                    </div>
                </div>

                <div class="min-h-0">


                    <div x-show="activeTab === 'siswa'" x-transition>

                        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-6 gap-4">
                            <form method="GET" action="{{ route('admin.manajemen-user.index') }}"
                                class="w-full lg:w-3/4 flex flex-col md:flex-row gap-2" x-data="{
                                    status: '{{ request('status', '') }}',
                                    kelas: '{{ request('kelas_id', '') }}'
                                }">
                                <input type="hidden" name="tab" value="siswa">
                                <input type="hidden" name="per_page" value="{{ request('per_page', 10) }}">

                                <div class="relative w-full md:w-1/3">
                                    <input type="text" name="search" value="{{ request('search') }}"
                                        placeholder="Cari Nama / NISN..."
                                        class="w-full rounded-full border-gray-300 pl-5 pr-10 py-2 focus:border-[#3B3E42] focus:ring-[#3B3E42] shadow-sm text-sm">
                                    <button type="submit"
                                        class="absolute right-3 top-2.5 text-gray-400 hover:text-[#3B3E42]">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                        </svg>
                                    </button>
                                </div>

                                <div class="w-full md:w-1/4">
                                    <select name="kelas_id" x-model="kelas" onchange="this.form.submit()"
                                        @change="if(kelas !== '') { status = 'Aktif' }; $nextTick(() => $el.form.submit())"
                                        :disabled="status !== '' && status !== 'Aktif'"
                                        :class="status !== '' && status !== 'Aktif' ?
                                            'bg-gray-100 cursor-not-allowed opacity-60' : ''"
                                        class="w-full rounded-full border-gray-300 py-2 pl-4 pr-8 shadow-sm focus:border-[#3B3E42] focus:ring-[#3B3E42] cursor-pointer text-gray-700 text-sm">
                                        <option value="">-- Semua Kelas --</option>
                                        @foreach ($kelas as $k)
                                            <option value="{{ $k->id }}"
                                                {{ request('kelas_id') == $k->id ? 'selected' : '' }}>
                                                Kelas {{ $k->tingkat }} {{ $k->nama_kelas }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="w-full md:w-1/4">
                                    @php
                                        $selectedStatus = request('status');
                                        $filterStatusClass = match ($selectedStatus) {
                                            'Aktif' => 'bg-green-100 text-green-800 border-green-200',
                                            'Cuti' => 'bg-gray-100 text-gray-800 border-gray-200',
                                            'Lulus' => 'bg-blue-100 text-blue-800 border-blue-200',
                                            'Pindah' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                            'Keluar' => 'bg-red-100 text-red-800 border-red-200',
                                            default => 'bg-white text-gray-700 border-gray-300',
                                        };
                                    @endphp
                                    <select name="status" x-model="status" onchange="this.form.submit()"
                                        @change="if(status === '' || status !== 'Aktif') { kelas = '' }; $nextTick(() => $el.form.submit())"
                                        class="w-full rounded-full py-2 pl-4 pr-8 shadow-sm focus:border-[#3B3E42] focus:ring-[#3B3E42] cursor-pointer text-sm border">
                                        <option value="" class="bg-white text-gray-700">-- Semua Status --
                                        </option>
                                        @foreach (['Aktif', 'Cuti', 'Lulus', 'Pindah', 'Keluar'] as $st)
                                            <option value="{{ $st }}"
                                                {{ $selectedStatus == $st ? 'selected' : '' }}
                                                class="bg-white text-gray-700">
                                                {{ $st }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </form>

                            <div class="flex flex-col sm:flex-row items-center gap-3 mb-2 w-full lg:w-auto justify-end">
                                <button x-data="" x-on:click="$dispatch('open-modal', 'import-siswa')"
                                    class="inline-flex w-full items-center justify-center gap-2 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-semibold text-sm transition shadow-sm whitespace-nowrap">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                    </svg>
                                    Import Akun Siswa
                                </button>
                                <button type="button" x-data=""
                                    x-on:click="$dispatch('open-modal', 'add-siswa')"
                                    class="inline-flex w-full items-center justify-center gap-2 bg-[#1072B8] hover:bg-[#0d5a91] text-white px-4 py-2 rounded-lg font-semibold text-sm transition shadow-sm whitespace-nowrap">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    Tambah Siswa
                                </button>
                            </div>
                        </div>

                        <x-modal name="import-siswa" focusable>
                            <form method="POST" action="{{ route('admin.manajemen-user.siswa.import') }}"
                                enctype="multipart/form-data" class="p-6" x-data="{ fileName: '', isLoading: false }"
                                @submit="isLoading = true; $dispatch('loading')">
                                @csrf
                                <div class="flex justify-between items-start mb-5 border-b pb-3">
                                    <h2 class="text-xl font-bold text-gray-900">Import Data Siswa</h2>
                                    <button type="button" x-on:click="$dispatch('close')"
                                        class="text-gray-400 hover:text-gray-600">
                                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>

                                <div class="bg-red-50 border-2 border-red-500 p-4 rounded-lg mb-4">
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <svg class="h-5 w-5 text-red-500" viewBox="0 0 20 20"
                                                fill="currentColor">
                                                <path fill-rule="evenodd"
                                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                        <div class="ml-3">
                                            <h3 class="text-sm font-bold text-red-800">Syarat Wajib Import:</h3>
                                            <ul class="mt-1 list-disc list-inside text-xs text-red-700 font-medium">
                                                <li>Pastikan <strong>Tahun Ajaran Aktif</strong> sudah diset di sistem.
                                                </li>
                                                <li>Pastikan <strong>Data Kelas Tersedia</strong> di sistem.</li>
                                                <li>Pastikan <strong>Kesesuaian Data</strong> di Excel sebelum melakukan
                                                    import.</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>


                                <div
                                    class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4 flex justify-between items-center">
                                    <div>
                                        <h3 class="text-sm font-bold text-blue-800 mb-1">Langkah 1: Siapkan File</h3>
                                        <p class="text-xs text-blue-600">Gunakan template resmi.</p>
                                    </div>
                                    <a href="{{ route('admin.manajemen-user.siswa.template') }}"
                                        class="bg-white text-blue-700 hover:bg-blue-100 border border-blue-300 px-3 py-2 rounded text-sm font-bold shadow-sm transition flex items-center gap-2"><svg
                                            class="w-4 h-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4">
                                            </path>
                                        </svg>Template</a>
                                </div>
                                <div class="mb-6" x-data="{ fileName: '', fileInput: null }">
                                    <h3 class="text-sm font-bold text-gray-700 mb-2">Langkah 2: Upload File</h3>

                                    <div class="relative border-2 border-dashed rounded-lg p-8 transition text-center group"
                                        :class="fileName
                                            ?
                                            'border-green-500 bg-green-50' :
                                            'border-gray-300 bg-gray-50 hover:border-[#3B3E42]'">

                                        <input type="file" name="file"
                                            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                                            required accept=".xlsx, .xls, .csv"
                                            @change="fileName = $event.target.files[0] ? $event.target.files[0].name : ''"
                                            x-ref="fileInput">

                                        <template x-if="fileName">
                                            <button type="button" @click="fileName = ''; $refs.fileInput.value = ''"
                                                class="absolute top-2 right-2 z-20 p-1 rounded-full bg-white shadow-sm border border-green-200 text-green-600 hover:bg-green-100 transition-colors">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4"
                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </template>

                                        <div
                                            class="pointer-events-none space-y-2 flex flex-col items-center justify-center">
                                            <div class="mb-2">
                                                <svg class="w-12 h-12 transition-colors duration-300"
                                                    :class="fileName ? 'text-green-500' : 'text-gray-400'"
                                                    fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10">
                                                    </path>
                                                </svg>
                                            </div>

                                            <p class="text-sm font-medium text-center transition-colors duration-300"
                                                :class="fileName ? 'text-green-700 font-bold' : 'text-gray-600'"
                                                x-text="fileName ? 'File: ' + fileName : 'Klik untuk pilih file'">
                                            </p>

                                            <template x-if="fileName">
                                                <p class="text-xs text-green-600">File siap diunggah!</p>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex justify-end gap-3 pt-4 border-t">
                                    <x-secondary-button x-on:click="$dispatch('close')"
                                        type="button">Batal</x-secondary-button>
                                    <x-primary-button ::disabled="isLoading"><span
                                            x-text="isLoading ? 'Memproses...' : 'Proses Import'"></span></x-primary-button>
                                </div>
                            </form>
                        </x-modal>

                        @if (session()->has('import_errors'))
                            <div x-data="{ open: true }" x-show="open"
                                class="relative mb-4 p-4 bg-red-50  border-red-500 rounded shadow-sm border-2 border-red-500 p-4 rounded-lg mb-4">
                                <p class="text-red-800 font-bold mb-2 ">Data Tidak Lengkap / Salah Format:</p>
                                <ul class="list-disc list-inside text-xs text-red-700">
                                    @foreach (session()->get('import_errors') as $failure)
                                        <li>Baris {{ $failure->row() }}: {{ $failure->errors()[0] }}</li>
                                    @endforeach
                                </ul>
                                <button @click="open = false"
                                    class="absolute top-2 right-2 text-red-400 hover:text-red-600 transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        @endif

                        @if (session()->has('fallback_info'))
                            <div x-data="{ open: true }" x-show="open"
                                class="relative mb-4 p-4 bg-blue-50 border-blue-500 rounded shadow-sm border-2 border-blue-500 p-4 rounded-lg mb-4">
                                <p class="text-blue-800 font-bold mb-2">Informasi Penyesuaian Kelas:</p>
                                <ul class="list-disc list-inside text-xs text-blue-700">
                                    @foreach (session()->get('fallback_info') as $info)
                                        <li>Siswa <strong>{{ $info['nama'] }}</strong>: Kelas "{{ $info['input'] }}"
                                            tidak terdaftar, sistem tetap menggunakan
                                            <strong>{{ $info['tetap'] }}</strong>.
                                        </li>
                                    @endforeach
                                </ul>
                                <button @click="open = false"
                                    class="absolute top-2 right-2 text-blue-400 hover:text-blue-600 transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        @endif

                        <form id="bulkDeleteForm" action="{{ route('admin.siswas.bulk_delete') }}" method="POST">
                            @csrf @method('DELETE')
                        </form>

                        <form id="bulkResetForm" action="{{ route('admin.manajemen-user.siswa.bulk_reset') }}"
                            method="POST">
                            @csrf @method('PATCH')
                        </form>

                        <div id="bulkDeleteContainer"
                            class="hidden mb-3 bg-indigo-50 p-2 rounded flex justify-between items-center border border-indigo-200">
                            <span class="text-indigo-700 text-sm font-semibold ml-2">
                                <span id="selectedCount">0</span> Siswa dipilih
                            </span>

                            <div class="flex gap-2">
                                <button type="button" x-data=""
                                    x-on:click="$dispatch('open-modal', 'bulk-reset-confirm')"
                                    class="bg-amber-500 hover:bg-amber-600 text-white px-3 py-1 rounded text-xs font-bold transition shadow-sm">
                                    Reset Password
                                </button>

                                <x-danger-button type="button" x-data=""
                                    x-on:click="$dispatch('open-modal', 'bulk-delete-confirm')" class="text-xs">
                                    Hapus Terpilih
                                </x-danger-button>
                            </div>
                        </div>

                        <div class="overflow-x-auto bg-white rounded-xl shadow border border-gray-200">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-[#3B3E42]">
                                    <tr>
                                        <th class="px-4 py-4 w-10 text-center">
                                            <input type="checkbox" id="selectAll"
                                                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 cursor-pointer">
                                        </th>
                                        <th class="px-4 py-4 text-center text-xs font-bold text-white uppercase w-12">
                                            No</th>
                                        <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase">Nama
                                            Siswa</th>
                                        <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase">NISN
                                        </th>
                                        <th class="px-6 py-3 text-center text-xs font-bold text-white uppercase">Role
                                        </th>
                                        <th class="px-6 py-4 text-center text-xs font-bold text-white uppercase">Kelas
                                        </th>
                                        <th class="px-6 py-4 text-center text-xs font-bold text-white uppercase">Status
                                        </th>
                                        <th class="px-6 py-4 text-center text-xs font-bold text-white uppercase">Aksi
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-100">
                                    @foreach ($userSiswa as $u)
                                        <tr class="hover:bg-indigo-50/50 transition even:bg-gray-50">
                                            <td class="px-4 py-4 text-center">
                                                @if ($u->dataSiswa)
                                                    <input type="checkbox" name="ids[]" form="bulkDeleteForm"
                                                        value="{{ $u->dataSiswa->id }}"
                                                        class="select-item rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 cursor-pointer">
                                                @else
                                                    <span class="text-gray-300">-</span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-4 text-center text-sm font-medium text-black-300">
                                                {{ $userSiswa->firstItem() + $loop->index }}</td>
                                            <td class="px-6 py-4">
                                                <div class="text-sm font-bold text-gray-900">{{ $u->name }}</div>
                                                <div class="text-xs text-gray-400">{{ $u->email }}</div>
                                            </td>
                                            </td>
                                            <td class="px-6 py-4 text-sm font-mono text-gray-600">
                                                @if ($u->dataSiswa && $u->dataSiswa->nisn)
                                                    {{ $u->dataSiswa->nisn }}
                                                @else
                                                    <span class="text-red-600 font-semibold italic text-center ">
                                                        NISN Ganda, Cek Excel & Import Ulang Data Ini!
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="text-center px-6 py-4 whitespace-nowrap">
                                                <span
                                                    class="text-center px-2.5 py-1 inline-flex text-sm font-semibold rounded-full bg-blue-50 text-blue-700 border border-blue-100 capitalize">
                                                    {{ $u->role ?? 'Siswa' }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-center">
                                                @if ($u->dataSiswa && $u->dataSiswa->kelas)
                                                    <span
                                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 whitespace-nowrap">
                                                        {{ $u->dataSiswa->kelas->tingkat }}
                                                        {{ $u->dataSiswa->kelas->nama_kelas }}
                                                    </span>
                                                @else
                                                    <span
                                                        class="text-xs text-red-400 font-semibold whitespace-nowrap">No
                                                        Class</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 text-center">
                                                @if ($u->dataSiswa && $u->dataSiswa->status)
                                                    @php
                                                        $statusSiswa = $u->dataSiswa->status;
                                                        $badgeClass = match ($statusSiswa) {
                                                            'Aktif' => 'bg-green-100 text-green-800 border-green-200',
                                                            'Cuti' => 'bg-gray-100 text-gray-800 border-gray-200',
                                                            'Lulus' => 'bg-blue-100 text-blue-800 border-blue-200',
                                                            'Pindah'
                                                                => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                                            'Keluar' => 'bg-red-100 text-red-800 border-red-200',
                                                            default => 'bg-gray-100 text-gray-800 border-gray-200',
                                                        };
                                                    @endphp
                                                    <span
                                                        class="px-3 py-1 inline-flex text-[10px] font-bold border rounded-full {{ $badgeClass }}">
                                                        {{ strtoupper($statusSiswa) }}
                                                    </span>
                                                @else
                                                    <span class="text-gray-300">-</span>
                                                @endif
                                            </td>
                                            <td
                                                class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium flex justify-end items-center gap-3">
                                                @if ($u->dataSiswa)
                                                    <x-modal-delete-global trigger="reset-pw-{{ $u->id }}"
                                                        :action="route(
                                                            'admin.manajemen-user.siswa.reset',
                                                            $u->dataSiswa->id,
                                                        )" :message="$u->name" title="Reset Password"
                                                        submitText="Ya, Reset Sekarang" type="warning">
                                                        <x-slot name="buttonText"
                                                            class="!p-1 !bg-transparent !shadow-none !border-none text-yellow-600 hover:text-yellow-700 hover:!bg-yellow-50 transition">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5"
                                                                fill="none" viewBox="0 0 24 24"
                                                                stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                                                            </svg>
                                                        </x-slot>

                                                        <div class=" border-yellow-400 text-yellow-800 text-xs">
                                                            Password akan dikembalikan ke <strong>NISN
                                                                Siswa</strong>.<br>
                                                        </div>
                                                    </x-modal-delete-global>

                                                    <button type="button" x-data
                                                        x-on:click="$dispatch('open-modal', 'edit-siswa-{{ $u->id }}')"
                                                        class="text-indigo-600 hover:text-indigo-900 font-semibold">Edit</button>

                                                    <x-modal name="edit-siswa-{{ $u->id }}" focusable>
                                                        <form method="POST"
                                                            action="{{ route('admin.siswas.update', $u->dataSiswa->id) }}"
                                                            class="p-6 text-left">
                                                            @csrf @method('PUT')
                                                            <h2
                                                                class="text-lg font-bold mb-4 border-b pb-2 text-[#1072B8]">
                                                                Edit Data Siswa: {{ $u->name }}</h2>
                                                            <x-siswa.edit-form :siswa="$u->dataSiswa" :kelas="$kelas"
                                                                :tahunAjaran="$tahunAjaranList" />
                                                            <div class="mt-6 flex justify-end gap-3 border-t pt-4">
                                                                <x-secondary-button
                                                                    x-on:click="$dispatch('close')">Batal</x-secondary-button>
                                                                <x-primary-button>Simpan Perubahan</x-primary-button>
                                                            </div>
                                                        </form>
                                                    </x-modal>

                                                    <x-siswa.delete-modal
                                                        trigger="delete-siswa-{{ $u->dataSiswa->id }}"
                                                        action="{{ route('admin.manajemen-user.destroy', $u->id) }}"
                                                        message="{{ $u->name }}" />
                                                @else
                                                    <button x-data
                                                        x-on:click="$dispatch('open-modal', 'delete-user-{{ $u->id }}')"
                                                        class="text-red-600 hover:text-red-900 font-semibold">Hapus
                                                        User</button>
                                                    <x-modal name="delete-user-{{ $u->id }}" focusable>
                                                        <form method="post"
                                                            action="{{ route('admin.manajemen-user.destroy', $u->id) }}"
                                                            class="p-6">
                                                            @csrf @method('delete')
                                                            <h2 class="text-lg font-bold">Hapus User?</h2>
                                                            <p class="mt-2 text-sm text-gray-600">Yakin hapus
                                                                <strong>{{ $u->name }}</strong>?
                                                            </p>
                                                            <div class="mt-6 flex justify-end gap-3">
                                                                <x-secondary-button
                                                                    x-on:click="$dispatch('close')">Batal</x-secondary-button>
                                                                <x-danger-button>Hapus</x-danger-button>
                                                            </div>
                                                        </form>
                                                    </x-modal>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <x-modal name="bulk-delete-confirm" focusable>
                            <div class="p-6">
                                <h2 class="text-lg font-bold text-gray-900">Konfirmasi Hapus Massal</h2>
                                <p class="mt-1 text-sm text-gray-600">
                                    Apakah Anda yakin ingin menghapus <strong>semua data siswa yang dipilih</strong>?
                                    <br>
                                    <span class="text-red-500 font-bold text-xs">Peringatan: Data akun user, dan
                                        tagihan terkait juga akan dihapus permanen.</span>
                                </p>
                                <div class="mt-6 flex justify-end gap-3">
                                    <x-secondary-button x-on:click="$dispatch('close')">Batal</x-secondary-button>
                                    <x-danger-button type="submit" form="bulkDeleteForm">
                                        Ya, Hapus Semua
                                    </x-danger-button>
                                </div>
                            </div>
                        </x-modal>

                        <x-modal name="bulk-reset-confirm" focusable>
                            <div class="p-6">
                                <h2 class="text-lg font-bold text-gray-900">Konfirmasi Reset Password Massal</h2>
                                <p class="mt-1 text-sm text-gray-600">
                                    Apakah Anda yakin ingin mereset password <strong>semua siswa yang dipilih</strong>?
                                    <br>
                                    <span class="text-amber-600 font-bold text-xs">Password akan dikembalikan ke NISN
                                        masing-masing.</span>
                                </p>
                                <div class="mt-6 flex justify-end gap-3">
                                    <x-secondary-button x-on:click="$dispatch('close')">Batal</x-secondary-button>
                                    <x-primary-button type="submit" form="bulkResetForm"
                                        class="bg-amber-500 hover:bg-amber-600">
                                        Ya, Reset Password
                                    </x-primary-button>
                                </div>
                            </div>
                        </x-modal>

                        <div class="mt-6 flex flex-col sm:flex-row justify-between items-center gap-4">
                            <form method="GET" action="{{ route('admin.manajemen-user.index') }}">
                                <input type="hidden" name="tab" value="siswa">
                                <input type="hidden" name="search" value="{{ request('search') }}">
                                <input type="hidden" name="kelas_id" value="{{ request('kelas_id') }}">
                                <input type="hidden" name="status" value="{{ request('status') }}">

                                <div class="flex items-center gap-2">
                                    <span class="text-sm text-gray-500">Total Siswa:</span>
                                    <div class="text-sm text-gray-600">
                                        <strong>{{ $totalSiswa }}</strong>, Show:
                                    </div>
                                    <select name="per_page" onchange="this.form.submit()"
                                        class="text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-1 pl-2 pr-8">
                                        <option value="30" {{ request('per_page') == 30 ? 'selected' : '' }}>30
                                        </option>
                                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50
                                        </option>
                                        <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100
                                        </option>
                                    </select>
                                </div>
                            </form>

                            <div>
                                {{ $userSiswa->appends(['search' => request('search'), 'kelas_id' => request('kelas_id'), 'status' => request('status'), 'per_page' => request('per_page'), 'tab' => 'siswa'])->links() }}
                            </div>
                        </div>

                    </div>

                    <div x-show="activeTab === 'guru'" x-transition>
                        <div class="flex flex-col lg:flex-row justify-between items-center mb-6 gap-4">
                            <form method="GET" action="{{ route('admin.manajemen-user.index') }}"
                                class="w-full lg:w-1/2 flex gap-2">
                                <input type="hidden" name="tab" value="guru">
                                <input type="hidden" name="per_page_guru"
                                    value="{{ request('per_page_guru', 30) }}">
                                <div class="relative w-full">
                                    <input type="text" name="search_guru" value="{{ request('search_guru') }}"
                                        placeholder="Cari Nama / Email Guru..."
                                        class="w-full rounded-full border-gray-300 pl-5 pr-10 py-2 focus:border-[#3B3E42] shadow-sm text-sm">
                                    <button type="submit"
                                        class="absolute right-3 top-2.5 text-gray-400 hover:text-[#3B3E42]">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                        </svg>
                                    </button>
                                </div>
                            </form>

                            <div
                                class="flex flex-col sm:flex-row items-center gap-3 mb-2 w-full lg:w-auto justify-end">
                                <button x-data="" x-on:click="$dispatch('open-modal', 'import-guru')"
                                    class="inline-flex w-full items-center justify-center gap-2 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-semibold text-sm transition shadow-sm whitespace-nowrap">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                    </svg>
                                    Import Akun Guru
                                </button>

                                <button type="button" x-data=""
                                    x-on:click="$dispatch('open-modal', 'add-guru')"
                                    class="inline-flex w-full items-center justify-center gap-2 bg-[#1072B8] hover:bg-[#0d5a91] text-white px-4 py-2 rounded-lg font-semibold text-sm transition shadow-sm whitespace-nowrap">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    Tambah Guru
                                </button>
                            </div>
                        </div>

                        <div class="overflow-x-auto bg-white rounded-xl shadow border border-gray-200">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-[#3B3E42]">
                                    <tr>
                                        <th class="px-4 py-4 text-center text-xs font-bold text-white uppercase w-12">
                                            No</th>
                                        <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase">Nama
                                        </th>
                                        <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase">Email
                                        </th>
                                        <th class="px-6 py-4 text-center text-xs font-bold text-white uppercase">Role
                                        </th>
                                        <th class="px-6 py-4 text-center text-xs font-bold text-white uppercase">Aksi
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-100">
                                    @foreach ($userGuru as $g)
                                        <tr class="hover:bg-indigo-50/50 transition even:bg-gray-50">
                                            <td class="px-4 py-4 text-center text-sm font-medium text-gray-500">
                                                {{ $userGuru->firstItem() + $loop->index }}
                                            </td>
                                            <td class="px-6 py-4 font-medium text-gray-900">{{ $g->name }}</td>
                                            <td class="px-6 py-4 text-gray-600">{{ $g->email }}</td>
                                            <td class="px-6 py-4 text-center">
                                                <span
                                                    class="bg-green-100 text-green-800 text-xs px-2.5 py-1 rounded-full font-semibold border border-green-200">Guru</span>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="flex justify-center items-center gap-3">
                                                    <button type="button" x-data
                                                        x-on:click="$dispatch('open-modal', 'edit-guru-{{ $g->id }}')"
                                                        class="text-indigo-600 hover:text-indigo-900 font-bold text-sm">Edit</button>

                                                    <button type="button" x-data
                                                        x-on:click="$dispatch('open-modal', 'confirm-delete-guru-{{ $g->id }}')"
                                                        class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-xs font-bold transition">
                                                        Hapus
                                                    </button>

                                                    <x-modal name="confirm-delete-guru-{{ $g->id }}" focusable>
                                                        <form method="post"
                                                            action="{{ route('admin.manajemen-user.destroy', $g->id) }}"
                                                            class="p-6 text-left">
                                                            @csrf @method('delete')
                                                            <h2 class="text-lg font-bold text-gray-900">Hapus Akun
                                                                Guru?</h2>
                                                            <p class="mt-2 text-sm text-gray-600">Yakin ingin menghapus
                                                                <strong>{{ $g->name }}</strong>? Tindakan ini
                                                                tidak bisa dibatalkan.
                                                            </p>
                                                            <div class="mt-6 flex justify-end gap-3">
                                                                <x-secondary-button
                                                                    x-on:click="$dispatch('close')">Batal</x-secondary-button>
                                                                <x-danger-button>Ya, Hapus Akun</x-danger-button>
                                                            </div>
                                                        </form>
                                                    </x-modal>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-6 flex flex-col sm:flex-row justify-between items-center gap-4">
                            <form method="GET" action="{{ route('admin.manajemen-user.index') }}">
                                <input type="hidden" name="tab" value="guru">
                                <input type="hidden" name="search_guru" value="{{ request('search_guru') }}">
                                <div class="flex items-center gap-2">
                                    <span class="text-sm text-gray-500">Show:</span>
                                    <select name="per_page_guru" onchange="this.form.submit()"
                                        class="text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-1 pl-2 pr-8 cursor-pointer">
                                        <option value="30"
                                            {{ request('per_page_guru', 30) == 30 ? 'selected' : '' }}>30</option>
                                        <option value="50" {{ request('per_page_guru') == 50 ? 'selected' : '' }}>
                                            50</option>
                                        <option value="100"
                                            {{ request('per_page_guru') == 100 ? 'selected' : '' }}>100</option>
                                    </select>
                                </div>
                            </form>
                            <div>
                                {{ $userGuru->appends(['tab' => 'guru', 'search_guru' => request('search_guru'), 'per_page_guru' => request('per_page_guru', 30)])->links() }}
                            </div>
                        </div>
                    </div>

                    <x-modal name="add-guru" focusable>
                        <form method="POST" action="{{ route('admin.manajemen-user.gurus.store') }}"
                            class="p-6">
                            @csrf
                            <h2 class="text-lg font-bold text-gray-900 mb-4 pb-2 border-b">Tambah Akun Guru</h2>
                            <div class="mb-4">
                                <x-input-label value="Nama Lengkap Guru" />
                                <x-text-input name="name" class="w-full mt-1 focus:border-[#3B3E42]"
                                    placeholder="Contoh: Ahmad Subarjo" required />
                            </div>
                            <div class="mb-4">
                                <x-input-label value="Email (Kosongkan untuk otomatis)" />
                                <x-text-input name="email" type="email"
                                    class="w-full mt-1 focus:border-[#3B3E42]"
                                    placeholder="namalengkap@raudhah.com" />
                                <p class="text-[10px] text-red-500 mt-1 capitalize font-bold tracking-wider">*Jika
                                    kosong, sistem akan otomatis menggunakan format: namalengkap@raudhah.com</p>
                            </div>
                            <div class="mt-6 flex justify-end gap-3 border-t pt-4">
                                <x-secondary-button x-on:click="$dispatch('close')">Batal</x-secondary-button>
                                <x-primary-button type="submit">Buat Akun Guru</x-primary-button>
                            </div>
                        </form>
                    </x-modal>

                    @foreach ($userGuru as $g)
                        <x-modal name="edit-guru-{{ $g->id }}" focusable>
                            <form method="POST" action="{{ route('admin.manajemen-user.gurus.update', $g->id) }}"
                                class="p-6">
                                @csrf @method('PUT')
                                <h2 class="text-lg font-bold text-gray-900 mb-4 pb-2 border-b">Edit Akun:
                                    {{ $g->name }}</h2>
                                <div class="mb-3">
                                    <x-input-label value="Nama Guru" />
                                    <x-text-input name="name" value="{{ $g->name }}"
                                        class="w-full focus:border-[#3B3E42]" required />
                                </div>
                                <div class="mb-3">
                                    <x-input-label value="Email" />
                                    <x-text-input name="email" type="email" value="{{ $g->email }}"
                                        class="w-full focus:border-[#3B3E42]" required />
                                </div>
                                <div class="mt-6 flex justify-end gap-3">
                                    <x-secondary-button x-on:click="$dispatch('close')">Batal</x-secondary-button>
                                    <x-primary-button type="submit">Simpan Perubahan</x-primary-button>
                                </div>
                            </form>
                        </x-modal>
                    @endforeach

                    <x-modal name="import-guru" focusable>
                        <form method="POST" action="{{ route('admin.manajemen-user.gurus.import') }}"
                            enctype="multipart/form-data" class="p-6" x-data="{ fileName: '', isLoading: false }"
                            @submit="isLoading = true; $dispatch('loading')">
                            @csrf

                            <div class="flex justify-between items-start mb-5 border-b pb-3">
                                <h2 class="text-xl font-bold text-gray-900">Import Data Guru</h2>
                                <button type="button" x-on:click="$dispatch('close')"
                                    class="text-gray-400 hover:text-gray-600">
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>

                            <div
                                class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4 flex justify-between items-center">
                                <div>
                                    <h3 class="text-sm font-bold text-blue-800 mb-1">Langkah 1: Siapkan File</h3>
                                    <p class="text-xs text-blue-600">Gunakan format kolom: Nama, Email.</p>
                                </div>
                                <a href="{{ route('admin.manajemen-user.gurus.template') }}"
                                    class="bg-white text-blue-700 hover:bg-blue-100 border border-blue-300 px-3 py-2 rounded text-sm font-bold shadow-sm flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4">
                                        </path>
                                    </svg>Template
                                </a>
                            </div>

                            <div class="mb-6">
                                <h3 class="text-sm font-bold text-gray-700 mb-2">Langkah 2: Upload File</h3>

                                <div class="relative border-2 border-dashed rounded-lg p-8 transition text-center group"
                                    :class="fileName ? 'border-green-500 bg-green-50' :
                                        'border-gray-300 bg-gray-50 hover:border-[#3B3E42]'">

                                    <input type="file" name="file"
                                        class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" required
                                        accept=".xlsx, .xls, .csv"
                                        @change="fileName = $event.target.files[0] ? $event.target.files[0].name : ''"
                                        x-ref="fileInput">

                                    <template x-if="fileName">
                                        <button type="button" @click="fileName = ''; $refs.fileInput.value = ''"
                                            class="absolute top-2 right-2 z-20 p-1 rounded-full bg-white shadow-sm border border-green-200 text-green-600 hover:bg-green-100 transition-colors">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </template>

                                    <div
                                        class="pointer-events-none space-y-2 flex flex-col items-center justify-center">
                                        <div class="mb-2">
                                            <svg class="w-12 h-12 transition-colors duration-300"
                                                :class="fileName ? 'text-green-500' : 'text-gray-400'" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10">
                                                </path>
                                            </svg>
                                        </div>

                                        <p class="text-sm font-medium text-center transition-colors duration-300"
                                            :class="fileName ? 'text-green-700 font-bold' : 'text-gray-600'"
                                            x-text="fileName ? 'File: ' + fileName : 'Klik untuk pilih file'">
                                        </p>

                                        <template x-if="fileName">
                                            <p class="text-xs text-green-600">File siap diunggah!</p>
                                        </template>
                                    </div>
                                </div>
                            </div>

                            <div class="flex justify-end gap-3 pt-4 border-t">
                                <x-secondary-button x-on:click="$dispatch('close')" type="button">
                                    Batal
                                </x-secondary-button>

                                <x-primary-button ::disabled="isLoading"><span
                                        x-text="isLoading ? 'Memproses...' : 'Proses Import'"></span></x-primary-button>
                            </div>
                        </form>
                    </x-modal>
                </div>

                <div x-show="activeTab === 'panduan'" x-transition>
                    <div class="p-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 items-start">

                            <div class="bg-amber-50 border border-amber-200 rounded-lg p-5 shadow-sm">
                                <div class="flex items-center gap-3 mb-4">
                                    <div class="text-amber-600 flex-shrink-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                                        </svg>
                                    </div>
                                    <h3 class="font-bold text-amber-900 text-base">Pertama Sistem dijalankan & Update
                                        Data
                                    </h3>
                                </div>
                                <ul class="text-sm text-amber-800 space-y-3"> {{-- Jarak antar poin dirapatkan --}}
                                    <li class="flex gap-3">
                                        <span
                                            class="flex-shrink-0 w-6 h-6 bg-amber-200 text-amber-800 rounded-full flex items-center justify-center font-bold text-xs">1</span>
                                        <p><strong>Tahun Ajaran:</strong> Buat Tahun Ajaran baru dan Set status Tahun
                                            Ajaran terbaru menjadi
                                            <strong>"Aktif"</strong>.
                                        </p>
                                    </li>
                                    <li class="flex gap-3">
                                        <span
                                            class="flex-shrink-0 w-6 h-6 bg-amber-200 text-amber-800 rounded-full flex items-center justify-center font-bold text-xs">2</span>
                                        <p><strong>Kelas Tersedia:</strong> Buat semua daftar kelas baru yang
                                            diperlukan.</p>
                                    </li>
                                    <li class="flex gap-3">
                                        <span
                                            class="flex-shrink-0 w-6 h-6 bg-amber-200 text-amber-800 rounded-full flex items-center justify-center font-bold text-xs">3</span>
                                        <p><strong>Import Data Siswa:</strong> Buat Nama sekaligus Akun Siswa di Menu
                                            Manajemen User Siswa .
                                        </p>
                                    </li>
                                    <li class="flex gap-3">
                                        <span
                                            class="flex-shrink-0 w-6 h-6 bg-amber-200 text-amber-800 rounded-full flex items-center justify-center font-bold text-xs">4</span>
                                        <p><strong>Verifikasi Import Akun:</strong> Bandingkan data Excel dengan data di
                                            sistem lama yang ada
                                            sebelum upload data Excel siswa untuk update data.</p>
                                    </li>
                                </ul>
                            </div>

                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-5 shadow-sm">
                                <div class="flex items-center gap-3 mb-4">
                                    <div class="text-blue-600 flex-shrink-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <h3 class="font-bold text-blue-900 text-base">Panduan Alur Update Data Siswa &
                                        Kenaikan Kelas</h3>
                                </div>
                                <ul class="text-sm text-blue-800 space-y-3">
                                    <li class="flex gap-3">
                                        <span
                                            class="flex-shrink-0 w-6 h-6 bg-blue-200 text-blue-800 rounded-full flex items-center justify-center font-bold text-xs">1</span>
                                        <p><strong>Kelulusan:</strong> Proses lulus kelas 9 di Kelola Tahun Ajaran,
                                            Proses Kelulusan (siswa kelas 9 yang lulus otomatis kelas jadi null).</p>
                                    </li>
                                    <li class="flex gap-3">
                                        <span
                                            class="flex-shrink-0 w-6 h-6 bg-blue-200 text-blue-800 rounded-full flex items-center justify-center font-bold text-xs">2</span>
                                        <p><strong>Tahun Ajaran:</strong> Buat Tahun Ajaran baru jika diperlukan & Atur
                                            Tahun Ajaran baru ke
                                            <strong>"Aktif"</strong>.
                                        </p>
                                    </li>
                                    <li class="flex gap-3">
                                        <span
                                            class="flex-shrink-0 w-6 h-6 bg-blue-200 text-blue-800 rounded-full flex items-center justify-center font-bold text-xs">3</span>
                                        <p><strong>Import Data Siswa:</strong> Import Kembali data siswa dengan update
                                            data <strong>Kelas</strong> masing" siswa di Excel .</p>
                                    </li>
                                    <li class="flex gap-3">
                                        <span
                                            class="flex-shrink-0 w-6 h-6 bg-blue-200 text-blue-800 rounded-full flex items-center justify-center font-bold text-xs">4</span>
                                        <p><strong> Panduan ini dilakukan biasanya disaat terjadi Kenaikan Kelas dan
                                                Tahun Ajaran Berubah</strong></p>
                                    </li>
                                </ul>
                            </div>

                        </div>
                    </div>
                </div>

                <x-siswa.add-student-modal :kelas="$kelas" />

                <form id="deleteForm" method="POST" class="hidden">@csrf @method('DELETE')</form>

            </div>
        </div>
    </div>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function() {

            const activeTabSession = "{{ session('active_tab') }}";
            if (activeTabSession) {

            }

            const selectAllSiswa = document.getElementById('selectAll');
            const itemsSiswa = document.querySelectorAll('.select-item');
            const bulkSiswaContainer = document.getElementById('bulkDeleteContainer');
            const selectedSiswaCount = document.getElementById('selectedCount');

            function toggleSiswaBulk() {
                const checked = document.querySelectorAll('.select-item:checked');
                if (selectedSiswaCount) selectedSiswaCount.innerText = checked.length;

                if (checked.length > 0) {
                    bulkSiswaContainer?.classList.remove('hidden');
                } else {
                    bulkSiswaContainer?.classList.add('hidden');
                }

                document.querySelectorAll('.dynamic-siswa-id').forEach(el => el.remove());
                checked.forEach(item => {
                    ['bulkDeleteForm', 'bulkResetForm'].forEach(formId => {
                        const form = document.getElementById(formId);
                        if (form) {
                            const input = document.createElement('input');
                            input.type = 'hidden';
                            input.name = 'ids[]';
                            input.value = item.value;
                            input.className = 'dynamic-siswa-id';
                            form.appendChild(input);
                        }
                    });
                });
            }
            if (selectAllSiswa) {
                selectAllSiswa.addEventListener('change', () => {
                    itemsSiswa.forEach(i => i.checked = selectAllSiswa.checked);
                    toggleSiswaBulk();
                });
            }
            itemsSiswa.forEach(i => i.addEventListener('change', toggleSiswaBulk));

            const selectAllGuru = document.getElementById('selectAllGuru');
            const itemsGuru = document.querySelectorAll('.select-item-guru');
            const bulkGuruContainer = document.getElementById('bulkGuruContainer');
            const selectedGuruCount = document.getElementById('selectedGuruCount');

            function toggleGuruBulk() {
                const checked = document.querySelectorAll('.select-item-guru:checked');
                if (selectedGuruCount) selectedGuruCount.innerText = checked.length;

                if (checked.length > 0) {
                    bulkGuruContainer?.classList.remove('hidden');
                } else {
                    bulkGuruContainer?.classList.add('hidden');
                }

                document.querySelectorAll('.dynamic-guru-id').forEach(el => el.remove());
                checked.forEach(item => {
                    ['bulkDeleteGuruForm', 'bulkResetGuruForm'].forEach(formId => {
                        const form = document.getElementById(formId);
                        if (form) {
                            const input = document.createElement('input');
                            input.type = 'hidden';
                            input.name = 'ids[]';
                            input.value = item.value;
                            input.className = 'dynamic-guru-id';
                            form.appendChild(input);
                        }
                    });
                });
            }

            if (selectAllGuru) {
                selectAllGuru.addEventListener('change', () => {
                    itemsGuru.forEach(i => i.checked = selectAllGuru.checked);
                    toggleGuruBulk();
                });
            }
            itemsGuru.forEach(i => i.addEventListener('change', toggleGuruBulk));
        });

        function confirmDelete(url, name) {
            if (confirm('Yakin ingin menghapus data ' + name + ' secara permanen?')) {
                const form = document.getElementById('deleteForm');
                if (form) {
                    form.action = url;
                    form.submit();
                }
            }
        }
    </script>
</x-app-layout>
