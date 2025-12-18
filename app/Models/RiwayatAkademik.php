<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiwayatAkademik extends Model
{
    use HasFactory;

    protected $table = 'riwayat_akademiks';
    protected $fillable = ['siswa_id', 'kelas_id', 'tahun_ajaran_id', 'status_siswa'];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    public function tahun_ajaran()
    {
        return $this->belongsTo(TahunAjaran::class);
    }
    public function tagihan_spps()
    {
        return $this->hasMany(TagihanSpp::class);
    }
}