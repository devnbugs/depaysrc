<?php

namespace App\Services\Bills\Providers;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use RuntimeException;

abstract class AbstractBillPaymentProvider
{
    abstract public function key(): string;

    abstract public function supports(string $service): bool;

    public function label(): string
    {
        return ucfirst($this->key());
    }

    public function isConfigured(array $settings): bool
    {
        return (bool) ($settings['enabled'] ?? false) && filled($settings['secret_key'] ?? null);
    }

    public function listAirtimeProviders(array $settings): array
    {
        throw new RuntimeException($this->label().' does not expose airtime providers right now.');
    }

    public function purchaseAirtime(array $payload, array $settings): array
    {
        throw new RuntimeException($this->label().' does not support airtime purchases right now.');
    }

    public function listDataProviders(array $settings): array
    {
        throw new RuntimeException($this->label().' does not expose data providers right now.');
    }

    public function listDataPlans(string $provider, array $settings): array
    {
        throw new RuntimeException($this->label().' does not expose data plans right now.');
    }

    public function purchaseData(array $payload, array $settings): array
    {
        throw new RuntimeException($this->label().' does not support data purchases right now.');
    }

    public function listTvProviders(array $settings): array
    {
        throw new RuntimeException($this->label().' does not expose TV providers right now.');
    }

    public function listTvPackages(string $provider, array $settings): array
    {
        throw new RuntimeException($this->label().' does not expose TV packages right now.');
    }

    public function validateTv(array $payload, array $settings): array
    {
        throw new RuntimeException($this->label().' does not support TV validation right now.');
    }

    public function purchaseTv(array $payload, array $settings): array
    {
        throw new RuntimeException($this->label().' does not support TV purchases right now.');
    }

    public function listElectricityProviders(array $settings): array
    {
        throw new RuntimeException($this->label().' does not expose electricity providers right now.');
    }

    public function validateElectricity(array $payload, array $settings): array
    {
        throw new RuntimeException($this->label().' does not support electricity validation right now.');
    }

    public function purchaseElectricity(array $payload, array $settings): array
    {
        throw new RuntimeException($this->label().' does not support electricity purchases right now.');
    }

    protected function client(array $settings): PendingRequest
    {
        $client = Http::baseUrl(rtrim((string) ($settings['base_url'] ?? ''), '/').'/')
            ->acceptJson()
            ->asJson()
            ->withToken((string) ($settings['secret_key'] ?? ''))
            ->timeout(25)
            ->retry(1, 250);

        if (app()->environment('local')) {
            $client = $client->withoutVerifying();
        }

        return $client;
    }

    protected function requestWithSslFallback(callable $request, array $settings): Response
    {
        try {
            return $request($this->client($settings));
        } catch (ConnectionException $exception) {
            if (! str_contains(strtolower($exception->getMessage()), 'curl error 60')) {
                throw $exception;
            }

            return $request($this->client($settings)->withoutVerifying());
        }
    }

    protected function responseData(Response $response): array
    {
        return $response->json() ?? [];
    }

    protected function ensureSuccess(Response $response, array $body, string $fallbackMessage): array
    {
        if (! $response->successful() || ! $this->responseLooksSuccessful($body)) {
            throw new RuntimeException((string) ($body['message'] ?? $fallbackMessage));
        }

        return $body;
    }

    protected function responseLooksSuccessful(array $body): bool
    {
        if (array_key_exists('success', $body)) {
            return filter_var($body['success'], FILTER_VALIDATE_BOOLEAN);
        }

        if (array_key_exists('status', $body)) {
            return filter_var($body['status'], FILTER_VALIDATE_BOOLEAN) || in_array(strtolower((string) $body['status']), ['success', 'successful'], true);
        }

        return true;
    }

    protected function purchaseResult(array $body, string $fallbackMessage, ?string $defaultReference = null): array
    {
        $status = strtolower((string) (
            data_get($body, 'data.status')
            ?: data_get($body, 'data.transaction_status')
            ?: data_get($body, 'data.payment_status')
            ?: data_get($body, 'status')
            ?: 'success'
        ));

        return [
            'status' => in_array($status, ['pending', 'processing', 'queued'], true) ? 'pending' : (in_array($status, ['failed', 'error'], true) ? 'failed' : 'success'),
            'message' => (string) data_get($body, 'message', $fallbackMessage),
            'reference' => (string) (
                data_get($body, 'data.reference')
                ?: data_get($body, 'data.transaction_reference')
                ?: data_get($body, 'data.order_number')
                ?: data_get($body, 'reference')
                ?: $defaultReference
            ),
            'provider_status' => $status,
            'token' => (string) (
                data_get($body, 'data.purchased_code')
                ?: data_get($body, 'data.token')
                ?: data_get($body, 'purchased_code')
                ?: ''
            ),
            'units' => (string) (
                data_get($body, 'data.units')
                ?: data_get($body, 'data.token_units')
                ?: data_get($body, 'units')
                ?: ''
            ),
            'customer_name' => (string) (
                data_get($body, 'data.customer_name')
                ?: data_get($body, 'data.customer.account_name')
                ?: data_get($body, 'customer_name')
                ?: ''
            ),
            'address' => (string) (
                data_get($body, 'data.customer_address')
                ?: data_get($body, 'data.customer.address')
                ?: data_get($body, 'address')
                ?: ''
            ),
            'meta' => $body,
        ];
    }
}
