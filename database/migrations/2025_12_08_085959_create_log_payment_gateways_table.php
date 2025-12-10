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
    Schema::create('log_payment_gateways', function (Blueprint $table) {
        $table->id();
        $table->foreignId('pembayaran_id')->nullable()->constrained('pembayarans')->onDelete('set null');
        $table->string('order_id_gateway')->nullable();
        $table->text('request_body'); // Data mentah Midtrans
        $table->text('response_body')->nullable();
        $table->timestamps();
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('log_payment_gateways');
    }
};
