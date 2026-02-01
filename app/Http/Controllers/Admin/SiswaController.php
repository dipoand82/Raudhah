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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SiswaController extends Controller
{
    use HandlesExcelImports; 

    // === 1. HALAMAN UTAMA (FILTER + SEARCH) ===
    public function index(Request $request)
    {
        // Dapatkan limit pagination (Kode Lama)
        $limit = $request->input('per_page', 30);

        // ==========================================================
        // A. Query Dasar dengan JOIN
        // ==========================================================
        $query = Siswa::query()
            // 1. Join ke Users (untuk sorting berdasarkan Nama)
            ->join('users', 'siswas.user_id', '=', 'users.id')
            // 2. Left Join (Tetap gunakan Left Join dari kode lama agar siswa tanpa kelas tetap muncul)
            ->leftJoin('kelas', 'siswas.kelas_id', '=', 'kelas.id')
            // 3. Select siswas.* agar output tetap berupa model Siswa
            ->select('siswas.*');

        // ==========================================================
        // B. Logika Search (Nama / NISN) - (DIPERTAHANKAN)
        // ==========================================================
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('siswas.nisn', 'like', "%{$search}%")
                ->orWhere('users.name', 'like', "%{$search}%");
            });
        }

        // ==========================================================
        // C. Logika Filter Tambahan - (DIPERTAHANKAN)
        // ==========================================================
        
        // Filter Kelas
        if ($request->filled('kelas_id')) {
            $query->where('siswas.kelas_id', $request->kelas_id);
        }

        // Filter Status
        if ($request->filled('status') ) {
            $query->where('siswas.status', $request->status);
        }
        

        // ==========================================================
        // D. Logika Pengurutan
        // ==========================================================
        $siswas = $query
            // --- [BARU DITAMBAHKAN] ---
            // Logika: Urutkan status dulu (Aktif -> Cuti -> Lulus -> Keluar -> Pindah)
            ->orderByRaw("FIELD(siswas.status, 'Aktif', 'Cuti', 'Lulus', 'Pindah', 'Keluar') ASC") 
            // --------------------------

            // --- [LOGIKA LAMA DIPERTAHANKAN SEBAGAI CADANGAN] ---
            ->orderBy('kelas.tingkat', 'asc')     // 1. Kelas 7, 8, 9
            ->orderBy('kelas.nama_kelas', 'asc')  // 2. Kelas A, B, C
            ->orderBy('users.name', 'asc')        // 3. Nama Ahmad, Budi, dst
            
            // Load relasi (Tetap dipertahankan)
            ->with(['user', 'kelas', 'tahunAjaran']) 
            ->paginate($limit);

        // ==========================================================
        // E. Data Pendukung (Modal) - (DIPERTAHANKAN)
        // ==========================================================
        $kelas = Kelas::orderBy('tingkat')->orderBy('nama_kelas')->get();

        // Untuk modal EDIT (ambil semua untuk dropdown)

        $tahunAjaranList = TahunAjaran::orderBy('tahun', 'desc')->get(); 


        // F. Kirim ke View - (DIPERTAHANKAN)
        return view('admin.data_siswa.index', compact('siswas', 'kelas', 'tahunAjaranList'));
    }

    // === 2. EXPORT DATA (FITUR BARU UNTUK ROUND-TRIP EXCEL) ===
     public function export(Request $request) 
    {
    // Ambil nilai asli dari request (bisa null jika tidak dipilih)
    $kelasId = $request->get('kelas_id');
    $status = $request->get('status');
    $search = $request->get('search'); // Tambahkan search jika ingin filter kata kunci juga 

    // Logika untuk LABEL Nama File saja
    $labelStatus = $status ?: 'Semua-Status';
    $labelKelas = 'Semua-Kelas';

    if ($kelasId) {
        $kelas = \App\Models\Kelas::find($kelasId);
        if ($kelas) {
            $labelKelas = "Kelas-" .$kelas->nama_lengkap; 
        }
    }

    // Susun nama file
    $fileName = "Data-Siswa-{$labelKelas} & Status-{$labelStatus} " . ".xlsx";

    // Kirim $status yang asli (null/isi) agar query database di SiswaExport benar
    return Excel::download(new \App\Exports\SiswaExport($kelasId, $status, $search), $fileName);
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

        // 1. VALIDASI: Menambahkan aturan status/kelas dan pesan error kustom
        $request->validate([
            'email' => 'required|email|unique:users,email,' . $siswa->user_id,
            'nisn' => 'required|numeric|unique:siswas,nisn,' . $siswa->id,
            'name' => 'required|string|max:255',
            'status' => 'required|in:Aktif,Cuti,Lulus,Keluar,Pindah',
            'kelas_id' => [
                'nullable',
                'required_if:status,Aktif', // Jika status Aktif, kelas WAJIB diisi
                'exists:kelas,id'
            ],
        ], [
            // --- BARU: Pesan Error Kustom agar Admin tidak bingung ---
            'kelas_id.required_if' => 'Siswa dengan status Aktif wajib memiliki kelas!',
            'email.unique' => 'Email ini sudah digunakan oleh pengguna lain.',
            'nisn.unique' => 'NISN sudah terdaftar.',
        ]);

    return DB::transaction(function () use ($request, $siswa) {
    
    // 2. LOGIKA KONSISTENSI
    $kelasId = $request->kelas_id;
    $tahunAjaranId = $request->tahun_ajaran_id;

    // Jika status TIDAK Aktif, maka Kelas dan Tahun Ajaran dipaksa NULL
    if ($request->status !== 'Aktif') {
        $kelasId = null;
        // Optional: Biasanya siswa lulus/pindah tahun ajarannya tetap disimpan 
        // untuk arsip, tapi jika ingin null juga, aktifkan baris bawah ini:
        // $tahunAjaranId = null; 
    }

    // 3. Update data User
    if ($siswa->user) {
        $siswa->user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);
    }

    // 4. Update data Siswa
    $siswa->update([
        'nisn' => $request->nisn,
        'nama_lengkap' => $request->name,
        'kelas_id' => $kelasId, // Menggunakan variabel yang sudah difilter IF di atas
        'jenis_kelamin' => $request->jenis_kelamin,
        'tahun_ajaran_id' => $tahunAjaranId, 
        'status' => $request->status,
    ]);

    return redirect()->route('admin.manajemen-user.index')->with('success', 'Data siswa berhasil diperbarui!');
        });
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
    // Buat email otomatis jika input email kosong
    if (!$request->filled('email')) {
        // Ganti spasi jadi titik dan kecilkan semua huruf
        $cleanName = strtolower(str_replace(' ', '.', $request->name));
        $generatedEmail = $cleanName . '.' . $request->nisn . '@sekolah.id';
        $request->merge(['email' => $generatedEmail]);
    }
    // 1. Validasi Input
    $request->validate([
        'nisn' => 'required|numeric|unique:siswas,nisn',
        'nama_lengkap' => 'required|string|max:255',
        'jenis_kelamin' => 'required|in:L,P',
        'kelas_id' => 'required|exists:kelas,id',
        'tingkat' => 'required',
        'email' => 'required|email|unique:users,email',
    ]);
        // AMBIL TAHUN AKTIF (Inilah obat untuk error "tahunAktif")
        $tahunAktif = TahunAjaran::where('is_active', true)->first();

        // Menggunakan Transaction agar jika salah satu gagal, semua dibatalkan
        return DB::transaction(function () use ($request,$tahunAktif) {
            
            // 2. BUAT/UPDATE USER (Sama dengan logika Import)
          $user = User::updateOrCreate(
            ['email' => $request->email],
            [
                'name' => $request->nama_lengkap,
                'password' => Hash::make($request->nisn), // Password default = NISN
                'role' => 'siswa',
                'must_change_password' => true,
            ]
        );

        // 3. BUAT/UPDATE DATA SISWA (Sama dengan logika Import)
        Siswa::updateOrCreate(
            ['nisn' => $request->nisn],
            [
                'user_id'         => $user->id,
                'nama_lengkap'    => $request->nama_lengkap,
                'jenis_kelamin'   => strtoupper($request->jenis_kelamin),
                'kelas_id'        => $request->kelas_id,
                'tingkat'         => $request->tingkat,
                // Pastikan variabel $this->tahunAktif sudah didefinisikan di __construct atau method lain
                'tahun_ajaran_id' => $tahunAktif ? $tahunAktif->id : null,
                'status'          => 'Aktif',
            ]
        );

        return redirect()->route('admin.siswas.index')
            ->with('success', 'Data Siswa dan Akun Login berhasil dibuat.');
    });
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