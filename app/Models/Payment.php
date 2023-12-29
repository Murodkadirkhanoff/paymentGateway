<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    const PAYMENT_DAILY_LIMIT = 100000;
    const GATEWAY1 = 'gateway1';
    const GATEWAY2 = 'gateway2';

    public static function getTotalAmountPaidForGatewayWithinDay($gatewayType, $status, $merchant_id)
    {
        return Payment::where('gateway_type', $gatewayType)
            ->where('status', $status)
            ->where('merchant_id',$merchant_id)
            ->where('created_at', '>=', now()->subDay())
            ->sum('amount_paid');
    }
}
