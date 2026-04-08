# AlinmaPay Laravel Package

Professional, SOLID-compliant integration for Alinma Pay Payment Gateway in Laravel.

---

## Features

- Full integration with Hosted Payment Page
- SHA-256 signature generation & verification
- AES response decryption
- Webhook support with signature verification
- Tokenization & Recurring payments support
- OOP with Design Patterns (Builder, Factory, Strategy)
- Strict SOLID principles
- Type-safe with Enums & DTOs

---

## Installation

```bash
composer require alinmapay/alinmapay
php artisan vendor:publish --provider="AlinmaPay\\AlinmaPayServiceProvider"
```

---

## .env Example

Add the following to your Laravel .env file:

```env
# AlinmaPay Environment: sandbox or production
ALINMAPAY_ENV=sandbox

# Your merchant key (hex string from AlinmaPay dashboard)
ALINMAPAY_MERCHANT_KEY=your_merchant_key_hex

# Your terminal ID and password (from AlinmaPay dashboard)
ALINMAPAY_TERMINAL_ID=your_terminal_id
ALINMAPAY_TERMINAL_PASSWORD=your_terminal_password

# (Optional) Webhook timeout in seconds
ALINMAPAY_WEBHOOK_TIMEOUT=30
```

---

## Quick Example (Get Hosted Payment URL Only)

```php
use AlinmaPay\Facades\AlinmaPay;
use AlinmaPay\Factories\PaymentRequestFactory;

$paymentUrl = AlinmaPay::createHostedPaymentPage(
    PaymentRequestFactory::purchase()
        ->amount(99.99)
        ->order('ORD_123', 'Product Purchase')
        ->customerEmail('user@example.com')
        ->billingAddress(country: 'SA')
        ->receiptUrl(route('payment.callback'))
        ->build()
);

return redirect()->away($paymentUrl);
```

---

## Advanced Usage

### 1. Create Hosted Payment Page (Controller Example)

```php
public function createPaymentPage(Request $request)
{
    $validated = $request->validate([
        'order_id' => 'required|string|unique:orders,id',
        'amount' => 'required|numeric|min:0.01',
        'email' => 'required|email',
        'description' => 'nullable|string|max:255',
    ]);

    $paymentRequest = PaymentRequestFactory::purchase()
        ->amount($validated['amount'])
        ->order($validated['order_id'], $validated['description'] ?? 'Purchase')
        ->customerEmail($validated['email'])
        ->billingAddress(city: 'Riyadh', country: 'SA')
        ->receiptUrl(route('payment.callback'))
        ->userData([
            'user_id' => auth()->id(),
            'order_type' => 'online',
        ])
        ->build();

    $paymentUrl = AlinmaPay::createHostedPaymentPage($paymentRequest);
    return redirect()->away($paymentUrl);
}
```

### 2. Handle Payment Callback (Receipt URL) and Check Payment Status

```php
use AlinmaPay\DTOs\PaymentResponseDTO;

public function handle(Request $request)
{
    $encryptedData = $request->input('data');
    $merchantKey = config('alinmapay.merchant_key');
    $decrypted = app(ResponseEncryptionService::class)->decryptResponse($encryptedData, $merchantKey);
    $response = new PaymentResponseDTO(
        $decrypted['transactionId'] ?? '',
        $decrypted['orderId'] ?? '',
        $decrypted['status'] ?? ($decrypted['result'] ?? ''),
        $decrypted['responseCode'] ?? '',
        $decrypted['message'] ?? '',
        isset($decrypted['amount']) ? (float)$decrypted['amount'] : 0.0,
        $decrypted['currency'] ?? '',
        $decrypted['paymentId'] ?? null,
        $decrypted['signature'] ?? null,
        $decrypted // rawData
    );
    // To get the full raw response data (all fields returned by the gateway):
    $allResponseData = $response->getResultData();
    // Example: Log::info('Full payment response', $allResponseData);


    if ($response->isSuccess()) {
        // Payment completed successfully
        // Example: update order status in your database
        // Order::where('id', $response->orderId)->update(['status' => 'paid']);
    } elseif ($response->isFailed()) {
        // Payment failed
        $errorMessage = $response->message ?? $response->responseCode ?? 'Payment failed';
        // Example: log or show error
        // Log::error('Payment failed', ['error' => $errorMessage]);
    } elseif ($response->isCancelled()) {
        // Payment was cancelled by the user or system
        // Example: update order status or notify user
        // Order::where('id', $response->orderId)->update(['status' => 'cancelled']);
    } elseif ($response->isRefunded()) {
        // Payment was refunded
        // Example: update order status or notify user
        // Order::where('id', $response->orderId)->update(['status' => 'refunded']);
    } else {
        // Unknown status, handle as needed
        // Log::warning('Unknown payment status', $decrypted);
    }


}

```

### 3. Webhook Handling

```php
public function handle(Request $request)
{
    $payload = $request->all();
    // Verify signature if needed
    app(WebhookService::class)->handleWebhook($payload);
    return response()->json(['status' => 'received'], 200);
}
```

### 4. Recurring Payments

```php
$paymentRequest = PaymentRequestFactory::purchase()
    ->amount(100.00)
    ->order('SUB_12345', 'Monthly Subscription')
    ->customerEmail('customer@example.com')
    ->billingAddress(country: 'SA')
    ->withRecurring([
        'frequency' => 'M',
        'startDate' => '01/02/2026',
        'subscriptionAmount' => '100.00',
        'subscriptionType' => 's',
        'paymentDays' => '01',
        'paymentType' => 'R',
        'noOfSubscriptionPayments' => '12',
    ])
    ->build();
$paymentUrl = AlinmaPay::createHostedPaymentPage($paymentRequest);
```

### 5. Tokenization

```php
$tokenRequest = PaymentRequestFactory::tokenization()
    ->amount(1.00)
    ->order('TOKEN_' . uniqid(), 'Card Tokenization')
    ->customerEmail('customer@example.com')
    ->billingAddress(country: 'SA')
    ->withTokenization(cardToken: null, operation: 'A')
    ->receiptUrl(route('tokenization.callback'))
    ->build();
$paymentUrl = AlinmaPay::createHostedPaymentPage($tokenRequest);
```

---

## Technical Notes

- All requests are POST to the official endpoint.
- Signature is generated using SHA-256 as per documentation.
- Final response is AES-encrypted and must be decrypted using the merchant key.
- All code is OOP and SOLID-compliant.
- All errors are handled via clear Exceptions.

### Signature Verification

```php
$signature = app(SignatureService::class)->generateRequestSignature(...);
```

### Decrypting Response

```php
$decrypted = app(ResponseEncryptionService::class)->decryptResponse($encryptedData, $merchantKey);
```

---

## Support

For technical inquiries: yacoub@yacoubalhaidari.com
