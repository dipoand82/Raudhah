<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
Schema::create('tagihan_spps', function (Blueprint $table) {
        $table->id();
        $table->foreignId('master_tagihan_id')->constrained();
        $table->foreignId('riwayat_akademik_id')->constrained('riwayat_akademiks')->onDelete('cascade');
        $table->string('bulan')->nullable();
        $table->string('tahun');
        $table->decimal('jumlah_tagihan', 12, 0);
        $table->decimal('terbayar', 12, 0)->default(0); // [PENTING] Menyimpan akumulasi cicilan
        $table->enum('status', ['lunas', 'belum_lunas', 'cicilan'])->default('belum_lunas');
        $table->timestamps();
    });
    }

    public function down(): void
    {
        Schema::dropIfExists('tagihan_spps');
    }
};
