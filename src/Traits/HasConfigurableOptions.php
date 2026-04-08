<?php

namespace AlinmaPay\Traits;

use AlinmaPay\Enums\Currency;
use AlinmaPay\Enums\PaymentType;

trait HasConfigurableOptions
{
    /**
     * Configuration options with defaults
     */
    protected array $options = [
        'timeout' => 30,
        'verify_ssl' => true,
        'log_requests' => false,
        'log_level' => 'info',
        'retry_attempts' => 3,
        'retry_delay' => 1000, // milliseconds
        'signature_algorithm' => 'sha256',
        'encryption_algorithm' => 'AES-128-ECB',
        'default_currency' => 'SAR',
        'default_payment_type' => '1', // Purchase
        'webhook_verify_signature' => true,
        'webhook_timeout' => 15,
    ];

    /**
     * Set a single configuration option
     */
    public function setOption(string $key, mixed $value): static
    {
        if (array_key_exists($key, $this->options)) {
            $this->options[$key] = $value;
        }
        
        return $this;
    }

    /**
     * Set multiple configuration options
     */
    public function setOptions(array $options): static
    {
        foreach ($options as $key => $value) {
            $this->setOption($key, $value);
        }
        
        return $this;
    }

    /**
     * Get a configuration option with optional default
     */
    public function getOption(string $key, mixed $default = null): mixed
    {
        return $this->options[$key] ?? $default;
    }

    /**
     * Get all configuration options
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * Check if an option is set
     */
    public function hasOption(string $key): bool
    {
        return array_key_exists($key, $this->options);
    }

    /**
     * Reset option to default
     */
    public function resetOption(string $key): static
    {
        $defaults = $this->getDefaultOptions();
        
        if (array_key_exists($key, $defaults)) {
            $this->options[$key] = $defaults[$key];
        }
        
        return $this;
    }

    /**
     * Reset all options to defaults
     */
    public function resetOptions(): static
    {
        $this->options = $this->getDefaultOptions();
        return $this;
    }

    /**
     * Get default configuration options
     */
    protected function getDefaultOptions(): array
    {
        return [
            'timeout' => 30,
            'verify_ssl' => true,
            'log_requests' => false,
            'log_level' => 'info',
            'retry_attempts' => 3,
            'retry_delay' => 1000,
            'signature_algorithm' => 'sha256',
            'encryption_algorithm' => 'AES-128-ECB',
            'default_currency' => Currency::SAR->value,
            'default_payment_type' => PaymentType::PURCHASE->value,
            'webhook_verify_signature' => true,
            'webhook_timeout' => 15,
        ];
    }

    /**
     * Validate configuration options
     */
    protected function validateOptions(): void
    {
        // Validate timeout
        if (!is_numeric($this->options['timeout']) || $this->options['timeout'] < 1) {
            throw new \InvalidArgumentException('Timeout must be a positive number');
        }

        // Validate retry attempts
        if (!is_int($this->options['retry_attempts']) || $this->options['retry_attempts'] < 0) {
            throw new \InvalidArgumentException('Retry attempts must be a non-negative integer');
        }

        // Validate signature algorithm
        $supportedAlgorithms = hash_algos();
        if (!in_array($this->options['signature_algorithm'], $supportedAlgorithms, true)) {
            throw new \InvalidArgumentException(
                "Unsupported signature algorithm: {$this->options['signature_algorithm']}"
            );
        }

        // Validate currency
        if (!Currency::tryFrom($this->options['default_currency'])) {
            throw new \InvalidArgumentException(
                "Invalid currency code: {$this->options['default_currency']}"
            );
        }
    }

    /**
     * Merge with Laravel config
     */
    protected function mergeWithConfig(array $config): static
    {
        $mappable = [
            'http_timeout' => 'timeout',
            'verify_ssl' => 'verify_ssl',
            'log_enabled' => 'log_requests',
            'log_level' => 'log_level',
            'max_retries' => 'retry_attempts',
            'retry_delay_ms' => 'retry_delay',
            'webhook.verify_signature' => 'webhook_verify_signature',
            'webhook.timeout' => 'webhook_timeout',
        ];

        foreach ($mappable as $configKey => $optionKey) {
            $value = data_get($config, $configKey);
            if ($value !== null) {
                $this->setOption($optionKey, $value);
            }
        }

        return $this;
    }
}