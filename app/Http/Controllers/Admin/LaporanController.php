<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TagihanSpp;
use App\Models\MasterTagihan;
use App\Models\RiwayatAkademik;
use App\Models\Kelas;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $kelasList = Kelas::orderBy('tingkat')->get();
        $tahun     = $request->get('tahun', date('Y'));
        $periode   = $request->get('periode', 'ganjil');
        $perPage   = $request->get('per_page', 30);

        // Daftar bulan berdasarkan semester
        $bulanList = ($periode === 'ganjil')
            ? ['Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember']
            : ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni'];

        // Semua jenis tagihan (SPP + lainnya)
        $masterTagihans = MasterTagihan::orderBy('id')->get();

        // Query siswa dengan filter kelas
        $query = RiwayatAkademik::with(['siswa', 'kelas'])
            ->whereHas('siswa')
            ->whereHas('kelas');

        if ($request->filled('kelas_id')) {
            $query->where('kelas_id', $request->kelas_id);
        }

        // Urutkan berdasarkan kelas lalu nama siswa
        $query->join('kelas', 'riwayat_akademiks.kelas_id', '=', 'kelas.id')
              ->join('siswas', 'riwayat_akademiks.siswa_id', '=', 'siswas.id')
              ->orderBy('kelas.tingkat')
              ->orderBy('kelas.nama_kelas')
              ->orderBy('siswas.nama_lengkap')
              ->select('riwayat_akademiks.*');

        $siswas = $query->paginate($perPage)->withQueryString();

        // Ambil semua tagihan relevan (semua jenis, semua bulan) dalam 1 query
        $tagihanRaw = TagihanSpp::with('masterTagihan')
            ->whereIn('riwayat_akademik_id', $siswas->pluck('id'))
            ->where('tahun', $tahun)
            ->get();

        // Bentuk: $tagihans[riwayat_akademik_id][master_tagihan_id][bulan] = TagihanSpp
        // Untuk tagihan non-bulan (non-SPP), bulan diisi 'NO_BULAN'
        $tagihans = [];
        foreach ($tagihanRaw as $t) {
            $bulanKey = $t->bulan ?: 'NO_BULAN';
            $tagihans[$t->riwayat_akademik_id][$t->master_tagihan_id][$bulanKey] = $t;
        }

        return view('admin.keuangan.laporan.index', compact(
            'siswas',
            'tagihans',
            'bulanList',
            'kelasList',
            'masterTagihans',
            'periode',
            'tahun',
        ));
    }
}