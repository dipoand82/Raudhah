<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manajemen Siswa') }}
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


            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border border-gray-200">
                
                <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
                    
                    <h3 class="text-xl font-bold text-gray-800">
                        Kelola Data Akademik Siswa
                    </h3>

                    <div class="flex gap-2 w-full md:w-auto">
                        <button type="button" onclick="alert('Fitur ini akan segera hadir!')" class="bg-gray-100 text-gray-700 border border-gray-300 px-4 py-2 rounded hover:bg-gray-200 text-sm font-medium transition">
                            Proses Kelulusan Massal
                        </button>

                        <a href="{{ route('admin.siswas.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700 text-sm font-medium shadow-sm transition flex items-center justify-center">
                            Tambah Siswa
                        </a>
                    </div>
                </div>

                <div class="mb-6 bg-blue-50 p-4 rounded-lg border border-blue-100 flex items-center justify-between">
                    <span class="text-sm text-blue-800 font-medium">💡 Mau upload data cepat?</span>
                    <form action="{{ route('admin.siswas.import') }}" method="POST" enctype="multipart/form-data" class="flex gap-2 items-center">
                        @csrf
                        <input type="file" name="file" class="text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-100 file:text-blue-700 hover:file:bg-blue-200" required>
                        <button type="submit" class="text-blue-600 hover:text-blue-800 text-sm font-bold underline">Upload Excel</button>
                    </form>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b-2 border-gray-200 bg-gray-50">
                                <th class="py-3 px-4 text-sm font-bold text-gray-600 uppercase">NISN</th>
                                <th class="py-3 px-4 text-sm font-bold text-gray-600 uppercase">Nama Lengkap</th>
                                <th class="py-3 px-4 text-sm font-bold text-gray-600 uppercase">Kelas</th>
                                <th class="py-3 px-4 text-sm font-bold text-gray-600 uppercase">Tahun Ajaran</th>
                                <th class="py-3 px-4 text-sm font-bold text-gray-600 uppercase">Status</th>
                                <th class="py-3 px-4 text-sm font-bold text-gray-600 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($siswas as $siswa)
                            <tr class="hover:bg-gray-50 transition duration-150">
                                
                                <td class="py-3 px-4 text-gray-700 font-medium">
                                    {{ $siswa->nisn }}
                                </td>

                                <td class="py-3 px-4 text-gray-900">
                                    {{ $siswa->nama_lengkap }}
                                    <div class="text-xs text-gray-400">{{ $siswa->user->email ?? '' }}</div>
                                </td>

                                <td class="py-3 px-4 text-gray-700">
                                    {{ $siswa->kelas ?? '-' }}
                                </td>

                                <td class="py-3 px-4 text-gray-700">
                                    {{-- Mengambil dari relasi tahun_ajaran, jika null tampilkan default --}}
                                    {{ $siswa->tahun_ajaran->tahun ?? '2024/2025' }}
                                </td>

                                <td class="py-3 px-4">
                                    @php
                                        $statusClass = match($siswa->status) {
                                            'Aktif' => 'bg-green-100 text-green-800',
                                            'Cuti' => 'bg-gray-100 text-gray-800',
                                            'Lulus' => 'bg-blue-100 text-blue-800',
                                            'Pindah' => 'bg-yellow-100 text-yellow-800',
                                            'Keluar' => 'bg-red-100 text-red-800',
                                            default => 'bg-gray-100 text-gray-800'
                                        };
                                    @endphp
                                    <span class="px-2 py-1 rounded text-xs font-semibold {{ $statusClass }}">
                                        {{ $siswa->status }}
                                    </span>
                                </td>

                                <td class="py-3 px-4 flex gap-3">
                                    <a href="#" class="text-blue-600 hover:underline text-sm font-medium">Edit</a>
                                    <form action="#" method="POST" onsubmit="return confirm('Hapus siswa ini?');" class="inline">
                                        @csrf 
                                        {{-- @method('DELETE') --}}
                                        <button type="button" class="text-red-600 hover:underline text-sm font-medium">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-gray-500 italic bg-gray-50 rounded-lg mt-2">
                                    Belum ada data siswa. Silakan tambah data baru.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-6">
                    {{ $siswas->links() }}
                </div>

            </div>
        </div>
    </div>
</x-app-layout>