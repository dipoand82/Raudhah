<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Siswa') }}
        </h2>
    </x-slot>

    <div class="py-8" style="min-height: 100vh;">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- WELCOME BANNER --}}
            <div class="rounded-2xl overflow-hidden shadow-md" style="background: linear-gradient(135deg, #1072B8 0%, #0d5a91 60%, #0a4a78 100%);">
                <div class="px-6 py-6 flex items-center justify-between">
                    <div>
                        <p class="text-blue-200 text-sm font-medium mb-1">Selamat datang di sistem pembayaran SPP</p>
                        <h1 class="text-white text-2xl font-bold tracking-tight">
                            {{ auth()->user()->siswa->nama_lengkap ?? auth()->user()->name }}
                        </h1>
                        <p class="text-blue-200 text-sm mt-1">
                            SMP IT Raudhah El Jannah
                        </p>
                    </div>
                    <div class="hidden sm:flex items-center justify-center w-16 h-16 rounded-2xl" style="background: rgba(255,255,255,0.15);">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                </div>
            </div>

            {{-- STAT CARDS --}}
            <div class="grid grid-cols-3 gap-4">
                {{-- Total Tagihan --}}
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-blue-100 flex flex-col gap-2">
                    <div class="flex items-center justify-between">
                        <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Total Tagihan</p>
                        <div class="w-9 h-9 rounded-xl flex items-center justify-center" style="background:#e8f2fb;">
                            <svg class="w-4 h-4" style="color:#1072B8;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                    </div>
                    <p class="text-3xl font-extrabold text-gray-900">{{ $totalTagihan ?? 0 }}</p>
                </div>

                {{-- Belum Lunas --}}
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-orange-100 flex flex-col gap-2">
                    <div class="flex items-center justify-between">
                        <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Belum Lunas</p>
                        <div class="w-9 h-9 rounded-xl flex items-center justify-center bg-orange-50">
                            <svg class="w-4 h-4 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
                            </svg>
                        </div>
                    </div>
                    <p class="text-3xl font-extrabold text-orange-500">{{ $belumLunas ?? 0 }}</p>
                </div>

                {{-- Sudah Lunas --}}
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-green-100 flex flex-col gap-2">
                    <div class="flex items-center justify-between">
                        <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Sudah Lunas</p>
                        <div class="w-9 h-9 rounded-xl flex items-center justify-center bg-green-50">
                            <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <p class="text-3xl font-extrabold text-green-600">{{ $sudahLunas ?? 0 }}</p>
                </div>
            </div>

            {{-- TAGIHAN BELUM LUNAS --}}
            <div>
                <h2 class="text-base font-bold text-gray-800 mb-3">Tagihan Belum Lunas</h2>

                @forelse($tagihanBelumLunas ?? [] as $tagihan)
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 mb-3 overflow-hidden">
                        <div class="px-5 py-4">
                            <div class="flex items-start justify-between gap-3 mb-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0"
                                        style="background:#e8f2fb;">
                                        <svg class="w-5 h-5" style="color:#1072B8;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-bold text-gray-900 text-sm leading-tight">
                                            {{ $tagihan->masterTagihan->nama_tagihan }}
                                            @if($tagihan->bulan)
                                                Bulan {{ $tagihan->bulan }} {{ $tagihan->tahun }}
                                            @endif
                                        </p>
                                        <p class="text-xs text-gray-400 mt-0.5">{{ $tagihan->bulan }} {{ $tagihan->tahun }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2 flex-shrink-0">
                                    @if($tagihan->status === 'cicilan')
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-orange-100 text-orange-700 border border-orange-200">Cicilan</span>
                                    @else
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-yellow-100 text-yellow-700 border border-yellow-200">Belum Lunas</span>
                                    @endif
                                    <p class="text-base font-extrabold" style="color:#1072B8;">
                                        Rp {{ number_format($tagihan->jumlah_tagihan, 0, ',', '.') }}
                                    </p>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-x-4 gap-y-1 text-xs text-gray-500 mb-4">
                                <div>
                                    <span class="text-gray-400">Jatuh Tempo</span>
                                    <p class="font-semibold text-gray-700">
                                        {{ $tagihan->jatuh_tempo ? \Carbon\Carbon::parse($tagihan->jatuh_tempo)->format('d F Y') : '-' }}
                                    </p>
                                </div>
                                <div>
                                    <span class="text-gray-400">Kategori</span>
                                    <p class="font-semibold text-gray-700">{{ $tagihan->masterTagihan->nama_tagihan ?? '-' }}</p>
                                </div>
                                @if($tagihan->terbayar > 0)
                                    <div class="col-span-2">
                                        <span class="text-gray-400">Sudah Dibayar</span>
                                        <p class="font-semibold text-orange-600">Rp {{ number_format($tagihan->terbayar, 0, ',', '.') }} (Sisa: Rp {{ number_format($tagihan->jumlah_tagihan - $tagihan->terbayar, 0, ',', '.') }})</p>
                                    </div>
                                @endif
                            </div>

                            {{-- BAYAR BUTTON --}}
                            <a href="{{ route('siswa.keuangan.bayar', $tagihan->id) }}"
                                class="block w-full text-center py-3 rounded-xl text-white font-bold text-sm shadow-sm transition hover:opacity-90 active:scale-95"
                                style="background: linear-gradient(90deg, #1072B8 0%, #0d5a91 100%);">
                                Bayar Sekarang
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 px-5 py-10 text-center">
                        <div class="w-14 h-14 rounded-full bg-green-50 flex items-center justify-center mx-auto mb-3">
                            <svg class="w-7 h-7 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <p class="font-bold text-gray-700 text-sm">Semua tagihan sudah lunas!</p>
                        <p class="text-xs text-gray-400 mt-1">Tidak ada tagihan yang perlu dibayar saat ini.</p>
                    </div>
                @endforelse
            </div>

        </div>
    </div>
</x-app-layout>