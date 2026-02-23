<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TagihanSpp;
use App\Models\RiwayatAkademik;
use App\Models\Kelas;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $kelasList = Kelas::orderBy('tingkat')->get();
        $tahun = $request->get('tahun', date('Y'));
        $periode = $request->get('periode', 'ganjil'); // default ganjil

        // Tentukan daftar bulan berdasarkan periode (Semester)
        $bulanList = ($periode == 'ganjil')
            ? ['Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember']
            : ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni'];

        // Ambil data siswa dan relasi tagihannya
        $query = RiwayatAkademik::with(['siswa', 'kelas']);

        if ($request->filled('kelas_id')) {
            $query->where('kelas_id', $request->kelas_id);
        }

        $siswas = $query->get();

        // Ambil tagihan yang sesuai dengan tahun dan daftar bulan terpilih
        $tagihans = TagihanSpp::whereIn('riwayat_akademik_id', $siswas->pluck('id'))
            ->where('tahun', $tahun)
            ->whereIn('bulan', $bulanList)
            ->get()
            ->groupBy('riwayat_akademik_id');

        return view('admin.keuangan.laporan.index', compact('siswas', 'tagihans', 'bulanList', 'kelasList', 'periode'));
    }
}
