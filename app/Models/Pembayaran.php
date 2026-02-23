<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    use HasFactory;

    protected $table = 'pembayarans';

    // SAYA SUDAH SESUAIKAN DENGAN NAMA KOLOM TERBARU DI MIGRATION
    protected $fillable = [
        'siswa_id',
        'user_id_admin',
        'kode_pembayaran',
        'total_bayar', // <--- INI SUDAH DIPERBAIKI (Sebelumnya jumlah_bayar)
        'tanggal_bayar',
        'metode_pembayaran',
        'status_gateway',
        'snap_token',
    ];

    // Relasi ke detail (untuk melihat rincian tagihan apa saja yang dibayar)
    public function details()
    {
        return $this->hasMany(PembayaranDetail::class, 'pembayaran_id', 'id');
    }

    // Relasi ke Admin yang memproses
    public function admin()
    {
        return $this->belongsTo(User::class, 'user_id_admin');
    }

    // Relasi ke Siswa yang membayar
    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id', 'id');
    }
}
