<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    use HasFactory;

    protected $table = 'siswas';

    // === BAGIAN INI YANG DIUBAH ===
    // Kita hapus alamat & no_telp
    // Kita tambah kelas & status
    protected $fillable = [
        'user_id', 
        'tahun_masuk_id', 
        'nisn', 
        'nama_lengkap',
        'jenis_kelamin', 
        'kelas_id',   // <--- PENTING: Biar fitur penempatan kelas jalan
        'status'   // <--- PENTING: Default 'Aktif'
    ];

    // Relasi ke User (Login)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

        public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    // Relasi ke Tahun Masuk
    public function tahunMasuk()
    {
        return $this->belongsTo(TahunAjaran::class, 'tahun_masuk_id');
    }

    // === CATATAN TENTANG RIWAYAT AKADEMIK ===
    // Kalau Abang mau pakai sistem "Kelas" yang simpel (cuma kolom string '7A' di tabel siswa),
    // fungsi di bawah ini mungkin belum terpakai sekarang. 
    // Tapi dibiarkan saja tidak apa-apa, tidak bikin error kok.
    public function riwayat_akademiks()
    {
        return $this->hasMany(RiwayatAkademik::class);
    }

    public function kelas_aktif()
    {
        return $this->hasOne(RiwayatAkademik::class)->where('status_siswa', 'aktif')->latest();
    }
}