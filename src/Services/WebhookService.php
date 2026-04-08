<?php

namespace AlinmaPay\Services;

use AlinmaPay\Contracts\SignatureGeneratorInterface; 
use AlinmaPay\Contracts\WebhookHandlerInterface;
use Illuminate\Support\Facades\Log;
use Psr\Log\LoggerInterface;
use RuntimeException;

class WebhookService implements WebhookHandlerInterface
{
    /**
     * Supported webhook events from AlinmaPay
     */
    private const SUPPORTED_EVENTS = [
        'Transaction.Success',
        'Transaction.Failed',
        'Transaction.Pending',
        'Transaction.Refunded',
        'Transaction.Voided',
        'Subscription.Created',
        'Subscription.Cancelled',
        'Tokenization.Added',
        'Tokenization.Updated',
        'Tokenization.Deleted',
    ];

    /**
     * Constructor
     */
    public function __construct(
        private readonly SignatureGeneratorInterface $signatureService,
        private readonly ?LoggerInterface $logger = null
    ) {}

    /**
     * Verify webhook signature for authenticity
     * 
     * Per AlinmaPay docs: signature = SHA256(paymentId|merchantKey|responseCode|amount)
     */
    public function verifySignature(array $payload, string $signature, string $merchantKey): bool
    {
        // Extract required fields for signature verification
        $paymentId = $payload['transactionId'] ?? $payload['paymentId'] ?? '';
        $responseCode = $payload['responseCode'] ?? '';
        $amount = isset($payload['amount']) ? number_format((float) $payload['amount'], 2, '.', '') : '0.00';

        // Build signature string per AlinmaPay specification
        $plainText = implode('|', [
            $paymentId,
            $merchantKey,
            $responseCode,
            $amount,
        ]);

        // ✅ Use injected signature service to generate expected signature
        $expectedSignature = $this->signatureService->generateResponseSignature(
            paymentId: $paymentId,
            merchantKey: $merchantKey,
            responseCode: $responseCode,
            amount: (float) $amount
        );

        // Constant-time comparison to prevent timing attacks
        if (!hash_equals($expectedSignature, $signature)) {
            $this->logger?->warning('Webhook signature verification failed', [
                'expected' => $expectedSignature,
                'received' => $signature,
                'payment_id' => $paymentId,
            ]);
            return false;
        }

        $this->logger?->info('Webhook signature verified successfully', [
            'payment_id' => $paymentId,
        ]);
        
        return true;
    }

    /**
     * Parse and validate webhook payload structure
     */
    public function parsePayload(array $payload): array
    {
        $required = ['event', 'transactionId', 'orderId', 'result', 'responseCode'];
        
        foreach ($required as $field) {
            if (!isset($payload[$field])) {
                throw new \InvalidArgumentException(
                    "Webhook payload missing required field: {$field}"
                );
            }
        }

        // Parse userData JSON if present and is string
        if (isset($payload['userData']) && is_string($payload['userData'])) {
            try {
                $payload['userData'] = json_decode($payload['userData'], true, 512, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                $this->logger?->warning('Failed to parse userData JSON', ['error' => $e->getMessage()]);
                $payload['userData'] = [];
            }
        }

        // Normalize amount to float for consistent handling
        if (isset($payload['amount']) && is_numeric($payload['amount'])) {
            $payload['amount'] = (float) $payload['amount'];
        }

        return $payload;
    }

    /**
     * Handle specific webhook event types
     */
    public function handleEvent(string $event, array $data): bool
    {
        if (!in_array($event, self::SUPPORTED_EVENTS, true)) {
            $this->logger?->warning('Unhandled webhook event', ['event' => $event]);
            return false;
        }

        $this->logger?->info("Processing webhook event: {$event}", [
            'transaction_id' => $data['transactionId'] ?? null,
            'order_id' => $data['orderId'] ?? null,
        ]);

        // Dispatch to specific handler method using dynamic method name
        $handlerMethod = 'handle' . $this->camelizeEventName($event);
        
        if (method_exists($this, $handlerMethod)) {
            return $this->{$handlerMethod}($data);
        }

        // Fallback to default handler
        return $this->handleDefault($event, $data);
    }

    /**
     * Handle Transaction.Success event
     */
    private function handleTransactionSuccess(array $data): bool
    {
        $this->logger?->info('Transaction successful', [
            'transaction_id' => $data['transactionId'],
            'order_id' => $data['orderId'],
            'amount' => $data['amount'],
            'currency' => $data['currency'] ?? 'SAR',
            'payment_method' => $data['paymentMethod'] ?? null,
        ]);
        
        // 🎯 Hook point: Update your order status here
        // Example:
        // Order::where('id', $data['orderId'])->update([
        //     'status' => 'paid',
        //     'transaction_id' => $data['transactionId'],
        //     'paid_at' => now(),
        // ]);
        
        return true;
    }

    /**
     * Handle Transaction.Failed event
     */
    private function handleTransactionFailed(array $data): bool
    {
        $this->logger?->warning('Transaction failed', [
            'transaction_id' => $data['transactionId'],
            'order_id' => $data['orderId'],
            'response_code' => $data['responseCode'],
            'reason' => $data['message'] ?? 'Unknown',
        ]);
        
        // 🎯 Hook point: Handle failed payment
        // Example:
        // Order::where('id', $data['orderId'])->update(['status' => 'failed']);
        // Send notification to customer...
        
        return true;
    }

    /**
     * Handle Transaction.Refunded event
     */
    private function handleTransactionRefunded(array $data): bool
    {
        $this->logger?->info('Transaction refunded', [
            'transaction_id' => $data['transactionId'],
            'order_id' => $data['orderId'],
            'refund_amount' => $data['amount'],
        ]);
        
        // 🎯 Hook point: Update order as refunded
        // Order::where('id', $data['orderId'])->update(['status' => 'refunded']);
        
        return true;
    }

    /**
     * Handle Tokenization.Added event
     */
    private function handleTokenizationAdded(array $data): bool
    {
        $this->logger?->info('Card tokenized successfully', [
            'transaction_id' => $data['transactionId'],
            'masked_card' => $data['maskedCard'] ?? null,
            'card_brand' => $data['cardBrand'] ?? null,
            'customer_email' => $data['customerEmail'] ?? null,
        ]);
        
        // 🎯 Hook point: Save token for future payments
        // CustomerPaymentMethod::create([
        //     'customer_id' => $userId,
        //     'token' => $data['paymentId'], // or extract from userData
        //     'masked_card' => $data['maskedCard'],
        //     'brand' => $data['cardBrand'],
        // ]);
        
        return true;
    }

    /**
     * Default event handler for unhandled events
     */
    private function handleDefault(string $event, array $data): bool
    {
        $this->logger?->debug("Default handling for event: {$event}", $data);
        return true;
    }

    /**
     * Get supported event types
     */
    public function getSupportedEvents(): array
    {
        return self::SUPPORTED_EVENTS;
    }

    /**
     * Retry failed webhook delivery (for outbound webhooks from merchant)
     */
    public function retryDelivery(string $webhookUrl, array $payload, int $maxRetries = 3): bool
    {
        $attempt = 0;
        $lastError = null;

        while ($attempt < $maxRetries) {
            try {
                $response = $this->sendWebhookRequest($webhookUrl, $payload);
                
                if ($response['status'] >= 200 && $response['status'] < 300) {
                    $this->logger?->info('Webhook delivered successfully', [
                        'url' => $webhookUrl,
                        'attempt' => $attempt + 1,
                    ]);
                    return true;
                }
                
                $lastError = "HTTP {$response['status']}";
            } catch (RuntimeException $e) {
                $lastError = $e->getMessage();
            }

            $attempt++;
            
            // Exponential backoff: 1s, 2s, 4s
            if ($attempt < $maxRetries) {
                usleep(pow(2, $attempt - 1) * 1000000); // Convert to microseconds
            }
        }

        $this->logger?->error('Webhook delivery failed after retries', [
            'url' => $webhookUrl,
            'attempts' => $attempt,
            'last_error' => $lastError,
        ]);

        return false;
    }

    /**
     * Send webhook HTTP request using cURL
     */
    private function sendWebhookRequest(string $url, array $payload): array
    {
        $ch = curl_init($url);
        
        if ($ch === false) {
            throw new RuntimeException('Failed to initialize cURL');
        }
        
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($payload, JSON_THROW_ON_ERROR),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Accept: application/json',
                'User-Agent: AlinmaPay-Laravel-Webhook/1.0',
                'X-Webhook-Source: AlinmaPay-PG',
            ],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => config('alinmapay.webhook.timeout', 15),
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
        ]);

        $response = curl_exec($ch);
        
        if ($response === false) {
            $error = curl_error($ch);
            curl_close($ch);
            throw new RuntimeException("cURL error: {$error}");
        }
        
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return [
            'status' => $status,
            'body' => $response,
        ];
    }

    /**
     * Convert event name to camelCase method name
     * Transaction.Success => TransactionSuccess
     */
    private function camelizeEventName(string $event): string
    {
        $parts = explode('.', $event);
        return implode('', array_map('ucfirst', $parts));
    }

    /**
     * Create webhook response DTO for consistent handling
     */
    public function createWebhookResponse(array $payload): array
    {
        return [
            'event' => $payload['event'] ?? null,
            'transaction_id' => $payload['transactionId'] ?? null,
            'order_id' => $payload['orderId'] ?? null,
            'status' => $payload['result'] ?? null,
            'response_code' => $payload['responseCode'] ?? null,
            'amount' => isset($payload['amount']) ? (float) $payload['amount'] : null,
            'currency' => $payload['currency'] ?? 'SAR',
            'payment_method' => $payload['paymentMethod'] ?? null,
            'card_brand' => $payload['cardBrand'] ?? null,
            'masked_card' => $payload['maskedCard'] ?? null,
            'timestamp' => $payload['transactionDateTime'] ?? null,
            'signature' => $payload['signature'] ?? null,
            'user_data' => is_array($payload['userData']) ? $payload['userData'] : [],
        ];
    }
}