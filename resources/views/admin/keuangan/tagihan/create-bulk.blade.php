<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Generate Tagihan Massal') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Alert Notifications --}}
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative shadow-sm" role="alert">
                    <span class="block sm:inline font-semibold">{{ session('success') }}</span>
                </div>
            @endif
            @if(session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative shadow-sm" role="alert">
                    <span class="block sm:inline font-semibold">{{ session('error') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100">
                <div class="p-6 text-gray-900">
                    <div class="mb-6 border-b pb-4 flex justify-between items-center">
                        <div>
                            <h3 class="text-lg font-bold text-gray-800">Form Pembuatan Tagihan Massal</h3>
                            <p class="text-sm text-gray-500">Buat tagihan untuk banyak siswa sekaligus berdasarkan kelas dan tahun ajaran.</p>
                        </div>
                        <a href="{{ route('admin.keuangan.master.index') }}" class="text-sm text-indigo-600 hover:text-indigo-900 font-semibold underline">
                            &larr; Kembali ke Master Tagihan
                        </a>
                    </div>

                    <form action="{{ route('admin.keuangan.tagihan.store-bulk') }}" method="POST">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            {{-- Master Tagihan --}}
                            <div>
                                <x-input-label for="master_tagihan_id" value="Pilih Jenis Tagihan *" />
                                <select name="master_tagihan_id" id="master_tagihan_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm bg-gray-50" required>
                                    <option value="" disabled selected>-- Pilih Tagihan --</option>
                                    @foreach($masterTagihans as $mt)
                                        <option value="{{ $mt->id }}">{{ $mt->nama_tagihan }} (Rp {{ number_format($mt->nominal, 0, ',', '.') }})</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Tahun Ajaran Target --}}
                            <div>
                                <x-input-label for="tahun_ajaran_id" value="Tahun Ajaran Aktif *" />
                                <select name="tahun_ajaran_id" id="tahun_ajaran_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm bg-gray-50" required>
                                    @foreach($tahunAjarans as $ta)
                                        <option value="{{ $ta->id }}">{{ $ta->tahun }}</option>
                                    @endforeach
                                </select>
                                <p class="text-xs text-gray-500 mt-1">Hanya menagih siswa yang aktif di tahun ajaran ini.</p>
                            </div>

                            {{-- Bulan Tagihan --}}
                            <div>
                                <x-input-label for="bulan" value="Bulan (Opsional, khusus SPP bulanan)" />
                                <select name="bulan" id="bulan" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="">-- Tidak Berdasarkan Bulan (Misal: Uang Gedung) --</option>
                                    @foreach(['Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni'] as $bln)
                                        <option value="{{ $bln }}">{{ $bln }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Tahun Tagihan --}}
                            <div>
                                <x-input-label for="tahun" value="Tahun Tagihan *" />
                                <x-text-input id="tahun" name="tahun" type="number" class="mt-1 block w-full" placeholder="Contoh: 2024" required value="{{ date('Y') }}" />
                            </div>

                            {{-- Target Kelas --}}
                            <div class="md:col-span-2">
                                <x-input-label for="target_kelas" value="Target Siswa / Kelas *" />
                                <select name="target_kelas" id="target_kelas" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm bg-blue-50 font-medium border-blue-200" required>
                                    <option value="" disabled selected>-- Pilih Target Penagihan --</option>
                                    <option value="semua" class="font-bold text-indigo-700">⚡ SEMUA SISWA AKTIF (SELURUH KELAS)</option>

                                    <optgroup label="Per Tingkatan">
                                        <option value="tingkat_7">Semua Siswa Kelas 7</option>
                                        <option value="tingkat_8">Semua Siswa Kelas 8</option>
                                        <option value="tingkat_9">Semua Siswa Kelas 9</option>
                                    </optgroup>

                                    <optgroup label="Per Kelas Spesifik">
                                        @foreach($kelas as $k)
                                            <option value="{{ $k->id }}">Kelas {{ $k->tingkat }} {{ $k->nama_kelas }}</option>
                                        @endforeach
                                    </optgroup>
                                </select>
                            </div>

                        </div>

                        <div class="mt-8 flex items-start gap-4 bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                            <svg class="w-6 h-6 text-yellow-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <div>
                                <p class="text-sm font-bold text-yellow-800">Informasi Penting</p>
                                <p class="text-sm text-yellow-700 mt-1">Pastikan data yang dipilih sudah benar. Sistem akan mendeteksi otomatis jika siswa sudah memiliki tagihan yang sama pada bulan & tahun tersebut agar tidak terjadi duplikasi (double tagihan).</p>
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end">
                            <x-primary-button class="bg-indigo-600 hover:bg-indigo-700 px-6 py-3" onclick="return confirm('Apakah Anda yakin ingin men-generate tagihan ini secara massal?');">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                Generate Tagihan Sekarang
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
