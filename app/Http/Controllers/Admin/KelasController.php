<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kelas;
use Illuminate\Validation\Rule; // Tambahkan ini agar Rule::unique bekerja

class KelasController extends Controller
{
    public function index()
    {
        $kelas = Kelas::orderBy('tingkat', 'asc')
                      ->orderBy('nama_kelas', 'asc')
                      ->paginate(10);
        
        return view('admin.kelas.index', compact('kelas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tingkat'    => 'required|integer|in:7,8,9', 
            'nama_kelas' => [
                'required',
                'string',
                'max:5',
                // Validasi: nama_kelas boleh sama (misal 'A'), 
                // asalkan tingkatnya berbeda.
                Rule::unique('kelas')->where(function ($query) use ($request) {
                    return $query->where('tingkat', $request->tingkat);
                }),
            ], 
            'kode_kelas' => 'nullable|string|unique:kelas,kode_kelas',
        ], [
            // Custom pesan error agar user paham
            'nama_kelas.unique' => "Kelas {$request->tingkat}{$request->nama_kelas} sudah ada dalam database.",
        ]);

        Kelas::create($request->all());

        return back()->with('success', 'Kelas berhasil dibuat!');
    }

    public function update(Request $request, $id) 
    {
        $request->validate([
            'tingkat'    => 'required|integer|in:7,8,9',
            'nama_kelas' => [
                'required',
                'string',
                'max:5',
                // Validasi unique saat update (mengabaikan ID diri sendiri)
                Rule::unique('kelas')->where(function ($query) use ($request) {
                    return $query->where('tingkat', $request->tingkat);
                })->ignore($id),
            ],
        ], [
            'nama_kelas.unique' => "Kelas {$request->tingkat}{$request->nama_kelas} sudah digunakan.",
        ]);

        $kelas = Kelas::findOrFail($id);
        $kelas->update($request->all());

        return back()->with('success', 'Data Kelas berhasil diperbarui!');
    }

    public function destroy(Kelas $kelas)
    {
        $kelas->delete();
        return back()->with('success', 'Kelas dihapus.');
    }
}