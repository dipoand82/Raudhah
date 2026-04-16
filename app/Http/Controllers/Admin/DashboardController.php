<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use App\Models\RiwayatAkademik;
use App\Models\Siswa;
use App\Models\TagihanSpp;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
public function index()
    {
        $bulanIni = now()->month;
        $tahunIni = (string) now()->year;
        $kemarin  = now()->subDay();
        $role     = Auth::user()->role; // 'admin' | 'guru'

        // Mapping Bulan Indonesia
        $bulanIndo = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        $namaBulanIni = $bulanIndo[$bulanIni];

        // [PERBAIKAN 1]: Mengantisipasi semua variasi input bulan di database
        // Akan mencari: "April", "4", atau "04"
        $variasiBulan = [
            $namaBulanIni,
            (string)$bulanIni,
            str_pad($bulanIni, 2, '0', STR_PAD_LEFT)
        ];

        // ── 1. TOTAL SISWA AKTIF ─────────────────────────────────────
        $totalSiswa = Siswa::where('status', 'aktif')->count();

        // ── 2. TOTAL TERKUMPUL BULAN INI ─────────────────────────────
        $totalTerkumpul = Pembayaran::whereMonth('created_at', $bulanIni)
            ->whereYear('created_at', $tahunIni)
            ->sum('total_bayar');

        // ── 3. BELUM BAYAR (Sudah pakai array variasi bulan) ─────────
        $belumBayar = TagihanSpp::where('status', '!=', 'lunas')
            ->whereIn('bulan', $variasiBulan)
            ->where('tahun', $tahunIni)
            ->distinct('riwayat_akademik_id')
            ->count('riwayat_akademik_id');

        // ── 4. TRANSAKSI HARI INI & KEMARIN ──────────────────────────
        $transaksiHariIni = Pembayaran::whereDate('created_at', today())->count();
        $transaksiKemarin = Pembayaran::whereDate('created_at', $kemarin->toDateString())->count();
        $selisihTransaksi = $transaksiHariIni - $transaksiKemarin;

        // ── 5. TRANSAKSI TERBARU (10 data) ───────────────────────────
        $transaksiTerbaru = Pembayaran::with([
                'siswa',
                'detailPembayaran.tagihanSpp.masterTagihan',
                'detailPembayaran.tagihanSpp.riwayatAkademik.kelas',
            ])
            ->latest()
            ->limit(10)
            ->get();

        // ── 6. PROGRESS SPP PER KELAS ───────────────────────────────
        $progressPerKelas = [];
        foreach ([7, 8, 9] as $tingkat) {

            $jumlahSiswa = RiwayatAkademik::whereHas('kelas', fn($q) => $q->where('tingkat', $tingkat))
                ->whereHas('tahunAjaran', fn($q) => $q->where('is_active', true))
                ->count();

            $sudahLunas = TagihanSpp::where('status', 'lunas')
                ->whereIn('bulan', $variasiBulan) // <- Mencari 'April', '4', atau '04'
                ->where('tahun', $tahunIni)
                // [PERBAIKAN 2]: Syarat nama mengandung "SPP" dimatikan, supaya semua tagihan lunas di bulan ini terhitung!
                // ->whereHas('masterTagihan', fn($q) => $q->whereRaw('LOWER(nama_tagihan) LIKE ?', ['%spp%']))
                ->whereHas('riwayatAkademik.kelas', fn($q) => $q->where('tingkat', $tingkat))
                ->distinct('riwayat_akademik_id')
                ->count('riwayat_akademik_id');

            $progressPerKelas[] = [
                'tingkat'      => $tingkat,
                'jumlah_siswa' => $jumlahSiswa,
                'sudah_lunas'  => $sudahLunas,
                'persen'       => $jumlahSiswa > 0
                    ? (int) min(round(($sudahLunas / $jumlahSiswa) * 100), 100)
                    : 0,
            ];
        }

        // ── 7. OVERALL PROGRESS ──────────────────────────────────────
        $totalLunas = TagihanSpp::where('status', 'lunas')
            ->whereIn('bulan', $variasiBulan)
            ->where('tahun', $tahunIni)
            // Syarat "SPP" juga dimatikan di sini
            ->distinct('riwayat_akademik_id')
            ->count('riwayat_akademik_id');

        $overallPersen = $totalSiswa > 0
            ? (int) min(round(($totalLunas / $totalSiswa) * 100), 100)
            : 0;

        return view('admin.dashboard', compact(
            'role',
            'totalSiswa',
            'totalTerkumpul',
            'belumBayar',
            'transaksiHariIni',
            'transaksiKemarin',
            'selisihTransaksi',
            'transaksiTerbaru',
            'progressPerKelas',
            'totalLunas',
            'overallPersen',
        ));
    }
}
