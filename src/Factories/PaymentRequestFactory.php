<?php

namespace AlinmaPay\Factories;

use AlinmaPay\Builders\PaymentRequestBuilder;
use AlinmaPay\Enums\PaymentType;

class PaymentRequestFactory
{
    public static function make(PaymentType $type): PaymentRequestBuilder
    {
        return new PaymentRequestBuilder($type);
    }

    public static function purchase(): PaymentRequestBuilder
    {
        return PaymentRequestBuilder::forPurchase();
    }

    public static function preAuthorization(): PaymentRequestBuilder
    {
        return PaymentRequestBuilder::forPreAuth();
    }

    public static function refund(): PaymentRequestBuilder
    {
        return new PaymentRequestBuilder(PaymentType::REFUND);
    }

    public static function capture(): PaymentRequestBuilder
    {
        return new PaymentRequestBuilder(PaymentType::CAPTURE);
    }

    public static function tokenization(): PaymentRequestBuilder
    {
        return new PaymentRequestBuilder(PaymentType::TOKENIZATION);
    }
}