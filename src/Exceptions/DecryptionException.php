<?php

namespace AlinmaPay\Exceptions;

class DecryptionException extends AlinmaPayException
{
    /**
     * Create a new invalid key exception
     */
    public static function invalidKey(string $key): self
    {
        return new self(
            "Invalid merchant key format: {$key}. Key must be a valid hex-encoded string"
        );
    }

    /**
     * Create a new decryption failed exception
     */
    public static function failed(string $reason = 'Unknown error'): self
    {
        return new self(
            "Response decryption failed: {$reason}"
        );
    }

    /**
     * Create a new invalid encrypted data exception
     */
    public static function invalidData(string $data): self
    {
        return new self(
            "Invalid encrypted data format. Expected base64-encoded AES ciphertext"
        );
    }

    /**
     * Create a new JSON parse exception
     */
    public static function invalidJson(string $decrypted): self
    {
        return new self(
            "Decrypted response is not valid JSON: " . substr($decrypted, 0, 100) . '...'
        );
    }
}