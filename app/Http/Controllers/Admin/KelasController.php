<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class KelasController extends Controller
{
    public function index()
    {
        $kelas = Kelas::withCount('siswas')
            ->orderBy('tingkat', 'asc')
            ->orderBy('nama_kelas', 'asc')
            ->paginate(10);

        return view('admin.kelas.index', compact('kelas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tingkat' => 'required|integer|in:7,8,9',
            'nama_kelas' => [
                'required', 'string', 'max:5',
                Rule::unique('kelas')->where(function ($query) use ($request) {
                    return $query->where('tingkat', $request->tingkat);
                }),
            ],
        ], [
            'nama_kelas.unique' => "Gagal! Kelas {$request->tingkat}{$request->nama_kelas} sudah ada.",
        ]);

        try {
            Kelas::create([
                'tingkat' => $request->tingkat,
                'nama_kelas' => $request->nama_kelas,
            ]);

            return back()->with('success', 'Kelas berhasil dibuat!');

        } catch (\Exception $e) {
            Log::error('Gagal simpan kelas: '.$e->getMessage());

            return back()->with('error', 'Terjadi kesalahan sistem saat menyimpan data.');
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'tingkat' => 'required|integer|in:7,8,9',
            'nama_kelas' => [
                'required', 'string', 'max:5',
                Rule::unique('kelas')->where(function ($query) use ($request) {
                    return $query->where('tingkat', $request->tingkat);
                })->ignore($id),
            ],
        ], [
            'nama_kelas.unique' => "Gagal! Kelas {$request->tingkat}{$request->nama_kelas} sudah digunakan.",
        ]);

        try {
            $kelas = Kelas::findOrFail($id);
            $kelas->update([
                'tingkat' => $request->tingkat,
                'nama_kelas' => $request->nama_kelas,
            ]);

            return back()->with('success', 'Data Kelas berhasil diperbarui!');

        } catch (\Exception $e) {
            Log::error("Gagal update kelas ID {$id}: ".$e->getMessage());

            return back()->with('error', 'Gagal memperbarui data karena kesalahan sistem.');
        }
    }

    public function destroy($id)
    {
        try {
            $kelas = Kelas::findOrFail($id);

            if ($kelas->siswas()->count() > 0) {
                return back()->with('error', 'Gagal Hapus! Kelas ini masih memiliki siswa aktif.');
            }

            $kelas->delete();

            return back()->with('success', 'Kelas berhasil dihapus.');

        } catch (\Exception $e) {
            Log::error("Gagal hapus kelas ID {$id}: ".$e->getMessage());

            return back()->with('error', 'Terjadi kesalahan saat mencoba menghapus data.');
        }
    }
}
