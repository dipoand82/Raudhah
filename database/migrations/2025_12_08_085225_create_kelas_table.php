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
        $table->string('nama_kelas'); // Contoh: "VII A"
        $table->integer('tingkat'); // Contoh: 7
        $table->string('kode_kelas')->unique()->nullable(); // Contoh: 7A
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
