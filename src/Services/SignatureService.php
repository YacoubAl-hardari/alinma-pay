<?php

namespace AlinmaPay\Services;

use AlinmaPay\Contracts\SignatureGeneratorInterface;

class SignatureService implements SignatureGeneratorInterface
{
    public function generateRequestSignature(
        string $trackId,
        string $terminalId,
        string $password,
        string $merchantKey,
        float $amount,
        string $currency
    ): string {
        $plainText = implode('|', [
            $trackId,
            $terminalId,
            $password,
            $merchantKey,
            number_format($amount, 2, '.', ''),
            $currency,
        ]);

        return $this->hash($plainText, $merchantKey);
    }

    public function generateResponseSignature(
        string $paymentId,
        string $merchantKey,
        string $responseCode,
        float $amount
    ): string {
        $plainText = implode('|', [
            $paymentId,
            $merchantKey,
            $responseCode,
            number_format($amount, 2, '.', ''),
        ]);

        return $this->hash($plainText, $merchantKey);
    }

    public function verify(string $payload, string $signature, string $merchantKey): bool
    {
        $expectedSignature = hash('sha256', $payload . $merchantKey);
        return hash_equals($expectedSignature, $signature);
    }

    private function hash(string $data, string $merchantKey): string
    {
        // According to docs: SHA-256 of pipe-separated string
        // Note: merchantKey is NOT appended for request signature per docs
        return hash('sha256', $data);
    }
}