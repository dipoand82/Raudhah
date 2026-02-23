<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Riwayat Pembayaran SPP</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border p-6">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50 text-xs font-bold uppercase">
                        <tr>
                            <th class="px-4 py-3 text-left">Kode</th>
                            <th class="px-4 py-3 text-left">Siswa</th>
                            <th class="px-4 py-3 text-right">Total Bayar</th>
                            <th class="px-4 py-3 text-center">Status</th>
                            <th class="px-4 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 text-sm">
                        @foreach($pembayarans as $p)
                        <tr>
                            <td class="px-4 py-3 font-mono">{{ $p->kode_pembayaran }}</td>
                            <td class="px-4 py-3">{{ $p->siswa->nama_lengkap }}</td>
                            <td class="px-4 py-3 text-right">Rp {{ number_format($p->total_bayar, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-2 py-1 rounded-full text-[10px] bg-green-100 text-green-800">SUCCESS</span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <a href="#" class="text-indigo-600 hover:underline">Cetak</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
