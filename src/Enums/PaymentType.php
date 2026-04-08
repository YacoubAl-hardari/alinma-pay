<?php

namespace AlinmaPay\Enums;

enum PaymentType: string
{
    case PURCHASE = '1';
    case REFUND = '2';
    case VOID_PURCHASE = '3';
    case PRE_AUTHORIZATION = '4';
    case CAPTURE = '5';
    case VOID_REFUND = '6';
    case VOID_CAPTURE = '7';
    case VOID_PRE_AUTH = '9';
    case TRANSACTION_INQUIRY = '10';
    case TOKENIZATION = '12';
    case STANDALONE_REFUND = '14';
    case PAYMENT_LINK = '15';
}