<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Pembayaran Tagihan
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-lg mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm rounded-xl border border-gray-200 p-6">

                {{-- Detail Tagihan --}}
                <h3 class="text-lg font-bold text-gray-800 mb-4">Rincian Pembayaran</h3>

                <div class="space-y-3 text-sm mb-6">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Tagihan</span>
                        <span class="font-semibold">{{ $tagihan->masterTagihan->nama_tagihan }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Periode</span>
                        <span class="font-semibold">{{ $tagihan->bulan }} {{ $tagihan->tahun }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Nominal Tagihan</span>
                        <span class="font-semibold">Rp {{ number_format($sisaTagihan, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-orange-600">
                        <span>Biaya Layanan QRIS (0.7%)</span>
                        <span class="font-semibold">+ Rp {{ number_format($fee, 0, ',', '.') }}</span>
                    </div>
                    <div class="border-t pt-3 flex justify-between text-base font-bold text-[#1072B8]">
                        <span>Total Bayar</span>
                        <span>Rp {{ number_format($totalBayar, 0, ',', '.') }}</span>
                    </div>
                </div>

                {{-- Info QRIS --}}
                <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 mb-6 text-xs text-blue-700">
                    <p class="font-bold mb-1">ℹ️ Informasi Pembayaran QRIS</p>
                    <p>Pembayaran diproses melalui QRIS Midtrans. Scan QR Code yang muncul menggunakan aplikasi dompet digital (GoPay, OVO, Dana, dll) atau mobile banking.</p>
                </div>

                {{-- Tombol Bayar --}}
                <button id="pay-button"
                    class="w-full bg-[#1072B8] hover:bg-[#0d5a91] text-white font-bold py-3 rounded-xl text-sm transition shadow-md">
                    Bayar Sekarang via QRIS
                </button>

                <a href="{{ route('siswa.dashboard') }}"
                    class="block text-center mt-3 text-sm text-gray-400 hover:text-gray-600">
                    Kembali
                </a>

            </div>
        </div>
    </div>

    {{-- Midtrans Snap JS --}}
    @if(config('services.midtrans.is_production'))
        <script src="https://app.midtrans.com/snap/snap.js"
            data-client-key="{{ config('services.midtrans.client_key') }}"></script>
    @else
        <script src="https://app.sandbox.midtrans.com/snap/snap.js"
            data-client-key="{{ config('services.midtrans.client_key') }}"></script>
    @endif

    <script>
        document.getElementById('pay-button').onclick = function () {
            window.snap.pay('{{ $snapToken }}', {
                onSuccess: function (result) {
                    // Redirect ke halaman sukses
                    window.location.href = "{{ route('siswa.keuangan.bayar.sukses') }}?order_id=" + result.order_id;
                },
                onPending: function (result) {
                    alert('Pembayaran pending. Selesaikan pembayaran Anda.');
                },
                onError: function (result) {
                    alert('Pembayaran gagal. Silakan coba lagi.');
                },
                onClose: function () {
                    // Siswa tutup popup tanpa bayar
                }
            });
        };
    </script>
</x-app-layout>
