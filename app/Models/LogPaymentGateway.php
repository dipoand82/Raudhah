<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogPaymentGateway extends Model
{
    use HasFactory;

    protected $table = 'log_payment_gateways';
    protected $fillable = [
        'pembayaran_id', 'order_id_gateway', 
        'request_body', 'response_body'
    ];
}