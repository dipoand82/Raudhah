<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    use HasFactory;

    protected $table = 'pembayarans';
    protected $fillable = [
        'tagihan_spp_id', 'user_id_admin', 'kode_pembayaran',
        'jumlah_bayar', 'tanggal_bayar', 'metode_pembayaran',
        'status_gateway', 'snap_token', 'id_transaksi_gateway'
    ];

    public function tagihan_spp()
    {
        return $this->belongsTo(TagihanSpp::class);
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'user_id_admin');
    }
}