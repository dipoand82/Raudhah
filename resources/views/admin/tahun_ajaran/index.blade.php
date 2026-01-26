<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manajemen Tahun Ajaran') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- ALERT SUCCESS --}}
            @if(session('success'))
                <x-alert-success>
                    {{ session('success') }}
                </x-alert-success>
            @endif
            {{-- Tampilkan Alert Gagal (Misal dari Session Error) --}}
            @if(session('error'))
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

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                {{-- KOLOM KIRI: FORM & TABEL --}}
                <div class="lg:col-span-2 space-y-6">
                    
                    {{-- 1. FORM TAMBAH TAHUN BARU --}}
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                        <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-[#3B3E42]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                            Tambah Tahun Ajaran Baru
                        </h3>
                        
                        <form action="{{ route('admin.tahun-ajaran.store') }}" method="POST" class="flex flex-col sm:flex-row gap-4 items-end">
                            @csrf
                            <div class="flex-grow w-full">
                                <x-input-label value="Tahun (Cth: 2024/2025)" class="mb-1" />
                                <input type="text" name="tahun" class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#3B3E42] focus:ring-[#3B3E42]" placeholder="2024/2025" required>
                            </div>
                            
                            <x-primary-button class="w-full sm:w-auto justify-center h-[42px]">
                                {{ __('Simpan') }}
                            </x-primary-button>
                        </form>
                    </div>

                    {{-- 2. TABEL DATA --}}
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden overflow-x-auto bg-white rounded shadow">
                        <table class="min-w-full divide-y divide-gray-200 ">
                            <thead class="bg-[#3B3E42]">
                                <tr>
                                    <th class="px-6 py-4 text-center text-xs font-bold text-white uppercase w-12">No</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase">Tahun</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase">Status</th>
                                    <th class="px-6 py-4 text-right text-xs font-bold text-white uppercase">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 bg-white">
                                @forelse($tahunAjarans as $ta)
                                <tr class="hover:bg-indigo-50/50 transition even:bg-gray-50">
                                    <td class="px-6 py-4 text-center text-sm font-medium text-gray-500">
                                        {{ $loop->iteration }}
                                    </td>
                                    <td class="px-6 py-4 font-bold text-[#3B3E42] text-md">
                                        {{ $ta->tahun }}
                                    </td>
                                    
                                    <td class="px-6 py-4">
                                        @if($ta->is_active)
                                            <span class="inline-flex items-center gap-1 bg-green-100 text-green-800 text-xs px-3 py-1 rounded-full font-bold border border-green-200 shadow-sm">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                Aktif Sekarang
                                            </span>
                                        @else
                                            <form action="{{ route('admin.tahun-ajaran.activate', $ta->id) }}" method="POST">
                                                @csrf
                                                <button class="text-xs text-gray-500 hover:text-[#3B3E42] border border-gray-300 hover:border-[#3B3E42] px-3 py-1 rounded-full font-medium transition duration-150 ease-in-out">
                                                    Set Aktif
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        {{-- Tombol Edit --}}
                                        <button x-data="" x-on:click="$dispatch('open-modal', 'edit-tahun-{{ $ta->id }}')" 
                                            class="text-indigo-600 hover:text-indigo-900 mr-3 font-semibold transition">
                                            Edit
                                        </button>

                                        {{-- Tombol Hapus --}}
                                        @if(!$ta->is_active)
                                            <form action="{{ route('admin.tahun-ajaran.destroy', $ta->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus Tahun Ajaran {{ $ta->tahun }}? Data terkait mungkin ikut terhapus!');">
                                                @csrf @method('DELETE')
                                                {{-- PERBAIKAN TYPO DISINI: Menambahkan < sebelum x-danger --}}
                                                <x-danger-button>Hapus</x-danger-button>
                                            </form>
                                        @endif

                                        {{-- === MODAL EDIT === --}}
                                        <x-modal name="edit-tahun-{{ $ta->id }}" focusable>
                                            <div class="p-6 text-left">
                                                <div class="flex justify-between items-center mb-5 border-b pb-3">
                                                    <h2 class="text-lg font-bold text-gray-900">Edit Tahun Ajaran</h2>
                                                    <button x-on:click="$dispatch('close')" class="text-gray-400 hover:text-gray-600">
                                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                    </button>
                                                </div>

                                                <form action="{{ route('admin.tahun-ajaran.update', $ta->id) }}" method="POST">
                                                    @csrf @method('PUT')
                                                    
                                                    <div class="mb-6">
                                                        <x-input-label value="Tahun Ajaran" />
                                                        <x-text-input name="tahun" value="{{ $ta->tahun }}" class="w-full mt-1 focus:border-[#3B3E42] focus:ring-[#3B3E42]" required />
                                                    </div>

                                                    <div class="flex justify-end gap-3 pt-4 border-t">
                                                        <x-secondary-button x-on:click="$dispatch('close')">Batal</x-secondary-button>
                                                        <x-primary-button class="!bg-[#3B3E42] hover:!bg-[#2f3235]">Simpan Perubahan</x-primary-button>
                                                    </div>
                                                </form>
                                            </div>
                                        </x-modal>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-10 text-center text-gray-500 italic">Belum ada data tahun ajaran.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                        {{-- FOOTER TABEL --}}
                        <div class="divide-y divide-gray-100 rounded-xl bg-white p-2 text-center">
                            <p class="text-sm text-red-500 font-medium inline-block">
                                *Tahun ajaran baru otomatis menjadi <strong>Aktif</strong> setelah disimpan*
                                {{-- <br>*Pastikan Tahun Ajaran terbaru sudah di-set <strong>AKTIF</strong>                                  --}}
                            </p>
                        </div>
            </div>
                {{-- KOLOM KANAN: FITUR KELULUSAN --}}
                <div class="lg:col-span-1">
                    <div class="bg-orange-50 border border-orange-200 rounded-xl p-6 shadow-sm sticky top-6">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="p-2 bg-orange-200 rounded-lg text-orange-700">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                            </div>
                            <h3 class="text-lg font-bold text-orange-900">Proses Kelulusan</h3>
                        </div>
                        
                        <p class="text-sm text-orange-800 mb-5 leading-relaxed bg-orange-100/50 p-3 rounded-md border border-orange-100">
                            Gunakan fitur ini di akhir tahun ajaran untuk meluluskan siswa tingkat akhir (Misal: Kelas 9).
                        </p>

                        <form action="{{ route('admin.tahun-ajaran.graduation') }}" method="POST" onsubmit="return confirm('Yakin ingin meluluskan siswa tingkat ini? Status mereka akan berubah jadi Lulus.');">
                            @csrf
                            
                            <div class="mb-5">
                                <label class="block text-xs font-bold text-orange-800 uppercase mb-2">Pilih Tingkat Akhir</label>
                                {{-- <select name="tingkat_akhir" class="w-full border-orange-300 focus:border-orange-500 focus:ring-orange-500 rounded-md text-sm shadow-sm" required>
                                    <option value="">-- Pilih Tingkat --</option>
                                    @foreach($tingkatKelas as $t)
                                        <option value="{{ $t }}">Tingkat {{ $t }} (Kelas {{ $t }})</option>
                                    @endforeach
                                </select> --}}
                                {{-- Ganti bagian select di kolom kanan menjadi seperti ini --}}
<select name="tingkat_akhir" class="w-full border-orange-300 focus:border-orange-500 focus:ring-orange-500 rounded-md text-sm shadow-sm" required>
    <option value="">-- Pilih Tingkat --</option>
    {{-- Langsung tembak ke 9 jika ini sekolah SMP --}}
    <option value="9">Tingkat 9 (Kelas Akhir)</option>
</select>
                            </div>

                            <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-4 rounded-lg shadow transition text-sm flex justify-center items-center gap-2 group">
                                <svg class="w-4 h-4 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                LULUSKAN ANGKATAN INI
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>