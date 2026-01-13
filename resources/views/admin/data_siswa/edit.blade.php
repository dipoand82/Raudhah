<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Data Siswa') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                <div class="mb-6 border-b pb-4">
                    <h3 class="text-lg font-bold text-gray-700">Formulir Perubahan Data</h3>
                    <p class="text-sm text-gray-500">Pastikan data akademik diisi dengan benar.</p>
                </div>

                @if ($errors->any())
                    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                        <ul class="list-disc list-inside text-sm">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.siswas.update', $siswa->id) }}">
                    @csrf
                    @method('PUT') <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        
                        <div class="md:col-span-2">
                            <h4 class="font-bold text-indigo-600 mb-3 uppercase text-xs tracking-wider">Informasi Akun</h4>
                        </div>

                        <div>
                            <x-input-label for="name" :value="__('Nama Lengkap')" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $siswa->user->name)" required />
                        </div>

                        <div>
                            <x-input-label for="email" :value="__('Email Login')" />
                            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $siswa->user->email)" required />
                        </div>

                        <div class="md:col-span-2 mt-4">
                            <h4 class="font-bold text-indigo-600 mb-3 uppercase text-xs tracking-wider">Data Akademik</h4>
                        </div>

                        <div>
                            <x-input-label for="nisn" :value="__('NISN')" />
                            <x-text-input id="nisn" class="block mt-1 w-full" type="number" name="nisn" :value="old('nisn', $siswa->nisn)" />
                        </div>

                        <div>
                            <x-input-label for="jenis_kelamin" :value="__('Jenis Kelamin')" />
                            <select name="jenis_kelamin" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="L" {{ $siswa->jenis_kelamin == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="P" {{ $siswa->jenis_kelamin == 'P' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                        </div>

                        <div>
                            <x-input-label for="kelas_id" :value="__('Kelas Saat Ini')" />
                            <select name="kelas_id" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">-- Belum Masuk Kelas --</option>
                                @foreach($kelas as $k)
                                    <option value="{{ $k->id }}" {{ $siswa->kelas_id == $k->id ? 'selected' : '' }}>
                                        {{ $k->nama_kelas }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <x-input-label for="tahun_masuk_id" :value="__('Angkatan (Tahun Masuk)')" />
                            <select name="tahun_masuk_id" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">-- Pilih Tahun --</option>
                                @foreach($tahunAjaran as $ta)
                                    <option value="{{ $ta->id }}" {{ $siswa->tahun_masuk_id == $ta->id ? 'selected' : '' }}>
                                        {{ $ta->tahun }} ({{ $ta->semester }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="md:col-span-2">
                            <x-input-label for="status" :value="__('Status Siswa')" />
                            <select name="status" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-gray-50">
                                <option value="Aktif" {{ $siswa->status == 'Aktif' ? 'selected' : '' }}>✅ Aktif</option>
                                <option value="Lulus" {{ $siswa->status == 'Lulus' ? 'selected' : '' }}>🎓 Lulus</option>
                                <option value="Pindah" {{ $siswa->status == 'Pindah' ? 'selected' : '' }}>🚚 Pindah</option>
                                <option value="Keluar" {{ $siswa->status == 'Keluar' ? 'selected' : '' }}>❌ Keluar (DO)</option>
                            </select>
                            <p class="text-xs text-gray-500 mt-1">*Hati-hati mengubah status menjadi Lulus/Keluar.</p>
                        </div>

                    </div>

                    <div class="flex items-center justify-end mt-8 gap-3">
                        <a href="{{ route('admin.siswas.index') }}" class="text-gray-600 hover:text-gray-900 underline text-sm">
                            Batal & Kembali
                        </a>
                        <x-primary-button class="bg-indigo-600 hover:bg-indigo-700">
                            {{ __('Simpan Perubahan') }}
                        </x-primary-button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</x-app-layout>