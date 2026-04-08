<?php

namespace AlinmaPay\Contracts;

interface SignatureGeneratorInterface
{
    /**
     * Generate SHA-256 signature for request
     */
    public function generateRequestSignature(
        string $trackId,
        string $terminalId,
        string $password,
        string $merchantKey,
        float $amount,
        string $currency
    ): string;

    /**
     * Generate SHA-256 signature for response verification
     */
    public function generateResponseSignature(
        string $paymentId,
        string $merchantKey,
        string $responseCode,
        float $amount
    ): string;

    /**
     * Verify a signature
     */
    public function verify(string $payload, string $signature, string $merchantKey): bool;
}