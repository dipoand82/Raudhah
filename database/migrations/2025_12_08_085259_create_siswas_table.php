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
            
            // 1. Relasi ke Akun Login (Wajib ada user dulu)
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('cascade')
                  ->unique(); 
            
            // 2. Data Akademik
            $table->string('nisn')->unique();
            $table->string('nama_lengkap');
            $table->enum('jenis_kelamin', ['L', 'P']);
            
            // 3. Status Siswa (PENTING BUAT FITUR KELULUSAN)
            // Default 'Aktif'. Nanti bisa berubah jadi 'Lulus', 'Pindah', 'Keluar'
            $table->string('status')->default('Aktif'); 

            // 4. Data Kontak
            $table->text('alamat');
            $table->string('no_telp_wali')->nullable();
            
            // 5. Relasi ke Tahun Masuk (Angkatan)
            // Pastikan tabel 'tahun_ajarans' sudah dibuat migrasinya sebelum file ini
            $table->foreignId('tahun_masuk_id')
                  ->nullable()
                  ->constrained('tahun_ajarans')
                  ->nullOnDelete(); 
            
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