@extends('layouts.landing')

@section('content')
<div data-aos="fade-in" class="bg-gray-50 min-h-screen py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            {{-- KOLOM KIRI: KONTEN UTAMA (Detail Foto) --}}
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                    {{-- Gambar Besar --}}
                    <img src="{{ asset('storage/' . $item->gambar) }}"
                         class="w-full h-auto object-cover" alt="{{ $item->judul }}">

                    <div class="p-8">
                        <div class="flex items-center gap-3 mb-4 text-sm text-gray-500 font-medium">
                            <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs uppercase">Dokumentasi</span>
                            <span>•</span>
                            <span>{{ $item->created_at->format('d M Y') }}</span>
                        </div>

                        <h1 class="text-3xl font-bold text-gray-900 mb-6 leading-tight">{{ $item->judul }}</h1>

                        <div class="prose prose-blue max-w-none text-gray-700 leading-relaxed text-justify" style="font-family: 'Nunito', sans-serif;">
                            {!! nl2br(e($item->deskripsi)) !!}
                        </div>
                    </div>
                </div>

                <a href="{{ route('galeri.index') }}" class="inline-flex items-center text-blue-600 font-bold hover:gap-3 transition-all gap-2">
                    <i class="fas fa-arrow-left"></i> Kembali ke Galeri
                </a>
            </div>

            {{-- KOLOM KANAN: SIDEBAR INFO (Seperti Gambar 3) --}}
            <div data-aos="fade-in" class="space-y-6">
                <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100">
                    <h3 class="text-xl font-bold text-blue-900 mb-4 border-b pb-2">Informasi Sekolah</h3>
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

                <div class="bg-blue-900 p-6 rounded-3xl shadow-lg text-white">
                    <h3 class="font-bold mb-2">Pendaftaran Siswa Baru</h3>
                    <p class="text-sm text-blue-100 mb-4">Mewujudkan Generasi Cerdas dan Berakhlak Mulia.</p>
                    <a href="#" class="block text-center bg-amber-500 hover:bg-amber-400 text-white font-bold py-2 rounded-xl transition">
                        Hubungi Kami
                    </a>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
