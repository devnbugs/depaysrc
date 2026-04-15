<?php

namespace App\Services\Bills\Providers;

use Illuminate\Support\Arr;

class SquadBillPaymentProvider extends AbstractBillPaymentProvider
{
    public function key(): string
    {
        return 'squad';
    }

    public function supports(string $service): bool
    {
        return in_array($service, ['airtime', 'data', 'electricity'], true);
    }

    public function listAirtimeProviders(array $settings): array
    {
        return $this->defaultTelcos();
    }

    public function purchaseAirtime(array $payload, array $settings): array
    {
        $response = $this->requestWithSslFallback(fn ($client) => $client->post('vending/airtime', [
            'phone_number' => (string) $payload['phone'],
            'amount' => (float) $payload['amount'],
        ]), $settings);
        $body = $this->ensureSuccess($response, $this->responseData($response), 'Squad airtime purchase failed.');

        return $this->purchaseResult($body, 'Airtime purchase completed on Squad.', (string) ($payload['reference'] ?? null));
    }

    public function listDataProviders(array $settings): array
    {
        return $this->defaultTelcos();
    }

    public function listDataPlans(string $provider, array $settings): array
    {
        $response = $this->requestWithSslFallback(fn ($client) => $client->get('vending/data-bundles', [
            'network' => strtoupper($provider),
        ]), $settings);
        $body = $this->ensureSuccess($response, $this->responseData($response), 'Unable to load Squad data plans.');

        return array_values(array_map(fn ($item) => [
            'id' => (string) (data_get($item, 'plan_code') ?: data_get($item, 'id')),
            'name' => (string) (data_get($item, 'bundle_value') ?: data_get($item, 'plan_name') ?: data_get($item, 'name') ?: 'Data plan'),
            'amount' => (float) (data_get($item, 'amount') ?: data_get($item, 'price') ?: 0),
            'validity' => (string) (data_get($item, 'bundle_validity') ?: data_get($item, 'validity') ?: 'Other plans'),
            'category' => (string) (data_get($item, 'bundle_description') ?: data_get($item, 'plan_name') ?: 'Bundle'),
            'image' => '',
        ], Arr::wrap(data_get($body, 'data', []))));
    }

    public function purchaseData(array $payload, array $settings): array
    {
        $response = $this->requestWithSslFallback(fn ($client) => $client->post('vending/data', [
            'plan_code' => $payload['plan_id'],
            'phone_number' => (string) $payload['phone'],
            'amount' => (float) $payload['amount'],
        ]), $settings);
        $body = $this->ensureSuccess($response, $this->responseData($response), 'Squad data purchase failed.');

        return $this->purchaseResult($body, 'Data purchase completed on Squad.', (string) ($payload['reference'] ?? null));
    }

    public function listElectricityProviders(array $settings): array
    {
        $response = $this->requestWithSslFallback(fn ($client) => $client->get('vending/utilities/electricity/service-providers'), $settings);
        $body = $this->ensureSuccess($response, $this->responseData($response), 'Unable to load Squad electricity providers.');

        return array_values(array_map(fn ($item) => [
            'provider' => strtolower((string) (data_get($item, 'service_id') ?: data_get($item, 'provider') ?: data_get($item, 'name'))),
            'name' => (string) (data_get($item, 'name') ?: data_get($item, 'provider_name') ?: data_get($item, 'service_name') ?: data_get($item, 'service_id')),
            'image' => '',
        ], Arr::wrap(data_get($body, 'data', []))));
    }

    public function validateElectricity(array $payload, array $settings): array
    {
        $response = $this->requestWithSslFallback(fn ($client) => $client->post('vending/utilities/electricity/lookup', [
            'meter_type' => strtolower((string) $payload['type']),
            'meter_no' => (string) $payload['number'],
            'provider' => strtoupper((string) $payload['provider']),
        ]), $settings);
        $body = $this->ensureSuccess($response, $this->responseData($response), 'Squad could not validate this meter.');

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
            'minimum_amount' => (float) (data_get($body, 'data.minimum_amount') ?: 0),
            'meta' => $body,
        ];
    }

    public function purchaseElectricity(array $payload, array $settings): array
    {
        $response = $this->requestWithSslFallback(fn ($client) => $client->post('vending/utilities/electricity', [
            'reference' => (string) ($payload['validation_reference'] ?: $payload['reference']),
            'phone_number' => (string) $payload['phone'],
            'email' => (string) $payload['email'],
            'amount' => (float) $payload['amount'],
        ]), $settings);
        $body = $this->ensureSuccess($response, $this->responseData($response), 'Squad electricity purchase failed.');

        return $this->purchaseResult($body, 'Electricity purchase completed on Squad.', (string) ($payload['reference'] ?? null));
    }

    protected function defaultTelcos(): array
    {
        return [
            ['provider' => 'mtn', 'name' => 'MTN', 'symbol' => 'mtn', 'image' => '', 'min' => 50, 'max' => 500000],
            ['provider' => 'airtel', 'name' => 'AIRTEL', 'symbol' => 'airtel', 'image' => '', 'min' => 50, 'max' => 500000],
            ['provider' => 'glo', 'name' => 'GLO', 'symbol' => 'glo', 'image' => '', 'min' => 50, 'max' => 500000],
            ['provider' => '9mobile', 'name' => '9MOBILE', 'symbol' => '9mobile', 'image' => '', 'min' => 50, 'max' => 500000],
        ];
    }
}
