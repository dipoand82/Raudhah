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
        return new User([
            'name'     => $row['nama'],
            'email'    => $row['email'],
            'role'     => 'guru', // <--- KITA KUNCI ROLENYA JADI GURU
            'password' => Hash::make('12345678'),
            'must_change_password' => true,
        ]);
    }
}