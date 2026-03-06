<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran; // Menggunakan model lama Anda
use App\Models\TagihanSpp;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Midtrans\Config;
use Midtrans\Snap;

// Penting untuk cek waktu expired

class KeuanganController extends Controller
{
    /**
     * DASHBOARD: Load ringkasan tagihan siswa
     */
    public function dashboard()
    {
        $siswa = Auth::user()->siswa;

        // Menggunakan TagihanSpp dengan filter riwayatAkademik seperti struktur lama Anda
        $tagihanBelumLunas = TagihanSpp::whereHas('riwayatAkademik', function ($q) use ($siswa) {
            $q->where('siswa_id', $siswa->id);
        })
            ->whereIn('status', ['belum_lunas', 'cicilan'])
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

    /**
     * RIWAYAT: Riwayat pembayaran siswa
     */
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

    /**
     * GET SNAP TOKEN: Endpoint AJAX untuk pembayaran
     */
    public function getSnapToken(TagihanSpp $tagihan): JsonResponse
    {
        $tagihan->load('riwayatAkademik.siswa', 'masterTagihan');
        $siswa = Auth::user()->siswa;

        // 1. VALIDASI KEPEMILIKAN & STATUS
        if ($tagihan->riwayatAkademik->siswa_id !== $siswa->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if ($tagihan->status === 'lunas') {
            return response()->json(['message' => 'Tagihan sudah lunas'], 400);
        }

        // 2. LOGIKA CEK TOKEN LAMA (DIPERKUAT)
        // Jika sudah ada snap_token, kita pakai yang ada tanpa peduli status 'pending' atau 'belum_lunas'
        if (! empty($tagihan->snap_token)) {
            // Cek apakah token sudah lewat 23 jam
            $isExpired = \Carbon\Carbon::parse($tagihan->updated_at)->diffInHours(now()) > 23;

            if (! $isExpired) {
                return response()->json([
                    'snap_token' => $tagihan->snap_token,
                    'order_id' => $tagihan->midtrans_order_id,
                    'info' => 'Menggunakan transaksi yang sudah ada',
                ]);
            }
        }

        // 3. HITUNG NOMINAL
        $sisa = $tagihan->jumlah_tagihan - ($tagihan->terbayar ?? 0);
        $fee = (int) ceil($sisa * 0.007);
        $total = $sisa + $fee;

        // 4. CONFIG & PARAMS (Tetap sama)
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
            // MUNCUL QRIS
            'enabled_payments' => ['other_qris'],
        ];

        try {
            $snapToken = \Midtrans\Snap::getSnapToken($params);

            // 5. UPDATE DATABASE (Sangat Penting)
            // Simpan agar pemanggilan berikutnya masuk ke logika nomor 2
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

    /**
     * SUKSES: Halaman setelah bayar
     */
public function sukses(Request $request)
{
    $orderId = $request->query('order_id');

    // 1. Ambil data tagihan berdasarkan order_id
    $tagihan = TagihanSpp::where('midtrans_order_id', $orderId)->first();

    // 2. Jika tagihan tidak ketemu, balikkan ke dashboard agar tidak 404/error
    if (!$tagihan) {
        return redirect()->route('siswa.dashboard')
            ->with('error', 'Data transaksi tidak ditemukan.');
    }

    // 3. Jika ketemu, tampilkan halaman sukses
    return view('siswa.keuangan.sukses', compact('tagihan'));
}
public function getDetailSukses(Request $request): JsonResponse
{
    $orderId = $request->query('order_id');

    // Cari dari Pembayaran dulu (sudah diproses webhook)
    $pembayaran = Pembayaran::where('kode_pembayaran', $orderId)
        ->with('detailPembayaran.tagihanSpp.masterTagihan')
        ->first();

    if ($pembayaran) {
        $namaTagihan = $pembayaran->detailPembayaran
            ->map(fn($d) => $d->tagihanSpp?->masterTagihan?->nama_tagihan)
            ->filter()->unique()->implode(', ');

        $periode = $pembayaran->detailPembayaran
            ->map(fn($d) => $d->tagihanSpp?->bulan ? $d->tagihanSpp->bulan . ' ' . $d->tagihanSpp->tahun : null)
            ->filter()->unique()->implode(', ');

        return response()->json([
            'order_id'     => $orderId,
            'nama_tagihan' => $namaTagihan,
            'periode'      => $periode,
            'total'        => number_format($pembayaran->total_bayar, 0, ',', '.'),
        ]);
    }

    // Fallback: webhook belum jalan, ambil dari tagihan langsung
    $tagihan = TagihanSpp::where('midtrans_order_id', $orderId)
        ->with('masterTagihan')
        ->first();

    if (!$tagihan) {
        return response()->json(['message' => 'Data tidak ditemukan'], 404);
    }

    return response()->json([
        'order_id'     => $orderId,
        'nama_tagihan' => $tagihan->masterTagihan->nama_tagihan,
        'periode'      => $tagihan->bulan . ' ' . $tagihan->tahun,
        'total'        => number_format($tagihan->jumlah_tagihan, 0, ',', '.'),
    ]);
}
}
