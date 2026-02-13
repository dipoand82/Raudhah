@extends('layouts.landing')

@section('content')
    {{-- Wrapper utama dengan bg-gray-50 tanpa max-width agar warna abu-abu memenuhi layar --}}
    <div class="bg-gray-50 min-h-screen pt-6 pb-12">

        {{-- Kontainer dalam dengan max-width agar konten tetap di tengah dan rapi --}}
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Breadcrumb Navigasi --}}
            {{-- <nav class="flex md:hidden mb-4 text-sm font-medium text-gray-500">
                <a href="{{ url('/') }}" class="hover:text-[#1072B8] transition">Beranda</a>
                <span class="mx-2">/</span>
                <a href="{{ route('info.index') }}" class="hover:text-[#1072B8] transition">Informasi Penting</a>
                <span class="mx-2">/</span>
                <span class="text-gray-800">Galeri Kegiatan</span>
            </nav> --}}

            {{-- Judul Halaman --}}
            <div data-aos="fade-left" data-aos-delay="300" class="text-center mb-10">
                <h2 class="text-3xl font-bold text-gray-800 uppercase tracking-wider">Galeri Kegiatan</h2>
                <div class="h-1.5 w-20 bg-blue-600 mx-auto mt-2 rounded-full"></div>
            </div>

            {{-- Grid Galeri --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @forelse($galeri as $item)
                    <div data-aos="fade-up" class="bg-white rounded-2xl shadow-md overflow-hidden border border-gray-100 group">

                        {{-- Foto Kegiatan --}}
                        <div class="overflow-hidden h-64">
                            <img src="{{ asset('storage/' . $item->gambar) }}"
                                 class="w-full h-full object-cover transition duration-500 group-hover:scale-110">
                        </div>

                        <div class="p-5">
                            {{-- Judul: break-all mencegah teks panjang tanpa spasi merusak layout --}}
                            <h3 class="font-bold text-lg text-blue-900 break-all line-clamp-1">
                                {{ $item->judul }}
                            </h3>

                            {{-- Deskripsi: line-clamp-2 membatasi teks agar seragam 2 baris --}}
                            <p class="text-gray-600 text-sm mt-2 line-clamp-2 break-all">
                                {{ $item->deskripsi }}
                            </p>

                            <a href="{{ route('galeri.show', $item->id) }}"
                                class="inline-flex items-center mt-4 text-blue-600 font-bold hover:gap-2 transition-all text-sm gap-1">
                                Lihat Detail <span>→</span>
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="col-span-3 text-center py-20">
                        <p class="text-gray-400 italic">Belum ada foto kegiatan untuk SMP IT Raudhah.</p>
                    </div>
                @endforelse
            </div>

            {{-- Pagination untuk pindah halaman --}}
            <div class="mt-12">
                {{ $galeri->links() }}
            </div>
        </div>
    </div>
@endsection
