<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

class BudPayService
{
    public function client(?string $secretKey = null): PendingRequest
    {
        $client = Http::baseUrl(rtrim(config('services.budpay.base_url', 'https://api.budpay.com/api/v2/'), '/').'/')
            ->acceptJson()
            ->asJson()
            ->withToken($secretKey ?: $this->secretKey())
            ->timeout(30)
            ->retry(1, 250);

        if (app()->environment('local')) {
            $client = $client->withoutVerifying();
        }

        return $client;
    }

    public function secretKey(): string
    {
        return (string) config('services.budpay.secret_key', '');
    }

    public function createCustomer(array $payload, ?string $secretKey = null)
    {
        return $this->client($secretKey)->post('customer', $payload);
    }

    public function createDedicatedVirtualAccount(array $payload, ?string $secretKey = null)
    {
        return $this->client($secretKey)->post('dedicated_virtual_account', $payload);
    }
}
