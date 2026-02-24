<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Data Tagihan Siswa') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="tagihanManager()" x-init="init()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            {{-- Form Hapus Massal --}}
            <form id="form-hapus-massal" action="{{ route('admin.keuangan.tagihan.destroy-bulk') }}" method="POST">
                @csrf
                @method('DELETE')
                <template x-for="id in selectedIds" :key="id">
                    <input type="hidden" name="tagihan_ids[]" :value="id">
                </template>
            </form>

            <form id="form-pembayaran-massal" action="{{ route('admin.keuangan.pembayaran.store') }}" method="POST">
                @csrf
                <template x-for="id in selectedIds" :key="id">
                    <input type="hidden" name="tagihan_ids[]" :value="id">
                </template>
                <input type="hidden" name="jumlah_bayar_total" x-bind:value="jumlahBayarInput">
                <input type="hidden" name="metode" x-bind:value="metodePembayaran">
            </form>

            {{-- Alert Notifications --}}
            @if (session('success'))
                <x-alert-success>
                    {{ session('success') }}
                </x-alert-success>
            @endif
            {{-- Tampilkan Alert Gagal (Misal dari Session Error) --}}
            @if (session('error'))
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

            <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-200">
                <div class="p-6 text-gray-900">
                    <div class="mb-4">
                        <h3 class="text-lg font-bold text-gray-900">Daftar Tagihan Siswa</h3>
                        <p class="text-sm text-gray-500">Kelola dan proses pembayaran tagihan secara kolektif.</p>
                    </div>

                    {{-- FILTER BOX UTAMA --}}
                    <form method="GET" action="{{ route('admin.keuangan.tagihan.index') }}" id="filterForm"
                        class="p-5 rounded-xl border border-gray-100 mb-4 grid grid-cols-1 md:grid-cols-5 gap-4 items-center">

                        <input type="hidden" name="selected_ids" :value="selectedIds.join(',')">
                        <input type="hidden" name="per_page" value="{{ request('per_page', 30) }}">

                        {{-- Pencarian --}}
                        <div class="relative w-full">
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="Cari Nama / NISN..."
                                class="w-full text-sm rounded-full pl-5 pr-10 py-2 border-gray-300">
                            <button type="submit" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </button>
                        </div>

                        {{-- Kelas --}}
                        <div>
                            <select name="kelas_id" onchange="this.form.submit()"
                                class="w-full border-gray-300 rounded-full text-sm">
                                <option value="">-- Semua Kelas --</option>
                                @foreach ($kelasList as $k)
                                    <option value="{{ $k->id }}"
                                        {{ request('kelas_id') == $k->id ? 'selected' : '' }}>
                                        Kelas {{ $k->tingkat }} {{ $k->nama_kelas }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Jenis Tagihan --}}
                        <div>
                            <select name="master_tagihan_id" onchange="this.form.submit()"
                                class="w-full border-gray-300 rounded-full text-sm">
                                <option value="">-- Semua Jenis --</option>
                                @foreach ($masterTagihans as $mt)
                                    <option value="{{ $mt->id }}"
                                        {{ request('master_tagihan_id') == $mt->id ? 'selected' : '' }}>
                                        {{ $mt->nama_tagihan }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Periode --}}
                        <div>
                            <select name="periode" onchange="this.form.submit()"
                                class="w-full border-gray-300 rounded-full text-sm">
                                <option value="">-- Semua Periode --</option>
                                @foreach ($periodeList as $p)
                                    @php $val = $p->bulan . '|' . $p->tahun @endphp
                                    <option value="{{ $val }}"
                                        {{ request('periode') == $val ? 'selected' : '' }}>
                                        {{ $p->bulan }} {{ $p->tahun }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Status --}}
                        <div>
                            <select name="status" onchange="this.form.submit()"
                                class="w-full border-gray-300 rounded-full text-sm">
                                <option value="">-- Semua Status --</option>
                                <option value="belum_lunas" {{ request('status') == 'belum_lunas' ? 'selected' : '' }}>
                                    Belum Lunas</option>
                                <option value="cicilan" {{ request('status') == 'cicilan' ? 'selected' : '' }}>Cicilan
                                </option>
                                <option value="lunas" {{ request('status') == 'lunas' ? 'selected' : '' }}>Lunas
                                </option>
                            </select>
                        </div>
                    </form>
                    {{-- Info Total Floating --}}
                    <div x-show="selectedIds.length > 0" x-transition class="w-full mb-4">
                        <div
                            class="relative p-5 bg-blue-50 border border-blue-200 rounded-xl flex flex-col sm:flex-row justify-between items-center gap-4 shadow-sm">

                            {{-- Tombol Silang Kanan Atas --}}
                            <button type="button" @click="clearSelection()"
                                class="absolute top-3 right-3 p-1.5 rounded-full bg-blue-100 hover:bg-blue-100 text-blue-400 hover:text-blue-500 transition"
                                title="Batalkan semua pilihan">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>

                            <div class="flex items-center gap-4">
                                <div class="bg-[#1072B8] p-2.5 rounded-lg text-white shadow-sm">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                        </path>
                                    </svg>
                                </div>
                                <div class="flex flex-col md:flex-row md:items-center gap-2 md:gap-6">
                                    <div>
                                        <p class="text-[10px] text-blue-600 font-bold uppercase tracking-widest mb-0.5">
                                            Total Siswa</p>
                                        <span class="text-blue-900 font-bold text-lg"><span
                                                x-text="totalSiswa">0</span>
                                            Siswa</span>
                                    </div>
                                    <div class="hidden md:block w-px h-8 bg-blue-200"></div>
                                    <div>
                                        <p
                                            class="text-[10px] text-blue-600 font-bold uppercase tracking-widest mb-0.5">
                                            Total Nominal</p>
                                        <span class="text-blue-900 font-bold font-mono text-xl"
                                            x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(totalTagihan)"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="text-right pr-6">
                                <span class="text-[11px] text-blue-600 font-bold italic tracking-tight">*Pembayaran
                                    akan
                                    dialokasikan secara otomatis.</span>
                            </div>
                        </div>
                    </div>

                    {{-- Header & Tombol Generate --}}
                    <div class="flex flex-col lg:flex-row justify-end items-start lg:items-center mb-4 gap-4">

                        <div class="flex flex-col sm:flex-row gap-3 w-full lg:w-auto">
                            <button x-data=""
                                x-on:click="$dispatch('open-modal', 'modal-generate-tagihan')"
                                class="inline-flex items-center justify-center gap-2 bg-amber-600 hover:bg-amber-700 text-white px-4 py-2 rounded-lg text-sm font-semibold shadow-sm transition capitalize border-none cursor-pointer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4"></path>
                                </svg>
                                Generate Tagihan
                            </button>

                            {{-- 🔴 TOMBOL HAPUS --}}
                            <button type="button" @click="submitHapus()" :disabled="selectedIds.length === 0"
                                :class="selectedIds.length === 0 ? 'bg-red-300 cursor-not-allowed' :
                                    'bg-red-600 hover:bg-red-700'"
                                class="inline-flex items-center justify-center gap-2 text-white px-4 py-2 rounded-lg text-sm font-bold shadow-sm transition-all whitespace-nowrap">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                Hapus <span x-text="selectedIds.length">0</span> Tagihan
                            </button>

                            {{-- 🔵 TOMBOL LUNASI --}}
                            <form action="{{ route('admin.keuangan.pembayaran.store') }}" method="POST"
                                id="form-pembayaran-massal">
                                @csrf
                                <template x-for="id in selectedIds" :key="id">
                                    <input type="hidden" name="tagihan_ids[]" :value="id">
                                </template>
                                <input type="hidden" name="jumlah_bayar_total" :value="totalTagihan">
                                <input type="hidden" name="metode" value="tunai">

                                <button type="button" @click="submitPembayaran()"
                                    :disabled="selectedIds.length === 0"
                                    :class="selectedIds.length === 0 ?
                                        'bg-blue-300 w-full md:w-auto justify-center py-2 md:py-2 cursor-not-allowed' :
                                        'bg-[#1072B8] hover:bg-[#0d5a91] w-full md:w-auto justify-center py-2 md:py-2'"
                                    class="inline-flex items-center justify-center gap-2 text-white px-4 py-2 rounded-lg text-sm font-bold shadow-sm transition-all whitespace-nowrap">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                    Lunasi <span x-text="totalSiswa">0</span> Siswa (<span
                                        x-text="selectedIds.length">0</span> Tagihan )
                                </button>
                            </form>
                        </div>
                    </div>



                    {{-- TABEL DATA --}}
                    <div class="overflow-hidden border border-gray-200 rounded-xl shadow-sm">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-[#3B3E42]">
                                    <tr>
                                        <th class="px-4 py-4 text-center">
                                            <input type="checkbox" @click="toggleAll($event)"
                                                class="rounded border-gray-300 text-[#1072B8] shadow-sm focus:ring-[#1072B8] cursor-pointer">
                                        </th>
                                        <th
                                            class="px-4 py-4 text-center text-xs font-bold text-white uppercase w-12 tracking-wider">
                                            No</th>
                                        <th
                                            class="px-4 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">
                                            Nama Siswa</th>
                                        <th
                                            class="px-4 py-4 text-center text-xs font-bold text-white uppercase tracking-wider">
                                            Kelas</th>

                                        <th
                                            class="px-4 py-4 text-center text-xs font-bold text-white uppercase tracking-wider">
                                            Tagihan</th>
                                        <th
                                            class="px-4 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">
                                            Periode</th>
                                        <th
                                            class="px-4 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">
                                            Nominal</th>
                                        <th
                                            class="px-4 py-4 text-center text-xs font-bold text-white uppercase tracking-wider">
                                            Status</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-100">
                                    @forelse ($tagihans as $index => $tagihan)
                                        <tr class="hover:bg-indigo-50/50 transition even:bg-gray-50">
                                            <td class="px-4 py-4 text-center">
                                                @if ($tagihan->status !== 'lunas')
                                                    <input type="checkbox" value="{{ $tagihan->id }}"
                                                        data-nominal="{{ $tagihan->jumlah_tagihan - $tagihan->terbayar }}"
                                                        data-siswa-id="{{ $tagihan->riwayatAkademik->siswa->id }}"
                                                        {{-- Perbaikan: Gunakan .toString() agar perbandingan ID selalu akurat --}}
                                                        :checked="selectedIds.map(id => id.toString()).includes(
                                                            '{{ $tagihan->id }}')"
                                                        @change="updateSelection($event)"
                                                        class="tagihan-checkbox rounded border-gray-300 text-[#1072B8] shadow-sm focus:ring-[#1072B8] cursor-pointer">
                                                @else
                                                    <svg class="w-5 h-5 text-green-500 mx-auto" fill="currentColor"
                                                        viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd"
                                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                            clip-rule="evenodd"></path>
                                                    </svg>
                                                @endif
                                            </td>
                                            <td class="px-4 py-4 text-center text-sm font-medium text-gray-500">
                                                {{ $tagihans->firstItem() + $index }}
                                            </td>
                                            <td class="px-4 py-4">
                                                <div class="text-sm font-bold text-gray-900 leading-none">
                                                    {{ $tagihan->riwayatAkademik->siswa->nama_lengkap ?? 'N/A' }}</div>
                                                <div class="text-[11px] text-gray-400 mt-1 font-mono">
                                                    {{ $tagihan->riwayatAkademik->siswa->nisn ?? '-' }}</div>
                                                {{-- <div class="text-[11px] text-blue-500 font-bold mt-0.5">
                                                    Kelas {{ $tagihan->riwayatAkademik->kelas->tingkat ?? '' }}
                                                    {{ $tagihan->riwayatAkademik->kelas->nama_kelas ?? '-' }}</div> --}}
                                            </td>
                                            <td class="px-4 py-4 text-center">
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-blue-800 shadow-sm border border-blue-200">
                                                    {{ $tagihan->riwayatAkademik->kelas->tingkat ?? '' }}
                                                    {{ $tagihan->riwayatAkademik->kelas->nama_kelas ?? '-' }}
                                                </span>
                                            </td>

                                            <td class="px-4 py-4 text-center text-sm text-gray-700">
                                                {{ $tagihan->masterTagihan->nama_tagihan ?? 'Tagihan Terhapus' }}
                                            </td>
                                            <td class="px-4 py-4 text-sm text-gray-500">
                                                <span class="font-bold text-gray-700">{{ $tagihan->bulan }}</span>
                                                {{ $tagihan->tahun }}
                                            </td>
                                            <td class="px-4 py-4 text-left">
                                                <div class="text-sm font-bold text-[#1072B8]">
                                                    Rp {{ number_format($tagihan->jumlah_tagihan, 0, ',', '.') }}
                                                </div>
                                                @if ($tagihan->terbayar > 0)
                                                    <div
                                                        class="text-[10px] text-orange-600 font-bold uppercase tracking-tighter mt-1">
                                                        Sisa: Rp
                                                        {{ number_format($tagihan->jumlah_tagihan - $tagihan->terbayar, 0, ',', '.') }}
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="px-4 py-4 text-center">
                                                @php
                                                    $statusClass = match ($tagihan->status) {
                                                        'lunas' => 'bg-green-100 text-green-800 border-green-200',
                                                        'cicilan' => 'bg-orange-100 text-orange-800 border-orange-200',
                                                        default => 'bg-red-100 text-red-800 border-red-200',
                                                    };
                                                @endphp
                                                <span
                                                    class="px-3 py-1 rounded-full text-[10px] font-bold border {{ $statusClass }}">
                                                    {{ strtoupper($tagihan->status) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="px-6 py-12 text-center text-gray-400 italic">
                                                Tidak ada data tagihan yang ditemukan.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- PAGINATION & LIMIT --}}
                    <div class="mt-6 flex flex-col sm:flex-row justify-between items-center gap-4">
                        <form method="GET" action="{{ route('admin.keuangan.tagihan.index') }}"
                            class="flex items-center gap-2">
                            <input type="hidden" name="search" value="{{ request('search') }}">
                            <input type="hidden" name="kelas_id" value="{{ request('kelas_id') }}">
                            <input type="hidden" name="status" value="{{ request('status') }}">
                            <input type="hidden" name="periode" value="{{ request('periode') }}">
                            <input type="hidden" name="master_tagihan_id"
                                value="{{ request('master_tagihan_id') }}">

                            <span class="text-sm text-gray-500 font-medium">Show:</span>
                            <select name="per_page" onchange="this.form.submit()"
                                class="text-sm border-gray-300 rounded-lg shadow-sm focus:border-[#3B3E42] focus:ring-[#3B3E42] py-1 pl-2 pr-8 transition cursor-pointer">
                                <option value="30" {{ request('per_page') == 30 ? 'selected' : '' }}>30</option>
                                <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                                <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                            </select>
                        </form>

                        <div class="w-full sm:w-auto">
                            {{ $tagihans->appends(request()->query())->links() }}
                        </div>
                    </div>

                </div>
            </div>
        </div>
        {{-- MODAL GENERATE TAGIHAN --}}
        <x-modal name="modal-generate-tagihan" focusable>
            <form action="{{ route('admin.keuangan.tagihan.store-bulk') }}" method="POST" class="p-5 text-left">
                @csrf

                {{-- Header: Dibuat lebih ringkas --}}
                <div class="mb-4">
                    <h2 class="text-lg font-bold text-gray-900">Generate Tagihan Massal</h2>
                    <p class="text-xs text-gray-500">Buat tagihan otomatis untuk banyak siswa sekaligus.</p>
                </div>

                <div class="space-y-3.5">
                    {{-- Baris 1: Jenis Tagihan (Full Width karena teks biasanya panjang) --}}
                    <div class="space-y-1">
                        <x-input-label value="Jenis Tagihan *" class="text-xs font-bold text-gray-700" />
                        <select name="master_tagihan_id"
                            class="block w-full border-gray-300 focus:border-amber-500 focus:ring-amber-500 rounded-lg shadow-sm bg-gray-50 text-sm px-3 py-2 transition"
                            required>
                            <option value="" disabled selected>-- Pilih Tagihan --</option>
                            @foreach ($masterTagihans as $mt)
                                <option value="{{ $mt->id }}">{{ $mt->nama_tagihan }} (Rp
                                    {{ number_format($mt->nominal, 0, ',', '.') }})</option>
                            @endforeach
                        </select>
                        <p class="text-[10px] text-red-500 font-medium">* Khusus SPP, Harap Isi Pilihan BULAN di bawah.
                        </p>
                    </div>

                    {{-- Baris 2: Tahun Ajaran & Tahun Tagihan (Dibuat sejajar agar hemat tempat) --}}
                    <div class="grid grid-cols-2 gap-3">
                        <div class="space-y-1">
                            <x-input-label value="Tahun Ajaran *" class="text-xs font-bold text-gray-700" />
                            <select name="tahun_ajaran_id"
                                class="block w-full border-gray-300 focus:border-amber-500 focus:ring-amber-500 rounded-lg shadow-sm bg-gray-50 text-sm px-3 py-2 transition"
                                required>
                                @foreach ($tahunAjarans as $ta)
                                    <option value="{{ $ta->id }}">{{ $ta->tahun }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="space-y-1">
                            <x-input-label value="Tahun Tagihan *" class="text-xs font-bold text-gray-700" />
                            <input type="number" name="tahun" value="{{ date('Y') }}"
                                class="block w-full border-gray-300 focus:border-amber-500 focus:ring-amber-500 rounded-lg shadow-sm text-sm px-3 py-2 transition"
                                required min="{{ date('Y') }}" max="{{ date('Y') + 1 }}">
                        </div>
                    </div>

                    {{-- Baris 3: Bulan & Target Siswa (Sejajar) --}}
                    <div class="grid grid-cols-2 gap-3">
                        <div class="space-y-1">
                            <x-input-label value="Bulan (Khusus SPP)" class="text-xs font-bold text-gray-700" />
                            <select name="bulan"
                                class="block w-full border-gray-300 focus:border-amber-500 focus:ring-amber-500 rounded-lg shadow-sm text-sm px-3 py-2 transition">
                                <option value="">-- Non-SPP --</option>
                                @foreach (['Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni'] as $bln)
                                    <option value="{{ $bln }}">{{ $bln }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="space-y-1">
                            <x-input-label value="Target Siswa *" class="text-xs font-bold text-gray-700" />
                            <select name="target_kelas"
                                class="block w-full border-amber-200 focus:border-amber-500 focus:ring-amber-500 rounded-lg shadow-sm bg-amber-50 font-bold text-amber-900 text-sm px-3 py-2 transition cursor-pointer"
                                required>
                                <option value="" disabled selected>-- Pilih Target --</option>
                                <option value="semua" class="font-bold">Semua Siswa Aktif</option>
                                <optgroup label="Per Tingkatan">
                                    <option value="tingkat_7">Kelas 7</option>
                                    <option value="tingkat_8">Kelas 8</option>
                                    <option value="tingkat_9">Kelas 9</option>
                                </optgroup>
                                <optgroup label="Per Kelas Spesifik">
                                    @foreach ($kelasList as $k)
                                        <option value="{{ $k->id }}">Kelas {{ $k->tingkat }}
                                            {{ $k->nama_kelas }}</option>
                                    @endforeach
                                </optgroup>
                            </select>
                        </div>
                    </div>

                    {{-- Baris 4: Warning (Lebih compact) --}}
                    <div class="flex items-center gap-2 bg-orange-50 p-2.5 rounded-lg border border-orange-100">
                        <svg class="w-4 h-4 text-orange-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                clip-rule="evenodd"></path>
                        </svg>
                        <p class="text-[11px] text-orange-800 leading-tight">Data ganda otomatis dilewati sistem.</p>
                    </div>
                </div>

                {{-- Footer: Jarak dikurangi (mt-6) --}}
                <div class="mt-6 flex justify-end gap-2 border-t pt-4">
                    <x-secondary-button x-on:click="$dispatch('close')" class="text-xs">
                        Batal
                    </x-secondary-button>
                    <button type="submit" onclick="return confirm('Yakin ingin generate tagihan massal?')"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-lg text-xs font-bold shadow-sm transition capitalize">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Generate Sekarang
                    </button>
                </div>
            </form>
        </x-modal>


        {{-- MODAL KONFIRMASI --}}
        <div x-show="showModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-gray-600 bg-opacity-75 transition-opacity" @click="showModal = false">
                </div>

                <div
                    class="bg-white rounded-xl overflow-hidden shadow-2xl transform transition-all sm:max-w-lg sm:w-full p-8 relative z-10">
                    <h3 class="text-xl font-bold text-gray-900 mb-6 pb-3 border-b text-[#1072B8]">Konfirmasi Pembayaran
                    </h3>

                    <div class="space-y-6 text-left">
                        <div class="p-4 bg-blue-50 rounded-xl border border-blue-100">
                            <p class="text-xs text-blue-600 font-bold uppercase tracking-wider mb-1">Total Tagihan
                                Terpilih:</p>
                            <p class="text-2xl font-mono font-extrabold text-[#1072B8]"
                                x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(totalTagihan)">
                            </p>
                        </div>
                        {{-- Ganti bagian input Jumlah Bayar di modal --}}
                        <div>
                            <x-input-label class="font-bold text-gray-700">Jumlah Bayar (Rp)</x-input-label>
                            <div class="relative mt-2">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm font-bold">Rp</span>
                                </div>
                                <input type="number" x-model="jumlahBayarInput" :max="totalTagihan"
                                    :disabled="selectedIds.length > 1"
                                    :class="selectedIds.length > 1 ?
                                        'bg-gray-100 text-gray-400 cursor-not-allowed select-none' :
                                        'bg-white'"
                                    @input="if (jumlahBayarInput > totalTagihan) jumlahBayarInput = totalTagihan"
                                    class="pl-10 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-[#1072B8] focus:border-[#1072B8] sm:text-sm transition">
                            </div>

                            {{-- Pesan dinamis --}}
                            <p class="text-[10px] mt-2 font-medium italic"
                                :class="selectedIds.length > 1 ? 'text-red-400' : 'text-gray-500'">
                                <span x-show="selectedIds.length <= 1">*Ubah jika bayar dicicil (kurang dari
                                    total).</span>
                                <span x-show="selectedIds.length > 1">*Jumlah bayar tidak dapat diubah jika tagihan
                                    dipilih lebih dari 1.</span>
                            </p>
                        </div>
                        <div>
                            <x-input-label class="font-bold text-gray-700">Metode Pembayaran</x-input-label>
                            <select x-model="metodePembayaran"
                                class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-[#3B3E42] focus:border-[#3B3E42] sm:text-sm">
                                <option value="tunai">Tunai (Manual/Offline)</option>
                                <option value="midtrans">Midtrans (Online Gateway)</option>
                            </select>
                        </div>
                    </div>

                    <div class="mt-8 flex justify-end gap-3 border-t pt-5">
                        <button @click="showModal = false"
                            class="bg-white border px-5 py-2 rounded-lg text-sm font-semibold text-gray-600 hover:bg-gray-50 transition">Batal</button>
                        <button type="submit" form="form-pembayaran-massal"
                            class="bg-[#1072B8] text-white px-6 py-2 rounded-lg text-sm font-bold hover:bg-[#0d5a91] shadow-md transition">
                            Proses Sekarang
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- MODAL KONFIRMASI HAPUS --}}
        {{-- MODAL KONFIRMASI HAPUS (pakai x-modal component) --}}
        <x-modal name="modal-hapus-tagihan" maxWidth="md" focusable>
            <div class="p-6 text-center">

                {{-- Icon Warning --}}
                <div class="flex items-center justify-center w-16 h-16 mx-auto mb-4 bg-red-100 rounded-full">
                    <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>

                <h3 class="text-xl font-bold text-gray-900 mb-1">Hapus Tagihan?</h3>
                <p class="text-sm text-gray-500 mb-6">
                    Anda akan menghapus
                    <span class="font-bold text-red-600" x-text="$store.hapusInfo?.jumlah ?? 0"></span>
                    tagihan secara permanen. Tindakan ini tidak dapat dibatalkan.
                </p>

                {{-- Info Box --}}
                <div class="p-3 bg-red-50 border border-red-100 rounded-lg mb-6">
                    <p class="text-xs text-red-600 font-bold uppercase tracking-wider mb-1">Total Tagihan Dihapus</p>
                    <p class="text-xl font-mono font-extrabold text-red-700"
                        x-text="'Rp ' + new Intl.NumberFormat('id-ID').format($store.hapusInfo?.nominal ?? 0)"></p>
                </div>

                <div class="flex justify-center gap-3">
                    <x-secondary-button x-on:click="$dispatch('close')">
                        Batal
                    </x-secondary-button>
                    <button type="submit" form="form-hapus-massal"
                        class="bg-red-600 text-white px-6 py-2 rounded-lg text-sm font-bold hover:bg-red-700 shadow-md transition flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Ya, Hapus Sekarang
                    </button>
                </div>
            </div>
        </x-modal>


    </div>
    <script>
        @if (session('success') || session('error'))
            localStorage.removeItem('selected_tagihan_ids');
            localStorage.removeItem('tagihan_siswa_map');
            localStorage.removeItem('selected_total_nominal');
        @endif
        document.addEventListener('alpine:init', () => {
            Alpine.store('hapusInfo', {
                jumlah: 0,
                nominal: 0
            });
        });

        function tagihanManager() {
            return {
                selectedIds: JSON.parse(localStorage.getItem('selected_tagihan_ids')) || [],
                tagihanToSiswaMap: JSON.parse(localStorage.getItem('tagihan_siswa_map')) || {},
                totalTagihan: parseInt(localStorage.getItem('selected_total_nominal')) || 0,
                totalSiswa: 0,
                showModal: false,
                jumlahBayarInput: 0,
                metodePembayaran: 'tunai',


                calculateTotalSiswa() {
                    const uniqueSiswaIds = [...new Set(Object.values(this.tagihanToSiswaMap))];
                    this.totalSiswa = uniqueSiswaIds.length;
                },

                saveToStorage() {
                    localStorage.setItem('selected_tagihan_ids', JSON.stringify(this.selectedIds));
                    localStorage.setItem('tagihan_siswa_map', JSON.stringify(this.tagihanToSiswaMap));
                    localStorage.setItem('selected_total_nominal', this.totalTagihan);
                },

                updateSelection(e) {
                    const id = e.target.value;
                    const nominal = parseInt(e.target.dataset.nominal) || 0;
                    const siswaId = e.target.dataset.siswaId;

                    if (e.target.checked) {
                        if (!this.selectedIds.map(i => i.toString()).includes(id.toString())) {
                            this.selectedIds.push(id);
                            this.totalTagihan += nominal;
                            this.tagihanToSiswaMap[id] = siswaId;
                        }
                    } else {
                        this.selectedIds = this.selectedIds.filter(i => i.toString() !== id.toString());
                        this.totalTagihan -= nominal;
                        delete this.tagihanToSiswaMap[id];
                    }

                    this.calculateTotalSiswa();
                    this.saveToStorage();
                    this.jumlahBayarInput = this.totalTagihan;

                    const url = new URL(window.location.href);
                    const currentPage = parseInt(url.searchParams.get('page') || '1');

                    if (currentPage > 1) {
                        url.searchParams.set('page', '1');
                        url.searchParams.set('selected_ids', this.selectedIds.join(','));
                        window.location.href = url.toString();
                        return;
                    }

                    this.$nextTick(() => {
                        this.sortTable();
                        this.updatePaginationLinks();
                    });
                },

                toggleAll(e) {
                    const checkboxes = document.querySelectorAll('.tagihan-checkbox');
                    checkboxes.forEach(cb => {
                        if (cb.checked !== e.target.checked) {
                            cb.checked = e.target.checked;
                            this.updateSelection({
                                target: cb
                            });
                        }
                    });
                },

                sortTable() {
                    const tbody = document.querySelector('table tbody');
                    if (!tbody) return;
                    const rows = Array.from(tbody.querySelectorAll('tr'));
                    rows.sort((a, b) => {
                        const cbA = a.querySelector('.tagihan-checkbox');
                        const cbB = b.querySelector('.tagihan-checkbox');
                        const aChecked = cbA ? this.selectedIds.map(id => id.toString()).includes(cbA.value
                            .toString()) : false;
                        const bChecked = cbB ? this.selectedIds.map(id => id.toString()).includes(cbB.value
                            .toString()) : false;
                        if (aChecked && !bChecked) return -1;
                        if (!aChecked && bChecked) return 1;
                        return 0;
                    });
                    rows.forEach(row => tbody.appendChild(row));
                },

                updatePaginationLinks() {
                    const ids = this.selectedIds.join(',');
                    document.querySelectorAll('[aria-label="Pagination"] a, nav[aria-label="Pagination"] a, .pagination a')
                        .forEach(link => {
                            if (!link.href || link.href === '#') return;
                            try {
                                const url = new URL(link.href);
                                if (ids) {
                                    url.searchParams.set('selected_ids', ids);
                                } else {
                                    url.searchParams.delete('selected_ids');
                                }
                                link.href = url.toString();
                            } catch (e) {}
                        });
                },

                clearSelection() {
                    document.querySelectorAll('.tagihan-checkbox').forEach(cb => {
                        cb.checked = false;
                    });

                    this.selectedIds = [];
                    this.tagihanToSiswaMap = {};
                    this.totalTagihan = 0;
                    this.totalSiswa = 0;
                    this.jumlahBayarInput = 0;

                    localStorage.removeItem('selected_tagihan_ids');
                    localStorage.removeItem('tagihan_siswa_map');
                    localStorage.removeItem('selected_total_nominal');

                    this.updatePaginationLinks();
                },

                submitPembayaran() {
                    this.jumlahBayarInput = this.totalTagihan;
                    this.showModal = true;
                },

                async executePayment() {
                    if (this.jumlahBayarInput <= 0) return alert('Jumlah bayar tidak valid');
                    try {
                        const response = await fetch("{{ route('admin.keuangan.pembayaran.store') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                tagihan_ids: this.selectedIds,
                                jumlah_bayar_total: this.jumlahBayarInput,
                                metode: this.metodePembayaran
                            })
                        });
                        const result = await response.json();
                        if (result.success) {
                            localStorage.removeItem('selected_tagihan_ids');
                            localStorage.removeItem('tagihan_siswa_map');
                            localStorage.removeItem('selected_total_nominal');
                            window.location.reload();
                        } else {
                            alert('Gagal: ' + result.message);
                        }
                    } catch (error) {
                        console.error(error);
                        alert('Terjadi kesalahan sistem.');
                    }
                },

                // --- Method Baru Untuk Hapus Massal ---
                submitHapus() {
                    if (this.selectedIds.length === 0) return alert('Pilih tagihan terlebih dahulu');

                    // Simpan info ke Alpine store agar bisa dibaca modal
                    Alpine.store('hapusInfo', {
                        jumlah: this.selectedIds.length,
                        nominal: this.totalTagihan
                    });

                    this.$dispatch('open-modal', 'modal-hapus-tagihan');
                },

                async executeHapus() {
                    try {
                        const response = await fetch("{{ route('admin.keuangan.tagihan.destroy-bulk') }}", {
                            method: 'DELETE',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                tagihan_ids: this.selectedIds
                            })
                        });
                        const result = await response.json();
                        if (result.success) {
                            localStorage.removeItem('selected_tagihan_ids');
                            localStorage.removeItem('tagihan_siswa_map');
                            localStorage.removeItem('selected_total_nominal');
                            this.showModalHapus = false;
                            window.location.reload();
                        } else {
                            alert('Gagal: ' + result.message);
                        }
                    } catch (error) {
                        console.error(error);
                        alert('Terjadi kesalahan sistem.');
                    }
                }
            }
        }
    </script>
</x-app-layout>
