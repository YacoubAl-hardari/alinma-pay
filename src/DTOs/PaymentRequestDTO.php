<?php

namespace AlinmaPay\DTOs;

use AlinmaPay\Enums\Currency;
use AlinmaPay\Enums\PaymentInstrument;
use AlinmaPay\Enums\PaymentType;

class PaymentRequestDTO
{
    public function __construct(
        public PaymentType $paymentType,
        public float $amount,
        public Currency $currency,
        public OrderDTO $order,
        public CustomerDTO $customer,
        public ?string $referenceId = null,
        public ?string $merchantIp = null,
        public ?PaymentInstrument $paymentInstrument = null,
        public ?array $tokenization = null,
        public ?array $subscriptionSchedule = null,
        public ?array $paymentToken = null,
        public ?string $receiptUrl = null,
        public array $additionalUserData = [],
    ) {}

    public function toApiPayload(
        string $terminalId,
        string $password,
        string $merchantKey
    ): array {
        $userData = array_merge(
            $this->additionalUserData,
            $this->receiptUrl ? ['receiptUrl' => $this->receiptUrl] : []
        );

        $payload = [
            'terminalId' => $terminalId,
            'password' => $password,
            'signature' => '', // Will be set by SignatureService
            'paymentType' => $this->paymentType->value,
            'amount' => number_format($this->amount, 2, '.', ''),
            'currency' => $this->currency->value,
            'order' => $this->order->toArray(),
            'customer' => $this->customer->toArray(),
            'additionalDetails' => [
                'userData' => json_encode($userData),
            ],
        ];

        // Optional fields
        if ($this->referenceId) {
            $payload['referenceId'] = $this->referenceId;
        }
        if ($this->merchantIp) {
            $payload['merchantIp'] = $this->merchantIp;
        }
        if ($this->paymentInstrument) {
            $payload['paymentInstrument'] = [
                'paymentMethod' => $this->paymentInstrument->value,
            ];
        }
        if ($this->tokenization) {
            $payload['tokenization'] = $this->tokenization;
        }
        if ($this->subscriptionSchedule) {
            $payload['subscriptionSchedule'] = $this->subscriptionSchedule;
        }
        if ($this->paymentToken) {
            $payload['paymentToken'] = $this->paymentToken;
        }

        return $payload;
    }
}