<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\TagihanSpp;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user  = Auth::user();
        $siswa = $user->siswa;

        // Guard: jika data siswa tidak ditemukan
        if (!$siswa) {
            abort(403, 'Data siswa tidak ditemukan untuk akun ini.');
        }

        // Ambil semua tagihan lewat: Siswa → RiwayatAkademik → TagihanSpp
        $semuaTagihan = TagihanSpp::whereHas('riwayatAkademik', function ($q) use ($siswa) {
                $q->where('siswa_id', $siswa->id);
            })
            ->with('masterTagihan')
            ->get();

        $totalTagihan      = $semuaTagihan->count();
        $sudahLunas        = $semuaTagihan->where('status', 'lunas')->count();
        $belumLunas        = $semuaTagihan->whereIn('status', ['belum_lunas', 'cicilan'])->count();

        // Tagihan yang belum lunas untuk ditampilkan di dashboard
        $tagihanBelumLunas = $semuaTagihan
            ->whereIn('status', ['belum_lunas', 'cicilan'])
            ->sortBy('tahun')
            ->values();

        return view('siswa.dashboard', compact(
            'siswa',
            'totalTagihan',
            'sudahLunas',
            'belumLunas',
            'tagihanBelumLunas',
        ));
    }
}