<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Siswa;

class SiswaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Siswa::create([
            'nama' => ' Eman Santoso',
            'nisn' => '0032190551',
            'jenis_kelamin' => 'Laki-laki',
            'kelas' => '10A',
        ]);

        // Siswa kedua (yang kita tambahkan)
        Siswa::create([
            'nama' => 'Ani Suryani',
            'nisn' => '0087654321',
            'jenis_kelamin' => 'Perempuan',
            'kelas' => '10B',
        ]);
    }
    
}
