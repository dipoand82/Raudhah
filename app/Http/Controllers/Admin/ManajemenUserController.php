<?php

namespace App\Http\Controllers\Admin;

use App\Exports\TemplateSiswaExport;
use App\Http\Controllers\Controller;
use App\Imports\GuruImport;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class ManajemenUserController extends Controller
{
    public function index(Request $request)
    {
        $searchSiswa = $request->input('search');
        $searchGuru = $request->input('search_guru');
        $perPageSiswa = $request->input('per_page', 30);
        $perPageGuru = $request->input('per_page_guru', 30);
        $statusFilter = $request->input('status');
        $kelasFilter = $request->input('kelas_id');
        $userSiswa = User::where('role', 'siswa')
            ->with(['dataSiswa.kelas'])
            ->when($searchSiswa, function ($q) use ($searchSiswa) {
                $q->where(function ($query) use ($searchSiswa) {
                    $query->where('name', 'like', "%{$searchSiswa}%")
                        ->orWhereHas('dataSiswa', function ($sq) use ($searchSiswa) {
                            $sq->where('nisn', 'like', "%{$searchSiswa}%");
                        });
                });
            })
            ->when($statusFilter, function ($q) use ($statusFilter) {
                return $q->whereHas('dataSiswa', function ($sq) use ($statusFilter) {
                    $sq->where('status', $statusFilter);
                });
            })
            ->when($kelasFilter, function ($q) use ($kelasFilter) {
                return $q->whereHas('dataSiswa', function ($sq) use ($kelasFilter) {
                    $sq->where('kelas_id', $kelasFilter);
                });
            })
            ->leftJoin('siswas', 'users.id', '=', 'siswas.user_id')
            ->leftJoin('kelas', 'siswas.kelas_id', '=', 'kelas.id')
            ->select('users.*')
            ->orderByRaw("FIELD(siswas.status, 'Aktif', 'Cuti','Lulus','Pindah', 'Keluar') ASC")
            ->orderBy('kelas.tingkat', 'asc')
            ->orderBy('kelas.nama_kelas', 'asc')
            ->orderBy('users.name', 'asc')
            ->paginate($perPageSiswa, ['*'], 'siswa_page')
            ->withQueryString();
        $userGuru = User::where('role', 'guru')
            ->when($searchGuru, function ($q) use ($searchGuru) {
                $q->where(function ($query) use ($searchGuru) {
                    $query->where('name', 'like', "%{$searchGuru}%")
                        ->orWhere('email', 'like', "%{$searchGuru}%");
                });
            })
            ->latest()
            ->paginate($perPageGuru, ['*'], 'guru_page')
            ->withQueryString();
        $kelas = Kelas::orderBy('tingkat')->orderBy('nama_kelas')->get();
        $tahunAjaranList = TahunAjaran::orderBy('tahun', 'desc')->get();
        $tahunAjaran = TahunAjaran::where('is_active', true)->first();
        $totalSiswa = $userSiswa->total();

        return view('admin.manajemen_user.index', compact(
            'userSiswa', 'userGuru', 'kelas', 'tahunAjaran', 'tahunAjaranList', 'totalSiswa'
        ));
    }

    public function storeSiswa(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'nisn' => 'required|numeric|unique:siswas,nisn',
            'email' => 'nullable|email|unique:users,email',
            'kelas_id' => 'nullable|exists:kelas,id',
            'jenis_kelamin' => 'required|in:L,P',
        ]);

        $tahunAktif = TahunAjaran::where('is_active', true)->first();
        abort_if(! $tahunAktif, 422, 'Tidak ada tahun ajaran aktif. Aktifkan tahun ajaran terlebih dahulu.');
        if ($request->filled('email')) {
            $emailFinal = $request->email;
        } else {
            $namaDepan = Str::lower(explode(' ', trim($request->name))[0]);
            $emailFinal = $namaDepan.'.'.$request->nisn.'@raudhah.com';
            if (User::where('email', $emailFinal)->exists()) {
                $emailFinal = $namaDepan.$request->nisn.rand(1, 9).'@raudhah.com';
            }
        }

        return DB::transaction(function () use ($request, $emailFinal, $tahunAktif) {
            $user = User::updateOrCreate(
                ['email' => $emailFinal],
                [
                    'name' => $request->name,
                    'password' => Hash::make($request->nisn),
                    'role' => 'siswa',
                    'must_change_password' => true,
                ]
            );
            Siswa::updateOrCreate(
                ['nisn' => $request->nisn],
                [
                    'user_id' => $user->id,
                    'nama_lengkap' => $request->name,
                    'jenis_kelamin' => $request->jenis_kelamin,
                    'kelas_id' => $request->kelas_id,
                    'tingkat' => $request->kelas_id ? Kelas::find($request->kelas_id)->tingkat : null,
                    'tahun_ajaran_id' => $tahunAktif ? $tahunAktif->id : null,
                    'status' => 'Aktif',
                ]
            );

            return redirect()->back()->with('success', "Siswa {$request->name} berhasil ditambahkan! Email login: ".$emailFinal);
        });
    }

    public function importSiswa(Request $request)
    {
        set_time_limit(300);
        $request->validate(['file' => 'required|mimes:xlsx,xls,csv']);

        $tabSiswa = ['tab' => 'siswa'];

        try {
            $import = new \App\Imports\SiswaImport;
            $import->import($request->file('file'));

            if (! empty($import->fallbackClasses)) {
                session()->flash('fallback_info', $import->fallbackClasses);
            }

            if ($import->failures()->isNotEmpty()) {
                return redirect()->route('admin.manajemen-user.index', $tabSiswa)
                    ->with('import_errors', $import->failures());
            }

            return redirect()->route('admin.manajemen-user.index', $tabSiswa)
                ->with('success', 'Import Berhasil Selesai!');

        } catch (\Exception $e) {
            return redirect()->route('admin.manajemen-user.index', $tabSiswa)
                ->with('error', 'Gagal Import: '.$e->getMessage());
        }
    }

    public function downloadTemplate()
    {
        return Excel::download(new TemplateSiswaExport, 'template_import_siswa.xlsx');
    }

    public function storeGuru(Request $request)
    {
        $request->validate(['name' => 'required', 'email' => 'required|email|unique:users']);
        $namaClean = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $request->name));
        $passwordBaru = $namaClean.'12345.';

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'role' => 'guru',
            'password' => Hash::make($passwordBaru),
            'must_change_password' => false,
        ]);

        return back()->with('success', "Akun Guru berhasil dibuat dengan Password: {$passwordBaru}");
    }

    public function importGuru(Request $request)
    {
        $request->validate(['file' => 'required|mimes:xlsx,xls,csv']);

        try {
            Excel::import(new GuruImport, $request->file('file'));

            return back()->with('success', 'Import Guru Berhasil!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal Import: '.$e->getMessage());
        }
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|current_password',
            'password' => 'required|confirmed|min:8',
        ]);

        $request->user()->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Password Admin berhasil diubah!');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        $role = $user->role;

        $user->delete();

        return back()->with([
            'success' => 'User berhasil dihapus',
            'active_tab' => $role,
        ]);
    }

    public function resetPasswordSiswa($id)
    {
        $siswa = Siswa::findOrFail($id);

        if ($siswa->user) {
            $siswa->user->update([
                'password' => Hash::make($siswa->nisn),
                'must_change_password' => true,
            ]);

            return back()->with('success', "Password siswa {$siswa->nama_lengkap} berhasil di-reset kembali ke NISN ({$siswa->nisn}).");
        }

        return back()->with('error', 'Akun user tidak ditemukan untuk siswa ini.');
    }
}
