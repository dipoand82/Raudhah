    <nav x-data="{ open: false }" class="bg-white shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center relative">

                <a href="{{ url('/') }}"
    class="flex items-center gap-3 z-10 hover:opacity-80 transition-opacity min-w-max">

    {{-- LOGO SECTION --}}
@if (file_exists(public_path('storage/logos/logo_smp_raudhah.PNG')))
    {{-- Ring dihapus, hanya menyisakan ukuran dan object-contain --}}
    <img src="{{ asset('storage/logos/logo_smp_raudhah.PNG') }}" alt="Logo"
        class="h-10 w-10 object-contain">
@elseif($profil_sekolah && $profil_sekolah->logo_path)
    <img src="{{ asset('storage/' . $profil_sekolah->logo_path) }}" alt="Logo"
        class="h-10 w-10 object-contain">
@else
    {{-- Fallback jika tidak ada file gambar --}}
    <div class="h-10 w-10 bg-[#1072B8] rounded-full flex-shrink-0 flex items-center justify-center text-white text-xs font-bold">
        SMP
    </div>
@endif

    {{-- NAMA SEKOLAH SECTION --}}
    {{-- Logika: Jika URL adalah '/', gunakan warna biru gelap, jika tidak gunakan biru standar --}}
    <span class="font-bold text-lg sm:text-xl block transition-colors duration-300 {{ request()->is('/') ? 'text-[#0d5a92]' : 'text-[#1072B8]' }}">
        {{ $profil_sekolah->nama_sekolah ?? 'SMP IT Raudhah' }}
    </span>
</a>

                <div class="hidden md:flex items-center gap-12 absolute left-1/2 transform -translate-x-1/2">
{{-- Link Beranda: Menggunakan request()->is('/') karena beranda adalah root --}}
<a href="{{ url('/') }}"
    class="px-4 py-2 rounded-full transition-all {{ request()->is('/') ? 'bg-blue-50 text-[#1072B8] font-bold' : 'text-gray-600 hover:text-[#1072B8]' }}">
    Beranda
</a>

{{-- Link Info: Menggunakan request()->routeIs('info.*') agar aktif di semua sub-halaman info --}}
<a href="{{ route('info.index') }}"
    class="px-4 py-2 rounded-full transition-all {{ request()->routeIs('info.*') ? 'bg-blue-50 text-[#1072B8] font-bold' : 'text-gray-600 hover:text-[#1072B8]' }}">
    Info
</a>

{{-- Link Galeri: Menggunakan request()->routeIs('galeri.*') agar aktif saat melihat daftar maupun detail foto --}}
<a href="{{ route('galeri.index') }}"
    class="px-4 py-2 rounded-full transition-all {{ request()->routeIs('galeri.*') ? 'bg-blue-50 text-[#1072B8] font-bold' : 'text-gray-600 hover:text-[#1072B8]' }}">
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
                <a href="{{ url('/') }}" @click="open = false"
                    class="block px-4 py-3 rounded-xl text-base font-medium text-gray-700 hover:bg-blue-50 hover:text-blue-600">Beranda</a>
                <a href="{{ route('info.index') }}" @click="open = false"
                    class="block px-4 py-3 rounded-xl text-base font-medium text-gray-700 hover:bg-blue-50 hover:text-blue-600">Info</a>
                <a href="{{ route('galeri.index') }}" @click="open = false"
                    class="block px-4 py-3 rounded-xl text-base font-medium text-gray-700 hover:bg-blue-50 hover:text-blue-600">Galeri</a>
                @auth
                    <div class="border-t border-gray-100 my-2"></div>
                    <a href="{{ url('/dashboard') }}"
                        class="block px-4 py-3 rounded-xl text-base font-bold text-white bg-blue-600 hover:bg-blue-700 shadow-md text-center">Dashboard</a>
                @endauth
            </div>
        </div>
    </nav>
