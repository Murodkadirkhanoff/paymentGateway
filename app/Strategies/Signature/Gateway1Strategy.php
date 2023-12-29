<?php

namespace App\Strategies\Signature;

use App\Helpers\PaymentStatus;
use App\Interfaces\SignatureInterface;
use App\Models\Payment;

class Gateway1Strategy extends BaseGatewayStrategy implements SignatureInterface
{
    private string $merchant_key;
    private Payment $payment;

    public function __construct()
    {
        $this->payment = Payment::where('id', request()->get('payment_id'))->first();
        $this->merchant_key = '$merchant_key';
    }

    public function make($data)
    {
        $stringToHash = $this->implode(data: $data, separator: ':', item: $this->merchant_key);
        // Parse to hash sha256
        return hash('sha256', $stringToHash);
    }

    public function getRequestParams()
    {
        request()->validate([
            'merchant_id' => 'required',
            'payment_id' => 'required',
            'status' => 'required|in:new,pending,completed,expired,rejected',
            'amount' => 'required',
            'amount_paid' => 'required',
            'timestamp' => 'required',
            'sign' => 'required',
        ]);

        return request()->except('sign');
    }

    public function getModel()
    {
        return $this->payment;
    }

    public function checkLimit()
    {
        $status = request()->get('status');
        $merchant_id = request()->get('merchant_id');
        if ($status !== PaymentStatus::STATUS_COMPLETED) {
            return true; // Handle case where status is not 'completed'
        }

        $totalAmountPaid = Payment::getTotalAmountPaidForGatewayWithinDay(gatewayType: Payment::GATEWAY1, status: $status, merchant_id:$merchant_id);

        return $totalAmountPaid < Payment::PAYMENT_DAILY_LIMIT;
    }
}
