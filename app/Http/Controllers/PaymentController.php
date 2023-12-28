<?php

namespace App\Http\Controllers;

use App\Http\Requests\Gateway1Request;
use App\Http\Requests\Gateway2Request;
use App\Services\SignatureService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function callbackUrl1(Gateway1Request $request)
    {

        $data = $request->except('sign');



        $signature = (new SignatureService($data))->makeSign();

        if($signature == request()->get('sign')){
            Log::info('Gateway 1 Callback received and verified: ' . json_encode($data));
            $data['amount'] = $data['amount'] * 100; // dollar to cent
            $data['amount_paid'] = $data['amount_paid'] * 100; // dollar to cent
        }else {
            // Signature verification failed
            Log::warning('Gateway 1 Callback signature verification failed: ' . json_encode($data));
        }
    }

    public function callbackUrl2(Gateway2Request $request)
    {
        $data = $request->except('sign');

        $signature = (new SignatureService())->makeSign($data);

        if($signature == request()->get('sign')){
            Log::info('Gateway 1 Callback received and verified: ' . json_encode($data));
            $data['amount'] = $data['amount'] * 100; // dollar to cent
            $data['amount_paid'] = $data['amount_paid'] * 100; // dollar to cent
        }else {
            // Signature verification failed
            Log::warning('Gateway 1 Callback signature verification failed: ' . json_encode($data));
        }
    }
}
