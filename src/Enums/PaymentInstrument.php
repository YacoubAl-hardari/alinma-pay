<?php

namespace AlinmaPay\Enums;

enum PaymentInstrument: string
{
    case CREDIT_CARD = 'CCI';
    case DEBIT_CARD = 'DCI';
    case APPLE_PAY = 'APPLE PAY';
    case MADA = 'MADA';
}