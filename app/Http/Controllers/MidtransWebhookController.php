<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use App\Models\PembayaranDetail;
use App\Models\TagihanSpp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class MidtransWebhookController extends Controller
{
    public function handle(Request $request)
    {
        // 1. Verifikasi signature dari Midtrans
        $serverKey   = config('services.midtrans.server_key');
        $orderId     = $request->order_id;
        $statusCode  = $request->status_code;
        $grossAmount = $request->gross_amount;

        $signatureKey = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

        if ($signatureKey !== $request->signature_key) {
            Log::warning('Midtrans webhook: signature tidak valid', ['order_id' => $orderId]);
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        // 2. Hanya proses jika status settlement (pembayaran berhasil)
        $transactionStatus = $request->transaction_status;
        $fraudStatus       = $request->fraud_status ?? 'accept';

        if ($transactionStatus !== 'settlement' || $fraudStatus !== 'accept') {
            return response()->json(['message' => 'Ignored: ' . $transactionStatus]);
        }

        // 3. Cari tagihan berdasarkan order_id
        $tagihan = TagihanSpp::where('midtrans_order_id', $orderId)->first();

        if (!$tagihan) {
            Log::warning('Midtrans webhook: tagihan tidak ditemukan', ['order_id' => $orderId]);
            return response()->json(['message' => 'Tagihan tidak ditemukan'], 404);
        }

        // 4. Cek apakah sudah diproses sebelumnya (idempotent)
        if ($tagihan->status === 'lunas') {
            return response()->json(['message' => 'Already processed']);
        }

        // 5. Proses pembayaran dalam transaction
        DB::transaction(function () use ($tagihan, $request) {

            $sisaTagihan = $tagihan->jumlah_tagihan - $tagihan->terbayar;
            $fee         = (int) ceil($sisaTagihan * 0.007);

            // Ambil siswa_id dari relasi
            $siswaId = $tagihan->riwayatAkademik->siswa_id;

            // Buat header pembayaran
            $pembayaran = Pembayaran::create([
                'kode_pembayaran'   => $tagihan->midtrans_order_id,
                'siswa_id'          => $siswaId,
                'user_id_admin'     => null, // null karena dibayar sendiri oleh siswa
                'total_bayar'       => $sisaTagihan, // nominal asli tanpa fee
                'tanggal_bayar'     => now(),
                'metode_pembayaran' => 'qris',
                'status_gateway'    => 'settlement',
            ]);

            // Buat detail pembayaran
            PembayaranDetail::create([
                'pembayaran_id'   => $pembayaran->id,
                'tagihan_spp_id'  => $tagihan->id,
                'nominal_dibayar' => $sisaTagihan,
            ]);

            // Update status tagihan
            $tagihan->terbayar += $sisaTagihan;
            $tagihan->status    = 'lunas';
            $tagihan->save();
        });

        return response()->json(['message' => 'OK']);
    }
}
