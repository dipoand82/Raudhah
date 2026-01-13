<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Data Siswa') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-gray-100 p-6 rounded-lg shadow-sm border border-gray-200">
                
                <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
                    
                    <form method="GET" action="{{ route('admin.siswas.index') }}" class="relative w-full md:w-1/2">
                        <input type="text" name="search" value="{{ request('search') }}" 
                               placeholder="Cari siswa (Nama / NISN)..." 
                               class="w-full rounded-full border-gray-300 pl-5 pr-10 py-2 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                        <button type="submit" class="absolute right-3 top-2.5 text-gray-400 hover:text-indigo-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </button>
                    </form>

                    <a href="{{ route('admin.manajemen-user.index') }}" class="flex items-center gap-2 text-gray-600 hover:text-indigo-700 font-semibold transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Tambah Siswa
                    </a>
                </div>
                
                <div class="overflow-x-auto bg-white rounded shadow">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-200">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">NISN</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Nama</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Kelas</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Tahun Ajaran</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Status Siswa</th>
                                <th class="px-6 py-3 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($siswas as $siswa)
                            <tr class="hover:bg-gray-50 transition">
                                
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $siswa->nisn ?? '-' }}
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-bold text-gray-900">
                                        {{ $siswa->user->name ?? 'User Terhapus' }}
                                    </div>
                                    <div class="text-xs text-gray-400">
                                        {{ $siswa->user->email ?? '' }}
                                    </div>
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($siswa->kelas)
                                        <span class="text-sm font-medium text-gray-700">
                                            {{ $siswa->kelas->nama_kelas }}
                                        </span>
                                    @else
                                        <span class="text-xs text-red-400 italic">Belum ada kelas</span>
                                    @endif
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $siswa->tahunMasuk->tahun ?? '-' }}
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $statusClass = match($siswa->status) {
                                            'Aktif' => 'bg-green-100 text-green-800',
                                            'Lulus' => 'bg-blue-100 text-blue-800',
                                            'Keluar' => 'bg-red-100 text-red-800',
                                            'Pindah' => 'bg-yellow-100 text-yellow-800',
                                            default => 'bg-gray-100 text-gray-800',
                                        };
                                    @endphp
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClass }}">
                                        {{ $siswa->status }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="{{ route('admin.siswas.edit', $siswa->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-4 font-semibold">Edit</a>
                                    
                                    <form action="{{ route('admin.siswas.destroy', $siswa->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin hapus siswa ini? Akun login-nya juga akan terhapus permanen.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900 font-semibold">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-10 text-center text-gray-400 italic">
                                    Belum ada data siswa. 
                                    <a href="{{ route('admin.manajemen-user.index') }}" class="text-indigo-600 underline">Tambah di Manajemen User</a>.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $siswas->appends(['search' => request('search')])->links() }}
                </div>

            </div>
        </div>
    </div>
</x-app-layout>