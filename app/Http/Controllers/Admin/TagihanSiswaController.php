<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\MasterTagihan;
use App\Models\Siswa;
use App\Models\TagihanSpp;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;

// Tambahkan ini

class TagihanSiswaController extends Controller
{
    public function index(Request $request)
    {
        // 1. Ambil daftar kelas untuk dropdown filter
        $kelasList = Kelas::orderBy('tingkat')->orderBy('nama_kelas')->get();

        // 2. Query utama dengan relasi agar efisien
        $query = TagihanSpp::with(['riwayatAkademik.siswa', 'riwayatAkademik.kelas', 'masterTagihan']);

        // 3. Filter Pencarian (Nama/NISN)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('riwayatAkademik.siswa', function ($q) use ($search) {
                $q->where('nama_lengkap', 'LIKE', "%{$search}%")
                    ->orWhere('nisn', 'LIKE', "%{$search}%");
            });
        }

        // 4. Filter Kelas
        if ($request->filled('kelas_id')) {
            $query->whereHas('riwayatAkademik', function ($q) use ($request) {
                $q->where('kelas_id', $request->kelas_id);
            });
        }

        // 5. Filter Status (Lunas/Cicilan/Belum Lunas)
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // 6. Eksekusi dengan Pagination
        // Tambahkan di dalam index() untuk memudahkan perhitungan di Blade
        $tagihans = $query->latest()->paginate(10)->through(function ($item) {
            // Tambahkan atribut sisa secara dinamis
            $item->sisa_tagihan = $item->jumlah_tagihan - $item->terbayar;

            return $item;
        })->withQueryString();

        return view('admin.keuangan.tagihan.index', compact('tagihans', 'kelasList'));
    }

    public function createBulk()
    {
        $masterTagihans = MasterTagihan::all();
        // Perbaikan: Menggunakan is_active sesuai database Anda
        $tahunAjarans = TahunAjaran::where('is_active', true)->get();
        $kelas = Kelas::orderBy('tingkat')->get();

        return view('admin.keuangan.tagihan.create-bulk', compact('masterTagihans', 'tahunAjarans', 'kelas'));
    }

    // === PROSES GENERATE TAGIHAN MASSAL ===
    public function storeBulk(Request $request)
    {
        // 1. Validasi
        $request->validate([
            'master_tagihan_id' => 'required|exists:master_tagihans,id',
            'tahun_ajaran_id' => 'required|exists:tahun_ajarans,id',
            'bulan' => 'nullable|string',
            'tahun' => 'required|string',
            'target_kelas' => 'required', // Bisa 'semua', 'tingkat_X', atau ID Kelas
        ]);

        $master = \App\Models\MasterTagihan::findOrFail($request->master_tagihan_id);

        // 2. Ambil siswa aktif berdasarkan filter
        // 2. Ambil siswa aktif berdasarkan filter
        $query = \App\Models\Siswa::where('status', 'Aktif')
            ->where('tahun_ajaran_id', $request->tahun_ajaran_id)
            ->whereNotNull('kelas_id'); // <--- TAMBAHKAN BARIS INI (Abaikan siswa yang belum punya kelas)

        if ($request->target_kelas !== 'semua') {
            if (str_starts_with($request->target_kelas, 'tingkat_')) {
                // Logika jika memilih per angkatan (misal: Semua Kelas 7)
                $tingkat = str_replace('tingkat_', '', $request->target_kelas);
                $query->whereHas('kelas', function ($q) use ($tingkat) {
                    $q->where('tingkat', $tingkat);
                });
            } else {
                // Logika jika memilih 1 kelas spesifik
                $query->where('kelas_id', $request->target_kelas);
            }
        }

        $targets = $query->get();

        // Cek jika siswa kosong
        if ($targets->isEmpty()) {
            return back()->with('error', 'Gagal: Tidak ada siswa aktif yang cocok dengan Kelas dan Tahun Ajaran tersebut.');
        }

        // 3. Eksekusi menggunakan Database Transaction agar aman jika ada error di tengah jalan
        return \Illuminate\Support\Facades\DB::transaction(function () use ($targets, $master, $request) {
            $count = 0;
            $skipped = 0;

            foreach ($targets as $siswa) { // <- KITA PAKAI VARIABEL $siswa AGAR JELAS

                // KUNCI PERBAIKAN: Cari atau Buat Riwayat Akademik untuk siswa ini
                $riwayat = \App\Models\RiwayatAkademik::firstOrCreate([
                    'siswa_id' => $siswa->id,
                    'tahun_ajaran_id' => $request->tahun_ajaran_id,
                ], [
                    'kelas_id' => $siswa->kelas_id,
                ]);

                // Validasi agar tidak double tagihan di bulan yang sama
                $exists = \App\Models\TagihanSpp::where([
                    'master_tagihan_id' => $master->id,
                    'riwayat_akademik_id' => $riwayat->id, // <- KINI PAKE ID RIWAYAT YANG BENAR
                    'bulan' => $request->bulan,
                    'tahun' => $request->tahun,
                ])->exists();

                if (! $exists) {
                    \App\Models\TagihanSpp::create([
                        'master_tagihan_id' => $master->id,
                        'riwayat_akademik_id' => $riwayat->id, // <- KINI PAKE ID RIWAYAT YANG BENAR
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

            // Pesan Alert
            $pesan = "$count Tagihan Berhasil Dibuat!";
            if ($skipped > 0) {
                $pesan .= " ($skipped siswa dilewati karena sudah ada tagihan untuk bulan tersebut).";
            }

            return back()->with('success', $pesan);
        });
    }
}
