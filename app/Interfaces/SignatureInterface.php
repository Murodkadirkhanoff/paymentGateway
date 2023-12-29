<?php

namespace App\Interfaces;

interface SignatureInterface
{
    public function make($data);

    public function getRequestParams();

    public function getModel();

    public function checkLimit();
}
