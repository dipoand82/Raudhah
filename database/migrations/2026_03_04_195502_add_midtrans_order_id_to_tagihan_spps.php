<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tagihan_spps', function (Blueprint $table) {
            // Menambahkan kolom midtrans_order_id setelah kolom id
            $table->string('midtrans_order_id')->nullable()->after('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tagihan_spps', function (Blueprint $table) {
            // Menghapus kolom jika di-rollback
            $table->dropColumn('midtrans_order_id');
        });
    }
};
