<?php

namespace App\Http\Controllers\Admin;

use App\Exports\LaporanPembayaranExport;
use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\MasterTagihan;
use App\Models\RiwayatAkademik;
use App\Models\TagihanSpp;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $kelasList = Kelas::orderBy('tingkat')->orderBy('nama_kelas')->get();
        $tahun = $request->get('tahun', date('Y'));
        $periode = $request->get('periode', 'ganjil');
        $perPage = $request->get('per_page', 300);
        $bulanList = ($periode === 'ganjil')
            ? ['Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember']
            : ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni'];
        $masterTagihans = MasterTagihan::orderBy('id')->get();
        $query = RiwayatAkademik::with(['siswa', 'kelas'])
            ->whereHas('siswa')
            ->whereHas('kelas');

        if ($request->filled('kelas_id')) {
            $query->where('riwayat_akademiks.kelas_id', $request->kelas_id);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('siswa', fn ($q) => $q
                ->where('nama_lengkap', 'LIKE', "%{$search}%")
                ->orWhere('nisn', 'LIKE', "%{$search}%")
            );
        }
        $query->join('kelas', 'riwayat_akademiks.kelas_id', '=', 'kelas.id')
            ->join('siswas', 'riwayat_akademiks.siswa_id', '=', 'siswas.id')
            ->orderBy('kelas.tingkat')
            ->orderBy('kelas.nama_kelas')
            ->orderBy('siswas.nama_lengkap')
            ->select('riwayat_akademiks.*');
        $siswas = $query->paginate($perPage)->withQueryString();
        $tagihanRaw = TagihanSpp::whereIn('riwayat_akademik_id', $siswas->pluck('id'))
            ->where('tahun', $tahun)
            ->get();
        $tagihans = [];
        foreach ($tagihanRaw as $t) {
            $bulanKey = $t->bulan ?: 'NO_BULAN';
            $tagihans[$t->riwayat_akademik_id][$t->master_tagihan_id][$bulanKey] = $t;
        }

        return view('admin.keuangan.laporan.index', compact(
            'siswas', 'tagihans', 'bulanList', 'kelasList',
            'masterTagihans', 'periode', 'tahun', 'perPage',
        ));
    }

    public function export(Request $request)
    {
        $tahun = $request->get('tahun', date('Y'));
        $periode = $request->get('periode', 'ganjil');
        $kelasId = $request->get('kelas_id');
        $namaKelas = 'Semua_Kelas';
        if ($request->filled('kelas_id')) {
            $kelas = Kelas::find($kelasId);
            if ($kelas) {
                $namaKelas = 'Kelas_'.$kelas->tingkat.'_'.$kelas->nama_kelas;
            }
        }
        $bulanList = ($periode === 'ganjil')
            ? ['Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember']
            : ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni'];
        $masterTagihans = MasterTagihan::orderBy('id')->get();
        $query = RiwayatAkademik::with(['siswa', 'kelas'])
            ->whereHas('siswa')
            ->whereHas('kelas')
            ->join('kelas', 'riwayat_akademiks.kelas_id', '=', 'kelas.id')
            ->join('siswas', 'riwayat_akademiks.siswa_id', '=', 'siswas.id');
        if ($request->filled('kelas_id')) {
            $query->where('riwayat_akademiks.kelas_id', $kelasId);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(fn ($q) => $q
                ->where('siswas.nama_lengkap', 'LIKE', "%{$search}%")
                ->orWhere('siswas.nisn', 'LIKE', "%{$search}%")
            );
        }
        $siswas = $query->orderBy('kelas.tingkat')
            ->orderBy('kelas.nama_kelas')
            ->orderBy('siswas.nama_lengkap')
            ->select('riwayat_akademiks.*')
            ->get();
        $tagihanRaw = TagihanSpp::whereIn('riwayat_akademik_id', $siswas->pluck('id'))
            ->where('tahun', $tahun)
            ->get();
        $tagihans = [];
        foreach ($tagihanRaw as $t) {
            $bulanKey = $t->bulan ?: 'NO_BULAN';
            $tagihans[$t->riwayat_akademik_id][$t->master_tagihan_id][$bulanKey] = $t;
        }
        $data = compact('siswas', 'tagihans', 'bulanList', 'masterTagihans', 'periode', 'tahun');
        $filename = "Laporan_Keuangan_{$namaKelas}_{$periode}_{$tahun}.xlsx";

        return Excel::download(new LaporanPembayaranExport($data), $filename);
    }
}
