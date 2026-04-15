<?php

namespace App\Services\Bills\Providers;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Arr;

class BudPayBillPaymentProvider extends AbstractBillPaymentProvider
{
    public function key(): string
    {
        return 'budpay';
    }

    public function supports(string $service): bool
    {
        return in_array($service, ['airtime', 'data', 'tv', 'electricity'], true);
    }

    public function isConfigured(array $settings): bool
    {
        return parent::isConfigured($settings) && filled($settings['public_key'] ?? null);
    }

    protected function client(array $settings): PendingRequest
    {
        $client = parent::client($settings);
        $publicKey = (string) ($settings['public_key'] ?? '');

        if ($publicKey !== '') {
            $client = $client->withHeaders([
                'Encryption' => hash_hmac('sha512', '{}', $publicKey),
            ]);
        }

        return $client;
    }

    protected function signedClient(array $settings, array $payload): PendingRequest
    {
        $json = json_encode($this->sortRecursive($payload), JSON_UNESCAPED_SLASHES);
        $publicKey = (string) ($settings['public_key'] ?? '');

        return parent::client($settings)->withHeaders([
            'Encryption' => $publicKey !== '' ? hash_hmac('sha512', $json ?: '{}', $publicKey) : '',
        ]);
    }

    public function listAirtimeProviders(array $settings): array
    {
        $response = $this->requestWithSslFallback(fn ($client) => $client->get('airtime'), $settings);
        $body = $this->ensureSuccess($response, $this->responseData($response), 'Unable to load BudPay airtime providers.');

        return array_values(array_map(fn ($item) => [
            'provider' => strtolower((string) (data_get($item, 'code') ?: data_get($item, 'provider') ?: data_get($item, 'name'))),
            'name' => (string) (data_get($item, 'name') ?: data_get($item, 'provider_name') ?: data_get($item, 'provider')),
            'symbol' => strtolower((string) (data_get($item, 'code') ?: data_get($item, 'provider') ?: data_get($item, 'name'))),
            'image' => (string) data_get($item, 'logo'),
            'min' => (float) (data_get($item, 'minimum_amount') ?: 50),
            'max' => (float) (data_get($item, 'maximum_amount') ?: 500000),
        ], Arr::wrap(data_get($body, 'data', []))));
    }

    public function purchaseAirtime(array $payload, array $settings): array
    {
        $requestPayload = [
            'provider' => strtoupper((string) $payload['network']),
            'number' => (string) $payload['phone'],
            'amount' => (float) $payload['amount'],
        ];

        $response = $this->signedClient($settings, $requestPayload)->post('airtime', $requestPayload);
        $body = $this->ensureSuccess($response, $this->responseData($response), 'BudPay airtime purchase failed.');

        return $this->purchaseResult($body, 'Airtime purchase completed on BudPay.', (string) ($payload['reference'] ?? null));
    }

    public function listDataProviders(array $settings): array
    {
        $response = $this->requestWithSslFallback(fn ($client) => $client->get('internet'), $settings);
        $body = $this->ensureSuccess($response, $this->responseData($response), 'Unable to load BudPay data providers.');

        return array_values(array_map(fn ($item) => [
            'provider' => strtolower((string) (data_get($item, 'code') ?: data_get($item, 'provider') ?: data_get($item, 'name'))),
            'name' => (string) (data_get($item, 'name') ?: data_get($item, 'provider_name') ?: data_get($item, 'provider')),
            'symbol' => strtolower((string) (data_get($item, 'code') ?: data_get($item, 'provider') ?: data_get($item, 'name'))),
            'image' => (string) data_get($item, 'logo'),
        ], Arr::wrap(data_get($body, 'data', []))));
    }

    public function listDataPlans(string $provider, array $settings): array
    {
        $response = $this->requestWithSslFallback(fn ($client) => $client->get('internet/plans/'.$provider), $settings);
        $body = $this->ensureSuccess($response, $this->responseData($response), 'Unable to load BudPay data plans.');

        return array_values(array_map(function ($item) {
            $name = (string) (data_get($item, 'name') ?: data_get($item, 'plan_name') ?: 'Data plan');
            $validity = trim((string) (data_get($item, 'validity') ?: data_get($item, 'duration') ?: data_get($item, 'plan_validity') ?: $this->extractValidityFromName($name)));

            return [
                'id' => (string) (data_get($item, 'id') ?: data_get($item, 'plan_id') ?: data_get($item, 'code')),
                'name' => $name,
                'amount' => (float) (data_get($item, 'amount') ?: data_get($item, 'price') ?: 0),
                'validity' => $validity !== '' ? $validity : 'Other plans',
                'category' => (string) (data_get($item, 'category') ?: data_get($item, 'plan_type') ?: 'Bundle'),
                'image' => (string) data_get($item, 'logo'),
            ];
        }, Arr::wrap(data_get($body, 'data', []))));
    }

    public function purchaseData(array $payload, array $settings): array
    {
        $requestPayload = [
            'provider' => strtoupper((string) $payload['network']),
            'number' => (string) $payload['phone'],
            'plan_id' => $payload['plan_id'],
        ];

        $response = $this->signedClient($settings, $requestPayload)->post('internet', $requestPayload);
        $body = $this->ensureSuccess($response, $this->responseData($response), 'BudPay data purchase failed.');

        return $this->purchaseResult($body, 'Data purchase completed on BudPay.', (string) ($payload['reference'] ?? null));
    }

    public function listTvProviders(array $settings): array
    {
        $response = $this->requestWithSslFallback(fn ($client) => $client->get('tv'), $settings);
        $body = $this->ensureSuccess($response, $this->responseData($response), 'Unable to load BudPay TV providers.');

        return array_values(array_map(fn ($item) => [
            'provider' => strtolower((string) (data_get($item, 'code') ?: data_get($item, 'provider') ?: data_get($item, 'name'))),
            'name' => (string) (data_get($item, 'name') ?: data_get($item, 'provider_name') ?: data_get($item, 'provider')),
            'image' => (string) data_get($item, 'logo'),
        ], Arr::wrap(data_get($body, 'data', []))));
    }

    public function listTvPackages(string $provider, array $settings): array
    {
        $response = $this->requestWithSslFallback(fn ($client) => $client->get('tv/packages/'.$provider), $settings);
        $body = $this->ensureSuccess($response, $this->responseData($response), 'Unable to load BudPay TV packages.');

        return array_values(array_map(fn ($item) => [
            'id' => (string) (data_get($item, 'id') ?: data_get($item, 'package_id') ?: data_get($item, 'code')),
            'name' => (string) (data_get($item, 'name') ?: data_get($item, 'package_name') ?: 'Package'),
            'amount' => (float) (data_get($item, 'amount') ?: data_get($item, 'price') ?: 0),
        ], Arr::wrap(data_get($body, 'data', []))));
    }

    public function validateTv(array $payload, array $settings): array
    {
        $requestPayload = [
            'provider' => strtoupper((string) $payload['provider']),
            'number' => (string) $payload['number'],
        ];

        $response = $this->signedClient($settings, $requestPayload)->post('tv/validate', $requestPayload);
        $body = $this->ensureSuccess($response, $this->responseData($response), 'BudPay could not validate this decoder.');

        return [
            'status' => 'success',
            'message' => (string) data_get($body, 'message', 'Decoder validated successfully.'),
            'customer_name' => (string) (
                data_get($body, 'data.customer_name')
                ?: data_get($body, 'data.name')
                ?: data_get($body, 'data.account_name')
                ?: 'Validated customer'
            ),
            'reference' => (string) (
                data_get($body, 'data.reference')
                ?: data_get($body, 'reference')
                ?: ''
            ),
            'meta' => $body,
        ];
    }

    public function purchaseTv(array $payload, array $settings): array
    {
        $requestPayload = [
            'provider' => strtoupper((string) $payload['provider']),
            'number' => (string) $payload['number'],
            'package_id' => $payload['package_id'],
        ];

        $response = $this->signedClient($settings, $requestPayload)->post('tv', $requestPayload);
        $body = $this->ensureSuccess($response, $this->responseData($response), 'BudPay TV subscription failed.');

        return $this->purchaseResult($body, 'TV subscription completed on BudPay.', (string) ($payload['reference'] ?? null));
    }

    public function listElectricityProviders(array $settings): array
    {
        $response = $this->requestWithSslFallback(fn ($client) => $client->get('electricity'), $settings);
        $body = $this->ensureSuccess($response, $this->responseData($response), 'Unable to load BudPay electricity providers.');

        return array_values(array_map(fn ($item) => [
            'provider' => strtolower((string) (data_get($item, 'code') ?: data_get($item, 'provider') ?: data_get($item, 'name'))),
            'name' => (string) (data_get($item, 'name') ?: data_get($item, 'provider_name') ?: data_get($item, 'provider')),
            'image' => (string) data_get($item, 'logo'),
        ], Arr::wrap(data_get($body, 'data', []))));
    }

    public function validateElectricity(array $payload, array $settings): array
    {
        $requestPayload = [
            'provider' => strtoupper((string) $payload['provider']),
            'meter_type' => strtolower((string) $payload['type']),
            'number' => (string) $payload['number'],
        ];

        $response = $this->signedClient($settings, $requestPayload)->post('electricity/validate', $requestPayload);
        $body = $this->ensureSuccess($response, $this->responseData($response), 'BudPay could not validate this meter.');

        return [
            'status' => 'success',
            'message' => (string) data_get($body, 'message', 'Meter validated successfully.'),
            'customer_name' => (string) (
                data_get($body, 'data.customer_name')
                ?: data_get($body, 'data.name')
                ?: data_get($body, 'data.account_name')
                ?: ''
            ),
            'address' => (string) (data_get($body, 'data.customer_address') ?: data_get($body, 'data.address') ?: ''),
            'reference' => (string) (data_get($body, 'data.reference') ?: data_get($body, 'reference') ?: ''),
            'meta' => $body,
        ];
    }

    public function purchaseElectricity(array $payload, array $settings): array
    {
        $requestPayload = [
            'provider' => strtoupper((string) $payload['provider']),
            'meter_type' => strtolower((string) $payload['type']),
            'number' => (string) $payload['number'],
            'amount' => (float) $payload['amount'],
        ];

        $response = $this->signedClient($settings, $requestPayload)->post('electricity', $requestPayload);
        $body = $this->ensureSuccess($response, $this->responseData($response), 'BudPay electricity purchase failed.');

        return $this->purchaseResult($body, 'Electricity purchase completed on BudPay.', (string) ($payload['reference'] ?? null));
    }

    protected function extractValidityFromName(string $name): string
    {
        if (preg_match('/\(([^)]+)\)\s*$/', $name, $matches)) {
            return trim($matches[1]);
        }

        return '';
    }

    protected function sortRecursive(array $payload): array
    {
        ksort($payload);

        foreach ($payload as $key => $value) {
            if (is_array($value)) {
                $payload[$key] = $this->sortRecursive($value);
            }
        }

        return $payload;
    }
}
