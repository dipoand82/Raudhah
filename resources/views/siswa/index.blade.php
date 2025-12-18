<x-app-layout>
    <x-slot name="header">
        <div class="bg-gradient-to-r from-blue-600 to-blue-400 rounded-lg shadow-lg">
            <h2 class="font-bold text-2xl text-white leading-tight py-4 px-6">
                {{ __('Data Siswa') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    
                    <div class="mb-6 flex justify-between items-center">
                        <h1 class="text-2xl font-bold text-gray-800">Daftar Siswa</h1>
                        
                        <button class="bg-gradient-to-r from-blue-500 to-blue-700 hover:from-blue-600 hover:to-blue-800 text-white font-semibold py-2 px-6 rounded-lg shadow-md transition duration-300 transform hover:scale-105 flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                            </svg>
                            Tambah Siswa
                        </button>
                    </div>

                    <div class="overflow-x-auto rounded-lg shadow-md border border-gray-200">
                        <table class="min-w-full bg-white border-collapse">
                            <thead class="bg-gradient-to-r from-blue-600 to-blue-500">
                                <tr>
                                    <th class="py-4 px-6 text-left text-sm font-bold uppercase tracking-wider text-white border-b border-gray-200">Nama</th>
                                    <th class="py-4 px-6 text-left text-sm font-bold uppercase tracking-wider text-white border-b border-gray-200">NISN</th>
                                    <th class="py-4 px-6 text-left text-sm font-bold uppercase tracking-wider text-white border-b border-gray-200">Jenis Kelamin</th>
                                    <th class="py-4 px-6 text-left text-sm font-bold uppercase tracking-wider text-white border-b border-gray-200">Kelas</th>
                                    <th class="py-4 px-6 text-center text-sm font-bold uppercase tracking-wider text-white border-b border-gray-200">Aksi</th>
                                </tr>
                            </thead>
                            
                            <tbody class="divide-y divide-gray-200">
                                @forelse ($siswas as $siswa)
                                    <tr class="hover:bg-blue-50 transition duration-200">
                                        <td class="py-4 px-6 text-gray-800 font-medium border-b border-gray-200">{{ $siswa->nama }}</td>
                                        <td class="py-4 px-6 text-gray-600 border-b border-gray-200">{{ $siswa->nisn }}</td>
                                        <td class="py-4 px-6 border-b border-gray-200">
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $siswa->jenis_kelamin == 'L' ? 'bg-blue-100 text-blue-800' : 'bg-pink-100 text-pink-800' }}">
                                                {{ $siswa->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}
                                            </span>
                                        </td>
                                        <td class="py-4 px-6 border-b border-gray-200">
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                                                {{ $siswa->kelas }}
                                            </span>
                                        </td>
                                        <td class="py-4 px-6 text-center border-b border-gray-200">
                                            <div class="flex justify-center space-x-2">
                                                <button class="bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition duration-300 transform hover:scale-105 shadow-sm">
                                                    Edit
                                                </button>
                                                <button class="bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition duration-300 transform hover:scale-105 shadow-sm">
                                                    Hapus
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-8 px-6 text-center text-gray-500 border-b border-gray-200">
                                            <div class="flex flex-col items-center">
                                                <svg class="w-16 h-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                                </svg>
                                                <p class="text-lg font-semibold">Tidak ada data siswa</p>
                                                <p class="text-sm">Silakan tambahkan siswa baru</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if(method_exists($siswas, 'links'))
                        <div class="mt-6">
                            {{ $siswas->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>