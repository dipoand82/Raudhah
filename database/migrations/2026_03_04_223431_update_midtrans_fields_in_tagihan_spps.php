<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tagihan_spps', function (Blueprint $table) {
            // 1. Tambahkan snap_token untuk menyimpan token agar tidak generate ulang
            $table->string('snap_token')->nullable()->after('midtrans_order_id');

            // 2. Modifikasi kolom status agar mendukung status 'pending' dari Midtrans
            // Kita gunakan change() untuk memperbarui enum yang sudah ada
            $table->enum('status', ['lunas', 'belum_lunas', 'cicilan', 'pending'])
                  ->default('belum_lunas')
                  ->change();
        });
    }

    public function down(): void
    {
        Schema::table('tagihan_spps', function (Blueprint $table) {
            $table->dropColumn('snap_token');
            // Kembalikan status ke awal jika rollback
            $table->enum('status', ['lunas', 'belum_lunas', 'cicilan'])
                  ->default('belum_lunas')
                  ->change();
        });
    }
};
