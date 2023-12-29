<?php

namespace App\Strategies\Signature;

use App\Interfaces\SignatureInterface;

class BaseGatewayStrategy
{
    protected function implode($data, $separator, $item): string
    {
        ksort($data);
        $values = array_values($data);

        $stringToHash = implode("$separator", $values);
        return $stringToHash . "$separator$item";
    }
}
