<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up(): void
{
    Schema::table('users', function (Blueprint $table) {
        // Kolom penanda. Default 'false' biar Admin yang buat skrg aman.
        // Nanti kalau create siswa baru, kita set jadi 'true'.
        $table->boolean('must_change_password')->default(false)->after('password');
    });
}

public function down(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn('must_change_password');
    });
}
};
