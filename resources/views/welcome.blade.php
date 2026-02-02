<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $profil_sekolah->nama_sekolah ?? 'SMP IT Raudhah' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-50 text-gray-800 font-sans antialiased scroll-smooth">

    <nav class="bg-white shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">

                <a href="{{ url('/') }}" class="flex items-center gap-3 hover:opacity-80 transition-opacity">
                <div class="flex items-center gap-3">
                    @if(file_exists(public_path('storage/logos/logo_smp_raudhah.PNG')))
                        <img src="{{ asset('storage/logos/logo_smp_raudhah.PNG') }}" alt="Logo" class="h-10 w-10 object-contain">
                    @elseif($profil_sekolah && $profil_sekolah->logo_path)
                        <img src="{{ asset('storage/' . $profil_sekolah->logo_path) }}" alt="Logo" class="h-10 w-10 object-contain">
                    @else
                        <div class="h-10 w-10 bg-[#1072B8]rounded-full flex items-center justify-center text-white text-xs font-bold">SMP</div>
                    @endif
                    <span class="font-bold text-sm text-[#1072B8] sm:text-xl text-[#1072B8] block">{{ $profil_sekolah->nama_sekolah ?? 'SMP IT Raudhah' }}</span>
                </div>
                </a>

                <div class="hidden md:flex items-center gap-12 absolute left-1/2 transform -translate-x-1/2">
                    <a href="#" class="text-sm font-semibold text-gray-600 hover:text-blue-600 transition">Beranda</a>
                    <a href="#info" class="text-sm font-semibold text-gray-600 hover:text-blue-600 transition">Info</a>
                    <a href="#galeri" class="text-sm font-semibold text-gray-600 hover:text-blue-600 transition">Galeri</a>
                </div>

                <div class="flex items-center">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="text-sm font-semibold text-gray-600 hover:text-blue-600">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="px-5 py-2 bg-blue-600 text-white rounded-lg text-sm font-semibold hover:bg-blue-700 shadow-md transition">Log in</a>
                        @endauth
                    @endif
                </div>
            </div>
        </div>
    </nav>

<div class="max-w-[1440px] mx-auto">
    <div class="relative bg-gray-900 h-[500px] md:h-[600px] flex items-center justify-center overflow-hidden rounded-md">
        @if($profil_sekolah && $profil_sekolah->banner_path)
            <img src="{{ asset('storage/' . $profil_sekolah->banner_path) }}" alt="Banner" class="absolute w-full h-full object-cover opacity-60">
        @elseif(file_exists(public_path('storage/logos/profil_smp_raudhah.png')))
            <img src="{{ asset('storage/logos/profil_smp_raudhah.png') }}" alt="Banner Default" class="absolute w-full h-full object-cover opacity-60">
        @else
            <div class="absolute inset-0 bg-blue-900 opacity-90"></div>
        @endif

        <div class="absolute inset-0 bg-gradient-to-t from-gray-900/90 via-gray-500/40 to-transparent"></div>
        <div class="absolute inset-0 bg-gradient-to-b from-blue-900/40 via-blue-900/20 to-gray-900/80"></div>

        <div class="relative z-10 text-center px-4 max-w-4xl">
            <h1 class="text-4xl md:text-6xl font-extrabold text-white drop-shadow-lg mb-4 leading-tight">
                {{ $profil_sekolah->nama_sekolah ?? 'SMP IT Raudhah' }}
            </h1>
            <p class="text-white text-lg md:text-2xl drop-shadow-md font-light mb-8 italic">
                "Mewujudkan Generasi Cerdas, Berakhlak Mulia, dan Bertaqwa"
            </p>

            <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                <a href="{{ route('login') }}" class="w-full sm:w-auto px-8 py-3 bg-[#2158E1] hover:bg-[#4b79ec] text-white font-bold rounded-full shadow-lg transition-all transform hover:scale-105 flex items-center justify-center gap-2">
                    Masuk Ke Pembayaran SPP
                </a>
                <a href="#info" class="w-full sm:w-auto px-8 py-3 bg-amber-600 hover:bg-amber-500 text-white font-bold rounded-full shadow-lg transition-all transform hover:scale-105 flex items-center justify-center gap-2">
                    Informasi
                </a>
            </div>
        </div>
    </div>
</div>

<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-12">
<section id="info" class="scroll-mt-40 py-16 bg-gray-50">
    <div class="max-w-6xl mx-auto px-4">
        <div class="text-center mb-3">
            <h2 class="text-4xl font-extrabold text-blue-900 uppercase tracking-wider">Informasi Sekolah</h2>
            <div class="h-1.5 w-24 bg-blue-600 mx-auto mt-4 rounded-full"></div>
        </div>

        <div class="bg-white p-8 md:p-12 rounded-3xl shadow-xl shadow-blue-100/50 mb-16 border border-gray-100">
            <div class="prose prose-lg max-w-none text-gray-600 leading-relaxed text-justify">
                {!! nl2br(e($profil_sekolah->deskripsi_singkat ?? 'Deskripsi sekolah belum diisi.')) !!}
            </div>
        </div>

        <div class="grid md:grid-cols-2 gap-8 mb-16"> <div class="group bg-white p-8 rounded-3xl border-t-8 border-blue-600 shadow-lg hover:shadow-2xl transition-all duration-300">
                <div class="flex items-center mb-6">
                    <div class="p-3 bg-blue-100 rounded-2xl mr-4 group-hover:bg-blue-600 transition-colors">
                        <svg class="w-6 h-6 text-blue-600 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04"></path></svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800 uppercase tracking-wide">Visi</h3>
                </div>
                <p class="text-gray-600 text-lg leading-relaxed italic">
                    "{{ $profil_sekolah->visi ?? 'Belum tersedia' }}"
                </p>
            </div>

            <div class="group bg-white p-8 rounded-3xl border-t-8 border-green-500 shadow-lg hover:shadow-2xl transition-all duration-300">
                <div class="flex items-center mb-6">
                    <div class="p-3 bg-green-100 rounded-2xl mr-4 group-hover:bg-green-500 transition-colors">
                        <svg class="w-6 h-6 text-green-600 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800 uppercase tracking-wide">Misi</h3>
                </div>
                <div class="text-gray-600 text-lg leading-relaxed space-y-2">
                    {!! nl2br(e($profil_sekolah->misi ?? 'Belum tersedia')) !!}
                </div>
            </div>
        </div>

        <div class="mt-12 bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
            <div class="p-8 border-b border-gray-50 bg-gray-50/50">
                <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                    <div class="text-center md:text-left">
                        <h3 class="text-2xl font-bold text-blue-900 uppercase">Informasi Pendaftaran & Kegiatan</h3>
                        <p class="text-gray-500">Tahun Akademik {{ date('Y') }}/{{ date('Y')+1 }}</p>
                    </div>
                    <span class="px-4 py-1.5 bg-amber-100 text-amber-700 text-sm font-bold rounded-full uppercase tracking-wider">Informasi</span>
                </div>
            </div>
            
            <div class="p-4 md:p-8">
                <div class="relative group">
                    @if($profil_sekolah && $profil_sekolah->banner_path)
                        <img src="{{ asset('storage/' . $profil_sekolah->banner_path) }}" 
                             alt="Informasi PPDB" 
                             class="w-full h-auto rounded-2xl shadow-md transition-transform duration-500 group-hover:scale-[1.01]">
                    @else
                        <img src="{{ asset('storage/logos/profil_smp_raudhah.png') }}" 
                             alt="Informasi PPDB" 
                             class="w-full h-auto rounded-2xl shadow-md">
                    @endif
                    
                    <div class="absolute inset-0 rounded-2xl border-2 border-blue-600/5 group-hover:border-blue-600/20 transition-colors pointer-events-none"></div>
                </div>
                
                <div class="mt-6 text-center">
                    <p class="text-gray-500 italic text-sm">
                        *Klik gambar untuk memperbesar informasi pendaftaran.
                    </p>
                </div>
            </div>
        </div>
        </div>
</section>

        <section class="grid md:grid-cols-2 gap-12">
            <div class="bg-white p-8 rounded-3xl shadow-xl shadow-blue-100/50 border border-gray-100">
                <div class="flex items-center gap-3 mb-6">
                    <div class="p-3 bg-blue-600 rounded-2xl text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-800">Alasan Memilih Kami</h2>
                </div>

                <div class="space-y-4">
                    @if($profil_sekolah && $profil_sekolah->alasan_memilih)
                        @foreach(explode("\n", $profil_sekolah->alasan_memilih) as $alasan)
                            @if(trim($alasan) != "")
                            <div class="flex items-start gap-3 p-3 rounded-xl hover:bg-gray-50 transition">
                                <span class="flex-shrink-0 w-6 h-6 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-sm font-bold">✓</span>
                                <span class="text-gray-600 leading-relaxed">{{ $alasan }}</span>
                            </div>
                            @endif
                        @endforeach
                    @else
                        <p class="text-gray-400 italic">Data belum diisi.</p>
                    @endif
                </div>
            </div>

            <div class="bg-white p-8 rounded-3xl shadow-xl shadow-green-100/50 border border-gray-100">
                <div class="flex items-center gap-3 mb-6">
                    <div class="p-3 bg-teal-500 rounded-2xl text-white">
                        <svg xmlns="http://www.w3.org/2000/center" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-7.714 2.143L11 21l-2.286-6.857L1 12l7.714-2.143L11 3z" />
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-800">Program Unggulan</h2>
                </div>

                <div class="space-y-4">
                    @if($profil_sekolah && $profil_sekolah->program_unggulan)
                        @foreach(explode("\n", $profil_sekolah->program_unggulan) as $program)
                            @if(trim($program) != "")
                            <div class="flex items-start gap-3 p-3 rounded-xl hover:bg-gray-50 transition">
                                <span class="flex-shrink-0 w-6 h-6 bg-teal-100 text-teal-600 rounded-full flex items-center justify-center text-sm">★</span>
                                <span class="text-gray-600 leading-relaxed font-medium">{{ $program }}</span>
                            </div>
                            @endif
                        @endforeach
                    @else
                        <p class="text-gray-400 italic">Data belum diisi.</p>
                    @endif
                </div>
            </div>
        </section>

        <section id="galeri" class="scroll-mt-20">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-800 uppercase tracking-widest">Galeri Kegiatan</h2>
                <div class="h-1 w-20 bg-blue-600 mx-auto mt-2"></div>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="aspect-square bg-gray-200 rounded-xl overflow-hidden group border-4 border-white shadow-md">
                    <img src="https://via.placeholder.com/400x400?text=Kegiatan+1" class="w-full h-full object-cover group-hover:scale-110 transition duration-500 cursor-pointer">
                </div>
                <div class="aspect-square bg-gray-200 rounded-xl overflow-hidden group border-4 border-white shadow-md">
                    <img src="https://via.placeholder.com/400x400?text=Kegiatan+2" class="w-full h-full object-cover group-hover:scale-110 transition duration-500 cursor-pointer">
                </div>
                <div class="aspect-square bg-gray-200 rounded-xl overflow-hidden group border-4 border-white shadow-md">
                    <img src="https://via.placeholder.com/400x400?text=Kegiatan+3" class="w-full h-full object-cover group-hover:scale-110 transition duration-500 cursor-pointer">
                </div>
                <div class="aspect-square bg-gray-200 rounded-xl overflow-hidden group border-4 border-white shadow-md">
                    <img src="https://via.placeholder.com/400x400?text=Kegiatan+4" class="w-full h-full object-cover group-hover:scale-110 transition duration-500 cursor-pointer">
                </div>
            </div>
        </section>

    </main>

    <footer class="bg-gray-900 text-white py-12 mt-20 border-t-4">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <p class="font-bold text-2xl mb-4">{{ $profil_sekolah->nama_sekolah ?? 'SMP IT Raudhah' }}</p>
            <p class="text-gray-400 max-w-md mx-auto">{{ $profil_sekolah->alamat ?? 'Alamat Belum Diisi' }}</p>
            <p class="text-gray-400 mt-2">{{ $profil_sekolah->email ?? '' }}</p>
            <div class="mt-8 pt-8 border-t border-gray-800 text-gray-500 text-sm">
                &copy; {{ date('Y') }} {{ $profil_sekolah->nama_sekolah ?? 'Sistem Sekolah' }}. All rights reserved.
            </div>
        </div>
    </footer>

</body>
</html>
