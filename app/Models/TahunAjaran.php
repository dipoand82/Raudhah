<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TahunAjaran extends Model
{
    use HasFactory;

    protected $table = 'tahun_ajarans';
    protected $fillable = ['tahun', 'is_active'];
    public function siswas()
    {
        return $this->hasMany(Siswa::class, foreignKey: 'tahun_ajaran_id');
    }
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
