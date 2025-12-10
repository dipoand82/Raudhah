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
        
        $table->foreignId('master_tagihan_id')->constrained('master_tagihans');
        
        // Tagihan menempel ke Riwayat (Siswa di Kelas X)
        $table->foreignId('riwayat_akademik_id')->constrained('riwayat_akademiks')->onDelete('cascade');
        
        $table->string('bulan')->nullable(); // Juli
        $table->string('tahun'); // 2024
        $table->decimal('jumlah_tagihan', 12, 0);
        $table->enum('status', ['lunas', 'belum_lunas', 'cicilan'])->default('belum_lunas');
        
        $table->timestamps();
    });
    }

    public function down(): void
    {
        Schema::dropIfExists('tagihan_spps');
    }
};