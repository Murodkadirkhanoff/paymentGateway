<?php

namespace App\Http\Controllers;

use App\Factories\GatewayStrategyFactory;
use App\Http\Requests\Gateway1Request;
use App\Http\Requests\Gateway2Request;
use App\Models\Payment;
use App\Services\SignatureService;
use App\Strategies\Signature\Gateway1Strategy;
use App\Strategies\Signature\Gateway2Strategy;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{

    public function callbackUrl(Request $request, $gateway): JsonResponse
    {
        try {
            $gatewayStrategy = GatewayStrategyFactory::make($gateway);

            $data = $gatewayStrategy->getRequestParams();
            $signature = (new SignatureService($data))->makeSign($gatewayStrategy);

            if ($signature !== $request->sign) {
                throw new \Exception('Callback signature verification failed');
            }

            $payment = $gatewayStrategy->getModel();
            if (!$gatewayStrategy->checkLimit()) {
                throw new \Exception("Limit exceeded for $gateway");
            }

            $payment->update(['status' => $request->status]);
            Log::info("Callback received and verified for $gateway: " . json_encode($data));

            return response()->json([
                'success' => true,
                'data' => $payment
            ]);
        } catch (\Exception $e) {
            Log::warning($e->getMessage());
            return response()->json([
                'success' => false,
                'data' => $e->getMessage()
            ]);
        }
    }
}
