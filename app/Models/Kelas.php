<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    use HasFactory;

    protected $table = 'kelas'; // PENTING: Karena nama tabel tunggal/beda standar
    protected $fillable = ['nama_kelas', 'tingkat', 'kode_kelas'];

    public function riwayat_akademiks()
    {
        return $this->hasMany(RiwayatAkademik::class);
    }
}