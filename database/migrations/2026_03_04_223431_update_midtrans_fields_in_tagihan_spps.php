<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tagihan_spps', function (Blueprint $table) {
            $table->string('snap_token')->nullable()->after('midtrans_order_id');
            $table->enum('status', ['lunas', 'belum_lunas', 'cicilan', 'pending'])
                ->default('belum_lunas')
                ->change();
        });
    }

    public function down(): void
    {
        Schema::table('tagihan_spps', function (Blueprint $table) {
            $table->dropColumn('snap_token');
            $table->enum('status', ['lunas', 'belum_lunas', 'cicilan'])
                ->default('belum_lunas')
                ->change();
        });
    }
};
