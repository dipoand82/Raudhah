<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\TagihanSpp;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $siswa = $user->siswa;

        if (! $siswa) {
            abort(403, 'Data siswa tidak ditemukan untuk akun ini.');
        }

        $semuaTagihan = TagihanSpp::whereHas('riwayatAkademik', function ($q) use ($siswa) {
            $q->where('siswa_id', $siswa->id);
        })
            ->with('masterTagihan')
            ->get();

        $totalTagihan = $semuaTagihan->count();
        $sudahLunas = $semuaTagihan->where('status', 'lunas')->count();
        $belumLunas = $semuaTagihan->whereIn('status', ['belum_lunas', 'cicilan', 'pending'])->count();
        $tagihanBelumLunas = $semuaTagihan->whereIn('status', ['belum_lunas', 'cicilan', 'pending'])->values();

        return view('siswa.dashboard', compact(
            'user',
            'siswa',
            'totalTagihan',
            'sudahLunas',
            'belumLunas',
            'tagihanBelumLunas',
        ));
    }
}
