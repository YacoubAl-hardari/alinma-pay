<?php

namespace AlinmaPay\Enums;

enum Currency: string
{
    // GCC Currencies
    case SAR = 'SAR'; // Saudi Riyal
    case AED = 'AED'; // UAE Dirham
    case KWD = 'KWD'; // Kuwaiti Dinar
    case BHD = 'BHD'; // Bahraini Dinar
    case OMR = 'OMR'; // Omani Rial
    case QAR = 'QAR'; // Qatari Riyal
    
    // Major International Currencies
    case USD = 'USD'; // US Dollar
    case EUR = 'EUR'; // Euro
    case GBP = 'GBP'; // British Pound
    
    /**
     * Check if currency is supported by AlinmaPay
     */
    public function isSupported(): bool
    {
        // AlinmaPay primarily supports SAR, expand as needed
        return in_array($this, [
            self::SAR,
            self::USD,
            self::EUR,
        ], true);
    }

    /**
     * Get decimal places for currency
     */
    public function decimalPlaces(): int
    {
        return match ($this) {
            self::SAR, self::USD, self::EUR, self::GBP, 
            self::AED, self::KWD, self::BHD, self::OMR, self::QAR => 2,
            default => 2,
        };
    }

    /**
     * Format amount with currency symbol
     */
    public function format(float $amount): string
    {
        $symbol = match ($this) {
            self::SAR => 'ر.س',
            self::USD => '$',
            self::EUR => '€',
            self::GBP => '£',
            self::AED => 'د.إ',
            self::KWD => 'د.ك',
            default => $this->value,
        };

        return sprintf('%s %.2f', $symbol, $amount);
    }
}