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
        'bulan', 'tahun', 'jumlah_tagihan', 'terbayar', 'status',
    ];

    public function masterTagihan() // Ubah dari master_tagihan menjadi masterTagihan
    {
        // Tambahkan foreign key 'master_tagihan_id' agar lebih pasti
        return $this->belongsTo(MasterTagihan::class, 'master_tagihan_id');
    }

    // Tagihan nempel ke riwayat (Siswa di kelas X)
    public function riwayatAkademik()
    {
        // Gunakan riwayat_akademik_id sebagai foreign key sesuai protected $fillable kamu
        return $this->belongsTo(RiwayatAkademik::class, 'riwayat_akademik_id');
    }

    public function pembayarans()
    {
        return $this->hasMany(Pembayaran::class);
    }
}
