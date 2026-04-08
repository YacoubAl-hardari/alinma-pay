<?php

namespace AlinmaPay\Services;

use AlinmaPay\Contracts\ResponseHandlerInterface;
use AlinmaPay\Exceptions\DecryptionException;

class ResponseEncryptionService implements ResponseHandlerInterface
{
    public function __construct(
        private readonly string $algorithm = 'AES-128-ECB',
        private readonly string $encoding = 'base64'
    ) {}

    public function decryptResponse(string $encryptedData, string $merchantKey): array
    {
        try {
            // Convert hex merchant key to bytes
            $keyBytes = hex2bin($merchantKey);
            if ($keyBytes === false) {
                throw new DecryptionException('Invalid merchant key format');
            }

            // Decode base64 encrypted data
            $decodedData = base64_decode($encryptedData, true);
            if ($decodedData === false) {
                throw new DecryptionException('Invalid base64 encoded data');
            }

            // Decrypt using OpenSSL
            $decrypted = openssl_decrypt(
                $decodedData,
                $this->algorithm,
                $keyBytes,
                OPENSSL_RAW_DATA
            );

            if ($decrypted === false) {
                throw new DecryptionException('Decryption failed');
            }

            // Parse JSON response
            $result = json_decode($decrypted, true, 512, JSON_THROW_ON_ERROR);
            
            if (!is_array($result)) {
                throw new DecryptionException('Decrypted data is not valid JSON');
            }

            return $result;
        } catch (\JsonException $e) {
            throw new DecryptionException('Failed to parse decrypted JSON: ' . $e->getMessage(), 0, $e);
        } catch (\Exception $e) {
            throw new DecryptionException('Decryption error: ' . $e->getMessage(), 0, $e);
        }
    }

    public function parseResponse(array $responseData): array
    {
        // Validate required fields
        $required = ['transactionId', 'responseCode', 'result'];
        foreach ($required as $field) {
            if (!isset($responseData[$field])) {
                throw new \InvalidArgumentException("Missing required field: {$field}");
            }
        }

        return $responseData;
    }

    public function handleWebhook(array $payload): bool
    {
        // Verify webhook signature if configured
        // Process the webhook event
        // Return true if handled successfully
        
        return isset($payload['event'], $payload['transactionId']);
    }
}