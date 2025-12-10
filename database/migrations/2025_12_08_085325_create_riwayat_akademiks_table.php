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
    Schema::create('riwayat_akademiks', function (Blueprint $table) {
        $table->id();
        
        // Menghubungkan 3 Tabel: Siswa + Kelas + Tahun
        $table->foreignId('siswa_id')->constrained('siswas')->onDelete('cascade');
        $table->foreignId('kelas_id')->constrained('kelas')->onDelete('cascade');
        $table->foreignId('tahun_ajaran_id')->constrained('tahun_ajarans')->onDelete('cascade');
        
        $table->enum('status_siswa', ['aktif', 'lulus', 'pindah', 'do'])->default('aktif');
        
        $table->timestamps();
        
        // Mencegah duplikasi data riwayat
        $table->unique(['siswa_id', 'tahun_ajaran_id']);
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('riwayat_akademiks');
    }
};
