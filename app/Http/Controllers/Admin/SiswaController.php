<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Siswa;
use App\Models\User;
use App\Models\Kelas;
use App\Models\TahunAjaran;
use App\Exports\TemplateSiswaExport;
use App\Exports\SiswaExport; // <-- TAMBAHAN: Untuk Export Data Real
use App\Imports\SiswaImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Traits\HandlesExcelImports; 

class SiswaController extends Controller
{
    use HandlesExcelImports; 

    // === 1. HALAMAN UTAMA (FILTER + SEARCH) ===
    public function index(Request $request)
    {
        // A. Siapkan Query Dasar
        $query = Siswa::with(['user', 'kelas', 'tahunAjaran']);

        // B. Logika Search (Nama / NISN)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nisn', 'like', "%{$search}%")
                  ->orWhereHas('user', function($u) use ($search) {
                      $u->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // C. Logika Filter Kelas (INI YANG BARU)
        if ($request->filled('kelas_id')) {
            $query->where('kelas_id', $request->kelas_id);
        }

        // [BARU] Logika Filter Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // D. Eksekusi Query (Pagination)
        // $siswas = $query->latest()->paginate(10);
        // ==========================================================
        // D. Eksekusi Query (PAGINATION DINAMIS) - [BAGIAN INI DIUBAH]
        // ==========================================================
        
        // 1. Ambil input 'per_page' dari user. 
        //    Jika user tidak memilih (kosong), default-nya adalah 10.
        $limit = $request->input('per_page', 10);

        // 2. Masukkan variabel $limit ke dalam fungsi paginate()
        //    Ini akan otomatis mengikuti pilihan user (10, 20, 30, atau 50)
        $siswas = $query->latest()->paginate($limit);


        // E. Ambil Daftar Kelas untuk Dropdown (SOLUSI ERROR KELAS_LIST)
        $kelas_list = Kelas::orderBy('tingkat')->orderBy('nama_kelas')->get();

        // F. Kirim ke View
        return view('admin.data_siswa.index', compact('siswas', 'kelas_list'));
    }

    // === 2. EXPORT DATA (FITUR BARU UNTUK ROUND-TRIP EXCEL) ===
    public function export(Request $request) 
    {
        // 1. Inisialisasi bagian nama file
        $namaKelas = 'Semua_Kelas';
        $statusSiswa = $request->status ? ucfirst($request->status) : 'Semua_Status';

        // 2. Cari nama kelas asli jika filter kelas_id ada
        if ($request->filled('kelas_id')) {
            $kelas = \App\Models\Kelas::find($request->kelas_id);
            if ($kelas) {
                // str_slug atau str_replace agar nama file tidak mengandung spasi aneh
                $namaKelas = str_replace(' ', '_', $kelas->nama_kelas);
            }
        }

        // 3. Gabungkan menjadi nama file yang cantik
        // Hasilnya: Data_Siswa_Kelas_XII_RPL_1_Aktif.xlsx
        $fileName = "Data_Siswa_{$namaKelas}_{$statusSiswa}.xlsx";

        // 4. Kirim ke proses download
        return Excel::download(new SiswaExport($request), $fileName);
    }

    // === 3. HALAMAN EDIT DETAIL ===
    public function edit($id)
    {
        $siswa = Siswa::findOrFail($id);
        $kelas = Kelas::orderBy('nama_kelas')->get();
        $tahunAjaran = TahunAjaran::orderBy('tahun', 'desc')->get();
        
        return view('admin.data_siswa.edit', compact('siswa', 'kelas', 'tahunAjaran'));
    }

    // === 4. PROSES UPDATE ===
    public function update(Request $request, $id)
    {
        $siswa = Siswa::findOrFail($id);
        
        // Update data User (Nama & Email)
        $siswa->user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        // Update data Siswa (Akademik)
        $siswa->update([
            'nisn' => $request->nisn,
            'kelas_id' => $request->kelas_id,
            'jenis_kelamin' => $request->jenis_kelamin,
            'tahun_ajaran_id' => $request->tahun_ajaran_id, 
            'status' => $request->status,
        ]);

        return redirect()->route('admin.siswas.index')->with('success', 'Data siswa diperbarui!');
    }

    // === 5. HAPUS SISWA ===
    public function destroy($id)
    {
        $siswa = Siswa::findOrFail($id);
        
        // Hapus User-nya juga agar bersih
        if($siswa->user) {
            $siswa->user->delete(); 
        } else {
            $siswa->delete();
        }
        return back()->with('success', 'Data siswa dihapus.');
    }

    public function bulkDestroy(Request $request)
    {
    // Validasi ada ID yang dikirim
    $request->validate([
        'ids' => 'required|array',
        'ids.*' => 'exists:siswas,id',
    ]);

    // Ambil semua siswa yang mau dihapus
    $siswas = Siswa::whereIn('id', $request->ids)->get();

    foreach ($siswas as $siswa) {
        // Hapus User terkait (agar bersih)
        if ($siswa->user) {
            $siswa->user->delete();
        }
        // Hapus Siswa
        // $siswa->delete();
        $siswa->user->forceDelete();
    }

    return redirect()->back()->with('success', 'Data siswa terpilih berhasil dihapus!');
    // return back()->with(    'success', count($request->ids) . ' data siswa berhasil dihapus.');
    }
    
    // === 6. FORM CREATE (MANUAL) ===
    public function create()
    {
        $kelas = Kelas::orderBy('nama_kelas')->get();
        $tahunAjaran = TahunAjaran::where('is_active', true)->get();
        return view('admin.data_siswa.create', compact('kelas', 'tahunAjaran'));
    }
    
    // === 7. PROSES STORE (MANUAL) ===
    public function store(Request $request)
    {
         // Jika Anda punya logika store manual, masukkan di sini.
         // Saat ini redirect saja sesuai kode lama Anda.
         return redirect()->route('admin.siswas.index');
    }

    // === 8. DOWNLOAD TEMPLATE IMPORT ===
    public function downloadTemplate()
    {
        return Excel::download(new TemplateSiswaExport, 'template_siswa.xlsx');
    }

    // === 9. PROSES IMPORT (TRAIT) ===
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048'
        ]);

        $result = $this->importData(new SiswaImport, $request->file('file'));

        if ($result['status'] === 'validation_error') {
            return back()->with('import_errors', $result['failures']);
        }

        return back()->with($result['status'], $result['message']);
    }
}