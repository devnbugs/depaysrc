<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

class PaystackService
{
    public function client(): PendingRequest
    {
        return Http::baseUrl(rtrim(config('services.paystack.base_url', 'https://api.paystack.co/'), '/').'/')
            ->acceptJson()
            ->asJson()
            ->withToken($this->secretKey())
            ->retry(2, 250);
    }

    public function secretKey(): string
    {
        return (string) config('services.paystack.secret_key', '');
    }

    public function post(string $endpoint, array $data = [])
    {
        return $this->client()->post($endpoint, $data);
    }

    public function get(string $endpoint)
    {
        return $this->client()->get($endpoint);
    }

    public function option(string $endpoint)
    {
        return $this->get($endpoint);
    }

    public function createCustomer(array $payload)
    {
        return $this->post('customer', $payload);
    }

    public function createDedicatedAccount(array $payload)
    {
        return $this->post('dedicated_account', $payload);
    }

    public function initializeTransaction(array $payload)
    {
        return $this->post('transaction/initialize', $payload);
    }

    public function createPlan(array $payload)
    {
        return $this->post('plan', $payload);
    }

    public function updatePlan(string $code, array $payload)
    {
        return $this->client()->put("plan/{$code}", $payload);
    }

    public function subscriptionManageLink(string $subscriptionCode)
    {
        return $this->get("subscription/{$subscriptionCode}/manage/link");
    }

    public function verifyTransaction(string $reference)
    {
        return $this->get("transaction/verify/{$reference}");
    }
}
