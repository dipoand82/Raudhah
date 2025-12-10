<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
    Schema::create('pembayarans', function (Blueprint $table) {
        $table->id();
        
        $table->foreignId('tagihan_spp_id')->constrained('tagihan_spps')->onDelete('cascade');
        
        // Admin yang memvalidasi (Bisa NULL jika bayar otomatis)
        $table->foreignId('user_id_admin')->nullable()->constrained('users');
        
        // Data Payment Gateway
        $table->string('kode_pembayaran')->unique(); // Order ID
        $table->decimal('jumlah_bayar', 12, 0);
        $table->dateTime('tanggal_bayar')->nullable();
        $table->string('metode_pembayaran')->nullable(); 
        $table->string('status_gateway')->default('pending'); 
        $table->string('snap_token')->nullable();
        
        $table->timestamps();
    });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembayarans');
    }
};