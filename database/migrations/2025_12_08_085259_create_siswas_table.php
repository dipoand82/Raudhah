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
            
            // 3. KELAS
            $table->foreignId('kelas_id')
                  ->nullable()
                  ->constrained('kelas') // Pastikan nama tabel kelas Anda 'kelas' (bukan 'classes')
                  ->onDelete('set null');

            // === [BARU] ===
            // Menambahkan kolom tingkat (7, 8, atau 9)
            // Fungsinya: Memudahkan filter "Tampilkan semua anak kelas 7"
            $table->integer('tingkat')->default(7); 
            
            // 4. Status Siswa
            $table->string('status')->default('Aktif'); 

            // === [UBAH] ===
            // DARI: tahun_masuk_id (Statis)
            // KE: tahun_ajaran_id (Dinamis - Berubah tiap tahun)
            $table->foreignId('tahun_ajaran_id')
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