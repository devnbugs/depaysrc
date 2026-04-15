<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

class VtpassService
{
    public function mode(): int
    {
        return (int) config('services.vtpass.mode', 1);
    }

    public function baseUrl(): string
    {
        $baseUrl = $this->mode() === 0
            ? config('services.vtpass.sandbox_base_url', 'https://sandbox.vtpass.com/api/')
            : config('services.vtpass.base_url', 'https://vtpass.com/api/');

        return rtrim((string) $baseUrl, '/').'/';
    }

    public function username(): string
    {
        return (string) config('services.vtpass.username', '');
    }

    public function password(): string
    {
        return (string) config('services.vtpass.password', '');
    }

    public function client(): PendingRequest
    {
        return Http::baseUrl($this->baseUrl())
            ->acceptJson()
            ->asJson()
            ->timeout(30)
            ->retry(2, 250)
            ->withBasicAuth($this->username(), $this->password());
    }

    public function merchantVerify(array $payload)
    {
        return $this->client()->post('merchant-verify', $payload);
    }

    public function pay(array $payload)
    {
        return $this->client()->post('pay', $payload);
    }

    public function requery(string $requestId)
    {
        return $this->client()->post('requery', [
            'request_id' => $requestId,
        ]);
    }

    public function serviceVariations(string $serviceId)
    {
        return $this->client()->get('service-variations', [
            'serviceID' => $serviceId,
        ]);
    }
}
