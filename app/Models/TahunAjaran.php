<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TahunAjaran extends Model
{
    use HasFactory;
    
    // Karena nama tabelnya jamak 'tahun_ajarans', Laravel otomatis tahu.
    // Tapi kita definisikan saja biar aman.
    protected $table = 'tahun_ajarans'; 
    protected $fillable = ['tahun', 'semester', 'is_active'];

    public function siswas()
    {
        return $this->hasMany(Siswa::class, 'tahun_masuk_id');
    }
}