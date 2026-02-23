<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pembayaran_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pembayaran_id')->constrained('pembayarans')->onDelete('cascade');
            $table->foreignId('tagihan_spp_id')->constrained('tagihan_spps')->onDelete('cascade');
            $table->decimal('nominal_dibayar', 12, 0); // Nominal spesifik untuk tagihan ini
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembayaran_details');
    }
};
