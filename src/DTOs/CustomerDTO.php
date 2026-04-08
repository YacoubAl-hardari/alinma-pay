<?php

namespace AlinmaPay\DTOs;

class CustomerDTO
{
    public function __construct(
        public ?string $customerEmail = null,
        public ?string $cardHolderName = null,
        public ?string $billingAddressStreet = null,
        public ?string $billingAddressCity = null,
        public ?string $billingAddressState = null,
        public ?string $billingAddressPostalCode = null,
        public ?string $billingAddressCountry = 'SA',
        public ?string $customerIp = null,
    ) {}

    public function toArray(): array
    {
        return array_filter([
            'customerEmail' => $this->customerEmail,
            'cardHolderName' => $this->cardHolderName,
            'billingAddressStreet' => $this->billingAddressStreet,
            'billingAddressCity' => $this->billingAddressCity,
            'billingAddressState' => $this->billingAddressState,
            'billingAddressPostalCode' => $this->billingAddressPostalCode,
            'billingAddressCountry' => $this->billingAddressCountry,
            'customerIp' => $this->customerIp,
        ], fn($value) => $value !== null);
    }
}