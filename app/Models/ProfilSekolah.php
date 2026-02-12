<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfilSekolah extends Model
{
    use HasFactory;

    protected $table = 'profil_sekolahs'; // Pastikan nama tabel benar

    protected $fillable = [
        'nama_sekolah',
        'alamat',
        'email',
        'telepon',
        'visi',
        'misi',
        'logo_path',
        'banner_path',
        'brosur_info',
        'deskripsi_singkat',
        'info_penting',
        'alasan_memilih',
        'program_unggulan',
        'instagram',
        'tiktok',
        'info_footer',
    ];
}
