@props(['siswa', 'kelas', 'tahunAjaran'])
<div class="grid grid-cols-1 md:grid-cols-2 gap-6" x-data="{
    statusSiswa: '{{ old('status', $siswa->status) }}', // Menyimpan status saat ini
    kelasId: '{{ old('kelas_id', $siswa->kelas_id) }}' // Menyimpan ID kelas saat ini
}">

    <div class="md:col-span-2">
        <h4 class="font-bold text-[#1072B8] mb-3 uppercase text-xs tracking-wider">Informasi Akun</h4>
    </div>

    <div>
        <x-input-label for="name" :value="__('Nama Lengkap')" />
        <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $siswa->user->name ?? '')" required />
    </div>

    <div>
        <x-input-label for="email" :value="__('Email Login')" />
        <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $siswa->user->email ?? '')"
            required />
    </div>

    <div class="md:col-span-2 mt-4">
        <h4 class="font-bold text-[#1072B8] mb-3 uppercase text-xs tracking-wider">Data Akademik</h4>
    </div>

    <div>
        <x-input-label for="nisn" :value="__('NISN')" />
        <x-text-input id="nisn" class="block mt-1 w-full" type="number" name="nisn" :value="old('nisn', $siswa->nisn)"
            min="0" oninput="validity.valid||(value='');" />
    </div>

    <div>
        <x-input-label for="jenis_kelamin" :value="__('Jenis Kelamin')" />
        <select name="jenis_kelamin"
            class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            <option value="L" {{ $siswa->jenis_kelamin == 'L' ? 'selected' : '' }}>Laki-laki</option>
            <option value="P" {{ $siswa->jenis_kelamin == 'P' ? 'selected' : '' }}>Perempuan</option>
        </select>
    </div>

    <div>
        <x-input-label for="kelas_id" :value="__('Kelas Saat Ini')" />
        <select name="kelas_id" id="kelas_id" x-model="kelasId" :disabled="statusSiswa !== 'Aktif'"
            :class="statusSiswa !== 'Aktif' ? 'bg-gray-200 cursor-not-allowed text-gray-500' : 'bg-white'"
            class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition duration-150">
            <option value="">-- Belum Masuk Kelas --</option>
            @foreach ($kelas as $k)
                <option value="{{ $k->id }}">
                    {{ $k->tingkat }} {{ $k->nama_kelas }}
                </option>
            @endforeach
        </select>
        {{-- // PERUBAHAN: Menambahkan teks peringatan kecil yang muncul hanya saat tidak aktif --}}
        <p x-show="statusSiswa !== 'Aktif'" class="text-[12px] text-red-600 mt-1 italic font-medium">
            *Kelas otomatis dinonaktifkan untuk status selain Aktif.
        </p>
    </div>

    <div>
        <x-input-label for="tahun_ajaran_id" :value="__('Tahun Ajaran Aktif')" />
        <select name="tahun_ajaran_id"
            class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            <option value="">-- Pilih Tahun --</option>
            @foreach ($tahunAjaran as $ta)
                <option value="{{ $ta->id }}" {{ $siswa->tahun_ajaran_id == $ta->id ? 'selected' : '' }}>
                    {{ $ta->tahun }} @if ($ta->is_active)
                        (Aktif)
                    @endif
                </option>
            @endforeach
        </select>
    </div>

    <div class="md:col-span-2">
        <x-input-label for="status" :value="__('Status Siswa')" />
        <select name="status" x-model="statusSiswa" @change="if(statusSiswa !== 'Aktif') { kelasId = '' }"
            class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-gray-50">
            <option value="Aktif"> Aktif</option>
            <option value="Cuti"> Cuti</option>
            <option value="Lulus"> Lulus</option>
            <option value="Pindah"> Pindah</option>
            <option value="Keluar"> Keluar </option>
        </select>
        <p class="text-[12px] text-red-600 mt-1 italic font-medium">*Hati-hati mengubah status menjadi Lulus/Keluar.</p>
    </div>
</div>
