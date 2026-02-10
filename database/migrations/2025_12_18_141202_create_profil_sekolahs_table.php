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
        Schema::create('profil_sekolahs', function (Blueprint $table) {
            $table->id();

            // Data Utama
            $table->string('nama_sekolah')->default('SMP IT Raudhah');
            $table->text('deskripsi_singkat')->nullable(); // Intro sekolah

            // Visi Misi
            $table->text('visi')->nullable();
            $table->text('misi')->nullable();

            // Konten Tambahan
            $table->text('alasan_memilih')->nullable(); // Poin-poin alasan
            $table->text('program_unggulan')->nullable(); // Daftar program

            // Kontak
            $table->text('alamat')->nullable();
            $table->string('email')->nullable();
            $table->string('telepon',20)->nullable();

            // Media (Gambar)
            $table->string('logo_path')->nullable(); // Logo
            $table->string('banner_path')->nullable(); // Banner (Ditaruh di bawah logo)


            // Media Sosial
            $table->string('instagram')->nullable();
            $table->string('tiktok')->nullable();
            $table->text('info_footer')->nullable(); // Untuk info tambahan

            $table->timestamps();
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Karena up() membuat tabel, maka down() harus menghapus tabel
        Schema::dropIfExists('profil_sekolahs');
    }
};
