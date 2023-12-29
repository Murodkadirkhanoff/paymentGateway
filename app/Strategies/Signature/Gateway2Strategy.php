<?php

namespace App\Strategies\Signature;

use App\Helpers\PaymentStatus;
use App\Interfaces\SignatureInterface;
use App\Models\Payment;

class Gateway2Strategy extends BaseGatewayStrategy implements SignatureInterface
{
    private string $app_key;
    private Payment $payment;

    public function __construct()
    {
        $this->payment = Payment::where('id', request()->get('invoice'))->first();
        $this->app_key = 'app_key';
    }


    public function make($data)
    {
        $stringToHash = $this->implode(data: $data, separator: '.', item: $this->app_key);
        // Parse to md5
        return md5($stringToHash);
    }


    public function getRequestParams()
    {
        request()->validate([
            'project' => 'required',
            'invoice' => 'required',
            'status' => 'required|in:created,inprogress,paid,expired,rejected',
            'amount' => 'required',
            'amount_paid' => 'required',
            'rand' => 'required',
        ]);

        return request()->all();
    }

    public function getModel()
    {
        return $this->payment;
    }

    public function checkLimit()
    {
        $status = request()->get('status');
        $project = request()->get('project');
        if ($status !== PaymentStatus::STATUS_PAID) {
            return true; // Handle case where status is not 'completed'
        }

        $totalAmountPaid = Payment::getTotalAmountPaidForGatewayWithinDay(gatewayType: Payment::GATEWAY2, status: $status, merchant_id: $project);

        return $totalAmountPaid < Payment::PAYMENT_DAILY_LIMIT;
    }
}
