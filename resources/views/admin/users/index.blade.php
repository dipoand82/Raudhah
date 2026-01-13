<x-app-layout>
    <x-slot name="header">
        <div class="bg-gradient-to-r from-blue-600 to-blue-400 rounded-lg shadow-lg">
            <h2 class="font-bold text-2xl text-white leading-tight py-4 px-6">
                {{ __('Manajemen User') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            
            {{-- Alert Sukses --}}
            @if (session('success'))
                <div class="mb-6 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-md" role="alert">
                    <p class="font-bold">Berhasil!</p>
                    <p>{{ session('success') }}</p>
                </div>
            @endif

                        <form action="{{ route('admin.siswas.import') }}" method="POST" enctype="multipart/form-data" class="flex items-center gap-2 bg-white p-1 rounded border">
            @csrf
            <input type="file" name="file" class="text-sm text-gray-500 file:mr-2 file:py-1 file:px-2 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100" required>
            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white text-xs px-3 py-2 rounded transition">
                Import Excel
            </button>
            </form>

            <a href="{{ route('admin.siswas.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm px-4 py-2 rounded flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Tambah Manual
             </a>

            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    
                    <div class="mb-6 flex justify-between items-center">
                        <h1 class="text-2xl font-bold text-gray-800">Daftar Pengguna</h1>
                        
                        {{-- Tombol Tambah --}}
                        <a href="{{ route('admin.users.create') }}" class="bg-gradient-to-r from-blue-500 to-blue-700 hover:from-blue-600 hover:to-blue-800 text-white font-semibold py-2 px-6 rounded-lg shadow-md transition duration-300 transform hover:scale-105 flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                            </svg>
                            Tambah User
                        </a>
                    </div>

                    <div class="overflow-x-auto rounded-lg shadow-md border border-gray-200">
                        <table class="min-w-full bg-white border-collapse">
                            <thead class="bg-gradient-to-r from-blue-600 to-blue-500">
                                <tr>
                                    <th class="py-4 px-6 text-left text-sm font-bold uppercase tracking-wider text-white border-b border-gray-200">No</th>
                                    <th class="py-4 px-6 text-left text-sm font-bold uppercase tracking-wider text-white border-b border-gray-200">Nama Lengkap</th>
                                    <th class="py-4 px-6 text-left text-sm font-bold uppercase tracking-wider text-white border-b border-gray-200">Email</th>
                                    <th class="py-4 px-6 text-left text-sm font-bold uppercase tracking-wider text-white border-b border-gray-200">Terdaftar Sejak</th>
                                    <th class="py-4 px-6 text-center text-sm font-bold uppercase tracking-wider text-white border-b border-gray-200">Aksi</th>
                                </tr>
                            </thead>
                            
                            <tbody class="divide-y divide-gray-200">
                                @forelse ($users as $index => $user)
                                    <tr class="hover:bg-blue-50 transition duration-200">
                                        {{-- Nomor Urut (Memanfaatkan pagination) --}}
                                        <td class="py-4 px-6 text-gray-600 border-b border-gray-200">
                                            {{ $users->firstItem() + $index }}
                                        </td>
                                        
                                        <td class="py-4 px-6 text-gray-800 font-medium border-b border-gray-200">
                                            {{ $user->name }}
                                        </td>
                                        
                                        <td class="py-4 px-6 text-gray-600 border-b border-gray-200">
                                            {{ $user->email }}
                                        </td>
                                        
                                        <td class="py-4 px-6 text-gray-600 border-b border-gray-200">
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800">
                                                {{ $user->created_at->format('d M Y') }}
                                            </span>
                                        </td>
                                        
                                        <td class="py-4 px-6 text-center border-b border-gray-200">
                                            <div class="flex justify-center space-x-2">
                                                
                                                {{-- Tombol Edit --}}
                                                <a href="{{ route('admin.users.edit', $user->id) }}" class="bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition duration-300 transform hover:scale-105 shadow-sm">
                                                    Edit
                                                </a>

                                                {{-- Tombol Hapus (Form) --}}
                                                <form action="#" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus user ini?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition duration-300 transform hover:scale-105 shadow-sm">
                                                        Hapus
                                                    </button>
                                                </form>

                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-8 px-6 text-center text-gray-500 border-b border-gray-200">
                                            <div class="flex flex-col items-center">
                                                <svg class="w-16 h-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                                </svg>
                                                <p class="text-lg font-semibold">Tidak ada data user</p>
                                                <p class="text-sm">Silakan tambahkan user baru</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    @if($users->hasPages())
                        <div class="mt-6">
                            {{ $users->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>