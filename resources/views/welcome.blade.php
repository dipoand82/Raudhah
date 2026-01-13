<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $profil_sekolah->nama_sekolah ?? 'SMP IT Raudhah' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-800 font-sans antialiased">

    <nav class="bg-white shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex items-center gap-3">
                    @if($profil_sekolah && $profil_sekolah->logo_path)
                        {{-- <img src="{{ asset('storage/' . $profil_sekolah->logo_path) }}" alt="Logo" class="h-10 w-10 object-contain"> --}}
                    @else
                        {{-- <div class="h-10 w-10 bg-gray-300 rounded-full flex items-center justify-center text-xs font-bold">LOGO</div> --}}
                    @endif
                    <span class="font-bold text-xl text-blue-900">{{ $profil_sekolah->nama_sekolah ?? 'SMP IT Raudhah' }}</span>
                </div>

                <div class="flex items-center gap-4">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="text-sm font-semibold text-gray-600 hover:text-blue-600">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-semibold hover:bg-blue-700 transition">Log in</a>
                        @endauth
                    @endif
                </div>
            </div>
        </div>
    </nav>

    <div class="relative bg-gray-200 h-64 md:h-96 flex items-center justify-center overflow-hidden">
        @if($profil_sekolah && $profil_sekolah->banner_path)
            <img src="{{ asset('storage/' . $profil_sekolah->banner_path) }}" alt="Banner" class="absolute w-full h-full object-cover opacity-80">
        @else
            <div class="absolute inset-0 bg-gradient-to-r from-blue-500 to-cyan-400 opacity-90"></div>
        @endif
        
        <div class="relative z-10 text-center px-4">
            <h1 class="text-3xl md:text-5xl font-bold text-white drop-shadow-md mb-2">
                {{ $profil_sekolah->nama_sekolah ?? 'Selamat Datang' }}
            </h1>
            <p class="text-white text-lg md:text-xl drop-shadow-sm">Mewujudkan Generasi Cerdas & Berakhlak Mulia</p>
        </div>
    </div>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 space-y-16">

        <section>
            <h2 class="text-2xl font-bold text-center text-gray-800 mb-8 bg-gray-200 py-2 rounded">Alasan Memilih Raudhah</h2>
            <div class="grid md:grid-cols-3 gap-6">
                @if($profil_sekolah && $profil_sekolah->alasan_memilih)
                    @foreach(explode("\n", $profil_sekolah->alasan_memilih) as $alasan)
                    <div class="bg-white p-6 rounded-lg shadow-md border-t-4 border-blue-500 hover:shadow-lg transition">
                        <p class="text-gray-600">{{ $alasan }}</p>
                    </div>
                    @endforeach
                @else
                    <div class="bg-white p-6 rounded-lg shadow border border-gray-200 h-32"></div>
                    <div class="bg-white p-6 rounded-lg shadow border border-gray-200 h-32"></div>
                    <div class="bg-white p-6 rounded-lg shadow border border-gray-200 h-32"></div>
                @endif
            </div>
        </section>

        <section>
            <h2 class="text-2xl font-bold text-center text-gray-800 mb-8 bg-gray-200 py-2 rounded">Program Unggulan</h2>
            <div class="grid md:grid-cols-3 gap-6">
                @if($profil_sekolah && $profil_sekolah->program_unggulan)
                    @foreach(explode("\n", $profil_sekolah->program_unggulan) as $program)
                    <div class="bg-white p-6 rounded-lg shadow-md border-t-4 border-green-500 hover:shadow-lg transition">
                        <h3 class="font-bold text-lg mb-2 text-green-700">Program Sekolah</h3>
                        <p class="text-gray-600">{{ $program }}</p>
                    </div>
                    @endforeach
                @else
                    <div class="bg-white p-6 rounded-lg shadow border border-gray-200 h-32"></div>
                    <div class="bg-white p-6 rounded-lg shadow border border-gray-200 h-32"></div>
                    <div class="bg-white p-6 rounded-lg shadow border border-gray-200 h-32"></div>
                @endif
            </div>
        </section>

        <section class="bg-white rounded-xl shadow-lg p-8">
            <h2 class="text-2xl font-bold text-center text-gray-800 mb-6 bg-gray-200 py-2 rounded">Tentang SMP</h2>
            
            <div class="prose max-w-none text-gray-600 mb-8 text-center">
                {{ $profil_sekolah->deskripsi_singkat ?? 'Deskripsi sekolah belum diisi.' }}
            </div>

            <div class="grid md:grid-cols-2 gap-8 mt-8">
                <div class="bg-blue-50 p-6 rounded-lg">
                    <div class="bg-gray-300 text-center font-bold py-2 rounded mb-4 text-gray-700">VISI</div>
                    <p class="text-gray-700 whitespace-pre-line">{{ $profil_sekolah->visi ?? 'Belum tersedia' }}</p>
                </div>
                <div class="bg-green-50 p-6 rounded-lg">
                    <div class="bg-gray-300 text-center font-bold py-2 rounded mb-4 text-gray-700">MISI</div>
                    <p class="text-gray-700 whitespace-pre-line">{{ $profil_sekolah->misi ?? 'Belum tersedia' }}</p>
                </div>
            </div>
        </section>

    </main>

    <footer class="bg-gray-800 text-white py-8 mt-12">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <p class="font-semibold text-lg">{{ $profil_sekolah->nama_sekolah ?? 'SMP IT Raudhah' }}</p>
            <p class="text-gray-400 text-sm mt-2">
                {{ $profil_sekolah->alamat ?? 'Alamat sekolah' }} | {{ $profil_sekolah->email ?? 'email@sekolah.com' }}
            </p>
            <p class="text-gray-500 text-xs mt-8">© {{ date('Y') }} Sistem Informasi Sekolah. All rights reserved.</p>
        </div>
    </footer>

</body>
</html>