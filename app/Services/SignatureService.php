<?php

namespace App\Services;

use App\Interfaces\SignatureInterface;

class SignatureService
{
    private $merchant_key = 'KaTf5tZYHx4v7pgZ';
    private $requestData;

    /**
     * @param $requestData
     */
    public function __construct($requestData)
    {
        $this->requestData = $requestData;
    }

    public function makeSign()
    {
       // $signature->make();
    }
}
