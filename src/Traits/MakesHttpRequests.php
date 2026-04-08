<?php

namespace AlinmaPay\Traits;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use AlinmaPay\Exceptions\AlinmaPayException;

trait MakesHttpRequests
{
    protected function sendRequest(string $endpoint, array $payload): array
    {
        try {
            $client = new Client([
                'timeout' => config('alinmapay.http_timeout', 30),
                'verify' => config('alinmapay.verify_ssl', true),
            ]);

            $response = $client->post($endpoint, [
                'json' => $payload,
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ],
            ]);

            return json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);
        } catch (GuzzleException $e) {
            throw new AlinmaPayException(
                'API request failed: ' . $e->getMessage(),
                $e->getCode(),
                $e
            );
        } catch (\JsonException $e) {
            throw new AlinmaPayException(
                'Failed to parse API response: ' . $e->getMessage(),
                0,
                $e
            );
        }
    }
}