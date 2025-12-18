<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Galeri extends Model
{
    use HasFactory;

    protected $table = 'galeris';
    protected $fillable = ['user_id', 'judul', 'deskripsi', 'gambar'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}