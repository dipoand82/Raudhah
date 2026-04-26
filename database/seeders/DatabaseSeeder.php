<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {

        $adminId = DB::table('users')->insertGetId([
            'name' => 'Staf Administrasi 1',
            'email' => 'admin11@raudhah.com',
            'password' => Hash::make('AdminRaudhah1*'),
            'role' => 'admin',
            'must_change_password' => false,
            'created_at' => now(), 'updated_at' => now(),
        ]);

        DB::table('users')->insert([
            'name' => 'Staf Administrasi 2',
            'email' => 'admin22@raudhah.com',
            'password' => Hash::make('AdminRaudhah2*'),
            'role' => 'admin',
            'must_change_password' => false,
            'created_at' => now(), 'updated_at' => now(),
        ]);

        DB::table('users')->insert([
            'name' => 'Staf Administrasi 3',
            'email' => 'admin33@raudhah.com',
            'password' => Hash::make('AdminRaudhah3*'),
            'role' => 'admin',
            'must_change_password' => false,
            'created_at' => now(), 'updated_at' => now(),
        ]);

        DB::table('users')->insert([
            'name' => 'Staf Administrasi 4',
            'email' => 'admin44@raudhah.com',
            'password' => Hash::make('AdminRaudhah4*'),
            'role' => 'admin',
            'must_change_password' => false,
            'created_at' => now(), 'updated_at' => now(),
        ]);
    }
}
