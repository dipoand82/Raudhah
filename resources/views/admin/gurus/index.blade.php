{{-- <x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Data Guru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-4 bg-green-100 text-green-700 p-4 rounded">{{ session('success') }}</div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <div class="flex justify-between items-center mb-6">

                    <form action="{{ route('admin.manajemen-user.gurus.import') }}" method="POST" enctype="multipart/form-data" class="flex gap-2">
                        @csrf
                        <input type="file" name="file" class="border rounded p-1 text-sm" required>
                        <button type="submit" class="bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700 text-sm">Import Excel</button>
                    </form>

                    <a href="{{ route('admin.manajemen-user.gurus.index') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                        + Guru Baru
                    </a>
                </div>

                <table class="min-w-full border">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Nama Guru</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Status Akun</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($gurus as $guru)
                        <tr>
                            <td class="px-6 py-4">{{ $guru->name }}</td>
                            <td class="px-6 py-4 text-gray-500">{{ $guru->email }}</td>
                            <td class="px-6 py-4">
                                @if($guru->must_change_password)
                                    <span class="text-red-500 text-xs font-bold">⚠ Belum Aktivasi</span>
                                @else
                                    <span class="text-green-500 text-xs font-bold">✔ Aktif</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="mt-4">
                    {{ $gurus->links() }}
                </div>

            </div>
        </div>
    </div>
</x-app-layout> --}}
