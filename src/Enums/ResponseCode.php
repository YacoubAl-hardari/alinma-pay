<?php

namespace AlinmaPay\Enums;

enum ResponseCode: string
{
    /** Transaction Successful */
    case SUCCESS = '000';
    /** Transaction Initiated */
    case INITIATED = '001';
    /** Internal Mapping for ISO not set */
    case INTERNAL_MAPPING_NOT_SET = '102';
    /** ISO message field configuration not found */
    case ISO_FIELD_CONFIG_NOT_FOUND = '103';
    /** Response Code not found in ISO message */
    case RESPONSE_CODE_NOT_FOUND = '104';
    /** Problem while creating or parsing ISO Message */
    case ISO_MESSAGE_ERROR = '105';
    /** The {attribute} has invalid length */
    case INVALID_LENGTH = '106';
    /** Terminal does not exist */
    case TERMINAL_NOT_EXIST = '201';
    /** Merchant does not exist */
    case MERCHANT_NOT_EXIST = '202';
    /** Institution does not exist */
    case INSTITUTION_NOT_EXIST = '203';
    /** Card prefix does not belong to corresponding card Brand */
    case CARD_PREFIX_INVALID = '204';
    /** Negative List, Transaction Blocked, Customer is in negative list (BIN/CARD/VPA) */
    case NEGATIVE_LIST = '205';
    /** Negative IP, Customer is not allowed to perform Transaction */
    case NEGATIVE_IP = '206';
    /** Original Transaction not found */
    case ORIGINAL_TRANSACTION_NOT_FOUND = '207';
    /** Transaction Flow not set for Transaction Type */
    case TRANSACTION_FLOW_NOT_SET = '208';
    /** Transaction Declined as Terminal status is DEACTIVE */
    case TERMINAL_DEACTIVE = '209';
    /** Transaction Declined as Terminal status is CLOSED */
    case TERMINAL_CLOSED = '210';
    /** Transaction Declined as Terminal status is INVALID */
    case TERMINAL_INVALID = '211';
    /** Transaction Declined as Merchant status is DEACTIVE */
    case MERCHANT_DEACTIVE = '212';
    /** Transaction Declined as Merchant status is CLOSED */
    case MERCHANT_CLOSED = '213';
    /** Transaction Declined as Institution status is DEACTIVE */
    case INSTITUTION_DEACTIVE = '215';
    /** MOD10 Check Failed */
    case MOD10_CHECK_FAILED = '218';
    /** CVV Check Failed as CVV value not present */
    case CVV_CHECK_FAILED = '220';
    /** AVS Capture Check Failed, as Customer Address not found */
    case AVS_CAPTURE_FAILED = '221';
    /** Customer Information check failed, as Customer Information not found */
    case CUSTOMER_INFO_FAILED = '222';
    /** Card expiry date is not greater than current date */
    case CARD_EXPIRED = '223';
    /** Invalid Login Attempts exceeded */
    case INVALID_LOGIN_ATTEMPTS = '224';
    /** Wrong Terminal password, Please Re-Initiate transaction */
    case WRONG_TERMINAL_PASSWORD = '225';
    /** Negative Country, Customer is not allowed to perform Transaction */
    case NEGATIVE_COUNTRY = '226';
    /** Original transaction was done by different terminal */
    case ORIGINAL_TERMINAL_MISMATCH = '229';
    /** Instrument Type not supported */
    case INSTRUMENT_TYPE_NOT_SUPPORTED = '230';
    /** Card Data does not belong to the Instrument Type */
    case CARD_DATA_INVALID = '234';
    /** Global Instrument Table does not contain values */
    case GLOBAL_INSTRUMENT_TABLE_MISSING = '235';
    /** Invalid Token operation */
    case INVALID_TOKEN_OPERATION = '239';
    /** Token not found in vault */
    case TOKEN_NOT_FOUND = '244';
    /** Unable to generate Token, Error occurred */
    case TOKEN_GENERATION_ERROR = '245';
    /** Instrument Type Is Invalid */
    case INSTRUMENT_TYPE_INVALID = '246';
    /** Gateway Tokenization not supported for Standalone Refund */
    case TOKENIZATION_NOT_SUPPORTED = '247';
    /** Invalid Merchant IP Address */
    case INVALID_MERCHANT_IP = '248';
    /** Capture Amount exceeds for Terminal */
    case CAPTURE_AMOUNT_EXCEEDS_TERMINAL = '258';
    /** Payment failed by risk validation */
    case RISK_VALIDATION_FAILED = '259';
    /** Transaction not allowed for given Terminal */
    case NOT_ALLOWED_FOR_TERMINAL = '301';
    /** Transaction not allowed for given Merchant */
    case NOT_ALLOWED_FOR_MERCHANT = '302';
    /** Transaction not allowed for given Institution */
    case NOT_ALLOWED_FOR_INSTITUTION = '303';
    /** Currency not supported for Terminal */
    case CURRENCY_NOT_SUPPORTED_TERMINAL = '304';
    /** Currency not supported for Merchant */
    case CURRENCY_NOT_SUPPORTED_MERCHANT = '305';
    /** Currency not supported for Institution */
    case CURRENCY_NOT_SUPPORTED_INSTITUTION = '306';
    /** Refund Limit exceeds for Terminal */
    case REFUND_LIMIT_EXCEEDS_TERMINAL = '319';
    /** Refund Limit exceeds for Merchant */
    case REFUND_LIMIT_EXCEEDS_MERCHANT = '320';
    /** Refund Limit exceeds for Institution */
    case REFUND_LIMIT_EXCEEDS_INSTITUTION = '321';
    /** Transaction failed due to maximum OTP retry limit */
    case OTP_RETRY_LIMIT = '364';
    /** Subscription id not found */
    case SUBSCRIPTION_ID_NOT_FOUND = '371';
    /** Invalid subscription id */
    case INVALID_SUBSCRIPTION_ID = '372';
    /** Destination not configured or allowed */
    case DESTINATION_NOT_ALLOWED = '401';
    /** Can't lookup Destination */
    case DESTINATION_LOOKUP_FAILED = '402';
    /** Unable to route Message */
    case ROUTE_MESSAGE_FAILED = '403';
    /** Destination not logged on */
    case DESTINATION_NOT_LOGGED_ON = '405';
    /** Failed to connect destination */
    case DESTINATION_CONNECT_FAILED = '407';
    /** Refer to card issuer */
    case REFER_TO_CARD_ISSUER = '501';
    /** Invalid merchant */
    case INVALID_MERCHANT = '503';
    /** Pick-up card */
    case PICKUP_CARD = '504';
    /** Do not honour */
    case DO_NOT_HONOUR = '505';
    /** Error */
    case ERROR = '506';
    /** Request in progress */
    case REQUEST_IN_PROGRESS = '509';
    /** Invalid transaction */
    case INVALID_TRANSACTION = '512';
    /** Invalid amount */
    case INVALID_AMOUNT = '513';
    /** Invalid card number */
    case INVALID_CARD_NUMBER = '514';
    /** Operator Canceled */
    case OPERATOR_CANCELED = '517';
    /** Customer dispute */
    case CUSTOMER_DISPUTE = '518';
    /** Re-enter transaction */
    case REENTER_TRANSACTION = '519';
    /** Invalid response */
    case INVALID_RESPONSE = '520';
    /** Suspected malfunction */
    case SUSPECTED_MALFUNCTION = '522';
    /** File update failed */
    case FILE_UPDATE_FAILED = '530';
    /** Bank not supported */
    case BANK_NOT_SUPPORTED = '531';
    /** Expired card, pick-up */
    case EXPIRED_CARD_PICKUP = '533';
    /** Suspected fraud, pick-up */
    case SUSPECTED_FRAUD_PICKUP = '534';
    /** PIN tries exceeded */
    case PIN_TRIES_EXCEEDED = '538';
    /** Lost card */
    case LOST_CARD = '541';
    /** Stolen card */
    case STOLEN_CARD = '543';
    /** Insufficient funds */
    case INSUFFICIENT_FUNDS = '551';
    /** Incorrect PIN */
    case INCORRECT_PIN = '555';
    /** Transaction not permitted to cardholder */
    case NOT_PERMITTED_TO_CARDHOLDER = '557';
    /** Transaction not permitted on terminal */
    case NOT_PERMITTED_ON_TERMINAL = '558';
    /** Exceeds withdrawal limit */
    case EXCEEDS_WITHDRAWAL_LIMIT = '561';
    /** Restricted card */
    case RESTRICTED_CARD = '562';
    /** Response received too late */
    case RESPONSE_TOO_LATE = '568';
    /** Issuer or switch inoperative */
    case ISSUER_INOPERATIVE = '591';
    /** Duplicate transaction */
    case DUPLICATE_TRANSACTION = '594';
    /** Communication System malfunction */
    case COMM_SYSTEM_MALFUNCTION = '596';
    /** Host Decline */
    case HOST_DECLINE = '599';
    /** System Error, contact System Admin */
    case SYSTEM_ERROR_ADMIN = '601';
    /** System Error, Please try again */
    case SYSTEM_ERROR_TRY_AGAIN = '602';
    /** Transaction timed out from EBS */
    case TIMEOUT_EBS = '603';
    /** Invalid Card Number */
    case INVALID_CARD_NUMBER_2 = '604';
    /** Invalid CVV */
    case INVALID_CVV = '605';
    /** Invalid Terminal Password */
    case INVALID_TERMINAL_PASSWORD = '609';
    /** Invalid Transaction Amount */
    case INVALID_TRANSACTION_AMOUNT = '612';
    /** Transaction canceled by the user */
    case CANCELED_BY_USER = '624';
    /** 3D Secure Check Failed */
    case SECURE_CHECK_FAILED = '625';
    /** Refund Amount exceeds captured amount */
    case REFUND_EXCEEDS_CAPTURED = '629';
    /** Original Transaction not found */
    case ORIGINAL_TRANSACTION_NOT_FOUND_2 = '632';
    /** Transaction already Refunded */
    case ALREADY_REFUNDED = '633';
    /** Transaction fully refunded */
    case FULLY_REFUNDED = '644';
    /** Invalid subscription type */
    case INVALID_SUBSCRIPTION_TYPE = '647';
    /** Terminal not allowed for recurring payment */
    case TERMINAL_NOT_ALLOWED_RECURRING = '653';
    /** Transaction timed out from bank */
    case TIMEOUT_BANK = '699';
    /** Transaction Action Code Invalid */
    case ACTION_CODE_INVALID = '701';
    /** Invalid OTP – Request New OTP */
    case INVALID_OTP = '715';
    /** Transaction declined */
    case DECLINED = '760';
    /** Transaction failed */
    case FAILED = '901';
    /** Processing error at Destination */
    case PROCESSING_ERROR_DEST = '902';
    /** Invalid Message */
    case INVALID_MESSAGE = '903';
    /** Invalid Checksum for request */
    case INVALID_CHECKSUM_REQUEST = '904';
    /** Invalid Checksum for response */
    case INVALID_CHECKSUM_RESPONSE = '905';
    /** Refund initiated at Destination */
    case REFUND_INITIATED_DEST = '907';
    /** Refund completed by Destination */
    case REFUND_COMPLETED_DEST = '908';
    /** Maximum amount limit exceeded */
    case MAX_AMOUNT_LIMIT_EXCEEDED = '915';
    /** Terminal not supported for Link Based API */
    case TERMINAL_NOT_SUPPORTED_LINK_API = '916';
    /** Invalid Link Id */
    case INVALID_LINK_ID = '921';
    /** Expiry Days field not valid */
    case EXPIRY_DAYS_INVALID = '928';
    /** Invalid mobile number */
    case INVALID_MOBILE_NUMBER = '931';
    /** Invalid UserMetaData */
    case INVALID_USER_METADATA = '965';
    /** Either Email or Contact Number required */
    case EMAIL_OR_CONTACT_REQUIRED = '966';
    /** Invalid Customer Mobile Number */
    case INVALID_CUSTOMER_MOBILE = '967';
}