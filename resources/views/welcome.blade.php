<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $profil_sekolah->nama_sekolah ?? 'SMP IT Raudhah' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css2?family=Nunito:wght@600&family=Fredoka:wght@500&family=Quicksand:wght@600&family=Comfortaa:wght@700&family=Varela+Round&display=swap"
        rel="stylesheet">
</head>

<body class="bg-white text-gray-800 font-sans antialiased scroll-smooth">
    <nav x-data="{ open: false }" class="bg-white shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center relative">

                <a href="{{ url('/') }}"
                    class="flex items-center gap-3 z-10 hover:opacity-80 transition-opacity min-w-max">
                    @if (file_exists(public_path('storage/logos/logo_smp_raudhah.PNG')))
                        <img src="{{ asset('storage/logos/logo_smp_raudhah.PNG') }}" alt="Logo"
                            class="h-10 w-10 object-contain">
                    @elseif($profil_sekolah && $profil_sekolah->logo_path)
                        <img src="{{ asset('storage/' . $profil_sekolah->logo_path) }}" alt="Logo"
                            class="h-10 w-10 object-contain">
                    @else
                        <div
                            class="h-10 w-10 bg-[#1072B8] rounded-full flex-shrink-0 flex items-center justify-center text-white text-xs font-bold">
                            SMP</div>
                    @endif

                    <span class="font-bold text-[#1072B8] text-lg sm:text-xl block">
                        {{ $profil_sekolah->nama_sekolah ?? 'SMP IT Raudhah' }}
                    </span>
                </a>

                <div class="hidden md:flex items-center gap-12 absolute left-1/2 transform -translate-x-1/2">
                    <a href="#"
                        class="text-sm font-semibold text-gray-600 tracking-widest transition-all px-4 py-2 hover:text-blue-600 hover:bg-blue-50 hover:scale-105 rounded-full capitalize">
                        Beranda
                    </a>

                    <a href="#info"
                        class="text-sm font-semibold text-gray-600 tracking-widest transition-all px-4 py-2 hover:text-blue-600 hover:bg-blue-50 hover:scale-105 rounded-full capitalize">
                        Info
                    </a>

                    <a href="#galeri"
                        class="text-sm font-semibold text-gray-600 tracking-widest transition-all px-4 py-2 hover:text-blue-600 hover:bg-blue-50 hover:scale-105 rounded-full capitalize">
                        Galeri
                    </a>
                </div>

                <div class="flex items-center gap-4 z-10">
                    @auth
                        <a href="{{ url('/dashboard') }}"
                            class="hidden md:block px-4 py-2 rounded-full text-sm font-semibold tracking-widest  capitalize text-[#1072B8] bg-white border-2 border-[#1072B8] hover:bg-[#1072B8] hover:text-white hover:scale-105 hover:shadow-lg transition-all duration-200 text-center">
                            Dashboard
                        </a>
                    @endauth

                    <button @click="open = ! open"
                        class="md:hidden inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-blue-600 hover:bg-gray-100 focus:outline-none transition">
                        <svg class="h-7 w-7" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path :class="{ 'hidden': open, 'inline-flex': !open }" class="inline-flex"
                                stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                            <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden"
                                stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

            </div>
        </div>

        <div x-show="open" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
            class="md:hidden bg-white border-t border-gray-100 shadow-xl">
            <div class="px-4 pt-2 pb-6 space-y-2">
                <a href="#" @click="open = false"
                    class="block px-4 py-3 rounded-xl text-base font-medium text-gray-700 hover:bg-blue-50 hover:text-blue-600">Beranda</a>
                <a href="#info" @click="open = false"
                    class="block px-4 py-3 rounded-xl text-base font-medium text-gray-700 hover:bg-blue-50 hover:text-blue-600">Info</a>
                <a href="#galeri" @click="open = false"
                    class="block px-4 py-3 rounded-xl text-base font-medium text-gray-700 hover:bg-blue-50 hover:text-blue-600">Galeri</a>
                @auth
                    <div class="border-t border-gray-100 my-2"></div>
                    <a href="{{ url('/dashboard') }}"
                        class="block px-4 py-3 rounded-xl text-base font-bold text-white bg-blue-600 hover:bg-blue-700 shadow-md text-center">Dashboard</a>
                @endauth
            </div>
        </div>
    </nav>

    <div class="max-w-[1440px] mx-auto">
        <div
            class="relative bg-gray-900 h-[500px] md:h-[600px] flex items-center justify-center overflow-hidden rounded-md">
            @if ($profil_sekolah && $profil_sekolah->banner_path)
                <img src="{{ asset('storage/' . $profil_sekolah->banner_path) }}" alt="Banner"
                    class="absolute w-full h-full object-cover opacity-60">
            @elseif(file_exists(public_path('storage/logos/profil_smp_raudhah.png')))
                <img src="{{ asset('storage/logos/profil_smp_raudhah.png') }}" alt="Banner Default"
                    class="absolute w-full h-full object-cover opacity-60">
            @else
                <div class="absolute inset-0 bg-blue-900 opacity-90"></div>
            @endif

            <div class="absolute inset-0 bg-gradient-to-t from-gray-900/90 via-gray-500/40 to-transparent"></div>
            <div class="absolute inset-0 bg-gradient-to-b from-blue-900/40 via-blue-900/20 to-gray-900/80"></div>

            <div class="relative z-10 text-center px-4 max-w-4xl">
                <h1 data-aos="zoom-in" data-aos-duration="9000" data-aos-delay="500"
                    class="text-4xl md:text-6xl font-extrabold text-white drop-shadow-lg mb-4 leading-tight ">
                    {{ $profil_sekolah->nama_sekolah ?? 'SMP IT Raudhah' }}
                </h1>
                <p data-aos="fade-up" data-aos-delay="100"
                    class="text-white text-lg md:text-2xl drop-shadow-md font-light mb-8 italic">
                    "Mewujudkan Generasi Cerdas, Berakhlak Mulia, dan Bertaqwa"
                </p>

                <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                    <a href="{{ route('login') }}"
                        class="w-full sm:w-auto px-8 py-3 bg-[#2158E1] hover:bg-[#4b79ec] text-white font-bold rounded-full shadow-lg transition-all transform hover:scale-105 flex items-center justify-center gap-2">
                        Masuk Ke Pembayaran SPP
                    </a>
                    <a href="#info"
                        class="w-full sm:w-auto px-8 py-3 bg-amber-600 hover:bg-amber-500 text-white font-bold rounded-full shadow-lg transition-all transform hover:scale-105 flex items-center justify-center gap-2">
                        Informasi
                    </a>
                </div>
            </div>
        </div>
    </div>

    <main class="max-w-7xl mx-auto px-4 sm:px-2 lg:px-2 py-6 space-y-2">
        <section class="scroll-mt-40 py-16 bg-white-50">
            <div class="max-w-7xl mx-auto px-4">
                <div class="text-center mb-3">
                    <h2 data-aos="fade-up"
                        class="text-4xl font-extrabold text-blue-900 uppercase tracking-wider animate-gradient-x">SMP
                        IT
                        RAUDHAH</h2>
                    <div class="h-1.5 w-24 bg-blue-600 mx-auto mt-4 rounded-full"></div>
                </div>

                <div data-aos="fade-up"
                    class="bg-white p-8 md:p-12 rounded-3xl shadow-xl shadow-blue-100/50 mb-16 border border-gray-100 relative overflow-hidden">

                    {{-- Siluet Bintang Abu-abu di Belakang Teks --}}
                    <div
                        class="absolute justtify-center text-center -right-10 -bottom-10 text-gray-100 opacity-50 pointer-events-none select-none z-0">
                        <svg xmlns="http://www.w3.org/2000/svg" width="800" height="500" fill="currentColor"
                            viewBox="0 0 24 24">
                            <path d="M12 0L14.59 9.41L24 12L14.59 14.59L12 24L9.41 14.59L0 12L9.41 9.41L12 0Z" />
                        </svg>
                    </div>

                    {{-- Konten Deskripsi --}}
                    <div class="prose prose-lg max-w-none text-gray-700 leading-loose text-justify relative z-10"
                        style="font-family: 'Nunito', sans-serif; font-weight: 600;">

                        {!! nl2br(e($profil_sekolah->deskripsi_singkat ?? 'Deskripsi sekolah belum diisi.')) !!}

                    </div>
                </div>

                <div class="grid md:grid-cols-2 gap-8 mb-16">
                    <div data-aos="flip-right"
                        class="group bg-white p-8 rounded-3xl border-t-8 border-blue-600 shadow-lg hover:shadow-2xl duration-300">
                        <div class="flex items-center mb-6">
                            <div class="p-3 bg-blue-100 rounded-2xl mr-4 group-hover:bg-blue-600 transition-colors">
                                {{-- <svg class="w-6 h-6 text-blue-600 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04"></path></svg> --}}
                            </div>
                            <h3 class="text-2xl font-bold text-blue-800 uppercase tracking-wide">Program Unggulan</h3>
                        </div>
                        <p class="text-gray-600 text-lg leading-relaxed ">
                            @if ($profil_sekolah && $profil_sekolah->program_unggulan)
                                @foreach (explode("\n", $profil_sekolah->program_unggulan) as $program)
                                    @if (trim($program) != '')
                                        <div class="flex items-start gap-3 p-3 rounded-xl hover:bg-gray-50 transition">
                                            <span
                                                class="flex-shrink-0 w-6 h-6 bg-teal-100 text-blue-600 rounded-full flex items-center justify-center text-sm font-bold">★</span>
                                            <span
                                                class="text-gray-600 font-bold leading-relaxed">{{ $program }}</span>
                                        </div>
                                    @endif
                                @endforeach
                            @else
                                <p class="text-gray-400 italic text-base px-3 py-2">Data belum diisi.</p>
                            @endif
                        </p>
                    </div>

                    <div data-aos="flip-right"
                        class="group bg-white p-8 rounded-3xl border-t-8 border-green-500 shadow-lg hover:shadow-2xl transition-all duration-300">
                        <div class="flex items-center mb-6">
                            <div class="p-3 bg-green-100 rounded-2xl mr-4 group-hover:bg-green-500 transition-colors">
                                {{-- <svg class="w-6 h-6 text-green-600 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg> --}}
                            </div>
                            <h3 class="text-2xl font-bold text-green-800 uppercase tracking-wide">Alasan Memilih Kami
                            </h3>
                        </div>
                        <div class="text-gray-600 text-lg leading-relaxed space-y-2">
                            <div class="space-y-1">
                                @if ($profil_sekolah && $profil_sekolah->alasan_memilih)
                                    @foreach (explode("\n", $profil_sekolah->alasan_memilih) as $alasan)
                                        @if (trim($alasan) != '')
                                            <div
                                                class="flex items-start gap-3 p-3 rounded-xl hover:bg-gray-50 transition">
                                                <span
                                                    class="flex-shrink-0 w-6 h-6 bg-blue-100 text-green-600 rounded-full flex items-center justify-center text-sm font-bold">✓</span>
                                                <span
                                                    class="text-gray-600 font-bold leading-relaxed">{{ $alasan }}</span>
                                            </div>
                                        @endif
                                    @endforeach
                                @else
                                    <p class="text-gray-400 italic text-base px-3 py-2">Data belum diisi.</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div
                    class="bg-white p-8  rounded-3xl shadow-xl shadow-blue-100/50 border border-gray-100 relative overflow-hidden">
                    <div data-aos="fade-left" class="flex items-center gap-3 mb-6 relative z-10">
                        <div class="p-3 bg-[#1072B8] rounded-2xl text-white shadow-md">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-7.714 2.143L11 21l-2.286-6.857L1 12l7.714-2.143L11 3z" />
                            </svg>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-800">Visi</h2>
                    </div>

                    <div data-aos="fade-left" class="relative"
                        style="font-family: 'Nunito', sans-serif; font-weight: 600;">

                        @if ($profil_sekolah && $profil_sekolah->visi)
                            <div class="flex gap-4 items-start">
                                <div class="flex-shrink-0 text-[#1072B8] px-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                        fill="currentColor" viewBox="0 0 24 24">
                                        <path
                                            d="M12 0L14.59 9.41L24 12L14.59 14.59L12 24L9.41 14.59L0 12L9.41 9.41L12 0Z" />
                                    </svg>
                                </div>

                                <p class="text-gray-700 text-md leading-relaxed">
                                    {!! nl2br(e($profil_sekolah->visi)) !!}
                                </p>
                            </div>
                        @else
                            <div class="flex gap-4 items-center pl-2">
                                <p class="text-gray-500 italic">Visi belum tersedia</p>
                            </div>
                        @endif
                    </div>
                </div>
                <div
                    class="bg-white p-8 rounded-3xl shadow-xl shadow-green-100/50 border border-gray-100 mt-12 space-x-4">
                    <div data-aos="fade-left" class="flex items-center gap-3 mb-6">
                        <div class="p-3 bg-teal-500 rounded-2xl text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-800">Misi</h2>
                    </div>

                    <div data-aos="fade-left" class="space-y-4"
                        style="font-family: 'Nunito', sans-serif; font-weight: 600;">
                        @if ($profil_sekolah)
                            @php
                                // Tambahkan ?? '' untuk jaga-jaga jika property misi kosong
                                $lines = explode("\n", str_replace("\r", '', $profil_sekolah->misi ?? ''));
                                $daftar_misi = array_filter(array_map('trim', $lines));
                            @endphp

                            @forelse ($daftar_misi as $item)
                                <div class="flex gap-3 items-start">
                                    <span class="font-bold text-teal-600 text-lg leading-tight min-w-[25px] ">
                                        {{ $loop->iteration }}.
                                    </span>
                                    <p class="text-gray-700 leading-relaxed">
                                        {{ preg_replace('/^\d+\.\s*/', '', $item) }}
                                    </p>
                                </div>
                            @empty
                                <p class="text-gray-500 text-left text-lg">Misi belum tersedia</p>
                            @endforelse
                        @else
                            <p class="text-gray-500 text-left">Data profil belum tersedia</p>
                        @endif
                    </div>
                    <div id="info"></div>
                </div>


                <div class="mt-12 bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
                    <div class="p-7 border-b border-gray-50 bg-gray-50/90">

                        <div class="flex flex-col md:flex-row justify-between items-center gap-4">

                            <div data-aos="fade-left" data-aos-duration="600"class="text-center md:text-left">
                                <h3 class="text-2xl font-bold text-blue-900 uppercase ">
                                    {{ $profil_sekolah->info_penting ?? '' }}</h3>

                                {{-- <p class="text-gray-400 mt-2"> Email Sekolah : {{ $profil_sekolah->email ?? '' }}</p> --}}

                                <p class="text-gray-500">Tahun Akademik {{ date('Y') }}/{{ date('Y') + 1 }}</p>
                            </div>

                            <span
                                class="px-4 py-1.5 bg-amber-100 text-amber-700 text-sm font-bold rounded-full uppercase tracking-wider">Informasi</span>
                        </div>

                    </div>
                    <div data-aos="fade-up" class="p-4 md:p-8 flex justify-center">
                        {{-- Ubah ke max-w-2xl agar lebih ramping --}}
                        <div class="w-full max-w-2xl px-4 group relative">

                            @if ($profil_sekolah && $profil_sekolah->brosur_info)
                                <img src="{{ asset('storage/' . $profil_sekolah->brosur_info) }}"
                                    alt="Informasi penting"
                                    class="w-full h-auto rounded-2xl shadow-lg border border-gray-100 transition-all duration-500 group-hover:scale-[1.01]">
                            @else
                                {{-- Pastikan ekstensi file sesuai (di VS Code kamu ada brosur.png dan brosur1.jpeg) --}}
                                <img src="{{ asset('storage/logos/brosur.png') }}" alt="Informasi penting"
                                    class="w-full h-auto rounded-2xl shadow-lg border border-gray-100">
                            @endif

                            {{-- Overlay halus agar lebih premium --}}
                            <div class="absolute inset-0 rounded-2xl ring-1 ring-black/5 pointer-events-none"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="galeri">
            </div>
        </section>

        <section data-aos="fade-up" class="scroll-mt-12">
            <div class="text-center mb-12">
                <h2 data-aos="fade-up" class="text-3xl font-bold text-gray-800 uppercase tracking-widest ">Galeri
                    Kegiatan</h2>
                <div class="h-1 w-20 bg-blue-600 mx-auto mt-2"></div>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div
                    class="aspect-square bg-gray-200 rounded-xl overflow-hidden group border-4 border-white shadow-md">
                    <img src="https://via.placeholder.com/400x400?text=Kegiatan+1"
                        class="w-full h-full object-cover group-hover:scale-110 transition duration-500 cursor-pointer">
                </div>
                <div
                    class="aspect-square bg-gray-200 rounded-xl overflow-hidden group border-4 border-white shadow-md">
                    <img src="https://via.placeholder.com/400x400?text=Kegiatan+2"
                        class="w-full h-full object-cover group-hover:scale-110 transition duration-500 cursor-pointer">
                </div>
                <div
                    class="aspect-square bg-gray-200 rounded-xl overflow-hidden group border-4 border-white shadow-md">
                    <img src="https://via.placeholder.com/400x400?text=Kegiatan+3"
                        class="w-full h-full object-cover group-hover:scale-110 transition duration-500 cursor-pointer">
                </div>
                <div
                    class="aspect-square bg-gray-200 rounded-xl overflow-hidden group border-4 border-white shadow-md">
                    <img src="https://via.placeholder.com/400x400?text=Kegiatan+4"
                        class="w-full h-full object-cover group-hover:scale-110 transition duration-500 cursor-pointer">
                </div>
            </div>
        </section>

    </main>

    <footer class="bg-gray-900 text-white py-12 mt-20 border-t-4">
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
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            AOS.init({
                duration: 1000,
                once: false, // Agar animasi bisa berulang
                mirror: true, // Agar saat scroll ke atas muncul lagi
                offset: 100, // Jarak pemicu diperkecil agar lebih sensitif
                anchorPlacement: 'top-bottom', // Menentukan titik pemicu animasi
            });
        });

        // Tambahan: Paksa AOS refresh saat halaman selesai dimuat sempurna
        window.addEventListener('load', function() {
            AOS.refresh();
        });
    </script>
</body>

</html>
