<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    use HasFactory;

    protected $table = 'siswas';
    protected $fillable = [
        'user_id', 'tahun_masuk_id', 'nisn', 
        'nama_lengkap','jenis_kelamin', 'alamat', 'no_telp_wali'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tahun_masuk()
    {
        return $this->belongsTo(TahunAjaran::class, 'tahun_masuk_id');
    }

    // Untuk melihat sejarah kelas siswa
    public function riwayat_akademiks()
    {
        return $this->hasMany(RiwayatAkademik::class);
    }
    // Helper untuk mengambil kelas aktif saat ini
    public function kelas_aktif()
    {
        return $this->hasOne(RiwayatAkademik::class)->where('status_siswa', 'aktif')->latest();
    }
}