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
    Schema::create('siswas', function (Blueprint $table) {
        $table->id();
        
        // RELASI PENTING: 1 Akun User = 1 Biodata Siswa
        $table->foreignId('user_id')->constrained('users')->onDelete('cascade')->unique();
        
        // Data Pribadi
        $table->string('nisn')->unique();
        $table->string('nama_lengkap');
        $table->text('alamat');
        $table->string('no_telp_wali')->nullable();
        
        // Relasi ke Tahun Masuk
        $table->foreignId('tahun_masuk_id')->constrained('tahun_ajarans'); 
        
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('siswas');
    }
};
