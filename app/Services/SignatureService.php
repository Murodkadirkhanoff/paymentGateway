<?php

namespace App\Services;

use App\Interfaces\SignatureInterface;

class SignatureService
{
    private array $requestData;

    /**
     * @param $requestData
     */
    public function __construct($requestData)
    {
        $this->requestData = $requestData;
    }

    public function makeSign(SignatureInterface $signature)
    {
        return $signature->make($this->requestData);
    }
}
