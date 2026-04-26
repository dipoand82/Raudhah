<?php

namespace App\Http\Controllers\Admin;

use App\Exports\TemplateSiswaExport;
use App\Http\Controllers\Controller;
use App\Imports\SiswaImport;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Models\User;
use App\Traits\HandlesExcelImports;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;

class SiswaController extends Controller
{
    use HandlesExcelImports;

    public function index(Request $request)
    {
        $limit = $request->input('per_page', 30);
        $query = Siswa::query()
            ->join('users', 'siswas.user_id', '=', 'users.id')
            ->leftJoin('kelas', 'siswas.kelas_id', '=', 'kelas.id')
            ->select('siswas.*');
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('siswas.nisn', 'like', "%{$search}%")
                    ->orWhere('users.name', 'like', "%{$search}%");
            });
        }
        if ($request->filled('kelas_id')) {
            $query->where('siswas.kelas_id', $request->kelas_id);
        }
        if ($request->filled('status')) {
            $query->where('siswas.status', $request->status);
        }
        $siswas = $query
            ->orderByRaw("FIELD(siswas.status, 'Aktif', 'Cuti', 'Lulus', 'Pindah', 'Keluar') ASC")
            ->orderBy('kelas.tingkat', 'asc')
            ->orderBy('kelas.nama_kelas', 'asc')
            ->orderBy('users.name', 'asc')
            ->with(['user', 'kelas', 'tahunAjaran'])
            ->paginate($limit);
        $totalSiswa = $siswas->total();
        $kelas = Kelas::orderBy('tingkat')->orderBy('nama_kelas')->get();
        $tahunAjaranList = TahunAjaran::orderBy('tahun', 'desc')->get();

        return view('admin.data_siswa.index', compact('siswas', 'kelas', 'tahunAjaranList', 'totalSiswa'));
    }

    public function export(Request $request)
    {
        $kelasId = $request->get('kelas_id');
        $status = $request->get('status');
        $search = $request->get('search');
        $labelStatus = $status ?: 'Semua-Status';
        $labelKelas = 'Semua-Kelas';

        if ($kelasId) {
            $kelas = \App\Models\Kelas::find($kelasId);
            if ($kelas) {
                $labelKelas = 'Kelas-'.$kelas->nama_lengkap;
            }
        }

        $fileName = "Data-Siswa-{$labelKelas} & Status-{$labelStatus} ".'.xlsx';

        return Excel::download(new \App\Exports\SiswaExport($kelasId, $status, $search), $fileName);
    }

    public function edit($id)
    {
        $siswa = Siswa::findOrFail($id);
        $kelas = Kelas::orderBy('nama_kelas')->get();
        $tahunAjaran = TahunAjaran::orderBy('tahun', 'desc')->get();

        return view('admin.data_siswa.edit', compact('siswa', 'kelas', 'tahunAjaran'));
    }

    public function update(Request $request, $id)
    {
        $siswa = Siswa::findOrFail($id);

        $request->validate([
            'email' => 'required|email|unique:users,email,'.$siswa->user_id,
            'nisn' => 'required|numeric|unique:siswas,nisn,'.$siswa->id,
            'name' => 'required|string|max:255',
            'status' => 'required|in:Aktif,Cuti,Lulus,Keluar,Pindah',
            'kelas_id' => [
                'nullable',
                'required_if:status,Aktif',
                'exists:kelas,id',
            ],
        ], [
            'kelas_id.required_if' => 'Siswa dengan status Aktif wajib memiliki kelas!',
            'email.unique' => 'Email ini sudah digunakan oleh pengguna lain.',
        ]);

        return DB::transaction(function () use ($request, $siswa) {

            $kelasId = $request->kelas_id;
            $tahunAjaranId = $request->tahun_ajaran_id;

            if ($request->status !== 'Aktif') {
                $kelasId = null;
            }

            if ($siswa->user) {
                $siswa->user->update([
                    'name' => $request->name,
                    'email' => $request->email,
                ]);
            }

            $siswa->update([
                'nisn' => $request->nisn,
                'nama_lengkap' => $request->name,
                'kelas_id' => $kelasId,
                'jenis_kelamin' => $request->jenis_kelamin,
                'tahun_ajaran_id' => $tahunAjaranId,
                'status' => $request->status,
            ]);

            return redirect()->route('admin.manajemen-user.index')->with('success', 'Data siswa berhasil diperbarui!');
        });
    }

    public function destroy($id)
    {
        $siswa = Siswa::findOrFail($id);

        if ($siswa->user) {
            $siswa->user->delete();
        } else {
            $siswa->delete();
        }

        return back()->with('success', 'Data siswa dihapus.');
    }

    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:siswas,id',
        ]);
        $siswas = Siswa::whereIn('id', $request->ids)->with('user')->get();
        foreach ($siswas as $siswa) {
            if ($siswa->user) {
                $siswa->user->forceDelete();
            }
        }

        return redirect()->back()->with([
            'success' => count($request->ids).' data siswa terpilih berhasil dihapus!',
            'active_tab' => 'siswa',
        ]);
    }

    public function bulkResetPassword(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:siswas,id',
        ]);
        $siswas = Siswa::whereIn('id', $request->ids)->with('user')->get();
        $berhasil = 0;
        foreach ($siswas as $siswa) {
            if ($siswa && $siswa->user) {
                $siswa->user->update([
                    'password' => Hash::make($siswa->nisn),
                ]);
                $berhasil++;
            }
        }

        return redirect()->back()->with('success', $berhasil.' password siswa berhasil direset ke NISN.');
    }

    public function create()
    {
        $kelas = Kelas::orderBy('nama_kelas')->get();
        $tahunAjaran = TahunAjaran::where('is_active', true)->get();

        return view('admin.data_siswa.create', compact('kelas', 'tahunAjaran'));
    }

    public function store(Request $request)
    {
        if (! $request->filled('email')) {
            $cleanName = strtolower(str_replace(' ', '.', $request->name));
            $generatedEmail = $cleanName.'.'.$request->nisn.'@raudhah.com';
            $request->merge(['email' => $generatedEmail]);
        }
        $request->validate([
            'nisn' => 'required|numeric|unique:siswas,nisn',
            'nama_lengkap' => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:L,P',
            'kelas_id' => 'required|exists:kelas,id',
            'tingkat' => 'required',
            'email' => 'required|email|unique:users,email',
        ]);
        $tahunAktif = TahunAjaran::where('is_active', true)->first();

        return DB::transaction(function () use ($request, $tahunAktif) {

            $user = User::updateOrCreate(
                ['email' => $request->email],
                [
                    'name' => $request->nama_lengkap,
                    'password' => Hash::make($request->nisn),
                    'role' => 'siswa',
                    'must_change_password' => true,
                ]
            );

            Siswa::updateOrCreate(
                ['nisn' => $request->nisn],
                [
                    'user_id' => $user->id,
                    'nama_lengkap' => $request->nama_lengkap,
                    'jenis_kelamin' => strtoupper($request->jenis_kelamin),
                    'kelas_id' => $request->kelas_id,
                    'tingkat' => $request->tingkat,
                    'tahun_ajaran_id' => $tahunAktif ? $tahunAktif->id : null,
                    'status' => 'Aktif',
                ]
            );

            return redirect()->route('admin.siswas.index')
                ->with('success', 'Data Siswa dan Akun Login berhasil dibuat.');
        });
    }

    public function downloadTemplate()
    {
        return Excel::download(new TemplateSiswaExport, 'template_import_siswa.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048',
        ]);

        $result = $this->importData(new SiswaImport, $request->file('file'));

        if ($result['status'] === 'validation_error') {
            return back()->with('import_errors', $result['failures']);
        }

        return back()->with($result['status'], $result['message']);
    }
}
