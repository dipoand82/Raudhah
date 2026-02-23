<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MasterTagihan;
use Illuminate\Http\Request;

class MasterTagihanController extends Controller
{
public function index()
{
    $masterTagihans = MasterTagihan::latest()->get();
    // Gunakan titik sebagai pemisah folder
    return view('admin.keuangan.master-tagihan.index', compact('masterTagihans'));
}

    public function store(Request $request)
    {
        $request->validate([
            'nama_tagihan' => 'required|string|max:255',
            'nominal' => 'required|numeric|min:0',
            'deskripsi' => 'nullable|string',
        ]);

        MasterTagihan::create($request->all());

        return back()->with('success', 'Master Tagihan berhasil ditambahkan!');
    }

public function update(Request $request, $id)
{
    $request->validate([
        'nama_tagihan' => 'required|string|max:255',
        'nominal' => 'required|string', // Ubah ke string dulu karena ada kemungkinan input format ribuan
        'deskripsi' => 'nullable|string',
    ]);

    $master = MasterTagihan::findOrFail($id);

    // Sanitasi nominal: Menghapus titik/koma agar menjadi angka murni sebelum disimpan
    $nominalBersih = str_replace(['.', ','], '', $request->nominal);

    $master->update([
        'nama_tagihan' => $request->nama_tagihan,
        'nominal' => $nominalBersih,
        'deskripsi' => $request->deskripsi,
    ]);

    return back()->with('success', 'Master Tagihan berhasil diperbarui!');
}

    public function destroy($id)
    {
        $master = MasterTagihan::findOrFail($id);

        // Proteksi: Jangan hapus jika sudah ada siswa yang punya tagihan ini
        $cekDipakai = \App\Models\TagihanSpp::where('master_tagihan_id', $id)->count();
        if ($cekDipakai > 0) {
            return back()->with('error', 'Gagal: Master tagihan ini sudah digunakan oleh siswa. Tidak bisa dihapus.');
        }

        $master->delete();

        return back()->with('success', 'Master Tagihan berhasil dihapus!');
    }
}
