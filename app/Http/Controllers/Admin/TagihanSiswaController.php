<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\MasterTagihan;
use App\Models\RiwayatAkademik;
use App\Models\Siswa;
use App\Models\TagihanSpp;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TagihanSiswaController extends Controller
{
    public function index(Request $request)
    {
        $masterTagihans = MasterTagihan::all();
        $tahunAjarans = TahunAjaran::where('is_active', true)->get();
        $kelasList = Kelas::orderBy('tingkat')->orderBy('nama_kelas')->get();

        $periodeList = TagihanSpp::selectRaw('bulan, tahun')
            ->distinct()
            ->orderBy('tahun', 'desc')
            ->orderBy('bulan', 'asc')
            ->get();

        $query = TagihanSpp::with(['riwayatAkademik.siswa', 'riwayatAkademik.kelas', 'masterTagihan'])
            ->join('riwayat_akademiks', 'tagihan_spps.riwayat_akademik_id', '=', 'riwayat_akademiks.id')
            ->join('kelas', 'riwayat_akademiks.kelas_id', '=', 'kelas.id')
            ->select('tagihan_spps.*'); // ← penting agar tidak bentrok kolom
        // --- Filter Pencarian ---
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('riwayatAkademik.siswa', function ($q) use ($search) {
                $q->where('nama_lengkap', 'LIKE', "%{$search}%")
                    ->orWhere('nisn', 'LIKE', "%{$search}%");
            });
        }

        if ($request->filled('kelas_id')) {
            $query->whereHas('riwayatAkademik', function ($q) use ($request) {
                $q->where('kelas_id', $request->kelas_id);
            });
        }

        if ($request->filled('status')) {
            $query->where('tagihan_spps.status', $request->status);
        }

        if ($request->filled('periode')) {
            [$bulan, $tahun] = explode('|', $request->periode);
            $query->where('tagihan_spps.bulan', trim($bulan))->where('tagihan_spps.tahun', trim($tahun));
        }

        if ($request->filled('master_tagihan_id')) {
            $query->where('tagihan_spps.master_tagihan_id', $request->master_tagihan_id);
        }

        // --- Logika Urutan ---
        if ($request->filled('selected_ids')) {
            $selectedIds = array_filter(explode(',', $request->selected_ids));
            $selectedIds = array_map('intval', array_filter($selectedIds));

            if (! empty($selectedIds)) {
                $idsString = implode(',', $selectedIds);
                $query->orderByRaw("FIELD(tagihan_spps.id, {$idsString}) DESC")
                    ->orderByRaw("FIELD(tagihan_spps.status, 'belum_lunas', 'cicilan', 'lunas')")
                    ->orderByRaw("FIELD(kelas.tingkat, '7', '8', '9')")
                    ->orderBy('kelas.nama_kelas', 'asc');
            }
        } else {
            $query->orderByRaw("FIELD(tagihan_spps.status, 'belum_lunas', 'cicilan', 'lunas')")
                ->orderByRaw("FIELD(kelas.tingkat, '7', '8', '9')")
                ->orderBy('kelas.nama_kelas', 'asc');
        }
        $perPage = $request->input('per_page', 30);

        $tagihans = $query->paginate($perPage)->through(function ($item) {
            $item->sisa_tagihan = $item->jumlah_tagihan - $item->terbayar;

            return $item;
        })->withQueryString();

        return view('admin.keuangan.tagihan.index', compact('tagihans', 'kelasList', 'periodeList', 'masterTagihans', 'tahunAjarans'));
    }

    public function createBulk()
    {
        $masterTagihans = MasterTagihan::all();
        $tahunAjarans = TahunAjaran::where('is_active', true)->get();
        $kelas = Kelas::orderBy('tingkat')->get();

        return view('admin.keuangan.tagihan.create-bulk', compact('masterTagihans', 'tahunAjarans', 'kelas'));
    }

    public function storeBulk(Request $request)
    {
        $request->validate([
            'master_tagihan_id' => 'required|exists:master_tagihans,id',
            'tahun_ajaran_id' => 'required|exists:tahun_ajarans,id',
            'bulan' => 'nullable|string',
            'tahun' => 'required|string',
            'target_kelas' => 'required',
        ], [
            'master_tagihan_id.required' => 'Jenis biaya (Master Tagihan) wajib dipilih.',
            'master_tagihan_id.exists' => 'Jenis biaya yang dipilih tidak valid.',
            'tahun_ajaran_id.required' => 'Tahun ajaran wajib dipilih.',
            'tahun_ajaran_id.exists' => 'Tahun ajaran tidak valid.',
            'tahun.required' => 'Tahun tagihan wajib diisi.',
            'target_kelas.required' => 'Target kelas wajib dipilih.',
        ]);

        $master = MasterTagihan::findOrFail($request->master_tagihan_id);

        $query = Siswa::where('status', 'Aktif')
            ->where('tahun_ajaran_id', $request->tahun_ajaran_id)
            ->whereNotNull('kelas_id');

        if ($request->target_kelas !== 'semua') {
            if (str_starts_with($request->target_kelas, 'tingkat_')) {
                $tingkat = str_replace('tingkat_', '', $request->target_kelas);
                $query->whereHas('kelas', function ($q) use ($tingkat) {
                    $q->where('tingkat', $tingkat);
                });
            } else {
                $query->where('kelas_id', $request->target_kelas);
            }
        }

        $targets = $query->get();

        if ($targets->isEmpty()) {
            return back()->with('error', 'Gagal: Tidak ada siswa aktif yang ditemukan pada kriteria tersebut.');
        }

        return DB::transaction(function () use ($targets, $master, $request) {
            $count = 0;
            $skipped = 0;

            foreach ($targets as $siswa) {
                $riwayat = RiwayatAkademik::firstOrCreate([
                    'siswa_id' => $siswa->id,
                    'tahun_ajaran_id' => $request->tahun_ajaran_id,
                ], [
                    'kelas_id' => $siswa->kelas_id,
                ]);

                $exists = TagihanSpp::where([
                    'master_tagihan_id' => $master->id,
                    'riwayat_akademik_id' => $riwayat->id,
                    'bulan' => $request->bulan,
                    'tahun' => $request->tahun,
                ])->exists();

                if (! $exists) {
                    TagihanSpp::create([
                        'master_tagihan_id' => $master->id,
                        'riwayat_akademik_id' => $riwayat->id,
                        'bulan' => $request->bulan,
                        'tahun' => $request->tahun,
                        'jumlah_tagihan' => $master->nominal,
                        'terbayar' => 0,
                        'status' => 'belum_lunas',
                    ]);
                    $count++;
                } else {
                    $skipped++;
                }
            }

            $pesan = "$count Tagihan berhasil dibuat secara massal!";
            if ($skipped > 0) {
                $pesan .= " Sebanyak $skipped siswa dilewati karena sudah memiliki tagihan yang sama.";
            }

            return back()->with('success', $pesan);
        });
    }

    public function destroyBulk(Request $request)
    {
        $request->validate([
            'tagihan_ids' => 'required|array',
            'tagihan_ids.*' => 'exists:tagihan_spps,id',
        ], [
            'tagihan_ids.required' => 'Pilih setidaknya satu tagihan untuk dihapus.',
            'tagihan_ids.*.exists' => 'Salah satu tagihan tidak ditemukan atau sudah dihapus.',
        ]);

        try {
            $deletedCount = TagihanSpp::whereIn('id', $request->tagihan_ids)
                ->where('status', 'belum_lunas')
                ->delete();

            if ($deletedCount === 0) {
                return back()->with('error', 'Gagal. Hanya tagihan berstatus BELUM LUNAS yang bisa dihapus.');
            }

            return back()->with('success', "$deletedCount tagihan berhasil dihapus dari sistem.");

        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan sistem: '.$e->getMessage());
        }
    }
}
