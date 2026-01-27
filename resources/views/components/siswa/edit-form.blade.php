@props(['siswa', 'kelas', 'tahunAjaran'])

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="md:col-span-2">
        <h4 class="font-bold text-indigo-600 mb-3 uppercase text-xs tracking-wider">Informasi Akun</h4>
    </div>

    {{-- Nama Lengkap --}}
    <div>
        <x-input-label for="name" :value="__('Nama Lengkap')" />
        <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $siswa->user->name ?? '')" required />
    </div>

    {{-- Email Login --}}
    <div>
        <x-input-label for="email" :value="__('Email Login')" />
        <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $siswa->user->email ?? '')" required />
    </div>

    <div class="md:col-span-2 mt-4">
        <h4 class="font-bold text-indigo-600 mb-3 uppercase text-xs tracking-wider">Data Akademik</h4>
    </div>

    {{-- NISN --}}
    <div>
        <x-input-label for="nisn" :value="__('NISN')" />
        <x-text-input id="nisn" class="block mt-1 w-full" type="number" name="nisn" :value="old('nisn', $siswa->nisn)" min="0" oninput="validity.valid||(value='');" />
    </div>

    {{-- Jenis Kelamin --}}
    <div>
        <x-input-label for="jenis_kelamin" :value="__('Jenis Kelamin')" />
        <select name="jenis_kelamin" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            <option value="L" {{ $siswa->jenis_kelamin == 'L' ? 'selected' : '' }}>Laki-laki</option>
            <option value="P" {{ $siswa->jenis_kelamin == 'P' ? 'selected' : '' }}>Perempuan</option>
        </select>
    </div>

    {{-- Kelas --}}
    <div>
        <x-input-label for="kelas_id" :value="__('Kelas Saat Ini')" />
        <select name="kelas_id" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            <option value="">-- Belum Masuk Kelas --</option>
            @foreach($kelas as $k)
                <option value="{{ $k->id }}" {{ $siswa->kelas_id == $k->id ? 'selected' : '' }}>
                    {{ $k->tingkat }} {{ $k->nama_kelas }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- Tahun Ajaran --}}
    <div>
        <x-input-label for="tahun_ajaran_id" :value="__('Tahun Ajaran Aktif')" />
        <select name="tahun_ajaran_id" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            <option value="">-- Pilih Tahun --</option>
            @foreach($tahunAjaran as $ta)
                <option value="{{ $ta->id }}" {{ $siswa->tahun_ajaran_id == $ta->id ? 'selected' : '' }}>
                    {{ $ta->tahun }} @if($ta->is_active) (Aktif) @endif
                </option>
            @endforeach
        </select>
    </div>

    {{-- Status --}}
    <div class="md:col-span-2">
        <x-input-label for="status" :value="__('Status Siswa')" />
        <select name="status" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-gray-50">
            <option value="Aktif" {{ $siswa->status == 'Aktif' ? 'selected' : '' }}> Aktif</option>
            <option value="Cuti" {{ $siswa->status == 'Cuti' ? 'selected' : '' }}> Cuti</option>
            <option value="Lulus" {{ $siswa->status == 'Lulus' ? 'selected' : '' }}> Lulus</option>
            <option value="Pindah" {{ $siswa->status == 'Pindah' ? 'selected' : '' }}> Pindah</option>
            <option value="Keluar" {{ $siswa->status == 'Keluar' ? 'selected' : '' }}> Keluar (DO)</option>
        </select>
        <p class="text-xs text-red-500 font-semibold mt-1">*Hati-hati mengubah status menjadi Lulus/Keluar.</p>
    </div>
</div>