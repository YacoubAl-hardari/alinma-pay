 <?php

    namespace AlinmaPay\DTOs;

    class PaymentResponseDTO
    {

        /**
         * Returns the full raw response data array for the payment.
         */
        public function getResultData(): array
        {
            return $this->rawData;
        }


        public function __construct(
            public string $transactionId,
            public string $orderId,
            public string $status,
            public string $responseCode,
            public string $message,
            public float $amount,
            public string $currency,
            public ?string $paymentId = null,
            public ?string $signature = null,
            public array $rawData = [],
        ) {}

        /**
         * Returns true if the payment is successful.
         */
        public function isSuccess(): bool
        {
            $data = $this->rawData ?: [
                'result' => $this->status,
                'status' => $this->status,
                'responseCode' => $this->responseCode,
            ];
            return (
                (isset($data['result']) && $data['result'] === 'SUCCESS') ||
                (isset($data['status']) && $data['status'] === 'SUCCESS') ||
                (isset($data['responseCode']) && $data['responseCode'] === '000')
            );
        }

        /**
         * Returns true if the payment failed.
         */
        public function isFailed(): bool
        {
            $data = $this->rawData ?: [
                'result' => $this->status,
                'status' => $this->status,
                'responseCode' => $this->responseCode,
            ];
            return (
                (isset($data['result']) && $data['result'] === 'FAILED') ||
                (isset($data['status']) && $data['status'] === 'FAILED') ||
                (isset($data['responseCode']) && in_array($data['responseCode'], ['901', '760', '599', '601', '902']))
            );
        }

        /**
         * Returns true if the payment was cancelled by user or system.
         */
        public function isCancelled(): bool
        {
            $data = $this->rawData ?: [
                'result' => $this->status,
                'status' => $this->status,
                'responseCode' => $this->responseCode,
            ];
            return (
                (isset($data['result']) && stripos($data['result'], 'CANCEL') !== false) ||
                (isset($data['status']) && stripos($data['status'], 'CANCEL') !== false) ||
                (isset($data['responseCode']) && in_array($data['responseCode'], ['624', '517']))
            );
        }

        /**
         * Returns true if the payment was refunded.
         */
        public function isRefunded(): bool
        {
            $data = $this->rawData ?: [
                'result' => $this->status,
                'status' => $this->status,
                'responseCode' => $this->responseCode,
            ];
            return (
                (isset($data['result']) && stripos($data['result'], 'REFUND') !== false) ||
                (isset($data['status']) && stripos($data['status'], 'REFUND') !== false) ||
                (isset($data['responseCode']) && in_array($data['responseCode'], ['644', '633', '907', '908']))
            );
        }
    }
