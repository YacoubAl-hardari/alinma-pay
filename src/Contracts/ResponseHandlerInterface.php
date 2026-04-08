<?php

namespace AlinmaPay\Contracts;

interface ResponseHandlerInterface
{
    /**
     * Decrypt the encrypted response from AlinmaPay
     */
    public function decryptResponse(string $encryptedData, string $merchantKey): array;

    /**
     * Parse and validate the response
     */
    public function parseResponse(array $responseData): array;

    /**
     * Handle webhook payload
     */
    public function handleWebhook(array $payload): bool;
}