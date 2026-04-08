<?php

namespace AlinmaPay\Contracts;

interface WebhookHandlerInterface
{
    /**
     * Verify webhook signature for authenticity
     */
    public function verifySignature(array $payload, string $signature, string $merchantKey): bool;

    /**
     * Parse and validate webhook payload structure
     */
    public function parsePayload(array $payload): array;

    /**
     * Handle specific webhook event types
     */
    public function handleEvent(string $event, array $data): bool;

    /**
     * Get supported event types
     */
    public function getSupportedEvents(): array;

    /**
     * Retry failed webhook delivery (for outbound webhooks)
     */
    public function retryDelivery(string $webhookUrl, array $payload, int $maxRetries = 3): bool;
}