 @props(['kelas']) {{-- Menerima data kelas sebagai properti --}}
   <x-modal name="add-siswa" focusable>
        <form method="POST" action="{{ route('admin.manajemen-user.siswa.store') }}" class="p-6">
            @csrf
            <div class="flex justify-between items-center mb-4 border-b pb-2"><h2 class="text-lg font-bold text-gray-900">Tambah Siswa</h2><button type="button" x-on:click="$dispatch('close')" class="text-gray-400 hover:text-gray-500"><span class="sr-only">Tutup</span><svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button></div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="col-span-2"><x-input-label value="Nama Lengkap" /><x-text-input name="name" class="w-full mt-1 focus:border-[#3B3E42] focus:ring-[#3B3E42]" required placeholder="Contoh: Tegar Satrio" /></div>
                <div class="col-span-2"><x-input-label value="Email Login (Opsional)" /><x-text-input name="email" type="email" class="w-full mt-1 focus:border-[#3B3E42] focus:ring-[#3B3E42]" placeholder="Jika kosong, akan pakai nama.nisn@raudhah.com" /></div>
                <div><x-input-label value="NISN (Wajib)" /><x-text-input name="nisn" type="number" class="w-full mt-1 focus:border-[#3B3E42] focus:ring-[#3B3E42]" required placeholder="00123456" min="0" oninput="validity.valid||(value='');"/></div>
                <div><x-input-label value="Jenis Kelamin" /><select name="jenis_kelamin" class="w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-[#3B3E42] focus:ring-[#3B3E42]"><option value="L">Laki-laki</option><option value="P">Perempuan</option></select></div>
                <div class="col-span-2"><x-input-label value="Masuk Kelas" /><select name="kelas_id" class="w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-[#3B3E42] focus:ring-[#3B3E42]"><option value="">-- Belum Ada Kelas --</option>@foreach($kelas as $k)<option value="{{ $k->id }}">Kelas {{ $k->tingkat }} {{ $k->nama_kelas }}</option>@endforeach</select></div>
            </div>
            <div class="mt-6 flex justify-end gap-3"><x-secondary-button x-on:click="$dispatch('close')">Batal</x-secondary-button><x-primary-button> Simpan Akun Siswa</x-primary-button></div>
        </form>
    </x-modal>
