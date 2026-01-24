<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TahunAjaran extends Model
{
    use HasFactory;
    
    protected $table = 'tahun_ajarans'; 
    
    // Ini sudah benar (Whitelist kolom yang boleh diisi)
    protected $fillable = ['tahun', 'is_active'];

    // Relasi ke Siswa (One to Many)
    public function siswas()
    {
        return $this->hasMany(Siswa::class, foreignKey: 'tahun_ajaran_id');
    }

    // --- TAMBAHAN (HELPER) ---
    // Gunanya biar gampang cari mana tahun yang aktif
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}