<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use App\Models\PembayaranDetail;
use App\Models\TagihanSpp;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str; // <-- Wajib untuk generate random string

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
              ->orWhereHas('siswa', fn($q) => $q
                  ->where('nama_lengkap', 'LIKE', "%{$search}%")
                  ->orWhere('nisn', 'LIKE', "%{$search}%")
              );
        });
    }

    if ($request->filled('kelas_id')) {
        $query->whereHas('detailPembayaran.tagihanSpp.riwayatAkademik', fn($q) =>
            $q->where('kelas_id', $request->kelas_id)
        );
    }

    $pembayarans = $query->paginate($perPage)->withQueryString();

    return view('admin.keuangan.pembayaran.index', compact('pembayarans', 'kelasList'));
}

public function store(Request $request)
{
    $request->validate([
        'tagihan_ids'        => 'required|array|min:1',
        'tagihan_ids.*'      => 'exists:tagihan_spps,id',
        'jumlah_bayar_total' => 'required|numeric|min:1',
        'metode'             => 'required|in:tunai,midtrans',
    ]);

    $tagihanPertama = TagihanSpp::with('riwayatAkademik')->findOrFail($request->tagihan_ids[0]);

    if (!$tagihanPertama->riwayatAkademik) {
        return back()->with('error', 'Riwayat akademik siswa tidak ditemukan.');
    }

    $siswaId = $tagihanPertama->riwayatAkademik->siswa_id;

    try {
        return DB::transaction(function () use ($request, $siswaId) {

            $kodeGenerate = 'PAY-' . date('YmdHi') . '-' . strtoupper(Str::random(4));

            $pembayaran = Pembayaran::create([
                'kode_pembayaran'   => $kodeGenerate,
                'siswa_id'          => $siswaId,
                'user_id_admin'     => Auth::id(),
                'total_bayar'       => $request->jumlah_bayar_total,
                'tanggal_bayar'     => now(),
                'metode_pembayaran' => $request->metode,
                'status_gateway'    => ($request->metode == 'tunai') ? 'settlement' : 'pending',
            ]);

            $uangTersedia = $request->jumlah_bayar_total;

            foreach ($request->tagihan_ids as $id) {
                if ($uangTersedia <= 0) break;

                $tagihan    = TagihanSpp::lockForUpdate()->find($id);
                $sisaHutang = $tagihan->jumlah_tagihan - $tagihan->terbayar;

                $bayarBulanIni = min($uangTersedia, $sisaHutang);

                PembayaranDetail::create([
                    'pembayaran_id'   => $pembayaran->id,
                    'tagihan_spp_id'  => $tagihan->id,
                    'nominal_dibayar' => $bayarBulanIni,
                ]);

                $tagihan->terbayar += $bayarBulanIni;
                $tagihan->status    = ($tagihan->terbayar >= $tagihan->jumlah_tagihan) ? 'lunas' : 'cicilan';
                $tagihan->save();

                $uangTersedia -= $bayarBulanIni;
            }

            return back()->with('success', "Pembayaran berhasil! Kode: {$kodeGenerate}");
        });

    } catch (\Exception $e) {
        return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
    }
}
}
