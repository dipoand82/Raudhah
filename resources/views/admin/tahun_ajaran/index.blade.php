<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manajemen Tahun Ajaran & Akademik') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="mb-6 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-sm">
                    <p class="font-bold">Berhasil!</p>
                    <p>{{ session('success') }}</p>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-sm">
                    <p class="font-bold">Gagal!</p>
                    <p>{{ session('error') }}</p>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <div class="lg:col-span-2 space-y-6">
                    
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                        <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                            Tambah Tahun Ajaran Baru
                        </h3>
                        <form action="{{ route('admin.tahun-ajaran.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                            @csrf
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tahun (Cth: 2024/2025)</label>
                                <input type="text" name="tahun" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" placeholder="2024/2025" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Semester</label>
                                <select name="semester" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                                    <option value="Ganjil">Ganjil</option>
                                    <option value="Genap">Genap</option>
                                </select>
                            </div>
                            <div class="flex items-center gap-2 pb-3">
                                <input type="checkbox" name="is_active" value="1" id="activeCheck" class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
                                <label for="activeCheck" class="text-sm text-gray-600">Langsung Aktifkan?</label>
                            </div>
                            <div class="md:col-span-3">
                                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                                    Simpan Data
                                </button>
                            </div>
                        </form>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="overflow-x-auto bg-white rounded shadow">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tahun</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Semester</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </th>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($tahunAjarans as $ta)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $ta->tahun }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $ta->semester }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($ta->is_active)
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                Aktif
                                            </span>
                                        @else
                                            <form action="{{ route('admin.tahun-ajaran.activate', $ta->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="text-xs text-blue-600 hover:text-blue-900 hover:underline">
                                                    Set Aktif
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <form action="{{ route('admin.tahun-ajaran.destroy', $ta->id) }}" method="POST" onsubmit="return confirm('Yakin hapus?');" class="inline">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-4 text-center text-gray-500 italic">Belum ada data tahun ajaran.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="lg:col-span-1">
                    <div class="bg-gradient-to-br from-yellow-50 to-orange-50 border border-yellow-200 rounded-xl p-6 shadow-sm sticky top-6">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="p-2 bg-yellow-100 rounded-lg text-yellow-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                            </div>
                            <h3 class="text-lg font-bold text-yellow-900">Proses Akademik</h3>
                        </div>
                        
                        <p class="text-sm text-yellow-800 mb-6 leading-relaxed">
                            Fitur ini digunakan pada <b>akhir tahun ajaran</b>. Semua siswa dengan status <b>'Aktif'</b> akan diubah statusnya menjadi <b>'Lulus'</b> secara otomatis.
                        </p>

                        <div class="border-t border-yellow-200 my-4"></div>

                        <form action="{{ route('admin.tahun-ajaran.graduation') }}" method="POST" onsubmit="return confirm('PERINGATAN KERAS:\n\nApakah Anda yakin ingin meluluskan SEMUA siswa aktif?\n\nTindakan ini akan mengubah status siswa secara massal.');">
                            @csrf
                            <button type="submit" class="w-full flex justify-center items-center gap-2 bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-4 rounded-lg shadow-lg hover:shadow-xl transition transform hover:-translate-y-0.5">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                PROSES KELULUSAN
                            </button>
                        </form>
                        
                        <p class="text-xs text-red-500 mt-3 text-center">
                            *Hati-hati, aksi ini tidak dapat dibatalkan dengan mudah.
                        </p>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>