<x-app-layout>
    @if (str_contains(Auth::user()->email, 'dummy') || is_null(Auth::user()->email))
        <div x-data="{ showEmailModal: true }" x-show="showEmailModal"
            class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900 bg-opacity-50">

            <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6 mx-4">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold text-blue-700">📧 Hubungkan Email Anda</h3>
                    <button @click="showEmailModal = false"
                        class="text-gray-400 hover:text-gray-600 font-bold text-2xl">&times;</button>
                </div>

                <p class="text-sm text-gray-600 mb-4">
                    Demi keamanan, mohon masukkan email pribadi Anda. Email ini berfungsi untuk memulihkan akun jika
                    Anda lupa password.
                </p>

                <form method="POST" action="{{ route('profile.update') }}">
                    @csrf
                    @method('patch')

                    <input type="hidden" name="name" value="{{ Auth::user()->name }}">

                    <x-text-input name="email" type="email" class="w-full mb-3" placeholder="email@contoh.com"
                        required />

                    <div class="flex justify-end gap-2">
                        <x-secondary-button @click="showEmailModal = false">Nanti Saja</x-secondary-button>
                        <x-primary-button>Simpan Email</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    @endif
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Siswa') }}
        </h2>
    </x-slot>

    {{-- Midtrans Snap JS (load sekali di sini) --}}
    @if (config('services.midtrans.is_production'))
        <script src="https://app.midtrans.com/snap/snap.js" data-client-key="{{ config('services.midtrans.client_key') }}">
        </script>
    @else
        <script src="https://app.sandbox.midtrans.com/snap/snap.js"
            data-client-key="{{ config('services.midtrans.client_key') }}"></script>
    @endif



    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        /* Modal Backdrop */
        #modal-backdrop {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(10, 30, 60, 0.55);
            backdrop-filter: blur(4px);
            z-index: 999;
            align-items: center;
            justify-content: center;
        }

        #modal-backdrop.active {
            display: flex;
            animation: fadeIn 0.2s ease;
        }

        /* Modal Box */
        #modal-box {
            background: #fff;
            border-radius: 24px;
            width: 100%;
            max-width: 420px;
            margin: 1rem;
            box-shadow: 0 25px 60px rgba(10, 30, 60, 0.25);
            transform: translateY(20px);
            opacity: 0;
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
            overflow: hidden;
        }

        #modal-backdrop.active #modal-box {
            transform: translateY(0);
            opacity: 1;
        }

        /* Modal Header Gradient */
        .modal-header {
            background: linear-gradient(135deg, #1072B8 0%, #0a4a78 100%);
            padding: 20px 24px 18px;
            position: relative;
        }

        /* Row item in modal */
        .detail-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px dashed #f0f0f0;
        }

        .detail-row:last-child {
            border-bottom: none;
        }

        /* Loading spinner */
        .spinner {
            display: inline-block;
            width: 18px;
            height: 18px;
            border: 2px solid rgba(255, 255, 255, 0.4);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 0.7s linear infinite;
            vertical-align: middle;
            margin-right: 8px;
        }

        /* Pulse badge */
        .pulse-dot {
            display: inline-block;
            width: 8px;
            height: 8px;
            background: #22c55e;
            border-radius: 50%;
            margin-right: 6px;
            box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.5);
            animation: pulse 1.5s infinite;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.5);
            }

            70% {
                box-shadow: 0 0 0 8px rgba(34, 197, 94, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(34, 197, 94, 0);
            }
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(12px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .card-animate {
            animation: slideUp 0.4s ease both;
        }

        .card-animate:nth-child(1) {
            animation-delay: 0.05s;
        }

        .card-animate:nth-child(2) {
            animation-delay: 0.1s;
        }

        .card-animate:nth-child(3) {
            animation-delay: 0.15s;
        }
    </style>

    <div class="py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            {{-- WELCOME BANNER --}}
            <div class="rounded-2xl overflow-hidden shadow-md"
                style="background: linear-gradient(135deg, #1072B8 0%, #0d5a91 60%, #0a4a78 100%);">
                <div class="px-6 py-6 flex items-center justify-between">
                    <div>
                        <p class="text-blue-200 text-sm font-medium mb-1">Selamat datang di sistem pembayaran SPP</p>
                        <h1 class="text-white text-2xl font-bold tracking-tight">
                            {{ auth()->user()->siswa->nama_lengkap ?? auth()->user()->name }}
                        </h1>
                        <p class="text-blue-200 text-sm mt-1">SMP IT Raudhah El Jannah</p>
                    </div>
                    <div class="hidden sm:flex items-center justify-center w-16 h-16 rounded-2xl"
                        style="background: rgba(255,255,255,0.15);">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                </div>
            </div>

            {{-- STAT CARDS --}}
            <div class="grid grid-cols-3 gap-4">
                <div class="card-animate bg-white rounded-2xl p-5 shadow-sm border border-blue-100 flex flex-col gap-2">
                    <div class="flex items-center justify-between">
                        <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Total Tagihan</p>
                        <div class="w-9 h-9 rounded-xl flex items-center justify-center" style="background:#e8f2fb;">
                            <svg class="w-4 h-4" style="color:#1072B8;" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                    </div>
                    <p class="text-3xl font-extrabold text-gray-900">{{ $totalTagihan ?? 0 }}</p>
                </div>

                <div
                    class="card-animate bg-white rounded-2xl p-5 shadow-sm border border-orange-100 flex flex-col gap-2">
                    <div class="flex items-center justify-between">
                        <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Belum Lunas</p>
                        <div class="w-9 h-9 rounded-xl flex items-center justify-center bg-orange-50">
                            <svg class="w-4 h-4 text-orange-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
                            </svg>
                        </div>
                    </div>
                    <p class="text-3xl font-extrabold text-orange-500">{{ $belumLunas ?? 0 }}</p>
                </div>

                <div
                    class="card-animate bg-white rounded-2xl p-5 shadow-sm border border-green-100 flex flex-col gap-2">
                    <div class="flex items-center justify-between">
                        <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Sudah Lunas</p>
                        <div class="w-9 h-9 rounded-xl flex items-center justify-center bg-green-50">
                            <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <p class="text-3xl font-extrabold text-green-600">{{ $sudahLunas ?? 0 }}</p>
                </div>
            </div>

            {{-- TAGIHAN BELUM LUNAS --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h2 class="text-base font-bold text-gray-800">Tagihan Belum Lunas</h2>
                    <span class="text-xs text-gray-400 font-medium">
                        <span class="pulse-dot"></span>Aktif
                    </span>
                </div>

                <div class="p-4 space-y-4 bg-gray-50/30">
                    @forelse($tagihanBelumLunas ?? [] as $tagihan)
                        <div
                            class="px-6 py-5 bg-white border border-gray-200 rounded-xl shadow-sm hover:shadow-md hover:border-blue-200 transition-all duration-200">
                            {{-- Baris Atas --}}
                            <div class="flex items-start justify-between gap-3 mb-3">
                                <div>
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <p class="font-bold text-gray-900 text-sm">
                                            {{ $tagihan->masterTagihan->nama_tagihan }}
                                            @if ($tagihan->bulan)
                                                Bulan {{ $tagihan->bulan }} {{ $tagihan->tahun }}
                                            @endif
                                        </p>
                                        @if ($tagihan->status === 'cicilan')
                                            <span
                                                class="px-2 py-0.5 rounded text-[10px] font-bold bg-orange-100 text-orange-700 border border-orange-200">Cicilan</span>
                                        @else
                                            <span
                                                class="px-2 py-0.5 rounded text-[10px] font-bold bg-yellow-100 text-yellow-700 border border-yellow-200">Belum
                                                Lunas</span>
                                        @endif
                                    </div>
                                    <p class="text-xs text-gray-400 mt-0.5">{{ $tagihan->bulan }}
                                        {{ $tagihan->tahun }}
                                    </p>
                                </div>
                                <div class="text-right flex-shrink-0">
                                    <p class="text-[10px] text-gray-400 mb-0.5">Jumlah</p>
                                    <p class="text-base font-extrabold text-blue-600">
                                        Rp {{ number_format($tagihan->jumlah_tagihan, 0, ',', '.') }}
                                    </p>
                                </div>
                            </div>

                            {{-- Baris Info --}}
                            <div class="grid grid-cols-2 gap-4 text-xs mb-4">
                                <div>
                                    <p class="text-gray-400 mb-0.5">Jatuh Tempo</p>
                                    <p class="font-semibold text-gray-700">
                                        {{ $tagihan->jatuh_tempo ? \Carbon\Carbon::parse($tagihan->jatuh_tempo)->format('d F Y') : '-' }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-gray-400 mb-0.5">Kategori</p>
                                    <p class="font-semibold text-gray-700">
                                        {{ $tagihan->masterTagihan->nama_tagihan ?? '-' }}</p>
                                </div>
                                @if ($tagihan->terbayar > 0)
                                    <div class="col-span-2">
                                        <p class="text-gray-400 mb-0.5">Sudah Dibayar</p>
                                        <p class="font-semibold text-orange-600">
                                            Rp {{ number_format($tagihan->terbayar, 0, ',', '.') }}
                                            (Sisa: Rp
                                            {{ number_format($tagihan->jumlah_tagihan - $tagihan->terbayar, 0, ',', '.') }})
                                        </p>
                                    </div>
                                @endif
                            </div>

                            {{-- TOMBOL BAYAR — panggil modal dengan teks dinamis --}}
                            <button
                                onclick="bukaModalBayar({
                                id: {{ $tagihan->id }},
                                nama: '{{ addslashes($tagihan->masterTagihan->nama_tagihan) }}',
                                bulan: '{{ $tagihan->bulan }} {{ $tagihan->tahun }}',
                                jumlah: {{ $tagihan->jumlah_tagihan }},
                                terbayar: {{ $tagihan->terbayar ?? 0 }},
                                jatuh_tempo: '{{ $tagihan->jatuh_tempo ? \Carbon\Carbon::parse($tagihan->jatuh_tempo)->format('d F Y') : '-' }}'
                            })"
                                class="w-full text-center py-3 rounded-xl text-white font-bold text-sm transition hover:opacity-90 active:scale-95 cursor-pointer"
                                style="background: {{ $tagihan->status === 'pending' ? 'linear-gradient(90deg, #F59E0B 0%, #D97706 100%)' : 'linear-gradient(90deg, #1072B8 0%, #0d5a91 100%)' }};">

                                @if ($tagihan->status === 'pending')
                                    <div class="flex items-center justify-center gap-2">
                                        <svg class="w-4 h-4 animate-pulse" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Lanjutkan Pembayaran
                                    </div>
                                @else
                                    Bayar Sekarang
                                @endif
                            </button>
                        </div>
                    @empty
                        <div class="px-6 py-14 text-center">
                            <div
                                class="w-14 h-14 rounded-full bg-green-50 flex items-center justify-center mx-auto mb-3">
                                <svg class="w-7 h-7 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                            <p class="font-bold text-gray-700 text-sm">Semua tagihan sudah lunas!</p>
                            <p class="text-xs text-gray-400 mt-1">Tidak ada tagihan yang perlu dibayar saat ini.</p>
                        </div>
                    @endforelse
                </div>

            </div>
        </div>
        <div x-data="{ showAktivasi: {{ $user->must_change_password ? 'true' : 'false' }} }">
            <x-modal name="modal-aktivasi-profil" show="showAktivasi" maxWidth="md" focusable>
                <div class="p-6 text-center">

                    <div class="flex items-center justify-center w-20 h-20 mx-auto mb-6 bg-amber-100 rounded-full">
                        <svg class="w-10 h-10 text-amber-600" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>

                    <h3 class="text-2xl font-black text-gray-900 mb-2">Perbarui Email Anda!</h3>
                    <p class="text-sm text-gray-500 mb-6 px-4">
                        Ini adalah login pertama Anda menggunakan sistem. Silakan ubah email.
                    </p>

                    <div class="p-4 bg-amber-50 border border-amber-200 rounded-xl mb-8 text-left">
                        <p class="text-[10px] text-amber-700 font-black uppercase tracking-widest mb-1">Peringatan
                            Keamanan</p>
                        <p class="text-sm text-amber-900 leading-relaxed justify-center text-center">
                            <strong>Harap  mengganti email bawaan sistem demi keamanan data</strong>
                        </p>
                    </div>

                    <div class="flex flex-col gap-3">
                        <a href="{{ route('profile.edit') }}"
                            class="w-full bg-amber-600 text-white px-6 py-3 rounded-xl text-sm font-bold hover:bg-amber-700 shadow-lg shadow-amber-200 transition-all flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Ya, Perbarui Sekarang
                        </a>
                        <x-secondary-button x-on:click="$dispatch('close')" class="w-full bg-white text-gray-600 px-6 py-3 rounded-xl text-sm font-semibold hover:bg-gray-50 transition-all text-center justify-center">
                            Nanti Saja
                        </x-secondary-button>
                    </div>
                </div>
            </x-modal>
        </div>

        {{-- =========================================
         MODAL KONFIRMASI PEMBAYARAN
         ========================================= --}}
        <div id="modal-backdrop" onclick="tutupModal(event)">
            <div id="modal-box">

                {{-- Modal Header --}}
                <div class="modal-header">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-blue-200 text-xs font-medium mb-0.5">Konfirmasi Pembayaran</p>
                            <h3 class="text-white text-lg font-bold" id="modal-nama-tagihan">—</h3>
                        </div>
                        <button onclick="tutupModal(null, true)"
                            class="w-8 h-8 rounded-full flex items-center justify-center transition hover:bg-white/20"
                            style="color: rgba(255,255,255,0.7);">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    {{-- Ikon QRIS kecil --}}
                    <div class="mt-3 inline-flex items-center gap-1.5 bg-white/15 rounded-lg px-3 py-1.5">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                        </svg>
                        <span class="text-white text-xs font-semibold">Bayar via QRIS</span>
                    </div>
                </div>

                {{-- Modal Body --}}
                <div class="p-6">

                    {{-- Detail rows --}}
                    <div class="mb-5">
                        <div class="detail-row">
                            <span class="text-xs text-gray-400 font-medium">Periode</span>
                            <span class="text-xs font-semibold text-gray-700" id="modal-periode">—</span>
                        </div>
                        <div class="detail-row">
                            <span class="text-xs text-gray-400 font-medium">Jatuh Tempo</span>
                            <span class="text-xs font-semibold text-gray-700" id="modal-jatuh-tempo">—</span>
                        </div>
                        <div class="detail-row">
                            <span class="text-xs text-gray-400 font-medium">Nominal Tagihan</span>
                            <span class="text-xs font-semibold text-gray-800" id="modal-nominal">—</span>
                        </div>
                        <div class="detail-row" id="modal-row-terbayar" style="display:none;">
                            <span class="text-xs text-gray-400 font-medium">Sudah Dibayar</span>
                            <span class="text-xs font-semibold text-orange-500" id="modal-terbayar">—</span>
                        </div>
                        <div class="detail-row">
                            <span class="text-xs text-orange-500 font-medium">Biaya Layanan QRIS (0.7%)</span>
                            <span class="text-xs font-semibold text-orange-500" id="modal-fee">—</span>
                        </div>
                    </div>

                    {{-- Total --}}
                    <div class="rounded-xl p-4 mb-5"
                        style="background: linear-gradient(135deg, #e8f2fb 0%, #dceefa 100%); border: 1px solid #c3dff5;">
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-bold text-gray-700">Total Bayar</span>
                            <span class="text-xl font-extrabold" style="color:#1072B8;" id="modal-total">—</span>
                        </div>
                    </div>

                    {{-- Info --}}
                    <div class="bg-blue-50 border border-blue-100 rounded-xl p-3 mb-5">
                        <p class="text-xs text-blue-600 leading-relaxed">
                            <span class="font-bold">ℹ️ Cara bayar:</span> Klik tombol di bawah, QR Code akan muncul.
                            Scan
                            menggunakan GoPay, OVO, Dana, atau mobile banking.
                        </p>
                    </div>

                    {{-- Tombol Bayar --}}
                    <button id="modal-pay-btn" onclick="prosessPembayaran()"
                        class="w-full py-3.5 rounded-xl text-white font-bold text-sm transition active:scale-95 flex items-center justify-center gap-2"
                        style="background: linear-gradient(90deg, #1072B8 0%, #0d5a91 100%); box-shadow: 0 4px 15px rgba(16,114,184,0.35);">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                        </svg>
                        Buka QR Code & Bayar
                    </button>

                    <button onclick="tutupModal(null, true)"
                        class="w-full mt-2.5 py-2.5 rounded-xl text-gray-400 text-sm font-medium hover:text-gray-600 hover:bg-gray-50 transition">
                        Batalkan
                    </button>

                </div>
            </div>
        </div>

        <div id="modal-success-backdrop"
            class="fixed inset-0 z-[1000] hidden items-center justify-center bg-slate-900/50 backdrop-blur-sm">
            <div class="bg-white rounded-3xl w-full max-w-sm m-4 overflow-hidden shadow-2xl transform transition-all">
                <div class="p-8 text-center">
                    <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-10 h-10 text-green-500" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-extrabold text-gray-900 mb-2">Pembayaran Berhasil!</h3>
                    <p class="text-gray-500 text-sm mb-6">Tagihan Anda telah lunas dan tercatat di sistem.</p>

                    <div class="bg-gray-50 rounded-2xl p-4 text-left mb-6 space-y-2 border border-gray-100">
                        <div class="flex justify-between text-xs">
                            <span class="text-gray-400">Order ID</span>
                            <span class="font-bold text-gray-700" id="success-order-id">-</span>
                        </div>
                        <div class="flex justify-between text-xs">
                            <span class="text-gray-400">Item</span>
                            <span class="font-bold text-gray-700" id="success-item">-</span>
                        </div>
                        <div class="flex justify-between pt-2 border-t border-dashed border-gray-200">
                            <span class="text-sm font-bold text-gray-700">Total</span>
                            <span class="text-sm font-extrabold text-blue-600">Rp <span
                                    id="success-total">0</span></span>
                        </div>
                    </div>

                    <button onclick="location.reload()"
                        class="w-full py-3 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 transition">
                        Selesai
                    </button>
                </div>
            </div>
        </div>

        <script>
            // ─────────────────────────────────────
            // State aktif tagihan yang dibuka
            // ─────────────────────────────────────
            let activeTagihanId = null;

            function formatRupiah(angka) {
                return 'Rp ' + angka.toLocaleString('id-ID');
            }

            // ─────────────────────────────────────
            // Buka modal & isi data
            // ─────────────────────────────────────
            function bukaModalBayar(data) {
                activeTagihanId = data.id;

                const sisa = data.jumlah - data.terbayar;
                const fee = Math.ceil(sisa * 0.007);
                const total = sisa + fee;

                document.getElementById('modal-nama-tagihan').textContent = data.nama;
                document.getElementById('modal-periode').textContent = data.bulan;
                document.getElementById('modal-jatuh-tempo').textContent = data.jatuh_tempo;
                document.getElementById('modal-nominal').textContent = formatRupiah(data.jumlah);
                document.getElementById('modal-fee').textContent = '+ ' + formatRupiah(fee);
                document.getElementById('modal-total').textContent = formatRupiah(total);

                // Tampilkan baris "sudah dibayar" hanya kalau ada cicilan
                const rowTerbayar = document.getElementById('modal-row-terbayar');
                if (data.terbayar > 0) {
                    rowTerbayar.style.display = 'flex';
                    document.getElementById('modal-terbayar').textContent =
                        formatRupiah(data.terbayar) + ' (sisa ' + formatRupiah(sisa) + ')';
                } else {
                    rowTerbayar.style.display = 'none';
                }

                // Reset tombol
                setLoadingBtn(false);

                // Tampilkan modal
                document.getElementById('modal-backdrop').classList.add('active');
                document.body.style.overflow = 'hidden';
            }

            // ─────────────────────────────────────
            // Tutup modal
            // ─────────────────────────────────────
            function tutupModal(event, force = false) {
                if (!force && event && event.target !== document.getElementById('modal-backdrop')) return;
                document.getElementById('modal-backdrop').classList.remove('active');
                document.body.style.overflow = '';
                activeTagihanId = null;
            }

            // ─────────────────────────────────────
            // Loading state tombol
            // ─────────────────────────────────────
            function setLoadingBtn(loading) {
                const btn = document.getElementById('modal-pay-btn');
                if (loading) {
                    btn.disabled = true;
                    btn.innerHTML = `<span class="spinner"></span> Memuat QR Code...`;
                    btn.style.opacity = '0.85';
                } else {
                    btn.disabled = false;
                    btn.innerHTML = `
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                    </svg>
                    Buka QR Code & Bayar`;
                    btn.style.opacity = '1';
                }
            }

            // ─────────────────────────────────────
            // Fetch Snap Token → Buka Midtrans Snap
            // ─────────────────────────────────────
            async function prosessPembayaran() {
                if (!activeTagihanId) return;

                setLoadingBtn(true);

                try {
                    const res = await fetch(`/siswa/keuangan/snap-token/${activeTagihanId}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content'),
                        },
                    });

                    const json = await res.json();

                    if (!json.snap_token) {
                        throw new Error(json.message ?? 'Gagal mendapatkan token');
                    }

                    // Tutup modal kita, buka Snap Midtrans
                    tutupModal(null, true);

                    // TAMBAHKAN INI UNTUK MEMBERSIHKAN STATE MIDTRANS YANG NYANGKUT
                    if (typeof window.snap.hide === 'function') {
                        window.snap.hide();
                    }

                    window.snap.pay(json.snap_token, {
                        onSuccess: async function(result) {
                            // 1. Tampilkan loading sebentar selagi mengambil data terbaru
                            showToast('Pembayaran Berhasil! Memuat detail...');

                            try {
                                // 2. Ambil detail sukses dari endpoint yang kita buat tadi
                                const res = await fetch(
                                    `/siswa/keuangan/pembayaran/detail-sukses?order_id=${result.order_id}`);
                                const data = await res.json();

                                if (res.ok) {
                                    // 3. Isi data ke elemen Modal Sukses
                                    document.getElementById('success-order-id').textContent = '#' + data
                                        .order_id;
                                    document.getElementById('success-item').textContent = data.nama_tagihan +
                                        ' (' + data.periode + ')';
                                    document.getElementById('success-total').textContent = data.total;

                                    // 4. Munculkan Modal Sukses
                                    const modalSukses = document.getElementById('modal-success-backdrop');
                                    modalSukses.classList.remove('hidden');
                                    modalSukses.classList.add('flex');
                                } else {
                                    throw new Error('Gagal memuat detail');
                                }
                            } catch (e) {
                                // Fallback: Jika modal gagal muncul, reload halaman agar status tagihan terupdate
                                console.error(e);
                                window.location.reload();
                            }
                        },
                        onPending: function() {
                            showToast('Pembayaran pending. Selesaikan pembayaran Anda.');
                            // Untuk pending, sebaiknya reload agar status tombol berubah jadi 'Lanjutkan Pembayaran'
                            setTimeout(() => window.location.reload(), 2000);
                        },
                        onError: function() {
                            showToast('Pembayaran gagal. Silakan coba lagi.', 'error');
                        },
                        onClose: function() {
                            window.location.reload();
                        }
                    });

                } catch (err) {
                    setLoadingBtn(false);
                    showToast(err.message || 'Terjadi kesalahan. Coba lagi.', 'error');
                }
            }

            // ─────────────────────────────────────
            // Toast notifikasi ringan
            // ─────────────────────────────────────
            function showToast(msg, type = 'info') {
                const existing = document.getElementById('toast-notif');
                if (existing) existing.remove();

                const colors = type === 'error' ?
                    'background:#fee2e2; border-color:#fca5a5; color:#b91c1c;' :
                    'background:#eff6ff; border-color:#93c5fd; color:#1d4ed8;';

                const toast = document.createElement('div');
                toast.id = 'toast-notif';
                toast.style.cssText = `
                position:fixed; bottom:24px; left:50%; transform:translateX(-50%);
                padding:12px 20px; border-radius:12px; border:1px solid;
                font-size:13px; font-weight:600; z-index:9999;
                box-shadow: 0 4px 20px rgba(0,0,0,0.12);
                animation: slideUp 0.3s ease;
                ${colors}
            `;
                toast.textContent = msg;
                document.body.appendChild(toast);
                setTimeout(() => toast.remove(), 4000);
            }

            // Escape key tutup modal
            document.addEventListener('keydown', e => {
                if (e.key === 'Escape') tutupModal(null, true);
            });
        </script>

</x-app-layout>
