@extends('layouts.landing')

@section('content')
{{--
  PERBAIKAN:
  1. Ganti 'h-full' menjadi 'min-h-screen' agar tinggi minimal se-layar tapi bisa memanjang ke bawah.
  2. Tambahkan 'pb-12' (padding bottom) agar konten tidak mentok ke footer/bawah layar.
--}}
<div data-aos="fade-in" class="bg-gray-50 min-h-screen pt-6 pb-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <a href="{{ route('galeri.index') }}" class="inline-flex items-center text-[#1072B8] mb-4 font-bold hover:gap-3 transition-all gap-2">
            <i class="fas fa-arrow-left"></i> Kembali ke Galeri
        </a>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            {{-- KOLOM KIRI --}}
            <div data-aos="fade-up" data-aos-delay="300" class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                    <img src="{{ asset('storage/' . $item->gambar) }}" class="w-full h-auto object-cover" alt="{{ $item->judul }}">

                    <div class="p-8">
                        <div class="flex items-center gap-3 mb-4 text-sm text-gray-500 font-medium">
                            <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs uppercase">Dokumentasi</span>
                            <span>•</span>
                            <span>{{ $item->created_at->format('d M Y') }}</span>
                        </div>

                        <h1 class="text-3xl font-bold text-gray-900 mb-6 leading-tight break-all">
                            {{ $item->judul }}
                        </h1>

                        <div class="prose prose-blue max-w-none text-gray-700 leading-relaxed text-justify break-all" style="font-family: 'Nunito', sans-serif;">
                            {!! nl2br(e($item->deskripsi)) !!}
                        </div>
                    </div>
                </div>
            </div>

            {{-- KOLOM KANAN (SIDEBAR) --}}
            <div data-aos="fade-left" class="space-y-6">

                {{-- Info Sekolah --}}
                <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100">
                    <h3 class="text-xl font-bold text-[#1072B8] mb-4 border-b pb-2">Informasi Sekolah</h3>
                    <ul class="space-y-4">
                        <li class="flex gap-3">
                            <i class="fas fa-map-marker-alt text-blue-600 mt-1"></i>
                            <div>
                                <p class="text-xs text-gray-400 uppercase font-bold">Alamat</p>
                                <p class="text-sm text-gray-700">{{ $profil_sekolah->alamat ?? 'Bandar Lampung' }}</p>
                            </div>
                        </li>
                        <li class="flex gap-3">
                            <i class="fas fa-phone text-blue-600 mt-1"></i>
                            <div>
                                <p class="text-xs text-gray-400 uppercase font-bold">Telepon</p>
                                <p class="text-sm text-gray-700">{{ $profil_sekolah->telepon ?? '-' }}</p>
                            </div>
                        </li>
                        <li class="flex gap-3">
                            <i class="fas fa-envelope text-blue-600 mt-1"></i>
                            <div>
                                <p class="text-xs text-gray-400 uppercase font-bold">Email</p>
                                <p class="text-sm text-gray-700">{{ $profil_sekolah->email ?? '-' }}</p>
                            </div>
                        </li>
                    </ul>
                </div>

                {{-- Kotak Info Penting --}}
                <div class="bg-gray-50 p-6 rounded-3xl shadow-lg border border-gray-100 group overflow-hidden">
                    <h3 class="block mb-4 text-center bg-amber-500 hover:bg-amber-400 text-white font-bold py-2 rounded-xl transition shadow-md">
                        {{ $profil_sekolah->info_penting ?? 'Informasi Penting' }}
                    </h3>

                    <p class="text-sm text-blue-900 text-center mb-5 italic">"Mewujudkan Generasi Cerdas dan Berakhlak Mulia."</p>

                    <div class="mb-4 rounded-2xl overflow-hidden border-2 border-white/20">
                        @if ($profil_sekolah && $profil_sekolah->brosur_info)
                            <img src="{{ asset('storage/' . $profil_sekolah->brosur_info) }}" alt="Informasi penting" class="w-full h-auto transition-transform duration-500 group-hover:scale-110">
                        @else
                            <img src="{{ asset('storage/logos/brosur.png') }}" alt="Default" class="w-full h-auto">
                        @endif
                    </div>
                </div>

            </div> {{-- End Sidebar --}}

        </div>
    </div>
</div>
@endsection
