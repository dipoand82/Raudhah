<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use App\Models\PembayaranDetail;
use App\Models\TagihanSpp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MidtransWebhookController extends Controller
{
    public function handle(Request $request)
    {
        Log::info('Midtrans webhook diterima', $request->all());
        $serverKey = config('services.midtrans.server_key');
        $orderId = $request->order_id;
        $statusCode = $request->status_code;
        $grossAmount = $request->gross_amount;

        $signatureKey = hash('sha512', $orderId.$statusCode.$grossAmount.$serverKey);

        if ($signatureKey !== $request->signature_key) {
            Log::warning('Midtrans webhook: signature tidak valid', ['order_id' => $orderId]);

            return response()->json(['message' => 'Invalid signature'], 403);
        }

        $tagihan = TagihanSpp::where('midtrans_order_id', $orderId)->first();

        if (! $tagihan) {
            Log::warning('Midtrans webhook: tagihan tidak ditemukan', ['order_id' => $orderId]);

            return response()->json(['message' => 'Tagihan tidak ditemukan'], 404);
        }

        $transactionStatus = $request->transaction_status;
        $fraudStatus = $request->fraud_status ?? 'accept';

        if ($transactionStatus == 'settlement' || $transactionStatus == 'capture') {
            if ($fraudStatus == 'accept') {

                if ($tagihan->status === 'lunas') {
                    return response()->json(['message' => 'Already processed']);
                }

                DB::transaction(function () use ($tagihan, $orderId) {
                    $sisaTagihan = $tagihan->jumlah_tagihan - $tagihan->terbayar;
                    $siswaId = $tagihan->riwayatAkademik->siswa_id;

                    $pembayaran = Pembayaran::create([
                        'kode_pembayaran' => $orderId,
                        'siswa_id' => $siswaId,
                        'user_id_admin' => null,
                        'total_bayar' => $sisaTagihan,
                        'tanggal_bayar' => now(),
                        'metode_pembayaran' => 'qris',
                        'status_gateway' => 'settlement',
                    ]);

                    PembayaranDetail::create([
                        'pembayaran_id' => $pembayaran->id,
                        'tagihan_spp_id' => $tagihan->id,
                        'nominal_dibayar' => $sisaTagihan,
                    ]);

                    $tagihan->update([
                        'terbayar' => $tagihan->terbayar + $sisaTagihan,
                        'status' => 'lunas',
                        'snap_token' => null,
                    ]);
                });
            }
        } elseif ($transactionStatus == 'expire' || $transactionStatus == 'cancel' || $transactionStatus == 'deny') {
            $tagihan->update([
                'status' => 'belum_lunas',
                'snap_token' => null,
                'midtrans_order_id' => null,
            ]);
            Log::info("Tagihan $orderId dibatalkan/expired.");
        }

        return response()->json(['message' => 'OK']);
    }
}
