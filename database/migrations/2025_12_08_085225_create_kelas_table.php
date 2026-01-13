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
        Schema::create('kelas', function (Blueprint $table) {
            $table->id();
            $table->string('tingkat'); // Contoh: 7, 8, 9
            $table->string('nama_kelas'); // Contoh: A, B, C
            
            // Tambahkan kolom ini agar sesuai dengan Controller
            $table->string('kode_kelas')->nullable()->unique(); 
            
            // Kunci Utama: Mencegah duplikat 7A, tapi membolehkan 7A dan 8A
            $table->unique(['tingkat', 'nama_kelas']); 
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kelas');
    }
};