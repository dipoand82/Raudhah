<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    use HasFactory;

    protected $table = 'kelas';
    protected $fillable = ['nama_kelas', 'tingkat', 'kode_kelas'];

    // Relasi ke Siswa (Nanti dipakai)
    public function siswas()
    {
        return $this->hasMany(Siswa::class);
    }
    // Relasi ke Riwayat Akademik (Nanti dipakai)
    public function riwayat_akademiks()
    {
        return $this->hasMany(RiwayatAkademik::class);
    }
    public function getNamaLengkapAttribute()
    {
        return $this->tingkat . $this->nama_kelas;
    }
}