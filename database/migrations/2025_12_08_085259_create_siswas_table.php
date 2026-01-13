<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('siswas', function (Blueprint $table) {
            $table->id();
            
            // 1. Relasi ke Akun Login
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('cascade')
                  ->unique(); 
            
            // 2. Data Akademik
            $table->string('nisn')->unique();
            $table->string('nama_lengkap');
            $table->enum('jenis_kelamin', ['L', 'P']);
            
            // === TAMBAHAN PENTING (KELAS) ===
            // Default null, karena siswa baru belum punya kelas
            // $table->string('kelas')->nullable(); 
            $table->foreignId('kelas_id')->nullable()->constrained('kelas')->onDelete(action: 'set null');
            // 3. Status Siswa
            $table->string('status')->default('Aktif'); 

            // 4. Relasi ke Tahun Masuk
            $table->foreignId('tahun_masuk_id')
                  ->nullable()
                  ->constrained('tahun_ajarans')
                  ->nullOnDelete(); 
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('siswas');
    }
};