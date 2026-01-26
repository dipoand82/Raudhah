<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Data Siswa</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                <form method="POST" action="{{ route('admin.siswas.update', $siswa->id) }}">
                    @csrf
                    @method('PUT') 
                    
                    {{-- PANGGIL KOMPONEN DI SINI --}}
                    <x-siswa.edit-form :siswa="$siswa" :kelas="$kelas" :tahunAjaran="$tahunAjaran" />

                    <div class="flex items-center justify-end mt-8 gap-3">
                        <a href="{{ route('admin.siswas.index') }}" class="text-gray-600 hover:text-gray-900 underline text-sm">
                            Batal & Kembali
                        </a>
                        <x-primary-button>
                            {{ __('Simpan Perubahan') }}
                        </x-primary-button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>