<?php

namespace App\Factories;

use App\Interfaces\SignatureInterface;
use App\Models\Payment;
use App\Strategies\Signature\Gateway1Strategy;
use App\Strategies\Signature\Gateway2Strategy;

class GatewayStrategyFactory
{
    public static function make($gateway): SignatureInterface
    {
        switch ($gateway) {
            case Payment::GATEWAY1:
                return new Gateway1Strategy();
            case Payment::GATEWAY2:
                return new Gateway2Strategy();
            default:
                throw new \Exception("Unknown gateway received: $gateway");
        }
    }
}
