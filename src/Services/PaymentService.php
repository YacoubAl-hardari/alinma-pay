<?php

namespace AlinmaPay\Services;

use AlinmaPay\Contracts\PaymentGatewayInterface;
use AlinmaPay\Contracts\SignatureGeneratorInterface;
use AlinmaPay\DTOs\PaymentRequestDTO;
use AlinmaPay\DTOs\PaymentResponseDTO;
use AlinmaPay\Enums\PaymentType;
use AlinmaPay\Exceptions\PaymentFailedException;
use AlinmaPay\Traits\MakesHttpRequests;

class PaymentService implements PaymentGatewayInterface
{
    use MakesHttpRequests;

    public function __construct(
        private readonly SignatureGeneratorInterface $signatureService,
        private readonly string $endpoint,
        private readonly string $terminalId,
        private readonly string $terminalPassword,
        private readonly string $merchantKey,
    ) {}

    /**
     * Create a hosted payment page and return the payment URL.
     *
     * @param PaymentRequestDTO $request
     * @return string
     * @throws PaymentFailedException
     */
    public function createHostedPaymentPage(PaymentRequestDTO $request): string
    {
        $payload = $this->preparePayload($request);
        $response = $this->sendRequest($this->endpoint, $payload);



        // Support both 'redirectUrl' and 'paymentLink.linkUrl' as redirect URLs
        if (isset($response['redirectUrl']) && !empty($response['redirectUrl'])) {
            return $response['redirectUrl'];
        }

        if (isset($response['paymentLink']['linkUrl'])) {
            $linkUrl = $response['paymentLink']['linkUrl'];
            // Stricter validation for paymentId in the link
            $query = parse_url($linkUrl, PHP_URL_QUERY);
            $hasPaymentId = false;
            $paymentIdValue = null;
            if ($query) {
                parse_str($query, $queryParams);
                if (isset($queryParams['paymentId']) && !empty($queryParams['paymentId'])) {
                    $hasPaymentId = true;
                    $paymentIdValue = $queryParams['paymentId'];
                }
            }
            // If paymentId is missing or empty, but transactionId is present, reconstruct the link
            if (!$hasPaymentId) {
                if (isset($response['transactionId']) && !empty($response['transactionId'])) {
                    // If the link contains 'paymentId=' (even if empty), replace it correctly
                    if (preg_match('/([?&]paymentId=)$/', $linkUrl)) {
                        // Ends with ?paymentId= or &paymentId=, just append the transactionId
                        $rebuiltLink = $linkUrl . $response['transactionId'];
                    } elseif (strpos($linkUrl, 'paymentId=') !== false) {
                        // Replace existing paymentId value (even if empty)
                        $rebuiltLink = preg_replace(
                            '/([?&]paymentId=)[^&]*/',
                            '$1' . $response['transactionId'],
                            $linkUrl
                        );
                    } else {
                        // If paymentId param is missing, append it
                        $sep = (strpos($linkUrl, '?') === false) ? '?' : '&';
                        $rebuiltLink = $linkUrl . $sep . 'paymentId=' . $response['transactionId'];
                    }
                    return $rebuiltLink;
                }
                throw new PaymentFailedException('[AlinmaPay] Payment failed: Payment link is invalid or missing paymentId. Please try again later or contact support.');
            }
            return $linkUrl;
        }

        // If the gateway returns a known error structure
        if (isset($response['result']) && $response['result'] !== 'SUCCESS') {
            throw new PaymentFailedException(
                $response['message'] ?? 'Payment page creation failed',
                $response['responseCode'] ?? null
            );
        }

        throw new PaymentFailedException('[AlinmaPay] Unexpected response from payment gateway. Please try again later or contact support.');
    }

        /**
         * Process a payment and return the response DTO.
         *
         * @param PaymentRequestDTO $request
         * @return PaymentResponseDTO
         */
    public function processPayment(PaymentRequestDTO $request): PaymentResponseDTO
    {
        $payload = $this->preparePayload($request);
        $response = $this->sendRequest($this->endpoint, $payload);

        return $this->mapToResponseDTO($response);
    }

        /**
         * Query a transaction by reference ID.
         *
         * @param string $referenceId
         * @return PaymentResponseDTO
         */
    public function queryTransaction(string $referenceId): PaymentResponseDTO
    {
        $request = new PaymentRequestDTO(
            paymentType: PaymentType::TRANSACTION_INQUIRY,
            amount: 0.00,
            currency: \AlinmaPay\Enums\Currency::SAR,
            order: new \AlinmaPay\DTOs\OrderDTO(orderId: 'INQUIRY'),
            customer: new \AlinmaPay\DTOs\CustomerDTO(billingAddressCountry: 'SA'),
            referenceId: $referenceId,
            merchantIp: request()->ip() ?? '127.0.0.1',
        );

        return $this->processPayment($request);
    }

        /**
         * Capture a payment by reference ID.
         *
         * @param string $referenceId
         * @param float $amount
         * @param string $currency
         * @return PaymentResponseDTO
         */
    public function capture(string $referenceId, float $amount, string $currency): PaymentResponseDTO
    {
        $request = new PaymentRequestDTO(
            paymentType: PaymentType::CAPTURE,
            amount: $amount,
            currency: \AlinmaPay\Enums\Currency::from($currency),
            order: new \AlinmaPay\DTOs\OrderDTO(orderId: 'CAPTURE'),
            customer: new \AlinmaPay\DTOs\CustomerDTO(),
            referenceId: $referenceId,
        );

        return $this->processPayment($request);
    }

        /**
         * Refund a payment by reference ID.
         *
         * @param string $referenceId
         * @param float $amount
         * @param string $currency
         * @return PaymentResponseDTO
         */
    public function refund(string $referenceId, float $amount, string $currency): PaymentResponseDTO
    {
        $request = new PaymentRequestDTO(
            paymentType: PaymentType::REFUND,
            amount: $amount,
            currency: \AlinmaPay\Enums\Currency::from($currency),
            order: new \AlinmaPay\DTOs\OrderDTO(orderId: 'REFUND'),
            customer: new \AlinmaPay\DTOs\CustomerDTO(),
            referenceId: $referenceId,
        );

        return $this->processPayment($request);
    }

        /**
         * Void a purchase by reference ID.
         *
         * @param string $referenceId
         * @return PaymentResponseDTO
         */
    public function void(string $referenceId): PaymentResponseDTO
    {
        $request = new PaymentRequestDTO(
            paymentType: PaymentType::VOID_PURCHASE,
            amount: 0.00,
            currency: \AlinmaPay\Enums\Currency::SAR,
            order: new \AlinmaPay\DTOs\OrderDTO(orderId: 'VOID'),
            customer: new \AlinmaPay\DTOs\CustomerDTO(),
            referenceId: $referenceId,
        );

        return $this->processPayment($request);
    }

    private function preparePayload(PaymentRequestDTO $request): array
    {
        $payload = $request->toApiPayload(
            terminalId: $this->terminalId,
            password: $this->terminalPassword,
            merchantKey: $this->merchantKey
        );

        // Generate signature using trackId (orderId)
        $payload['signature'] = $this->signatureService->generateRequestSignature(
            trackId: $request->order->orderId,
            terminalId: $this->terminalId,
            password: $this->terminalPassword,
            merchantKey: $this->merchantKey,
            amount: $request->amount,
            currency: $request->currency->value
        );

        return $payload;
    }

    private function mapToResponseDTO(array $apiResponse): PaymentResponseDTO
    {
        return new PaymentResponseDTO(
            transactionId: $apiResponse['transactionId'] ?? '',
            orderId: $apiResponse['orderId'] ?? '',
            status: $apiResponse['result'] ?? 'UNKNOWN',
            responseCode: $apiResponse['responseCode'] ?? '999',
            message: $apiResponse['message'] ?? 'No message',
            amount: (float) ($apiResponse['amount'] ?? 0),
            currency: $apiResponse['currency'] ?? 'SAR',
            paymentId: $apiResponse['paymentId'] ?? null,
            signature: $apiResponse['signature'] ?? null,
            rawData: $apiResponse,
        );
    }
}