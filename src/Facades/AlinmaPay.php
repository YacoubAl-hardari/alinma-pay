<?php

namespace AlinmaPay\Facades;

use AlinmaPay\Builders\PaymentRequestBuilder;
use AlinmaPay\DTOs\PaymentRequestDTO;
use AlinmaPay\DTOs\PaymentResponseDTO;
use Illuminate\Support\Facades\Facade;

/**
 * @method static string createHostedPaymentPage(PaymentRequestDTO $request)
 * @method static PaymentResponseDTO processPayment(PaymentRequestDTO $request)
 * @method static PaymentResponseDTO queryTransaction(string $referenceId)
 * @method static PaymentResponseDTO capture(string $referenceId, float $amount, string $currency)
 * @method static PaymentResponseDTO refund(string $referenceId, float $amount, string $currency)
 * @method static PaymentResponseDTO void(string $referenceId)
 * @method static PaymentRequestBuilder builder()
 * 
 * @see \AlinmaPay\Services\PaymentService
 */
class AlinmaPay extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \AlinmaPay\Contracts\PaymentGatewayInterface::class;
    }

    /**
     * Get a new payment request builder instance
     */
    public static function builder(): PaymentRequestBuilder
    {
        return new \AlinmaPay\Builders\PaymentRequestBuilder(
            \AlinmaPay\Enums\PaymentType::PURCHASE
        );
    }
}