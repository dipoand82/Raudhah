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
        'bulan', 'tahun', 'jumlah_tagihan', 'terbayar', 'status', 'snap_token',
        'midtrans_order_id',
    ];

    public function masterTagihan()
    {
        return $this->belongsTo(MasterTagihan::class, 'master_tagihan_id');
    }

    public function riwayatAkademik()
    {
        return $this->belongsTo(RiwayatAkademik::class, 'riwayat_akademik_id');
    }

    public function detailPembayaran()
    {
        return $this->hasMany(PembayaranDetail::class, 'tagihan_spp_id');
    }
}
