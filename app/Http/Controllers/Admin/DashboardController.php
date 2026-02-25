<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Siswa;
use App\Models\Pembayaran;
use App\Models\TagihanSpp;
use App\Models\RiwayatAkademik;

class DashboardController extends Controller
{
    public function index()
    {
        $bulanIni = now()->month;
        $tahunIni = now()->year;
        $kemarin  = now()->subDay();
        $role     = Auth::user()->role; // 'admin' | 'guru'

        // ── 1. TOTAL SISWA AKTIF ─────────────────────────────────────
        $totalSiswa = Siswa::where('status', 'aktif')->count();

        // ── 2. TOTAL TERKUMPUL BULAN INI ─────────────────────────────
        // Guru hanya bisa lihat nominal, tidak bisa akses detail transaksi
        $totalTerkumpul = Pembayaran::whereMonth('created_at', $bulanIni)
            ->whereYear('created_at', $tahunIni)
            ->sum('total_bayar');

        // ── 3. BELUM BAYAR ────────────────────────────────────────────
        $belumBayar = TagihanSpp::where('status', '!=', 'lunas')
            ->whereMonth('created_at', $bulanIni)
            ->whereYear('created_at', $tahunIni)
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

        // ── 6. PROGRESS SPP PER KELAS ────────────────────────────────
        $progressPerKelas = [];
        foreach ([7, 8, 9] as $tingkat) {
            $jumlahSiswa = RiwayatAkademik::whereHas('kelas', fn($q) => $q->where('tingkat', $tingkat))
                ->whereHas('tahunAjaran', fn($q) => $q->where('is_active', true))
                ->count();

            $sudahLunas = TagihanSpp::where('status', 'lunas')
                ->whereMonth('created_at', $bulanIni)
                ->whereYear('created_at', $tahunIni)
                ->whereHas('masterTagihan', fn($q) => $q->whereRaw('LOWER(nama_tagihan) LIKE ?', ['%spp%']))
                ->whereHas('riwayatAkademik.kelas', fn($q) => $q->where('tingkat', $tingkat))
                ->count();

            $progressPerKelas[] = [
                'tingkat'      => $tingkat,
                'jumlah_siswa' => $jumlahSiswa,
                'sudah_lunas'  => $sudahLunas,
                'persen'       => $jumlahSiswa > 0
                    ? (int) round(($sudahLunas / $jumlahSiswa) * 100)
                    : 0,
            ];
        }

        // ── 7. OVERALL PROGRESS ──────────────────────────────────────
        $totalLunas = TagihanSpp::where('status', 'lunas')
            ->whereMonth('created_at', $bulanIni)
            ->whereYear('created_at', $tahunIni)
            ->whereHas('masterTagihan', fn($q) => $q->whereRaw('LOWER(nama_tagihan) LIKE ?', ['%spp%']))
            ->distinct('riwayat_akademik_id')
            ->count('riwayat_akademik_id');

        $overallPersen = $totalSiswa > 0
            ? (int) round(($totalLunas / $totalSiswa) * 100)
            : 0;

        // View: resources/views/admin/dashboard.blade.php
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