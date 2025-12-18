<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Data Siswa') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="mb-4 bg-blue-100 text-blue-700 p-4 rounded">{{ session('success') }}</div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                <div class="flex justify-between items-center mb-6">
                    <form action="{{ route('admin.siswas.import') }}" method="POST" enctype="multipart/form-data" class="flex gap-2">
                        @csrf
                        <input type="file" name="file" class="border rounded p-1 text-sm" required>
                        <button type="submit" class="bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700 text-sm">Import Excel</button>
                    </form>

                    <a href="{{ route('admin.siswas.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                        + Siswa Baru
                    </a>
                </div>

                <table class="min-w-full border">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Nama Siswa</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($siswas as $siswa)
                        <tr>
                            <td class="px-6 py-4">{{ $siswa->name }}</td>
                            <td class="px-6 py-4 text-gray-500">{{ $siswa->email }}</td>
                            <td class="px-6 py-4">
                                @if($siswa->must_change_password)
                                    <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full text-xs">Belum Login</span>
                                @else
                                    <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs">Aktif</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                
                <div class="mt-4">
                    {{ $siswas->links() }}
                </div>

            </div>
        </div>
    </div>
</x-app-layout>