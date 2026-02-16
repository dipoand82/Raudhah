<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class GuruImport implements ToModel, WithHeadingRow
{
public function model(array $row)
{
    // 1. Ambil Nama Lengkap, hilangkan spasi dan karakter aneh
    $namaLengkap = trim($row['nama']);
    $username = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $namaLengkap));

    // 2. Generate email: namapanjang@raudhah.com
    $email = !empty($row['email']) ? $row['email'] : $username . '@raudhah.com';

    // 3. Cek Duplikasi
    $existingUser = \App\Models\User::where('email', $email)->first();
    if ($existingUser) return null;

    return new \App\Models\User([
        'name'     => $namaLengkap,
        'email'    => $email,
        'role'     => 'guru',
        'password' => \Illuminate\Support\Facades\Hash::make('12345678'),
        'must_change_password' => false,
    ]);
}
}
