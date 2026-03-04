<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\TagihanSpp;
use App\Models\Pembayaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KeuanganController extends Controller
{
    /**
     * Riwayat pembayaran siswa yang login.
     */
    public function riwayat(Request $request)
    {
        $user  = Auth::user();
        $siswa = $user->siswa;

        $query = Pembayaran::where('siswa_id', $siswa->id)
            ->with([
                'detailPembayaran.tagihanSpp.masterTagihan',
                'detailPembayaran.tagihanSpp.riwayatAkademik.kelas',
            ])
            ->latest();

        // Filter bulan jika dipilih
        if ($request->filled('bulan')) {
            $query->whereHas('detailPembayaran.tagihanSpp', function ($q) use ($request) {
                $q->where('bulan', $request->bulan);
            });
        }

        $pembayarans = $query->paginate(10);

        return view('siswa.keuangan.riwayat', compact('pembayarans'));
    }

    /**
     * Halaman bayar tagihan — redirect ke Midtrans atau proses tunai.
     * Sesuaikan dengan payment gateway yang Anda pakai.
     */
public function bayar(Request $request, TagihanSpp $tagihan)
{
    $tagihan->load('riwayatAkademik.siswa');
    $siswa = Auth::user()->siswa;

    abort_if($tagihan->riwayatAkademik->siswa_id !== $siswa->id, 403, 'Tagihan ini bukan milik Anda.');
    abort_if($tagihan->status === 'lunas', 403, 'Tagihan ini sudah lunas.');

    // Hitung fee QRIS 0.7%
    $sisaTagihan = $tagihan->jumlah_tagihan - $tagihan->terbayar;
    $fee         = (int) ceil($sisaTagihan * 0.007);
    $totalBayar  = $sisaTagihan + $fee;

    // Config Midtrans
    \Midtrans\Config::$serverKey    = config('services.midtrans.server_key');
    \Midtrans\Config::$isProduction = config('services.midtrans.is_production');
    \Midtrans\Config::$isSanitized  = true;
    \Midtrans\Config::$is3ds        = true;

    $orderId = 'SPP-' . $tagihan->id . '-' . time();

    $params = [
        'transaction_details' => [
            'order_id'     => $orderId,
            'gross_amount' => $totalBayar,
        ],
        'item_details' => [
            [
                'id'       => $tagihan->id,
                'price'    => $sisaTagihan,
                'quantity' => 1,
                'name'     => $tagihan->masterTagihan->nama_tagihan .
                              ' (' . $tagihan->bulan . ' ' . $tagihan->tahun . ')',
            ],
            [
                'id'       => 'FEE-QRIS',
                'price'    => $fee,
                'quantity' => 1,
                'name'     => 'Biaya Layanan QRIS (0.7%)',
            ],
        ],
        'customer_details' => [
            'first_name' => $siswa->nama_lengkap,
        ],
        'enabled_payments' => ['qris'], // ✅ Khusus QRIS saja
    ];

    // Simpan order_id dulu ke tagihan (untuk referensi webhook)
    $tagihan->update(['midtrans_order_id' => $orderId]);

    // Generate Snap Token
    $snapToken = \Midtrans\Snap::getSnapToken($params);

    return view('siswa.keuangan.bayar', compact('tagihan', 'snapToken', 'totalBayar', 'fee', 'sisaTagihan'));
}
public function sukses(Request $request)
{
    $orderId = $request->query('order_id');
    $tagihan = TagihanSpp::where('midtrans_order_id', $orderId)->first();

    return view('siswa.keuangan.sukses', compact('tagihan'));
}
}
