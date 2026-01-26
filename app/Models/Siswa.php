<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    use HasFactory;

    // Pastikan nama tabel sesuai dengan migration (biasanya Laravel otomatis mendeteksi 'siswas')
    // Jika migration Anda 'siswas', baris ini opsional, tapi aman ditulis.
    protected $table = 'siswas';

    // === BAGIAN PENTING: FILLABLE ===
    // Daftar kolom yang boleh diisi lewat Create/Update
    protected $fillable = [
        'user_id',
        'nisn',
        'nama_lengkap',
        'jenis_kelamin',
        
        // Kolom Akademik
        'kelas_id',
        'tingkat',          // <--- [BARU] Jangan lupa ini
        'tahun_ajaran_id',  // <--- [BARU] Ganti dari tahun_masuk_id
        
        'status',
    ];

    // === RELASI ===

    // 1. Relasi ke User (Akun Login)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 2. Relasi ke Kelas
    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    // 3. Relasi ke Tahun Ajaran (Posisi Tahun Sekarang)
    // Nama method diganti jadi 'tahunAjaran' biar sesuai konteks
    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class, 'tahun_ajaran_id');
    }

    // 4. Relasi ke Tagihan (Untuk Generate SPP nanti)
    public function tagihans()
    {
        return $this->hasMany(TagihanSpp::class);
    }
}