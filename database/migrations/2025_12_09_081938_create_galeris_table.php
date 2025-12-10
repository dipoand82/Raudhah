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
    Schema::create('galeris', function (Blueprint $table) {
        $table->id();
        
        // Relasi: Siapa uploader-nya?
        $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
        
        $table->string('judul'); // Judul Kegiatan
        $table->text('deskripsi')->nullable(); // Ket. singkat
        $table->string('gambar'); // File foto dokumentasi
        
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('galeris');
    }
};
