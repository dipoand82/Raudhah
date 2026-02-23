<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PembayaranDetail extends Model
{
    use HasFactory;

    protected $table = 'pembayaran_details';

    protected $fillable = [
        'pembayaran_id',
        'tagihan_spp_id',
        'nominal_dibayar'
    ];

    // Relasi balik ke header pembayaran (Struk Induk)
    public function pembayaran()
    {
        return $this->belongsTo(Pembayaran::class, 'pembayaran_id', 'id');
    }

    // Relasi ke tagihan SPP yang dibayar
    public function tagihan_spp()
    {
        return $this->belongsTo(TagihanSpp::class, 'tagihan_spp_id', 'id');
    }
}
