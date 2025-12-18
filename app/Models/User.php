<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'username', // Tambahan
        'password',
        'role',     // Tambahan
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Relasi: User punya 1 data Siswa (Khusus kalau rolenya siswa)
    public function siswa()
    {
        return $this->hasOne(Siswa::class);
    }

    // Relasi: User (Admin/Guru) bisa menulis banyak artikel
    public function artikels()
    {
        return $this->hasMany(Artikel::class);
    }

    // Relasi: User bisa jadi pengupload Galeri
    public function galeris()
    {
        return $this->hasMany(Galeri::class);
    }
}