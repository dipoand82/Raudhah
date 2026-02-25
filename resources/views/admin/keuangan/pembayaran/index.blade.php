<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Riwayat Pembayaran SPP') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-200">
                <div class="p-6 text-gray-900">

                    {{-- HEADER --}}
                    <div class="mb-4">
                        <h3 class="text-lg font-bold text-gray-900">Log Transaksi Pembayaran</h3>
                        <p class="text-sm text-gray-500">Daftar seluruh transaksi pembayaran yang telah berhasil
                            diproses.</p>
                    </div>

                    {{-- FILTER --}}
                    <form method="GET" action="{{ route('admin.keuangan.pembayaran.index') }}"
                        class="p-5 rounded-xl border border-gray-100 mb-6 grid grid-cols-1 md:grid-cols-4 gap-4 items-center">

                        {{-- Pencarian --}}
                        <div class="relative md:col-span-3">
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="Cari Nama / NISN / Kode Bayar..."
                                class="w-full text-sm rounded-full pl-5 pr-10 py-2 border-gray-300">
                            <button type="submit" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </button>
                        </div>

                        {{-- Filter Kelas --}}
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
                    </form>

                    {{-- TABEL --}}
                    <div class="overflow-hidden border border-gray-200 rounded-xl shadow-sm">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-[#3B3E42]">
                                    <tr class="text-xs font-bold text-white uppercase tracking-wider">
                                        <th class="px-4 py-4 text-center w-12">No</th>
                                        <th class="px-4 py-4 text-left">Kode Bayar</th>
                                        <th class="px-4 py-4 text-left">Data Siswa</th>
                                        <th class="px-4 py-4 text-left">Tagihan</th>
                                        <th class="px-4 py-4 text-left">Periode</th>
                                        <th class="px-4 py-4 text-left">Total Nominal</th>
                                        <th class="px-4 py-4 text-center">Status</th>
                                        <th class="px-4 py-4 text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 bg-white">
                                    @forelse($pembayarans as $index => $p)
                                        <tr class="hover:bg-indigo-50/50 transition even:bg-gray-50">

                                            <td class="px-4 py-4 text-center text-sm font-medium text-gray-500">
                                                {{ $pembayarans->firstItem() + $index }}
                                            </td>

                                            <td class="px-4 py-4">
                                                <span
                                                    class="font-mono text-xs font-bold text-gray-600 bg-gray-100 px-2 py-1 rounded border border-gray-200">
                                                    {{ $p->kode_pembayaran }}
                                                </span>
                                                <div class="text-[10px] text-gray-400 mt-1">
                                                    {{ $p->created_at->format('d M Y, H:i') }}
                                                </div>
                                            </td>

                                            <td class="px-4 py-4">
                                                @php
                                                    $kelas = $p->detailPembayaran->first()?->tagihanSpp
                                                        ?->riwayatAkademik?->kelas;
                                                @endphp
                                                <div class="text-sm font-bold text-gray-900 leading-none">
                                                    {{ $p->siswa->nama_lengkap }}
                                                </div>
                                                <div class="text-[11px] text-gray-400 mt-1 font-mono">
                                                    {{ $p->siswa->nisn ?? '-' }}
                                                </div>
                                                @if ($kelas)
                                                    <div class="text-[11px] text-blue-500 font-bold mt-0.5">
                                                        Kelas {{ $kelas->tingkat }} {{ $kelas->nama_kelas }}
                                                    </div>
                                                @endif
                                            </td>

                                            <td class="px-4 py-4 text-sm text-gray-700">
                                                @php
                                                    $tagihanNames = $p->detailPembayaran
                                                        ->map(fn($d) => $d->tagihanSpp?->masterTagihan?->nama_tagihan)
                                                        ->filter()
                                                        ->unique()
                                                        ->values();
                                                @endphp
                                                @forelse($tagihanNames as $nama)
                                                    <div>{{ $nama }}</div>
                                                @empty
                                                    <span class="text-gray-400">-</span>
                                                @endforelse
                                            </td>

                                            <td class="px-4 py-4 text-sm text-gray-500">
                                                @php
                                                    $periodes = $p->detailPembayaran
                                                        ->map(
                                                            fn($d) => $d->tagihanSpp?->bulan
                                                                ? [
                                                                    'bulan' => $d->tagihanSpp->bulan,
                                                                    'tahun' => $d->tagihanSpp->tahun,
                                                                ]
                                                                : null,
                                                        )
                                                        ->filter()
                                                        ->unique()
                                                        ->values();
                                                @endphp
                                                @forelse($periodes as $periode)
                                                    <div>
                                                        <span
                                                            class="font-bold text-gray-700">{{ $periode['bulan'] }}</span>
                                                        {{ $periode['tahun'] }}
                                                    </div>
                                                @empty
                                                    <span class="text-gray-400">-</span>
                                                @endforelse
                                            </td>

                                            <td class="px-4 py-4">
                                                <div class="text-sm font-bold text-[#1072B8]">
                                                    Rp {{ number_format($p->total_bayar, 0, ',', '.') }}
                                                </div>
                                            </td>

                                            <td class="px-4 py-4 text-center">
                                                <span
                                                    class="px-3 py-1 rounded-full text-[10px] font-bold border bg-green-100 text-green-800 border-green-200 inline-flex items-center gap-1">
                                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd"
                                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                    SUCCESS
                                                </span>
                                            </td>
                                            <td class="px-4 py-4 text-center">

                                                <a href="{{ route('admin.keuangan.pembayaran.cetak', $p->id) }}"
                                                    target="_blank"
                                                    class="inline-flex items-center gap-1 px-3 py-1.5 bg-white border border-gray-300 rounded-lg text-xs font-bold text-gray-700 hover:bg-gray-50 hover:text-[#1072B8] transition shadow-sm group">
                                                    <svg class="w-3.5 h-3.5 text-gray-400 group-hover:text-[#1072B8]"
                                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                                    </svg>
                                                    Cetak
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="px-6 py-12 text-center text-gray-400 italic">
                                                Belum ada riwayat pembayaran.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- PAGINATION & SHOW --}}
                    <div class="mt-6 flex flex-col sm:flex-row justify-between items-center gap-4">
                        <form method="GET" action="{{ route('admin.keuangan.pembayaran.index') }}"
                            class="flex items-center gap-2">
                            <input type="hidden" name="search" value="{{ request('search') }}">
                            <input type="hidden" name="kelas_id" value="{{ request('kelas_id') }}">
                            <span class="text-sm text-gray-500 font-medium">Show:</span>
                            <select name="per_page" onchange="this.form.submit()"
                                class="text-sm border-gray-300 rounded-lg shadow-sm focus:border-[#3B3E42] focus:ring-[#3B3E42] py-1 pl-2 pr-8 transition cursor-pointer">
                                <option value="30" {{ request('per_page', 30) == 30 ? 'selected' : '' }}>30
                                </option>
                                <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                                <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                            </select>
                        </form>
                        <div class="w-full sm:w-auto">
                            {{ $pembayarans->appends(request()->query())->links() }}
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
