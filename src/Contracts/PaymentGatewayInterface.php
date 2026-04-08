<?php

namespace AlinmaPay\Contracts;

use AlinmaPay\DTOs\PaymentRequestDTO;
use AlinmaPay\DTOs\PaymentResponseDTO;

interface PaymentGatewayInterface
{
    /**
     * Create a hosted payment page and return the redirect URL
     */
    public function createHostedPaymentPage(PaymentRequestDTO $request): string;

    /**
     * Process a direct payment request
     */
    public function processPayment(PaymentRequestDTO $request): PaymentResponseDTO;

    /**
     * Query transaction status by reference ID
     */
    public function queryTransaction(string $referenceId): PaymentResponseDTO;

    /**
     * Capture a pre-authorized transaction
     */
    public function capture(string $referenceId, float $amount, string $currency): PaymentResponseDTO;

    /**
     * Refund a transaction
     */
    public function refund(string $referenceId, float $amount, string $currency): PaymentResponseDTO;

    /**
     * Void a transaction
     */
    public function void(string $referenceId): PaymentResponseDTO;
}