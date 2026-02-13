
    <footer class="bg-gray-900 text-white py-12 mt-auto border-t-4">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <p class="font-bold text-2xl mb-4 ">{{ $profil_sekolah->nama_sekolah ?? 'SMP IT Raudhah' }}</p>
            <p class="text-gray-400 max-w-md mx-auto"> Alamat Sekolah : {{ $profil_sekolah->alamat ?? '' }}</p>
            <p class="text-gray-400 mt-2"> Email Sekolah : {{ $profil_sekolah->email ?? '' }}</p>

            @if ($profil_sekolah)
                {{-- CEK APAKAH DATA ADA --}}
                @php
                    $inputan = $profil_sekolah->telepon;

                    // 1. Bersihkan dari karakter non-angka
                    $clean = preg_replace('/[^0-9]/', '', $inputan);

                    // 2. Ubah format ke 62
                    if (substr($clean, 0, 1) === '0') {
                        $wa_link = '62' . substr($clean, 1);
                    } elseif (substr($clean, 0, 2) === '62') {
                        $wa_link = $clean;
                    } else {
                        $wa_link = $clean;
                    }
                @endphp

                {{-- Tampilan jika data ada --}}
                <p class="text-gray-400 mt-2 flex items-center justify-center gap-2">
                    No. Telp Sekolah :
                    <a href="https://wa.me/{{ $wa_link }}" target="_blank">
                        <i class="fab fa-whatsapp textgray-500 transition-all hover:text-green-500 fa-lg"></i>

                        {{ $inputan ?? '' }}
                    </a>

                </p>
            @else
                {{-- Tampilan jika data kosong (Baru install/belum input) --}}
                <p class="text-gray-400 mt-2">No. Telp Sekolah : Belum Diisi</p>
            @endif

            <p class="text-gray-400 mt-2"> Media Sosial :
                <a href="{{ $profil_sekolah->instagram ?? '' }}" target="_blank"
                    class="transition-all hover:text-pink-500">
                    <i class="fab fa-instagram fa-lg"></i>
                </a>
                <span class="text-gray-600">|</span>
                <a href="{{ $profil_sekolah->tiktok ?? '' }}" target="_blank"
                    class="transition-all hover:text-white">
                    <i class="fab fa-tiktok fa-lg"></i>
                </a>
            </p>
            <p class="text-gray-400 max-w-md mx-auto"> {{ $profil_sekolah->info_footer ?? '' }}</p>

            <div class="mt-8 pt-8 border-t border-gray-800 text-gray-500 text-sm">
                &copy; {{ date('Y') }} {{ $profil_sekolah->nama_sekolah ?? 'Sistem Sekolah' }}. All rights
                reserved.
            </div>


        </div>
    </footer>
