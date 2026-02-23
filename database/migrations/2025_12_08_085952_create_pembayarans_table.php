<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // INI ADALAH TABEL "KERANJANG TRANSAKSI MIDTRANS"
        Schema::create('pembayarans', function (Blueprint $table) {
            $table->id();

            // Siapa yang melakukan transaksi ini?
            $table->foreignId('siswa_id')->constrained('siswas')->onDelete('cascade');

            $table->foreignId('user_id_admin')->nullable()->constrained('users'); // Jika dibantu admin

            $table->string('kode_pembayaran')->unique(); // Order ID Midtrans (Misal: PAY-12345)
            $table->decimal('total_bayar', 12, 0); // Total keranjang (Misal: 300.000)
            $table->dateTime('tanggal_bayar')->nullable();
            $table->string('metode_pembayaran')->nullable();
            $table->string('status_gateway')->default('pending'); // pending, settlement, expire, cancel
            $table->string('snap_token')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembayarans');
    }
};
