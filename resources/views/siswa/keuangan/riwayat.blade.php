<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Riwayat Pembayaran') }}
        </h2>
    </x-slot>

    <div class="py-6" style= min-height: 100vh;">
        <div class="max-w-4xl mx-auto px-6 sm:px-6 lg:px-6 space-y-5">

            {{-- HEADER --}}
            <div>
                <p class="text-sm text-gray-500 mt-0.5">Lihat semua transaksi pembayaran SPP Anda</p>
            </div>

            {{-- FILTER BULAN --}}
            <div>
                <form method="GET" action="{{ route('siswa.keuangan.riwayat') }}">
                    <select name="bulan" onchange="this.form.submit()"
                        class="text-sm border-gray-300 rounded-xl shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2 pl-4 pr-8 bg-white cursor-pointer">
                        <option value="">Semua Bulan</option>
                        @foreach (['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $bln)
                            <option value="{{ $bln }}" {{ request('bulan') == $bln ? 'selected' : '' }}>{{ $bln }}</option>
                        @endforeach
                    </select>
                </form>
            </div>

            {{-- DAFTAR RIWAYAT --}}
            @forelse($pembayarans ?? [] as $p)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-5 py-4">

                        {{-- Header Kartu --}}
                        <div class="flex items-start justify-between gap-3 mb-3">
                            <div class="flex items-center gap-3">
                                {{-- Checkbox / Indikator --}}
                                <div class="flex items-center justify-center w-5 h-5 rounded border-2 flex-shrink-0"
                                    style="border-color:#1072B8;">
                                    <svg class="w-3 h-3" style="color:#1072B8;" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div>
                                    @php
                                        $namaTagihan = $p->detailPembayaran->map(fn($d) => $d->tagihanSpp?->masterTagihan?->nama_tagihan)->filter()->unique()->implode(', ');
                                        $periodeTagihan = $p->detailPembayaran->map(fn($d) => $d->tagihanSpp?->bulan ? $d->tagihanSpp->bulan . ' ' . $d->tagihanSpp->tahun : null)->filter()->unique()->implode(', ');
                                    @endphp
                                    <p class="font-bold text-gray-900 text-sm leading-tight">
                                        {{ $namaTagihan ?: 'Pembayaran' }}
                                        @if($periodeTagihan)
                                            {{ $periodeTagihan }}
                                        @endif
                                    </p>
                                    <p class="text-xs text-gray-400 mt-0.5">{{ $periodeTagihan }}</p>
                                </div>
                            </div>

                            <div class="flex items-center gap-2 flex-shrink-0">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-green-100 text-green-700 border border-green-200 inline-flex items-center gap-1">
                                    <svg class="w-2.5 h-2.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    Berhasil
                                </span>
                                <p class="text-base font-extrabold" style="color:#1072B8;">
                                    Rp {{ number_format($p->total_bayar, 0, ',', '.') }}
                                </p>
                            </div>
                        </div>

                        {{-- Divider --}}
                        <div class="border-t border-gray-50 mb-3"></div>

                        {{-- Detail Grid --}}
                        <div class="grid grid-cols-3 gap-x-4 gap-y-2 text-xs mb-4">
                            <div>
                                <span class="text-gray-400 block">ID Transaksi</span>
                                <span class="font-mono font-bold text-gray-700 text-[11px]">{{ $p->kode_pembayaran }}</span>
                            </div>
                            <div>
                                <span class="text-gray-400 block">Tanggal</span>
                                <span class="font-semibold text-gray-700">{{ $p->created_at->format('d M Y') }}</span>
                            </div>
                            <div>
                                <span class="text-gray-400 block">Metode</span>
                                <span class="font-semibold text-gray-700 capitalize">
                                    {{ $p->metode === 'tunai' ? 'Tunai / Manual' : ucfirst($p->metode) }}
                                </span>
                            </div>
                        </div>

                        {{-- CETAK BUTTON --}}
                        <a href="{{ route('admin.keuangan.pembayaran.cetak', $p->id) }}" target="_blank"
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-bold border-2 transition hover:opacity-80"
                            style="border-color:#1072B8; color:#1072B8; background: #e8f2fb;">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                            </svg>
                            Cetak Kuitansi
                        </a>

                    </div>
                </div>
            @empty
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 px-5 py-14 text-center">
                    <div class="w-14 h-14 rounded-full flex items-center justify-center mx-auto mb-3"
                        style="background:#e8f2fb;">
                        <svg class="w-7 h-7" style="color:#1072B8;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <p class="font-bold text-gray-700 text-sm">Belum ada riwayat pembayaran</p>
                    <p class="text-xs text-gray-400 mt-1">Transaksi pembayaran Anda akan muncul di sini.</p>
                </div>
            @endforelse

            {{-- PAGINATION --}}
            @if(isset($pembayarans) && $pembayarans->hasPages())
                <div class="flex justify-center">
                    {{ $pembayarans->appends(request()->query())->links() }}
                </div>
            @endif

        </div>
    </div>
</x-app-layout>