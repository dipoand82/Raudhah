@extends('layouts.landing')

@section('content')
    {{-- BAGIAN BANNER HERO (Letak Tetap Sama) --}}
    <div class="max-w-[1440px] mx-auto">
        <div class="relative bg-gray-900 h-[500px] md:h-[600px] flex items-center justify-center overflow-hidden rounded-md">
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
                    <a href="{{ route('info.index') }}"
                        class="w-full sm:w-auto px-8 py-3 bg-amber-600 hover:bg-amber-500 text-white font-bold rounded-full shadow-lg transition-all transform hover:scale-105 flex items-center justify-center gap-2">
                        Informasi
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- PEMBUNGKUS KONTEN UTAMA DENGAN BG-GRAY-50 --}}
    <div class="bg-gray-50 py-1"> {{-- Menambahkan background abu-abu terang --}}

        <main class="max-w-7xl mx-auto px-4 sm:px-2 lg:px-2 py-6 space-y-2">
            <section class="scroll-mt-40 py-6"> {{-- Background white-50 dihapus agar menyatu dengan bg-gray-50 --}}
                <div class="max-w-7xl mx-auto px-4">
                    <div class="text-center mb-3">
                        <h2 data-aos="fade-up"
                            class="text-4xl font-extrabold text-blue-900 uppercase tracking-wider animate-gradient-x">
                            {{ $profil_sekolah->nama_sekolah ?? 'SMP IT Raudhah' }}
                        </h2>
                        <div class="h-1.5 w-24 bg-blue-600 mx-auto mt-4 rounded-full"></div>
                    </div>

                    <div class="relative mb-10 group cursor-default" data-aos="fade-up">
                        <div class="absolute -bottom-4 -right-4 w-full h-full bg-blue-900 rounded-3xl z-0
                                    opacity-10 transition-all duration-500 ease-out
                                    group-hover:-bottom-3 group-hover:-right-3 group-hover:opacity-10">
                        </div>

                        <div class="bg-white p-8 md:p-12 rounded-3xl shadow-xl border border-gray-300 relative overflow-hidden z-10
                                    transition-transform duration-500 group-hover:-translate-y-2">
                            <div class="prose prose-lg max-w-none  text-gray-700 leading-loose text-center justify-center relative z-10"
                                style="font-family: 'Nunito', sans-serif; font-weight: 600;">
                                {!! nl2br(e($profil_sekolah->deskripsi_singkat ?? 'Deskripsi sekolah belum diisi.')) !!}
                            </div>
                        </div>
                    </div>

                    <div class="grid md:grid-cols-2 gap-8 mb-16">
                        <div data-aos="flip-right"
                            class="group bg-white p-8 rounded-3xl border-t-8 border-blue-600 shadow-lg hover:shadow-2xl duration-300">
                            <div class="flex items-center mb-6">
                                <div class="p-3 bg-blue-100 rounded-2xl mr-4 group-hover:bg-blue-600 transition-colors"></div>
                                <h3 class="text-2xl font-bold text-blue-800 uppercase tracking-wide">Program Unggulan</h3>
                            </div>
                            <p class="text-gray-600 text-lg leading-relaxed ">
                                @if ($profil_sekolah && $profil_sekolah->program_unggulan)
                                    @foreach (explode("\n", $profil_sekolah->program_unggulan) as $program)
                                        @if (trim($program) != '')
                                            <div class="flex items-start gap-3 p-3 rounded-xl hover:bg-gray-50 transition">
                                                <span class="flex-shrink-0 w-6 h-6 bg-teal-100 text-blue-600 rounded-full flex items-center justify-center text-sm font-bold">★</span>
                                                <span class="text-gray-600 font-bold leading-relaxed">{{ $program }}</span>
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
                                <div class="p-3 bg-green-100 rounded-2xl mr-4 group-hover:bg-green-500 transition-colors"></div>
                                <h3 class="text-2xl font-bold text-green-800 uppercase tracking-wide">Alasan Memilih Kami</h3>
                            </div>
                            <div class="text-gray-600 text-lg leading-relaxed space-y-2">
                                <div class="space-y-1">
                                    @if ($profil_sekolah && $profil_sekolah->alasan_memilih)
                                        @foreach (explode("\n", $profil_sekolah->alasan_memilih) as $alasan)
                                            @if (trim($alasan) != '')
                                                <div class="flex items-start gap-3 p-3 rounded-xl hover:bg-gray-50 transition">
                                                    <span class="flex-shrink-0 w-6 h-6 bg-blue-100 text-green-600 rounded-full flex items-center justify-center text-sm font-bold">✓</span>
                                                    <span class="text-gray-600 font-bold leading-relaxed">{{ $alasan }}</span>
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

                    <div class="bg-white p-8 rounded-3xl shadow-xl shadow-blue-100/50 border border-gray-100 relative overflow-hidden">
                        <div data-aos="fade-left" class="flex items-center gap-3 mb-6 relative z-10">
                            <div class="p-3 bg-[#1072B8] rounded-2xl text-white shadow-md">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-7.714 2.143L11 21l-2.286-6.857L1 12l7.714-2.143L11 3z" />
                                </svg>
                            </div>
                            <h2 class="text-2xl font-bold text-gray-800">Visi</h2>
                        </div>

                        <div data-aos="fade-left" class="relative" style="font-family: 'Nunito', sans-serif; font-weight: 600;">
                            @if ($profil_sekolah && $profil_sekolah->visi)
                                <div class="flex gap-1 items-start ">
                                    <div class="flex-shrink-0 text-[#1072B8] px-4">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 0L14.59 9.41L24 12L14.59 14.59L12 24L9.41 14.59L0 12L9.41 9.41L12 0Z" />
                                        </svg>
                                    </div>
                                    <p class="text-gray-700 text-md leading-relaxed ml-2">{!! nl2br(e($profil_sekolah->visi)) !!}</p>
                                </div>
                            @else
                                <p class="text-gray-500 italic pl-2">Visi belum tersedia</p>
                            @endif
                        </div>
                    </div>

                    <div class="bg-white p-8 rounded-3xl shadow-xl shadow-green-100/50 border border-gray-100 mt-12">
                        <div data-aos="fade-left" class="flex items-center gap-3 mb-6">
                            <div class="p-3 bg-teal-500 rounded-2xl text-white">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <h2 class="text-2xl font-bold text-gray-800">Misi</h2>
                        </div>

                        <div data-aos="fade-left" class="space-y-4" style="font-family: 'Nunito', sans-serif; font-weight: 600;">
                            @if ($profil_sekolah && $profil_sekolah->misi)
                                @php
                                    $lines = explode("\n", str_replace("\r", '', $profil_sekolah->misi ?? ''));
                                    $daftar_misi = array_filter(array_map('trim', $lines));
                                @endphp

                                @forelse ($daftar_misi as $item)
                                    <div class="flex gap-3 items-start">
                                        <span class="font-bold text-teal-600 text-lg ml-6 leading-tight min-w-[25px] ">{{ $loop->iteration }}.</span>
                                        <p class="text-gray-700 leading-relaxed">{{ preg_replace('/^\d+\.\s*/', '', $item) }}</p>
                                    </div>
                                @empty
                                    <p class="text-gray-500 text-lg">Misi belum tersedia</p>
                                @endforelse
                            @else
                                <p class="text-gray-500">Data profil belum tersedia</p>
                            @endif
                        </div>
                    </div>

                    <div data-aos="fade-up" class="mt-12 bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
                        <div class="p-7 border-b border-gray-50 bg-gray-50/90">
                            <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                                <div  class="text-center md:text-left">
                                    <h3 class="text-2xl font-bold text-blue-900 uppercase ">
                                        {{ $profil_sekolah->info_penting ?? 'Informasi Penting' }}
                                    </h3>
                                    <p class="text-gray-500">Tahun Akademik {{ date('Y') }}/{{ date('Y') + 1 }}</p>
                                </div>
                                <span class="px-4 py-1.5 bg-amber-100 text-amber-700 text-sm font-bold rounded-full uppercase tracking-wider">Informasi</span>
                            </div>
                        </div>
                        <div class="p-4 md:p-6 flex justify-center">
                            <div class="w-full max-w-2xl px-4 group relative">
                                @if ($profil_sekolah && $profil_sekolah->brosur_info)
                                    <img src="{{ asset('storage/' . $profil_sekolah->brosur_info) }}" alt="Informasi penting"
                                        class="w-full h-auto rounded-2xl shadow-lg border border-gray-100 transition-all duration-500 group-hover:scale-[1.01]">
                                @else
                                    <img src="{{ asset('storage/logos/brosur.png') }}" alt="Informasi penting"
                                        class="w-full h-auto rounded-2xl shadow-lg border border-gray-100">
                                @endif
                                <div class="absolute inset-0 rounded-2xl ring-1 ring-black/5 pointer-events-none"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section data-aos="fade-up" class="scroll-mt-12 py-12">
                <div class="text-center mb-6">
                    <h2 class="text-3xl font-bold text-gray-800 uppercase tracking-widest ">Galeri Kegiatan</h2>
                    <div class="h-1 w-20 bg-blue-600 mx-auto mt-2 rounded-full"></div>
                </div>

                <div data-aos="fade-up" class="grid grid-cols-2 md:grid-cols-4 gap-4 px-4">
                    @forelse($galeri->take(4) as $item)
                        <div class="aspect-square bg-gray-200 rounded-xl overflow-hidden group border-4 border-white shadow-md relative">
                            <a href="{{ route('galeri.show', $item->id) }}">
                                <img src="{{ asset('storage/' . $item->gambar) }}"
                                     class="w-full h-full object-cover group-hover:scale-110 transition duration-500 cursor-pointer"
                                     alt="{{ $item->judul }}">
                                <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300 p-2 text-center">
                                    <p class="text-white text-xs font-bold uppercase">{{ $item->judul }}</p>
                                </div>
                            </a>
                        </div>
                    @empty
                        @for ($i = 1; $i <= 4; $i++)
                        <div class="aspect-square bg-gray-200 rounded-xl overflow-hidden group border-4 border-white shadow-md">
                            <img src="https://via.placeholder.com/400x400?text=Kegiatan+{{ $i }}"
                                 class="w-full h-full object-cover group-hover:scale-110 transition duration-500 cursor-pointer">
                        </div>
                        @endfor
                    @endforelse
                </div>

                <div class="text-center mt-10">
                    <a href="{{ route('galeri.index') }}" class="px-8 py-3 bg-blue-600 text-white font-bold rounded-full hover:bg-blue-700 transition shadow-lg inline-flex items-center gap-2">
                        Lihat Galeri Lainnya <i class="fas fa-arrow-right text-sm"></i>
                    </a>
                </div>
            </section>
        </main>
    </div>
@endsection
