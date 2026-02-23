<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Rekapitulasi Pembayaran SPP</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 rounded-lg shadow-sm border">

                {{-- Filter Laporan --}}
                <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                    <select name="periode" class="rounded-md border-gray-300 text-sm">
                        <option value="ganjil" {{ request('periode') == 'ganjil' ? 'selected' : '' }}>Semester Ganjil (Juli - Des)</option>
                        <option value="genap" {{ request('periode') == 'genap' ? 'selected' : '' }}>Semester Genap (Jan - Jun)</option>
                    </select>
                    <select name="kelas_id" class="rounded-md border-gray-300 text-sm">
                        <option value="">-- Semua Kelas --</option>
                        @foreach($kelasList as $k)
                            <option value="{{ $k->id }}" {{ request('kelas_id') == $k->id ? 'selected' : '' }}>Kelas {{ $k->nama_kelas }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded-md text-sm">Tampilkan</button>
                    <button type="button" onclick="window.print()" class="border border-gray-300 px-4 py-2 rounded-md text-sm hover:bg-gray-50">Cetak PDF</button>
                </form>

                {{-- Tabel Matriks --}}
                <div class="overflow-x-auto border rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200 text-xs">
                        <thead class="bg-gray-100 font-bold">
                            <tr>
                                <th class="px-3 py-3 border-r sticky left-0 bg-gray-100">NAMA SISWA</th>
                                @foreach($bulanList as $bulan)
                                    <th class="px-2 py-3 text-center border-r">{{ strtoupper($bulan) }}</th>
                                @endforeach
                                <th class="px-3 py-3 text-center bg-indigo-50">TOTAL BAYAR</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($siswas as $riwayat)
                            <tr>
                                <td class="px-3 py-3 border-r font-bold sticky left-0 bg-white uppercase">
                                    {{ $riwayat->siswa->nama_lengkap }}
                                </td>

                                @php $totalPerSiswa = 0; @endphp
                                @foreach($bulanList as $bulan)
                                    @php
                                        $tagihan = $tagihans->get($riwayat->id)?->firstWhere('bulan', $bulan);
                                        $totalPerSiswa += $tagihan->terbayar ?? 0;
                                    @endphp
                                    <td class="px-2 py-3 text-center border-r">
                                        @if(!$tagihan)
                                            <span class="text-gray-300">-</span>
                                        @elseif($tagihan->status == 'lunas')
                                            <span class="text-green-600 font-bold">LUNAS</span>
                                        @elseif($tagihan->status == 'cicilan')
                                            <span class="text-orange-500 font-bold">{{ number_format($tagihan->terbayar / 1000, 0) }}k</span>
                                        @else
                                            <span class="text-red-500 font-bold">0</span>
                                        @endif
                                    </td>
                                @endforeach

                                <td class="px-3 py-3 text-right font-bold bg-indigo-50">
                                    Rp {{ number_format($totalPerSiswa, 0, ',', '.') }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
