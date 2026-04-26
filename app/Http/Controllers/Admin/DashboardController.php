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
        $kemarin = now()->subDay();
        $role = Auth::user()->role;
        $bulanIndo = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];
        $namaBulanIni = $bulanIndo[$bulanIni];
        $variasiBulan = [
            $namaBulanIni,
            (string) $bulanIni,
            str_pad($bulanIni, 2, '0', STR_PAD_LEFT),
        ];
        $totalSiswa = Siswa::where('status', 'aktif')->count();
        $totalTerkumpul = Pembayaran::whereMonth('created_at', $bulanIni)
            ->whereYear('created_at', $tahunIni)
            ->sum('total_bayar');
        $belumBayar = TagihanSpp::where('status', '!=', 'lunas')
            ->whereIn('bulan', $variasiBulan)
            ->where('tahun', $tahunIni)
            ->distinct('riwayat_akademik_id')
            ->count('riwayat_akademik_id');
        $transaksiHariIni = Pembayaran::whereDate('created_at', today())->count();
        $transaksiKemarin = Pembayaran::whereDate('created_at', $kemarin->toDateString())->count();
        $selisihTransaksi = $transaksiHariIni - $transaksiKemarin;
        $transaksiTerbaru = Pembayaran::with([
            'siswa',
            'detailPembayaran.tagihanSpp.masterTagihan',
            'detailPembayaran.tagihanSpp.riwayatAkademik.kelas',
        ])
            ->latest()
            ->limit(10)
            ->get();
        $progressPerKelas = [];
        foreach ([7, 8, 9] as $tingkat) {
            $jumlahSiswa = RiwayatAkademik::whereHas('kelas', fn ($q) => $q->where('tingkat', $tingkat))
                ->whereHas('tahunAjaran', fn ($q) => $q->where('is_active', true))
                ->count();
            $sudahLunas = TagihanSpp::where('status', 'lunas')
                ->whereIn('bulan', $variasiBulan)
                ->where('tahun', $tahunIni)
                ->whereHas('riwayatAkademik.kelas', fn ($q) => $q->where('tingkat', $tingkat))
                ->distinct('riwayat_akademik_id')
                ->count('riwayat_akademik_id');
            $progressPerKelas[] = [
                'tingkat' => $tingkat,
                'jumlah_siswa' => $jumlahSiswa,
                'sudah_lunas' => $sudahLunas,
                'persen' => $jumlahSiswa > 0
                    ? (int) min(round(($sudahLunas / $jumlahSiswa) * 100), 100)
                    : 0,
            ];
        }
        $totalLunas = TagihanSpp::where('status', 'lunas')
            ->whereIn('bulan', $variasiBulan)
            ->where('tahun', $tahunIni)
            ->distinct('riwayat_akademik_id')
            ->count('riwayat_akademik_id');
        $overallPersen = $totalSiswa > 0
            ? (int) min(round(($totalLunas / $totalSiswa) * 100), 100)
            : 0;

        return view('admin.dashboard', compact(
            'role', 'totalSiswa', 'totalTerkumpul', 'belumBayar', 'transaksiHariIni',
            'transaksiKemarin', 'selisihTransaksi', 'transaksiTerbaru', 'progressPerKelas', 'totalLunas', 'overallPersen',
        ));
    }
}
