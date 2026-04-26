<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use App\Models\TagihanSpp;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KeuanganController extends Controller
{
    public function dashboard()
    {
        $siswa = Auth::user()->siswa;
        $tagihanBelumLunas = TagihanSpp::whereHas('riwayatAkademik', function ($q) use ($siswa) {
            $q->where('siswa_id', $siswa->id);
        })
            ->whereIn('status', ['belum_lunas', 'cicilan', 'pending'])
            ->with('masterTagihan')
            ->orderBy('bulan', 'asc')
            ->get();

        $totalTagihan = TagihanSpp::whereHas('riwayatAkademik', function ($q) use ($siswa) {
            $q->where('siswa_id', $siswa->id);
        })->count();

        $belumLunas = $tagihanBelumLunas->count();

        $sudahLunas = TagihanSpp::whereHas('riwayatAkademik', function ($q) use ($siswa) {
            $q->where('siswa_id', $siswa->id);
        })->where('status', 'lunas')->count();

        return view('siswa.dashboard', compact(
            'tagihanBelumLunas',
            'totalTagihan',
            'belumLunas',
            'sudahLunas'
        ));
    }

    public function riwayat(Request $request)
    {
        $siswa = Auth::user()->siswa;

        $query = Pembayaran::where('siswa_id', $siswa->id)
            ->with([
                'detailPembayaran.tagihanSpp.masterTagihan',
                'detailPembayaran.tagihanSpp.riwayatAkademik.kelas',
            ])
            ->latest();

        if ($request->filled('bulan')) {
            $query->whereHas('detailPembayaran.tagihanSpp', function ($q) use ($request) {
                $q->where('bulan', $request->bulan);
            });
        }

        $pembayarans = $query->paginate(10);

        return view('siswa.keuangan.riwayat', compact('pembayarans'));
    }

    public function getSnapToken(TagihanSpp $tagihan): JsonResponse
    {
        $tagihan->load('riwayatAkademik.siswa', 'masterTagihan');
        $siswa = Auth::user()->siswa;

        if ($tagihan->riwayatAkademik->siswa_id !== $siswa->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if ($tagihan->status === 'lunas') {
            return response()->json(['message' => 'Tagihan sudah lunas'], 400);
        }

        if (! empty($tagihan->snap_token)) {
            $isExpired = \Carbon\Carbon::parse($tagihan->updated_at)->diffInHours(now()) > 23;

            if (! $isExpired) {
                return response()->json([
                    'snap_token' => $tagihan->snap_token,
                    'order_id' => $tagihan->midtrans_order_id,
                    'info' => 'Menggunakan transaksi yang sudah ada',
                ]);
            }
        }

        $sisa = $tagihan->jumlah_tagihan - ($tagihan->terbayar ?? 0);
        $fee = (int) ceil($sisa * 0.007);
        $total = $sisa + $fee;

        \Midtrans\Config::$serverKey = config('services.midtrans.server_key');
        \Midtrans\Config::$isProduction = config('services.midtrans.is_production');
        \Midtrans\Config::$isSanitized = true;
        \Midtrans\Config::$is3ds = true;

        $orderId = 'SPP-'.$tagihan->id.'-'.time();

        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $total,
            ],
            'customer_details' => [
                'first_name' => $siswa->nama_lengkap,
                'email' => Auth::user()->email,
            ],
            'item_details' => [
                [
                    'id' => 'TAGIHAN-'.$tagihan->id,
                    'price' => $sisa,
                    'quantity' => 1,
                    'name' => $tagihan->masterTagihan->nama_tagihan.' ('.$tagihan->bulan.')',
                ],
                [
                    'id' => 'QRIS-FEE',
                    'price' => $fee,
                    'quantity' => 1,
                    'name' => 'Biaya Layanan QRIS (0.7%)',
                ],
            ],
            'enabled_payments' => ['other_qris'],
        ];

        try {
            $snapToken = \Midtrans\Snap::getSnapToken($params);

            $tagihan->update([
                'midtrans_order_id' => $orderId,
                'snap_token' => $snapToken,
                'status' => 'pending',
            ]);

            return response()->json([
                'snap_token' => $snapToken,
                'order_id' => $orderId,
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function sukses(Request $request)
    {
        $orderId = $request->query('order_id');

        $tagihan = TagihanSpp::where('midtrans_order_id', $orderId)->first();

        if (! $tagihan) {
            return redirect()->route('siswa.dashboard')
                ->with('error', 'Data transaksi tidak ditemukan.');
        }

        return view('siswa.keuangan.sukses', compact('tagihan'));
    }

    public function getDetailSukses(Request $request): JsonResponse
    {
        $orderId = $request->query('order_id');
        $pembayaran = Pembayaran::where('kode_pembayaran', $orderId)
            ->with('detailPembayaran.tagihanSpp.masterTagihan')
            ->first();

        if ($pembayaran) {
            $namaTagihan = $pembayaran->detailPembayaran
                ->map(fn ($d) => $d->tagihanSpp?->masterTagihan?->nama_tagihan)
                ->filter()->unique()->implode(', ');

            $periode = $pembayaran->detailPembayaran
                ->map(fn ($d) => $d->tagihanSpp?->bulan ? $d->tagihanSpp->bulan.' '.$d->tagihanSpp->tahun : null)
                ->filter()->unique()->implode(', ');

            return response()->json([
                'order_id' => $orderId,
                'nama_tagihan' => $namaTagihan,
                'periode' => $periode,
                'total' => number_format($pembayaran->total_bayar, 0, ',', '.'),
            ]);
        }

        $tagihan = TagihanSpp::where('midtrans_order_id', $orderId)
            ->with('masterTagihan')
            ->first();

        if (! $tagihan) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }

        return response()->json([
            'order_id' => $orderId,
            'nama_tagihan' => $tagihan->masterTagihan->nama_tagihan,
            'periode' => $tagihan->bulan.' '.$tagihan->tahun,
            'total' => number_format($tagihan->jumlah_tagihan, 0, ',', '.'),
        ]);
    }
}
