<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterTagihan extends Model
{
    use HasFactory;

    protected $table = 'master_tagihans';
    protected $fillable = ['nama_tagihan', 'nominal', 'deskripsi'];

    public function tagihan_spps()
    {
        return $this->hasMany(TagihanSpp::class);
    }
}