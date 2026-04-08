<?php

namespace AlinmaPay\Exceptions;

class PaymentFailedException extends AlinmaPayException
{
    public function __construct(string $message, ?string $responseCode = null, ?\Throwable $previous = null)
    {
        $fullMessage = $responseCode 
            ? "Payment failed [Code: {$responseCode}]: {$message}" 
            : "Payment failed: {$message}";
            
        parent::__construct($fullMessage, 0, $previous);
    }
}