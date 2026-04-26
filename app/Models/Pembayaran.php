<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    use HasFactory;

    protected $table = 'pembayarans';

    protected $fillable = [
        'siswa_id',
        'user_id_admin',
        'kode_pembayaran',
        'total_bayar',
        'tanggal_bayar',
        'metode_pembayaran',
        'status_gateway',
        'snap_token',
    ];

    public function detailPembayaran()
    {
        return $this->hasMany(PembayaranDetail::class, 'pembayaran_id', 'id');
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'user_id_admin');
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id', 'id');
    }
}
