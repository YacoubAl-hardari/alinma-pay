<?php

namespace AlinmaPay\DTOs;

class OrderDTO
{
    public function __construct(
        public string $orderId,
        public ?string $description = null,
    ) {}

    public function toArray(): array
    {
        return array_filter([
            'orderId' => $this->orderId,
            'description' => $this->description,
        ], fn($value) => $value !== null);
    }
}