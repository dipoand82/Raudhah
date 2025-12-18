<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow; // Biar bisa baca Header Excel

class UsersImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // Pastikan Excel punya header: nama, email, role
        return new User([
            'name'     => $row['nama'],  // Sesuai header Excel
            'email'    => $row['email'], // Sesuai header Excel
            'role'     => $row['role'],  // Sesuai header Excel
            'password' => Hash::make('12345678'), // Default Password
            'must_change_password' => true,       // Wajib ganti pass
        ]);
    }
}