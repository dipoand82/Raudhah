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
    Schema::create('artikels', function (Blueprint $table) {
        $table->id();
        
        // Relasi: Penulisnya siapa? (Admin/Guru)
        $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
        
        $table->string('judul');
        $table->string('slug')->unique(); // URL (misal: pengumuman-libur)
        $table->text('isi'); // Teks berita panjang
        $table->string('gambar')->nullable(); // 1 Foto Thumbnail Utama
        $table->date('tanggal_publish')->nullable();
        $table->enum('status', ['draft', 'published'])->default('published');
        
        $table->timestamps();
    });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('artikels');
    }
};
