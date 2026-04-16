<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Pembayaran;
use App\Models\PembayaranDetail;
use App\Models\TagihanSpp;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; // <-- Wajib untuk generate random string
use Illuminate\Support\Str; // Pastikan baris ini ada

class PembayaranController extends Controller
{
    public function index(Request $request)
    {
        $kelasList = Kelas::orderBy('tingkat')->orderBy('nama_kelas')->get();
        $perPage = $request->input('per_page', 30);

        $query = Pembayaran::with([
            'siswa',
            'detailPembayaran.tagihanSpp.masterTagihan',
            'detailPembayaran.tagihanSpp.riwayatAkademik.kelas',
        ])->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('kode_pembayaran', 'LIKE', "%{$search}%")
                    ->orWhereHas('siswa', fn ($q) => $q
                        ->where('nama_lengkap', 'LIKE', "%{$search}%")
                        ->orWhere('nisn', 'LIKE', "%{$search}%")
                    );
            });
        }

        if ($request->filled('kelas_id')) {
            $query->whereHas('detailPembayaran.tagihanSpp.riwayatAkademik', fn ($q) => $q->where('kelas_id', $request->kelas_id)
            );
        }

        $pembayarans = $query->paginate($perPage)->withQueryString();

        return view('admin.keuangan.pembayaran.index', compact('pembayarans', 'kelasList'));
    }

public function store(Request $request)
    {
        $request->validate([
            'tagihan_ids' => 'required|array|min:1',
            'tagihan_ids.*' => 'exists:tagihan_spps,id',
            'metode' => 'required|in:tunai,midtrans',
        ]);

        return DB::transaction(function () use ($request) {

            $tagihans = TagihanSpp::with('riwayatAkademik')
                ->lockForUpdate()
                ->whereIn('id', $request->tagihan_ids)
                ->get();

            $grouped = $tagihans->groupBy(fn ($t) => $t->riwayatAkademik->siswa_id);
            $kodeList = [];

            // 1. TANGKAP UANG GLOBAL DI LUAR LOOPING
            $sisaBudgetGlobal = (int) $request->jumlah_bayar_total;
            $useGlobalBudget = $sisaBudgetGlobal > 0;

            foreach ($grouped as $siswaId => $tagihanSiswa) {

                // Hitung kewajiban asli khusus siswa ini
                $kewajibanSiswa = $tagihanSiswa->sum(fn($t) => $t->jumlah_tagihan - $t->terbayar);

                if ($useGlobalBudget) {
                    if ($sisaBudgetGlobal <= 0) break; // Uang sudah habis

                    // 2. SISWA HANYA BOLEH MENGAMBIL UANG SESUAI KEWAJIBANNYA, ATAU SISA UANG YANG ADA
                    $totalBayarSiswa = min($kewajibanSiswa, $sisaBudgetGlobal);

                    // 3. UANG KASIR BERKURANG UNTUK SISWA SELANJUTNYA
                    $sisaBudgetGlobal -= $totalBayarSiswa;
                } else {
                    $totalBayarSiswa = $kewajibanSiswa;
                }

                if ($totalBayarSiswa <= 0) continue;

                $kode = 'PAY-'.date('YmdHi').'-'.strtoupper(Str::random(4));

                $pembayaran = Pembayaran::create([
                    'kode_pembayaran' => $kode,
                    'siswa_id' => $siswaId,
                    'user_id_admin' => Auth::id(),
                    'total_bayar' => $totalBayarSiswa, // <-- SEKARANG SUDAH BENAR (Misal: 500rb)
                    'tanggal_bayar' => now(),
                    'metode_pembayaran' => $request->metode,
                    'status_gateway' => $request->metode === 'tunai' ? 'settlement' : 'pending',
                ]);

                $alokasiDetail = $totalBayarSiswa;

                foreach ($tagihanSiswa as $tagihan) {
                    $sisa = $tagihan->jumlah_tagihan - $tagihan->terbayar;

                    if ($sisa <= 0) continue;
                    if ($alokasiDetail <= 0) break;

                    $dibayar = min($sisa, $alokasiDetail);
                    $alokasiDetail -= $dibayar;

                    PembayaranDetail::create([
                        'pembayaran_id' => $pembayaran->id,
                        'tagihan_spp_id' => $tagihan->id,
                        'nominal_dibayar' => $dibayar,
                    ]);

                    $tagihan->terbayar += $dibayar;
                    $tagihan->status = $tagihan->terbayar >= $tagihan->jumlah_tagihan ? 'lunas' : 'cicilan';
                    $tagihan->save();
                }

                $kodeList[] = $kode;
            }

            return response()->json([
                'success' => true,
                'message' => count($kodeList).' pembayaran berhasil! Kode: '.implode(', ', $kodeList),
            ]);
        });
    }

    public function cetakKuitansi($id)
    {
        $p = Pembayaran::with([
            'siswa',
            'detailPembayaran.tagihanSpp.masterTagihan',
        ])->findOrFail($id);

        $terbilang = $this->terbilang($p->total_bayar);
        $profil_sekolah = \App\Models\ProfilSekolah::first(); // sesuaikan nama model jika berbeda

        $pdf = Pdf::loadView(
            'admin.keuangan.pembayaran.kuitansi_pdf',
            compact('p', 'terbilang', 'profil_sekolah')
        )->setPaper([0, 0, 480, 400], 'portrait');

        return $pdf->stream('Kuitansi-'.$p->kode_pembayaran.'.pdf');
    }

    // Fungsi pembantu untuk mengubah angka menjadi teks
    private function terbilang($angka)
    {
        $angka = abs($angka);
        $baca = ['', 'satu', 'dua', 'tiga', 'empat', 'lima', 'enam', 'tujuh', 'delapan', 'sembilan', 'sepuluh', 'sebelas'];
        $temp = '';
        if ($angka < 12) {
            $temp = ' '.$baca[$angka];
        } elseif ($angka < 20) {
            $temp = $this->terbilang($angka - 10).' belas';
        } elseif ($angka < 100) {
            $temp = $this->terbilang($angka / 10).' puluh'.$this->terbilang($angka % 10);
        } elseif ($angka < 200) {
            $temp = ' seratus'.$this->terbilang($angka - 100);
        } elseif ($angka < 1000) {
            $temp = $this->terbilang($angka / 100).' ratus'.$this->terbilang($angka % 100);
        } elseif ($angka < 2000) {
            $temp = ' seribu'.$this->terbilang($angka - 1000);
        } elseif ($angka < 1000000) {
            $temp = $this->terbilang($angka / 1000).' ribu'.$this->terbilang($angka % 1000);
        }

        return $temp;
    }
}
