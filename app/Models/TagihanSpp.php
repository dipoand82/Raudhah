<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TagihanSpp extends Model
{
    use HasFactory;

    protected $table = 'tagihan_spps';
    protected $fillable = [
        'master_tagihan_id', 'riwayat_akademik_id', 
        'bulan', 'tahun', 'jumlah_tagihan', 'status'
    ];

    public function master_tagihan()
    {
        return $this->belongsTo(MasterTagihan::class);
    }

    // Tagihan nempel ke riwayat (Siswa di kelas X)
    public function riwayat_akademik()
    {
        return $this->belongsTo(RiwayatAkademik::class);
    }

    public function pembayarans()
    {
        return $this->hasMany(Pembayaran::class);
    }
}