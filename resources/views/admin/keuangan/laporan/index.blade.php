<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Rekapitulasi Pembayaran') }}
        </h2>
    </x-slot>

    <div class="py-6 print:py-2">
        <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 space-y-4">

            {{-- ============================================================
                 FILTER (tersembunyi saat cetak)
                 ============================================================ --}}
            <div class="bg-white p-4 sm:p-5 rounded-xl shadow-sm border border-gray-200 print:hidden">
                <form method="GET" action="{{ route('admin.keuangan.laporan.index') }}"
                      class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 items-end">

                    {{-- Semester --}}
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-gray-600 uppercase tracking-wide">Semester</label>
                        <select name="periode"
                            class="w-full rounded-lg border-gray-300 text-sm focus:border-[#1072B8] focus:ring-[#1072B8] py-2">
                            <option value="ganjil" {{ $periode == 'ganjil' ? 'selected' : '' }}>Ganjil (Jul–Des)</option>
                            <option value="genap"  {{ $periode == 'genap'  ? 'selected' : '' }}>Genap (Jan–Jun)</option>
                        </select>
                    </div>

                    {{-- Tahun --}}
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-gray-600 uppercase tracking-wide">Tahun</label>
                        <input type="number" name="tahun" value="{{ $tahun }}"
                               min="2020" max="{{ date('Y') + 1 }}"
                               class="w-full rounded-lg border-gray-300 text-sm focus:border-[#1072B8] focus:ring-[#1072B8] py-2">
                    </div>

                    {{-- Kelas --}}
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-gray-600 uppercase tracking-wide">Kelas</label>
                        <select name="kelas_id"
                            class="w-full rounded-lg border-gray-300 text-sm focus:border-[#1072B8] focus:ring-[#1072B8] py-2">
                            <option value="">— Semua Kelas —</option>
                            @foreach($kelasList as $k)
                                <option value="{{ $k->id }}" {{ request('kelas_id') == $k->id ? 'selected' : '' }}>
                                    Kelas {{ $k->tingkat }} {{ $k->nama_kelas }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Per Page --}}
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-gray-600 uppercase tracking-wide">Tampilkan</label>
                        <select name="per_page"
                            class="w-full rounded-lg border-gray-300 text-sm focus:border-[#1072B8] focus:ring-[#1072B8] py-2">
                            <option value="30"  {{ request('per_page', 30) == 30  ? 'selected' : '' }}>30 Siswa</option>
                            <option value="50"  {{ request('per_page')     == 50  ? 'selected' : '' }}>50 Siswa</option>
                            <option value="100" {{ request('per_page')     == 100 ? 'selected' : '' }}>100 Siswa</option>
                        </select>
                    </div>

                    {{-- Tombol --}}
                    <div class="flex gap-2">
                        <button type="submit"
                            class="flex-1 bg-[#1072B8] hover:bg-[#0d5a91] text-white px-4 py-2 rounded-lg text-sm font-bold shadow-sm transition flex items-center justify-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
                            </svg>
                            Tampilkan
                        </button>
                        <button type="button" onclick="window.print()"
                            class="flex-1 border border-gray-300 bg-white hover:bg-gray-50 text-gray-700 px-4 py-2 rounded-lg text-sm font-bold transition flex items-center justify-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                            </svg>
                            Cetak
                        </button>
                    </div>
                </form>
            </div>

            {{-- ============================================================
                 JUDUL (hanya muncul saat cetak)
                 ============================================================ --}}
            <div class="hidden print:block text-center mb-4">
                <h1 class="text-xl font-bold uppercase">Rekapitulasi Pembayaran</h1>
                <p class="text-sm text-gray-600">
                    Semester {{ ucfirst($periode) }} — Tahun {{ $tahun }}
                    @if(request('kelas_id'))
                        @php $kelasLabel = $kelasList->firstWhere('id', request('kelas_id')); @endphp
                        | Kelas {{ $kelasLabel?->tingkat }} {{ $kelasLabel?->nama_kelas }}
                    @endif
                </p>
                <p class="text-xs text-gray-400 mt-1">Dicetak: {{ now()->format('d/m/Y H:i') }}</p>
            </div>

            {{-- ============================================================
                 TABEL MATRIKS
                 ============================================================ --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto" style="max-height: 72vh; overflow-y: auto;">
                    <table class="min-w-full divide-y divide-gray-200 text-[11px]" id="rekap-table">

                        {{-- THEAD --}}
                        <thead class="bg-[#3B3E42] sticky top-0 z-20">

                            {{-- Baris 1: Kelompok jenis tagihan --}}
                            <tr>
                                {{-- No --}}
                                <th rowspan="2"
                                    class="px-2 py-3 text-center text-white font-bold uppercase tracking-wider sticky left-0 z-30 bg-[#3B3E42] border-r border-gray-600 w-10">
                                    No
                                </th>
                                {{-- Nama --}}
                                <th rowspan="2"
                                    class="px-4 py-3 text-left text-white font-bold uppercase tracking-wider sticky left-10 z-30 bg-[#3B3E42] border-r border-gray-600 min-w-[170px] shadow-[2px_0_6px_rgba(0,0,0,0.2)]">
                                    Nama Siswa
                                </th>
                                {{-- Kelas --}}
                                <th rowspan="2"
                                    class="px-3 py-3 text-center text-white font-bold uppercase tracking-wider border-r border-gray-600 min-w-[70px]">
                                    Kelas
                                </th>

                                {{-- Kolom per Jenis Tagihan --}}
                                @foreach($masterTagihans as $master)
                                    @php
                                        $isSPP   = stripos($master->nama_tagihan, 'spp') !== false;
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

                                {{-- Total --}}
                                <th rowspan="2"
                                    class="px-3 py-3 text-center text-white font-bold uppercase tracking-wider bg-[#0d5a91] min-w-[100px]">
                                    Total<br>Terbayar
                                </th>
                            </tr>

                            {{-- Baris 2: Sub-header bulan / status --}}
                            <tr>
                                @foreach($masterTagihans as $master)
                                    @php $isSPP = stripos($master->nama_tagihan, 'spp') !== false; @endphp
                                    @if($isSPP)
                                        @foreach($bulanList as $bulan)
                                            <th class="px-1 py-2 text-center text-white font-semibold border-r border-gray-600 border-t border-gray-500 whitespace-nowrap text-[9px]">
                                                {{ strtoupper(substr($bulan, 0, 3)) }}
                                            </th>
                                        @endforeach
                                    @else
                                        <th class="px-2 py-2 text-center text-white font-semibold border-r border-gray-600 border-t border-gray-500 text-[9px]">
                                            STATUS
                                        </th>
                                    @endif
                                @endforeach
                            </tr>
                        </thead>

                        {{-- TBODY --}}
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @php
                                // Akumulator total kolom
                                $colTotals = [];
                                $grandTotal = 0;
                            @endphp

                            @forelse($siswas as $loopIndex => $riwayat)
                                @php
                                    $totalPerSiswa = 0;
                                    $siswaId = $riwayat->id;
                                    $isEven  = $loop->even;
                                @endphp
                                <tr class="hover:bg-indigo-50/40 transition {{ $isEven ? 'bg-gray-50/60' : 'bg-white' }}">

                                    {{-- No --}}
                                    <td class="px-2 py-3 text-center text-gray-500 font-medium sticky left-0 z-10 border-r border-gray-100
                                               {{ $isEven ? 'bg-gray-50' : 'bg-white' }}">
                                        {{ $siswas->firstItem() + $loopIndex }}
                                    </td>

                                    {{-- Nama --}}
                                    <td class="px-4 py-3 sticky left-10 z-10 border-r border-gray-100 shadow-[2px_0_4px_rgba(0,0,0,0.04)]
                                               {{ $isEven ? 'bg-gray-50' : 'bg-white' }}">
                                        <div class="font-bold text-gray-900 leading-tight uppercase text-[11px]">
                                            {{ $riwayat->siswa->nama_lengkap }}
                                        </div>
                                        <div class="text-[9px] text-gray-400 font-mono mt-0.5">
                                            {{ $riwayat->siswa->nisn ?? '-' }}
                                        </div>
                                    </td>

                                    {{-- Kelas --}}
                                    <td class="px-2 py-3 text-center border-r border-gray-100">
                                        <span class="inline-flex px-1.5 py-0.5 rounded bg-blue-100 text-blue-800 font-bold text-[9px] border border-blue-200 whitespace-nowrap">
                                            {{ $riwayat->kelas->tingkat ?? '' }}
                                            {{ $riwayat->kelas->nama_kelas ?? '-' }}
                                        </span>
                                    </td>

                                    {{-- Sel per jenis tagihan --}}
                                    @foreach($masterTagihans as $master)
                                        @php $isSPP = stripos($master->nama_tagihan, 'spp') !== false; @endphp

                                        @if($isSPP)
                                            {{-- SPP: satu sel per bulan --}}
                                            @foreach($bulanList as $bulan)
                                                @php
                                                    $tagihan = $tagihans[$siswaId][$master->id][$bulan] ?? null;
                                                    $terbayar = $tagihan->terbayar ?? 0;
                                                    $totalPerSiswa += $terbayar;

                                                    $colKey = $master->id . ':' . $bulan;
                                                    $colTotals[$colKey] = ($colTotals[$colKey] ?? 0) + $terbayar;
                                                @endphp
                                                <td class="px-1 py-3 text-center border-r border-gray-50 whitespace-nowrap">
                                                    @if(!$tagihan)
                                                        <span class="text-gray-200">—</span>
                                                    @elseif($tagihan->status === 'lunas')
                                                        <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-green-100 border border-green-300">
                                                            <svg class="w-2.5 h-2.5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
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
                                            {{-- Non-SPP: satu sel status --}}
                                            @php
                                                $tagihan  = collect($tagihans[$siswaId][$master->id] ?? [])->first();
                                                $terbayar = $tagihan->terbayar ?? 0;
                                                $totalPerSiswa += $terbayar;

                                                $colKey = 'master:' . $master->id;
                                                $colTotals[$colKey] = ($colTotals[$colKey] ?? 0) + $terbayar;
                                            @endphp
                                            <td class="px-2 py-3 text-center border-r border-gray-50">
                                                @if(!$tagihan)
                                                    <span class="text-gray-200">—</span>
                                                @elseif($tagihan->status === 'lunas')
                                                    <span class="inline-flex px-2 py-0.5 rounded-full bg-green-100 text-green-700 font-extrabold text-[8px] border border-green-200">
                                                        LUNAS
                                                    </span>
                                                @elseif($tagihan->status === 'cicilan')
                                                    <span class="text-orange-500 font-bold font-mono text-[9px]">
                                                        {{ number_format($terbayar / 1000, 0) }}k
                                                    </span>
                                                @else
                                                    <span class="inline-flex px-2 py-0.5 rounded-full bg-red-100 text-red-600 font-extrabold text-[8px] border border-red-200">
                                                        BELUM
                                                    </span>
                                                @endif
                                            </td>
                                        @endif
                                    @endforeach

                                    {{-- Total per siswa --}}
                                    @php $grandTotal += $totalPerSiswa; @endphp
                                    <td class="px-3 py-3 text-right font-mono font-bold text-[#1072B8] bg-blue-50 border-l border-blue-100 whitespace-nowrap">
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

                        {{-- TFOOT: Total per kolom --}}
                        @if($siswas->count() > 0)
                        <tfoot class="sticky bottom-0 z-20">
                            <tr class="bg-[#1e2a3a]">
                                {{-- Label --}}
                                <td colspan="2"
                                    class="px-4 py-3 text-right text-white font-extrabold uppercase text-[10px] tracking-widest sticky left-0 bg-[#1e2a3a] border-r border-gray-600">
                                    TOTAL ↓
                                </td>
                                {{-- Kelas kosong --}}
                                <td class="border-r border-gray-600 bg-[#1e2a3a]"></td>

                                @foreach($masterTagihans as $master)
                                    @php $isSPP = stripos($master->nama_tagihan, 'spp') !== false; @endphp
                                    @if($isSPP)
                                        @foreach($bulanList as $bulan)
                                            @php
                                                $colKey   = $master->id . ':' . $bulan;
                                                $colTotal = $colTotals[$colKey] ?? 0;
                                            @endphp
                                            <td class="px-1 py-3 text-center text-white font-bold font-mono border-r border-gray-600 whitespace-nowrap text-[9px]">
                                                @if($colTotal > 0)
                                                    {{ number_format($colTotal / 1000, 0) }}k
                                                @else
                                                    <span class="opacity-25">—</span>
                                                @endif
                                            </td>
                                        @endforeach
                                    @else
                                        @php
                                            $colKey   = 'master:' . $master->id;
                                            $colTotal = $colTotals[$colKey] ?? 0;
                                        @endphp
                                        <td class="px-2 py-3 text-center text-white font-bold font-mono border-r border-gray-600 whitespace-nowrap text-[9px]">
                                            @if($colTotal > 0)
                                                {{ number_format($colTotal / 1000, 0) }}k
                                            @else
                                                <span class="opacity-25">—</span>
                                            @endif
                                        </td>
                                    @endif
                                @endforeach

                                {{-- Grand Total pojok kanan bawah --}}
                                <td class="px-3 py-3 text-right text-white font-extrabold font-mono bg-[#1072B8] whitespace-nowrap text-[10px]">
                                    Rp {{ number_format($grandTotal, 0, ',', '.') }}
                                </td>
                            </tr>
                        </tfoot>
                        @endif

                    </table>
                </div>
            </div>

            {{-- PAGINATION + INFO --}}
            <div class="flex flex-col sm:flex-row justify-between items-center gap-4 print:hidden">
                <p class="text-sm text-gray-500">
                    Menampilkan <strong>{{ $siswas->firstItem() }}</strong>–<strong>{{ $siswas->lastItem() }}</strong>
                    dari <strong>{{ $siswas->total() }}</strong> siswa
                </p>
                <div>
                    {{ $siswas->appends(request()->query())->links() }}
                </div>
            </div>

            {{-- LEGENDA --}}
            <div class="flex flex-wrap gap-3 text-[10px] print:hidden">
                <div class="flex items-center gap-1.5 bg-white px-3 py-1.5 rounded-lg border border-gray-200 shadow-sm">
                    <span class="inline-flex items-center justify-center w-4 h-4 rounded-full bg-green-100 border border-green-300">
                        <svg class="w-2 h-2 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                    </span>
                    <span class="text-gray-600">Lunas</span>
                </div>
                <div class="flex items-center gap-1.5 bg-white px-3 py-1.5 rounded-lg border border-gray-200 shadow-sm">
                    <span class="text-orange-500 font-bold font-mono font-semibold">150k</span>
                    <span class="text-gray-600">Cicilan (nominal terbayar)</span>
                </div>
                <div class="flex items-center gap-1.5 bg-white px-3 py-1.5 rounded-lg border border-gray-200 shadow-sm">
                    <span class="text-red-400 font-bold">✗</span>
                    <span class="text-gray-600">Belum Bayar</span>
                </div>
                <div class="flex items-center gap-1.5 bg-white px-3 py-1.5 rounded-lg border border-gray-200 shadow-sm">
                    <span class="text-gray-300 font-bold">—</span>
                    <span class="text-gray-600">Tidak Ada Tagihan</span>
                </div>
                <div class="flex items-center gap-1.5 bg-white px-3 py-1.5 rounded-lg border border-gray-200 shadow-sm">
                    <span class="px-1.5 py-0.5 rounded bg-[#1072B8] text-white text-[8px] font-bold">SPP</span>
                    <span class="text-gray-600">Tagihan per bulan</span>
                </div>
                <div class="flex items-center gap-1.5 bg-white px-3 py-1.5 rounded-lg border border-gray-200 shadow-sm">
                    <span class="px-1.5 py-0.5 rounded bg-[#4A5568] text-white text-[8px] font-bold">LAIN</span>
                    <span class="text-gray-600">Tagihan non-bulanan</span>
                </div>
            </div>

        </div>
    </div>

    {{-- PRINT STYLES --}}
    <style>
        @media print {
            @page { size: landscape; margin: 0.8cm; }
            body  { font-size: 8px !important; }
            #rekap-table thead,
            #rekap-table tfoot { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .overflow-x-auto { overflow: visible !important; max-height: none !important; }
        }
    </style>
</x-app-layout>