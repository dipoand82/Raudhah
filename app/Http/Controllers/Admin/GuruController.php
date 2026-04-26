<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Imports\GuruImport;
use App\Models\User;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class GuruController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search_guru');
        $userGuru = User::where('role', 'guru')
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(30)
            ->withQueryString();

        $userSiswa = User::where('role', 'siswa')->paginate(30);
        $kelas = \App\Models\Kelas::all();

        return view('admin.manajemen-user.index', compact('userGuru', 'userSiswa', 'kelas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:users',
        ]);
        $email = $request->email;
        $username = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $request->name));
        if (! $email) {
            $email = $username.'@raudhah.com';
            $count = \App\Models\User::where('email', 'like', $username.'%')->count();
            if ($count > 0) {
                $email = $username.($count + 1).'@raudhah.com';
            }
        }
        $passwordBaru = $username.'12345.';
        \App\Models\User::create([
            'name' => $request->name,
            'email' => $email,
            'role' => 'guru',
            'password' => \Illuminate\Support\Facades\Hash::make($passwordBaru),
            'must_change_password' => false,
        ]);

        return redirect()->route('admin.manajemen-user.index', ['tab' => 'guru'])
            ->with('success', "Akun Guru {$request->name} berhasil dibuat! Email: {$email} | Password: {$passwordBaru}");
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$id,
        ]);

        $user = User::findOrFail($id);
        $user->update($request->only('name', 'email'));

        return redirect()->route('admin.manajemen-user.index', ['tab' => 'guru'])
            ->with('success', 'Data Guru berhasil diperbarui.');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
        ], [
            'file.mimes' => 'Format file harus .xlsx, .xls, atau .csv',
        ]);
        try {
            Excel::import(new GuruImport, $request->file('file'));

            return redirect()->route('admin.manajemen-user.index', ['tab' => 'guru'])
                ->with('success', 'Data Guru berhasil diimport!');
        } catch (\Exception $e) {
            return redirect()->route('admin.manajemen-user.index', ['tab' => 'guru'])
                ->with('error', 'Gagal mengimport data. Periksa kembali format file Anda.');
        }
    }

    public function downloadTemplate()
    {
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\TemplateGuruExport,
            'template_import_guru.xlsx'
        );
    }
}
