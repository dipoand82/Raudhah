<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use App\Models\PembayaranDetail;
use App\Models\TagihanSpp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str; // <-- Wajib untuk generate random string

class PembayaranController extends Controller
{
    public function index()
    {
        // Mengambil riwayat pembayaran untuk ditampilkan di halaman Riwayat Pembayaran
        $pembayarans = Pembayaran::with(['siswa', 'admin'])->latest()->paginate(10);

        return view('admin.keuangan.pembayaran.index', compact('pembayarans'));
    }

    public function store(Request $request)
    {
        // Validasi Keamanan: Pastikan nominal dan target tagihan benar
        $request->validate([
            'siswa_id' => 'required|exists:siswas,id',
            'tagihan_ids' => 'required|array', // ID tagihan hasil ceklis
            'jumlah_bayar_total' => 'required|numeric|min:1',
            'metode' => 'required|in:tunai,midtrans',
        ]);

        try {
            return DB::transaction(function () use ($request) {

                // [TAMBAHAN PENTING] Generate Kode Pembayaran Unik (Contoh: PAY-202602221030-ABCD)
                $kodeGenerate = 'PAY-' . date('YmdHi') . '-' . strtoupper(Str::random(4));

                // 1. Buat Header Pembayaran (Header Transaksi)
                $pembayaran = Pembayaran::create([
                    'kode_pembayaran' => $kodeGenerate, // <-- Dimasukkan ke sini
                    'siswa_id' => $request->siswa_id,
                    'user_id_admin' => Auth::id(),
                    'total_bayar' => $request->jumlah_bayar_total,
                    'tanggal_bayar' => now(),
                    'metode_pembayaran' => $request->metode,
                    'status_gateway' => ($request->metode == 'tunai') ? 'settlement' : 'pending',
                ]);

                $uangTersedia = $request->jumlah_bayar_total;

                // 2. Alokasi Uang ke setiap Tagihan (Logika Cicilan & Multi-Month)
                foreach ($request->tagihan_ids as $id) {
                    if ($uangTersedia <= 0) {
                        break;
                    }

                    $tagihan = TagihanSpp::lockForUpdate()->find($id);
                    $sisaHutang = $tagihan->jumlah_tagihan - $tagihan->terbayar;

                    // Hitung berapa yang bisa dialokasikan ke bulan ini
                    $bayarBulanIni = min($uangTersedia, $sisaHutang);

                    // Catat di Detail (Isi Keranjang)
                    PembayaranDetail::create([
                        'pembayaran_id' => $pembayaran->id,
                        'tagihan_spp_id' => $tagihan->id,
                        'nominal_dibayar' => $bayarBulanIni,
                    ]);

                    // Update Saldo Terbayar di Tabel Tagihan [PENTING]
                    $tagihan->terbayar += $bayarBulanIni;

                    // Update Status Otomatis (Lunas/Cicilan)
                    if ($tagihan->terbayar >= $tagihan->jumlah_tagihan) {
                        $tagihan->status = 'lunas';
                    } else {
                        $tagihan->status = 'cicilan';
                    }

                    $tagihan->save();
                    $uangTersedia -= $bayarBulanIni;
                }

                return response()->json(['success' => true, 'message' => 'Pembayaran Berhasil!']);
            });
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
