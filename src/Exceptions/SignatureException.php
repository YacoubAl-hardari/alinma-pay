<?php

namespace AlinmaPay\Exceptions;

class SignatureException extends AlinmaPayException
{
    /**
     * Create a new signature mismatch exception
     */
    public static function mismatch(string $expected, string $received): self
    {
        return new self(
            "Signature verification failed. Expected: {$expected}, Received: {$received}"
        );
    }

    /**
     * Create a new invalid signature format exception
     */
    public static function invalidFormat(string $signature): self
    {
        return new self(
            "Invalid signature format: {$signature}. Expected SHA-256 hex string (64 characters)"
        );
    }

    /**
     * Create a new missing signature exception
     */
    public static function missing(): self
    {
        return new self(
            "Signature is required but not provided"
        );
    }
}