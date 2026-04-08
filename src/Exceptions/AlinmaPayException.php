<?php

namespace AlinmaPay\Exceptions;

use Exception;

class AlinmaPayException extends Exception
{
    public function __construct(string $message, int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct('[AlinmaPay] ' . $message, $code, $previous);
    }
}