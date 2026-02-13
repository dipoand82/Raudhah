@extends('layouts.landing')

@section('content')
{{-- Mengurangi py-10 menjadi pt-2 pb-10 untuk merapatkan jarak ke Navbar --}}
<div class="bg-gray-50 min-h-screen pt-2 pb-10">
    {{-- Memperkecil max-w-5xl menjadi max-w-3xl agar pembungkus lebih pas dengan ukuran gambar --}}
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

{{-- <nav class="flex md:hidden mb-4 text-sm font-medium text-gray-500">
    <a href="/" class="hover:text-[#1072B8]">Beranda</a>
    <span class="mx-2">/</span>
    <span class="text-gray-800">Informasi Penting</span>
</nav> --}}

        {{-- Konten Utama --}}
        <div data-aos="fade-up" class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
            {{-- Header Kartu --}}
            <div class="p-6 border-b border-gray-50 bg-gray-50/90">
                <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                    <div class="text-center md:text-left">
                        <h3 class="text-xl font-bold text-blue-900 uppercase">
                            {{ $profil_sekolah->info_penting ?? 'Informasi Penting' }}
                        </h3>
                        <p class="text-xs text-gray-500">Tahun Akademik {{ date('Y') }}/{{ date('Y') + 1 }}</p>
                    </div>
                    <span class="px-3 py-1 bg-amber-100 text-amber-700 text-xs font-bold rounded-full uppercase tracking-wider">
                        PENGUMUMAN
                    </span>
                </div>
            </div>

            {{-- Area Gambar/Brosur --}}
            <div class="p-4 md:p-6 flex flex-col items-center">
                <div class="w-full max-w-2xl group relative mb-6">
                    @if ($profil_sekolah && $profil_sekolah->brosur_info)
                        <img src="{{ asset('storage/' . $profil_sekolah->brosur_info) }}"
                             alt="Informasi penting"
                             class="w-full h-auto rounded-2xl shadow-lg border border-gray-100 transition-all duration-500 group-hover:scale-[1.01]">
                    @else
                        <img src="{{ asset('storage/logos/brosur.png') }}"
                             alt="Informasi penting default"
                             class="w-full h-auto rounded-2xl shadow-lg border border-gray-100">
                    @endif
                    <div class="absolute inset-0 rounded-2xl ring-1 ring-black/5 pointer-events-none"></div>
                </div>

                {{-- Tombol Aksi --}}
                <div class="w-full max-w-md space-y-4">
                    @if($profil_sekolah && $profil_sekolah->brosur_info)
                    <a href="{{ asset('storage/' . $profil_sekolah->brosur_info) }}" download
                       class="flex items-center justify-center gap-3 w-full py-3 bg-gray-800 hover:bg-black text-white font-bold rounded-2xl shadow-lg transition text-sm">
                        <i class="fas fa-download"></i>
                        Unduh Brosur Informasi
                    </a>
                    @endif
                </div>
            </div>

            {{-- Footer Kartu --}}
            <div class="p-4 bg-blue-900 text-white text-center">
                <p class="text-xs opacity-80 italic">"Mewujudkan Generasi Cerdas, Berakhlak Mulia, dan Bertaqwa"</p>
            </div>
        </div>

    </div>
</div>
@endsection
