<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Data Tagihan Siswa') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="tagihanManager()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Alert Notifications --}}
            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative shadow-sm"
                    role="alert">
                    <span class="block sm:inline font-semibold">{{ session('success') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100">
                <div class="p-6 text-gray-900">

                    {{-- Header & Tombol Generate --}}
                    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
                        <div>
                            <h3 class="text-lg font-bold text-gray-800">Daftar Tagihan Siswa</h3>
                            <p class="text-sm text-gray-500">Pilih satu atau lebih tagihan untuk diproses.</p>
                        </div>
                        <div class="flex gap-2">
                            <a href="{{ route('admin.keuangan.tagihan.create-bulk') }}"
                                class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-md text-sm font-semibold shadow-sm hover:bg-gray-50 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4"></path>
                                </svg>
                                Generate Tagihan
                            </a>
                            {{-- TOMBOL AKSI BARU: Lunasi Terpilih --}}
                            <form action="{{ route('admin.keuangan.pembayaran.store') }}" method="POST"
                                id="form-pembayaran-massal">
                                @csrf
                                <input type="hidden" name="siswa_id" :value="selectedSiswaId">
                                <template x-for="id in selectedIds" :key="id">
                                    <input type="hidden" name="tagihan_ids[]" :value="id">
                                </template>
                                <input type="hidden" name="jumlah_bayar_total" :value="totalTagihan">
                                <input type="hidden" name="metode" value="tunai"> {{-- Default tunai, bisa diubah di modal --}}

                                <button type="button" @click="submitPembayaran()" :disabled="selectedIds.length === 0"
                                    :class="selectedIds.length === 0 ? 'bg-blue-300 cursor-not-allowed' :
                                        'bg-indigo-600 hover:bg-indigo-700'"
                                    class="text-white px-4 py-2 rounded-md text-sm font-bold shadow transition-all flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z">
                                        </path>
                                    </svg>
                                    Lunasi Terpilih (<span x-text="selectedIds.length">0</span>)
                                </button>
                            </form>
                        </div>
                    </div>

                    {{-- FILTER BOX (Tetap Menggunakan Desain Lama) --}}
                    <form method="GET" action="{{ route('admin.keuangan.tagihan.index') }}"
                        class="bg-gray-50 p-4 rounded-lg border mb-6 grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <x-text-input name="search" value="{{ request('search') }}" class="w-full text-sm"
                                placeholder="Cari Nama / NISN..." />
                        </div>
                        <div>
                            <select name="kelas_id"
                                class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm">
                                <option value="">-- Semua Kelas --</option>
                                @foreach ($kelasList as $k)
                                    <option value="{{ $k->id }}"
                                        {{ request('kelas_id') == $k->id ? 'selected' : '' }}>
                                        Kelas {{ $k->tingkat }} {{ $k->nama_kelas }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <select name="status"
                                class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm">
                                <option value="">-- Semua Status --</option>
                                <option value="belum_lunas" {{ request('status') == 'belum_lunas' ? 'selected' : '' }}>
                                    Belum Lunas</option>
                                <option value="cicilan" {{ request('status') == 'cicilan' ? 'selected' : '' }}>Cicilan
                                </option>
                                <option value="lunas" {{ request('status') == 'lunas' ? 'selected' : '' }}>Lunas
                                </option>
                            </select>
                        </div>
                        <div class="flex gap-2">
                            <button type="submit"
                                class="bg-gray-800 text-white px-4 py-2 rounded-md text-sm font-semibold hover:bg-gray-700 w-full">Filter</button>
                            <a href="{{ route('admin.keuangan.tagihan.index') }}"
                                class="bg-white border text-gray-700 px-4 py-2 rounded-md text-sm font-semibold hover:bg-gray-100 text-center flex items-center justify-center">Reset</a>
                        </div>
                    </form>

                    {{-- TABEL DATA DENGAN CHECKBOX --}}
                    <div class="overflow-x-auto border rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-center">
                                        <input type="checkbox" @click="toggleAll($event)"
                                            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                    </th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                        Siswa</th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                        Tagihan</th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                        Periode</th>
                                    <th
                                        class="px-4 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">
                                        Nominal</th>
                                    <th
                                        class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">
                                        Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($tagihans as $tagihan)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-4 py-3 text-center">
                                            @if ($tagihan->status !== 'lunas')
                                                <input type="checkbox" value="{{ $tagihan->id }}"
                                                    data-nominal="{{ $tagihan->jumlah_tagihan - $tagihan->terbayar }}"
                                                    data-siswa-id="{{ $tagihan->riwayatAkademik->siswa->id }}"
                                                    @change="updateSelection($event)"
                                                    class="tagihan-checkbox rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                            @else
                                                <svg class="w-5 h-5 text-green-500 mx-auto" fill="currentColor"
                                                    viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                        clip-rule="evenodd"></path>
                                                </svg>
                                            @endif
                                        </td>
                                        {{-- Sisa kolom tetap sama dengan desain lama --}}
                                        <td class="px-4 py-3">
                                            <div class="text-sm font-bold text-gray-900">
                                                {{ $tagihan->riwayatAkademik->siswa->nama_lengkap ?? 'N/A' }}</div>
                                            <div class="text-xs text-gray-500">
                                                {{ $tagihan->riwayatAkademik->siswa->nisn ?? '-' }}</div>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-900">
                                            {{ $tagihan->masterTagihan->nama_tagihan ?? 'Tagihan Terhapus' }}
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-500">
                                            <span class="font-semibold">{{ $tagihan->bulan }}</span>
                                            {{ $tagihan->tahun }}
                                        </td>
                                        <td class="px-4 py-3 text-right text-sm">
                                            <div class="font-bold text-gray-900">Rp
                                                {{ number_format($tagihan->jumlah_tagihan, 0, ',', '.') }}</div>
                                            @if ($tagihan->terbayar > 0)
                                                <div class="text-[10px] text-orange-600 font-semibold">Sisa: Rp
                                                    {{ number_format($tagihan->jumlah_tagihan - $tagihan->terbayar, 0, ',', '.') }}
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <span
                                                class="px-2 py-1 rounded-full text-[10px] font-bold
                                                {{ $tagihan->status === 'lunas' ? 'bg-green-100 text-green-800' : ($tagihan->status === 'cicilan' ? 'bg-orange-100 text-orange-800' : 'bg-red-100 text-red-800') }}">
                                                {{ strtoupper($tagihan->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-8 text-center text-gray-500 italic">Tidak
                                            ada data tagihan.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <div x-show="showModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
                            <div class="flex items-center justify-center min-h-screen px-4">
                                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

                                <div
                                    class="bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:max-w-lg sm:w-full p-6">
                                    <h3 class="text-lg font-bold text-gray-900 mb-4">Konfirmasi Pembayaran</h3>

                                    <div class="space-y-4">
                                        <div class="p-3 bg-gray-50 rounded-md">
                                            <p class="text-sm text-gray-600">Total Tagihan Terpilih:</p>
                                            <p class="text-xl font-bold text-indigo-600"
                                                x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(totalTagihan)">
                                            </p>
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Jumlah Bayar
                                                (Rp)</label>
                                            <input type="number" x-model="jumlahBayarInput"
                                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                            <p class="text-[10px] text-gray-500 mt-1">*Ubah jika siswa membayar dicicil
                                                (kurang dari total).</p>
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Metode
                                                Pembayaran</label>
                                            <select x-model="metodePembayaran"
                                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                                <option value="tunai">Tunai (Offline)</option>
                                                <option value="midtrans">Midtrans (Online Gateway)</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="mt-6 flex justify-end gap-3">
                                        <button @click="showModal = false"
                                            class="bg-white border px-4 py-2 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">Batal</button>
                                        <button @click="executePayment()"
                                            class="bg-indigo-600 text-white px-4 py-2 rounded-md text-sm font-bold hover:bg-indigo-700">
                                            Proses Sekarang
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Info Total Floating (Hanya muncul jika ada yang dipilih) --}}
                    <div x-show="selectedIds.length > 0"
                        class="mt-4 p-4 bg-indigo-50 border border-indigo-100 rounded-lg flex justify-between items-center">
                        <span class="text-sm text-indigo-700 font-medium">Total Terpilih: <strong
                                x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(totalTagihan)"></strong></span>
                        <span class="text-xs text-indigo-500">*Pembayaran akan dialokasikan secara urut.</span>
                    </div>

                    <div class="mt-4">
                        {{ $tagihans->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- Script untuk manajemen Checkbox (Menggunakan Alpine.js bawaan Laravel) --}}
    <script>
        function tagihanManager() {
    return {
        selectedIds: [],
        totalTagihan: 0,
        selectedSiswaId: null,
        showModal: false,
        jumlahBayarInput: 0,
        metodePembayaran: 'tunai',

        updateSelection(e) {
            const id = e.target.value;
            const nominal = parseInt(e.target.dataset.nominal);
            if (e.target.checked) {
                this.selectedIds.push(id);
                this.totalTagihan += nominal;
                this.selectedSiswaId = e.target.dataset.siswaId;
            } else {
                this.selectedIds = this.selectedIds.filter(i => i !== id);
                this.totalTagihan -= nominal;
            }
            this.jumlahBayarInput = this.totalTagihan; // Default jumlah bayar = total tagihan
        },

        submitPembayaran() {
            this.showModal = true;
        },

        async executePayment() {
            // Validasi sederhana
            if (this.jumlahBayarInput <= 0) return alert('Jumlah bayar tidak valid');

            // Kirim data menggunakan Fetch agar lebih modern (Laravel 12)
            try {
                const response = await fetch("{{ route('admin.keuangan.pembayaran.store') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        siswa_id: this.selectedSiswaId,
                        tagihan_ids: this.selectedIds,
                        jumlah_bayar_total: this.jumlahBayarInput,
                        metode: this.metodePembayaran
                    })
                });

                const result = await response.json();
                if (result.success) {
                    window.location.reload(); // Refresh untuk melihat update status lunas/cicil
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
