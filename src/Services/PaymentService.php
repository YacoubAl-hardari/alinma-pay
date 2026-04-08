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

    public function createHostedPaymentPage(PaymentRequestDTO $request): string
    {
        $payload = $this->preparePayload($request);
        $response = $this->sendRequest($this->endpoint, $payload);

        if (isset($response['redirectUrl'])) {
            return $response['redirectUrl'];
        }

        if (isset($response['result']) && $response['result'] !== 'SUCCESS') {
            throw new PaymentFailedException(
                $response['message'] ?? 'Payment page creation failed',
                $response['responseCode'] ?? null
            );
        }

        throw new PaymentFailedException('Unexpected response from payment gateway');
    }

    public function processPayment(PaymentRequestDTO $request): PaymentResponseDTO
    {
        $payload = $this->preparePayload($request);
        $response = $this->sendRequest($this->endpoint, $payload);

        return $this->mapToResponseDTO($response);
    }

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