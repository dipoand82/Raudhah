<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MasterTagihan;
use App\Models\TagihanSpp; // Import model untuk pengecekan status generate
use Illuminate\Http\Request;

class MasterTagihanController extends Controller
{
    public function index()
    {
        $masterTagihans = MasterTagihan::latest()->get();
        return view('admin.keuangan.master-tagihan.index', compact('masterTagihans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_tagihan' => 'required|string|max:255',
            'nominal' => 'required|numeric|min:0',
            'deskripsi' => 'nullable|string',
        ], [
            // Pesan Error Bahasa Indonesia
            'nama_tagihan.required' => 'Nama tagihan wajib diisi.',
            'nominal.required' => 'Nominal biaya wajib diisi.',
            'nominal.numeric' => 'Nominal harus berupa angka murni.',
            'nominal.min' => 'Nominal tidak boleh kurang dari 0.',
        ]);

        MasterTagihan::create($request->all());

        return back()->with('success', 'Master Tagihan berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $master = MasterTagihan::findOrFail($id);

        // REKOMENDASI: Cek apakah tagihan ini sudah pernah di-generate ke siswa
        $sudahGenerate = TagihanSpp::where('master_tagihan_id', $id)->exists();

        if ($sudahGenerate) {
            return back()->with('error', 'Gagal: Data tidak bisa diubah karena tagihan sudah di-generate ke siswa. Silakan buat Master Biaya baru jika ada perubahan kebijakan harga.');
        }

        $request->validate([
            'nama_tagihan' => 'required|string|max:255',
            'nominal' => 'required|string',
            'deskripsi' => 'nullable|string',
        ], [
            'nama_tagihan.required' => 'Nama tagihan wajib diisi.',
            'nominal.required' => 'Nominal biaya wajib diisi.',
        ]);

        // Sanitasi nominal (untuk jaga-jaga jika input mengandung titik ribuan)
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

        // Proteksi: Cek apakah sudah ada relasi di tabel tagihan_spps
        $cekDipakai = TagihanSpp::where('master_tagihan_id', $id)->exists();

        if ($cekDipakai) {
            return back()->with('error', 'Gagal: Master tagihan ini tidak bisa dihapus karena sudah digunakan dalam data tagihan siswa.');
        }

        $master->delete();

        return back()->with('success', 'Master Tagihan berhasil dihapus!');
    }
}
