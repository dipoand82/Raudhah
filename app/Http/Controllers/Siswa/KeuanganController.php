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
        $user  = Auth::user();
        $siswa = $user->siswa;

        // Pastikan tagihan ini milik siswa yang login
        if ($tagihan->siswa_id !== $siswa->id) {
            abort(403, 'Tagihan ini bukan milik Anda.');
        }

        // Jika sudah lunas, tidak perlu bayar lagi
        if ($tagihan->status === 'lunas') {
            return redirect()->route('siswa.dashboard')
                ->with('error', 'Tagihan ini sudah lunas.');
        }

        // Kembalikan view konfirmasi bayar
        // (Buat view siswa/keuangan/bayar.blade.php untuk halaman ini)
        return view('siswa.keuangan.bayar', compact('tagihan'));
    }
}