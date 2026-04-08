<?php

namespace AlinmaPay\Builders;

use AlinmaPay\DTOs\CustomerDTO;
use AlinmaPay\DTOs\OrderDTO;
use AlinmaPay\DTOs\PaymentRequestDTO;
use AlinmaPay\Enums\Currency;
use AlinmaPay\Enums\PaymentInstrument;
use AlinmaPay\Enums\PaymentType;

class PaymentRequestBuilder
{
    private PaymentType $paymentType;
    private float $amount;
    private Currency $currency;
    private OrderDTO $order;
    private CustomerDTO $customer;
    
    private ?string $referenceId = null;
    private ?string $merchantIp = null;
    private ?PaymentInstrument $paymentInstrument = null;
    private ?array $tokenization = null;
    private ?array $subscriptionSchedule = null;
    private ?array $paymentToken = null;
    private ?string $receiptUrl = null;
    private array $additionalUserData = [];

    public function __construct(PaymentType $paymentType)
    {
        $this->paymentType = $paymentType;
        $this->currency = Currency::SAR; // Default
    }

    public static function forPurchase(): self
    {
        return new self(PaymentType::PURCHASE);
    }

    public static function forPreAuth(): self
    {
        return new self(PaymentType::PRE_AUTHORIZATION);
    }

    public function amount(float $amount): self
    {
        $this->amount = $amount;
        return $this;
    }

    public function currency(Currency $currency): self
    {
        $this->currency = $currency;
        return $this;
    }

    public function order(string $orderId, ?string $description = null): self
    {
        $this->order = new OrderDTO($orderId, $description);
        return $this;
    }

    public function customer(CustomerDTO $customer): self
    {
        $this->customer = $customer;
        return $this;
    }

    public function customerEmail(string $email): self
    {
        $this->customer ??= new CustomerDTO();
        $this->customer->customerEmail = $email;
        return $this;
    }

    public function billingAddress(
        ?string $street = null,
        ?string $city = null,
        ?string $state = null,
        ?string $postalCode = null,
        ?string $country = 'SA'
    ): self {
        $this->customer ??= new CustomerDTO();
        $this->customer->billingAddressStreet = $street;
        $this->customer->billingAddressCity = $city;
        $this->customer->billingAddressState = $state;
        $this->customer->billingAddressPostalCode = $postalCode;
        $this->customer->billingAddressCountry = $country;
        return $this;
    }

    public function referenceId(string $referenceId): self
    {
        $this->referenceId = $referenceId;
        return $this;
    }

    public function merchantIp(string $ip): self
    {
        $this->merchantIp = $ip;
        return $this;
    }

    public function paymentInstrument(PaymentInstrument $instrument): self
    {
        $this->paymentInstrument = $instrument;
        return $this;
    }

    public function withApplePay(array $paymentToken): self
    {
        $this->paymentInstrument = PaymentInstrument::APPLE_PAY;
        $this->paymentToken = $paymentToken;
        return $this;
    }

    public function withTokenization(string $cardToken, string $operation = 'A'): self
    {
        $this->tokenization = [
            'cardToken' => $cardToken,
            'operation' => $operation,
        ];
        return $this;
    }

    public function withRecurring(array $schedule): self
    {
        $this->subscriptionSchedule = $schedule;
        return $this;
    }

    public function receiptUrl(string $url): self
    {
        $this->receiptUrl = $url;
        return $this;
    }

    public function userData(array $data): self
    {
        $this->additionalUserData = $data;
        return $this;
    }

    public function build(): PaymentRequestDTO
    {
        if (!isset($this->amount)) {
            throw new \InvalidArgumentException('Amount is required');
        }
        if (!isset($this->order)) {
            throw new \InvalidArgumentException('Order is required');
        }
        if (!isset($this->customer)) {
            throw new \InvalidArgumentException('Customer is required');
        }

        return new PaymentRequestDTO(
            paymentType: $this->paymentType,
            amount: $this->amount,
            currency: $this->currency,
            order: $this->order,
            customer: $this->customer,
            referenceId: $this->referenceId,
            merchantIp: $this->merchantIp,
            paymentInstrument: $this->paymentInstrument,
            tokenization: $this->tokenization,
            subscriptionSchedule: $this->subscriptionSchedule,
            paymentToken: $this->paymentToken,
            receiptUrl: $this->receiptUrl,
            additionalUserData: $this->additionalUserData,
        );
    }
}