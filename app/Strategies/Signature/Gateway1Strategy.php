<?php

namespace App\Strategies\Signature;

use App\Interfaces\SignatureInterface;

class Gateway1Strategy implements SignatureInterface
{

    private string $merchant_key;

    /**
     * @param string $merchant_key
     */
    public function __construct(string $merchant_key)
    {
        $this->merchant_key = '$merchant_key';
    }

    public function make($data)
    {
        // Sort the array by keys
        ksort($data);

        // Extract values from the array
        $values = array_values($data);

        // Concatenate values with ':'
        $stringToHash = implode(':', $values);
        $stringToHash = $stringToHash . ":$this->merchant_key";
        // Calculate SHA256 hash
        $result = hash('sha256', $stringToHash);

        return $result;
    }
}
