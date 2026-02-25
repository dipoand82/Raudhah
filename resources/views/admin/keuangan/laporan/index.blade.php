<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Rekapitulasi Pembayaran') }}
        </h2>
    </x-slot>

    <div class="py-6 print:py-2">
        <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 space-y-4">

            {{-- ============================================================
                 FILTER — rounded pill style
                 ============================================================ --}}
            <div class="bg-white p-4 sm:p-5 rounded-2xl shadow-sm border border-gray-200 print:hidden">
                <form method="GET" action="{{ route('admin.keuangan.laporan.index') }}"
                    class="flex flex-wrap gap-3 items-end">

                    {{-- Semester --}}
                    <div class="flex flex-col gap-1">
                        <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest pl-1"></label>
                        <select name="periode" onchange="this.form.submit()"
                            class="rounded-full border-gray-300 text-sm focus:border-[#1072B8] focus:ring-[#1072B8] py-2 pl-4 pr-8 ">
                            <option value="ganjil" {{ $periode == 'ganjil' ? 'selected' : '' }}>Ganjil (Jul–Des)
                            </option>
                            <option value="genap" {{ $periode == 'genap' ? 'selected' : '' }}>Genap (Jan–Jun)</option>
                        </select>
                    </div>

                    {{-- Tahun --}}
                    {{-- Tahun --}}
                    <div class="flex flex-col gap-1">
                        {{-- Tambahkan teks label agar user tahu ini filter apa --}}
                        <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest pl-1"></label>

                        <select name="tahun" onchange="this.form.submit()"
                            class="rounded-full border-gray-300 text-sm focus:border-[#1072B8] focus:ring-[#1072B8] py-2 pl-4 pr-8 cursor-pointer">

                            {{-- Opsi default atau placeholder --}}
                            <option value="">Pilih Tahun</option>

                            {{-- Loop Tahun: Dimulai dari tahun depan sampai tahun 2027 (sesuai data awal Anda) --}}
                            @for ($y = date('Y') + 1; $y >= 2026; $y--)
                                <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>
                                    {{ $y }}
                                </option>
                            @endfor
                        </select>
                    </div>

                    {{-- Kelas --}}
                    <div class="flex flex-col gap-1">
                        <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest pl-1"></label>
                        <select name="kelas_id" onchange="this.form.submit()"
                            class="rounded-full border-gray-300 text-sm focus:border-[#1072B8] focus:ring-[#1072B8] py-2 pl-4 pr-8 ">
                            <option value="">-- Semua Kelas --</option>
                            @foreach ($kelasList as $k)
                                <option value="{{ $k->id }}"
                                    {{ request('kelas_id') == $k->id ? 'selected' : '' }}>
                                    Kelas {{ $k->tingkat }} {{ $k->nama_kelas }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Spacer dorong tombol ke kanan --}}
                    <div class="flex-1"></div>

                    {{-- Tombol Cetak Excel --}}
                    <a href="{{ route('admin.keuangan.laporan.export', request()->query()) }}"
                        class="inline-flex items-center justify-center gap-2 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-semibold text-sm transition shadow-sm whitespace-nowrap">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Export Laporan
                    </a>

                    {{-- Tombol Cetak PDF
                    <button type="button" onclick="window.print()"
                        class="inline-flex items-center gap-2 border border-gray-300 bg-white hover:bg-gray-50 text-gray-700 px-5 py-2 rounded-full text-sm font-bold transition shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        Cetak PDF
                    </button> --}}

                    {{-- Hidden fields agar per_page & query lain ikut submit --}}
                    <input type="hidden" name="tahun" value="{{ $tahun }}">
                    <input type="hidden" name="per_page" value="{{ $perPage }}">
                </form>
            </div>

            {{-- JUDUL CETAK --}}
            <div class="hidden print:block text-center mb-4">
                <h1 class="text-xl font-bold uppercase">Rekapitulasi Pembayaran</h1>
                <p class="text-sm text-gray-600">
                    Semester {{ ucfirst($periode) }} — Tahun {{ $tahun }}
                    @if (request('kelas_id'))
                        @php $kelasLabel = $kelasList->firstWhere('id', request('kelas_id')); @endphp
                        | Kelas {{ $kelasLabel?->tingkat }} {{ $kelasLabel?->nama_kelas }}
                    @endif
                </p>
                <p class="text-xs text-gray-400 mt-1">Dicetak: {{ now()->format('d/m/Y H:i') }}</p>
            </div>

            {{-- ============================================================
                 TABEL MATRIKS
                 ============================================================ --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto" style="max-height: 72vh; overflow-y: auto;">
                    <table class="min-w-full divide-y divide-gray-200 text-[11px]" id="rekap-table">

                        {{-- THEAD --}}
                        <thead class="bg-[#3B3E42] sticky top-0 z-20">

                            {{-- Baris 1: kelompok jenis tagihan --}}
                            <tr>
                                <th rowspan="2"
                                    class="px-2 py-3 text-center text-white font-bold uppercase tracking-wider sticky left-0 z-30 bg-[#3B3E42] border-r border-gray-600 w-10">
                                    No
                                </th>
                                <th rowspan="2"
                                    class="px-4 py-3 text-left text-white font-bold uppercase tracking-wider sticky left-10 z-30 bg-[#3B3E42] border-r border-gray-600 min-w-[170px] shadow-[2px_0_6px_rgba(0,0,0,0.2)]">
                                    Data Siswa
                                </th>

                                @foreach ($masterTagihans as $master)
                                    @php
                                        $isSPP = stripos($master->nama_tagihan, 'spp') !== false;
                                        $colspan = $isSPP ? count($bulanList) : 1;
                                    @endphp
                                    <th colspan="{{ $colspan }}"
                                        class="px-2 py-2 text-center text-white font-bold uppercase tracking-wider border-r border-gray-500
                                               {{ $isSPP ? 'bg-[#1072B8]' : 'bg-[#4A5568]' }}">
                                        {{ $master->nama_tagihan }}
                                        <span class="block text-[9px] font-normal opacity-80 normal-case mt-0.5">
                                            Rp {{ number_format($master->nominal, 0, ',', '.') }}
                                        </span>
                                    </th>
                                @endforeach

                                <th rowspan="2"
                                    class="px-3 py-3 text-center text-white font-bold uppercase tracking-wider bg-[#0d5a91] min-w-[100px]">
                                    Total<br>Terbayar
                                </th>
                            </tr>

                            {{-- Baris 2: sub-header bulan / status --}}
                            <tr>
                                @foreach ($masterTagihans as $master)
                                    @php $isSPP = stripos($master->nama_tagihan, 'spp') !== false; @endphp
                                    @if ($isSPP)
                                        @foreach ($bulanList as $bulan)
                                            <th
                                                class="px-1 py-2 text-center text-white font-semibold border-r border-gray-600 border-t border-gray-500 whitespace-nowrap text-[9px]">
                                                {{ strtoupper(substr($bulan, 0, 3)) }}
                                            </th>
                                        @endforeach
                                    @else
                                        <th
                                            class="px-2 py-2 text-center text-white font-semibold border-r border-gray-600 border-t border-gray-500 text-[9px]">
                                            STATUS
                                        </th>
                                    @endif
                                @endforeach
                            </tr>
                        </thead>

                        {{-- TBODY --}}
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @php $grandTotal = 0; @endphp

                            @forelse($siswas as $loopIndex => $riwayat)
                                @php
                                    $totalPerSiswa = 0;
                                    $siswaId = $riwayat->id;
                                    $isEven = $loop->even;
                                @endphp
                                <tr
                                    class="hover:bg-indigo-50/40 transition {{ $isEven ? 'bg-gray-50/60' : 'bg-white' }}">

                                    {{-- No --}}
                                    <td
                                        class="px-2 py-3 text-center text-gray-500 font-medium sticky left-0 z-10 border-r border-gray-100
                                               {{ $isEven ? 'bg-gray-50' : 'bg-white' }}">
                                        {{ $siswas->firstItem() + $loopIndex }}
                                    </td>

                                    {{-- Nama --}}
                                    <td
                                        class="px-4 py-3 sticky left-10 z-10 border-r border-gray-100 shadow-[2px_0_4px_rgba(0,0,0,0.04)]
           {{ $isEven ? 'bg-gray-50' : 'bg-white' }}">
                                        <div class="font-bold text-gray-900 leading-tight uppercase text-[11px]">
                                            {{ $riwayat->siswa->nama_lengkap }}
                                        </div>
                                        <div class="text-[9px] text-gray-400 font-mono mt-0.5">
                                            {{ $riwayat->siswa->nisn ?? '-' }}
                                        </div>
                                        <div class="text-[9px] text-blue-400 font-bold mt-0.5">
                                            Kelas {{ $riwayat->kelas->tingkat ?? '' }}
                                            {{ $riwayat->kelas->nama_kelas ?? '-' }}
                                        </div>
                                    </td>

                                    {{-- Sel per jenis tagihan --}}
                                    @foreach ($masterTagihans as $master)
                                        @php $isSPP = stripos($master->nama_tagihan, 'spp') !== false; @endphp

                                        @if ($isSPP)
                                            @foreach ($bulanList as $bulan)
                                                @php
                                                    $tagihan = $tagihans[$siswaId][$master->id][$bulan] ?? null;
                                                    $terbayar = $tagihan->terbayar ?? 0;
                                                    $totalPerSiswa += $terbayar;
                                                @endphp
                                                <td
                                                    class="px-1 py-3 text-center border-r border-gray-50 whitespace-nowrap">
                                                    @if (!$tagihan)
                                                        <span class="text-gray-200">—</span>
                                                    @elseif($tagihan->status === 'lunas')
                                                        <span
                                                            class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-green-100 border border-green-300">
                                                            <svg class="w-2.5 h-2.5 text-green-600" fill="currentColor"
                                                                viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd"
                                                                    d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                                    clip-rule="evenodd" />
                                                            </svg>
                                                        </span>
                                                    @elseif($tagihan->status === 'cicilan')
                                                        <span class="text-orange-500 font-bold font-mono text-[9px]">
                                                            {{ number_format($terbayar / 1000, 0) }}k
                                                        </span>
                                                    @else
                                                        <span class="text-red-400 font-bold text-[9px]">✗</span>
                                                    @endif
                                                </td>
                                            @endforeach
                                        @else
                                            @php
                                                $tagihan = collect($tagihans[$siswaId][$master->id] ?? [])->first();
                                                $terbayar = $tagihan->terbayar ?? 0;
                                                $totalPerSiswa += $terbayar;
                                            @endphp
                                            <td class="px-2 py-3 text-center border-r border-gray-50">
                                                @if (!$tagihan)
                                                    <span class="text-gray-200">—</span>
                                                @elseif($tagihan->status === 'lunas')
                                                    <span
                                                        class="inline-flex px-2 py-0.5 rounded-full bg-green-100 text-green-700 font-extrabold text-[8px] border border-green-200">LUNAS</span>
                                                @elseif($tagihan->status === 'cicilan')
                                                    <span
                                                        class="text-orange-500 font-bold font-mono text-[9px]">{{ number_format($terbayar / 1000, 0) }}k</span>
                                                @else
                                                    <span
                                                        class="inline-flex px-2 py-0.5 rounded-full bg-red-100 text-red-600 font-extrabold text-[8px] border border-red-200">BELUM</span>
                                                @endif
                                            </td>
                                        @endif
                                    @endforeach

                                    {{-- Total per siswa --}}
                                    @php $grandTotal += $totalPerSiswa; @endphp
                                    <td
                                        class="px-3 py-3 text-right font-mono font-bold text-[#1072B8] bg-blue-50 border-l border-blue-100 whitespace-nowrap">
                                        Rp {{ number_format($totalPerSiswa, 0, ',', '.') }}
                                    </td>

                                </tr>
                            @empty
                                <tr>
                                    <td colspan="99" class="px-6 py-14 text-center text-gray-400 italic">
                                        Tidak ada data siswa untuk filter ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>


                        {{-- TFOOT: Grand Total pojok kanan --}}
                        @if ($siswas->count() > 0)
                            <tfoot class="sticky bottom-0 z-20">
                                <tr class="bg-[#1e2a3a]">
                                    <td colspan="2"
                                        class="px-4 py-3 text-right text-white font-extrabold uppercase text-[10px] tracking-widest sticky left-0 bg-[#1e2a3a] border-r border-gray-600">
                                        TOTAL
                                    </td>
                                    @foreach ($masterTagihans as $master)
                                        @php $isSPP = stripos($master->nama_tagihan, 'spp') !== false; @endphp
                                        @if ($isSPP)
                                            @foreach ($bulanList as $bulan)
                                                <td class="border-r border-gray-700 bg-[#1e2a3a]"></td>
                                            @endforeach
                                        @else
                                            <td class="border-r border-gray-700 bg-[#1e2a3a]"></td>
                                        @endif
                                    @endforeach
                                    <td
                                        class="px-3 py-3 text-right text-white font-extrabold font-mono bg-[#1072B8] whitespace-nowrap text-[11px]">
                                        Rp {{ number_format($grandTotal, 0, ',', '.') }}
                                    </td>
                                </tr>
                            </tfoot>
                        @endif

                    </table>
                </div>
            </div>

            {{-- ============================================================
                 FOOTER: Info + Show per page + Pagination
                 ============================================================ --}}
            <div class="flex flex-col sm:flex-row justify-between items-center gap-4 print:hidden">

                {{-- Kiri: info jumlah & show per page --}}
                <div class="flex items-center gap-3">
                    <span class="text-sm text-gray-500">Total <strong>{{ $siswas->total() }}</strong> siswa
                    </span>

                    <form method="GET" action="{{ route('admin.keuangan.laporan.index') }}"
                        class="flex items-center gap-2">
                        {{-- Pertahankan filter aktif --}}
                        <input type="hidden" name="periode" value="{{ $periode }}">
                        <input type="hidden" name="tahun" value="{{ $tahun }}">
                        <input type="hidden" name="kelas_id" value="{{ request('kelas_id') }}">

                        <label class="text-sm text-gray-500 font-medium">Show:</label>
                        <select name="per_page" onchange="this.form.submit()"
                            class="text-sm border-gray-300 rounded-full shadow-sm focus:border-[#1072B8] focus:ring-[#1072B8] py-1 pl-3 pr-8 cursor-pointer">
                            @foreach ([30, 50, 100, 300, 500, 800] as $opt)
                                <option value="{{ $opt }}" {{ $perPage == $opt ? 'selected' : '' }}>
                                    {{ $opt }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                </div>

                {{-- Kanan: Pagination links --}}
                <div>
                    {{ $siswas->appends(request()->query())->links() }}
                </div>
            </div>

        </div>
    </div>

    <style>
        @media print {
            @page {
                size: landscape;
                margin: 0.8cm;
            }

            body {
                font-size: 8px !important;
            }

            #rekap-table thead,
            #rekap-table tfoot {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .overflow-x-auto {
                overflow: visible !important;
                max-height: none !important;
            }
        }
    </style>
</x-app-layout>
