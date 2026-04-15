<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

class KoraService
{
    public function client(?string $secretKey = null): PendingRequest
    {
        $client = Http::baseUrl(rtrim(config('services.kora.base_url', 'https://api.korapay.com/'), '/').'/')
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
        return (string) config('services.kora.secret_key', '');
    }

    public function initializeCheckout(array $payload, ?string $secretKey = null)
    {
        return $this->client($secretKey)->post('merchant/api/v1/charges/initialize', $payload);
    }

    public function createVirtualBankAccount(array $payload, ?string $secretKey = null)
    {
        return $this->client($secretKey)->post('merchant/api/v1/virtual-bank-account', $payload);
    }

    public function verifyCharge(string $reference, ?string $secretKey = null)
    {
        return $this->client($secretKey)->get('merchant/api/v1/charges/'.$reference);
    }

    public function verifyBvn(string $bvn, ?string $secretKey = null)
    {
        return $this->client($secretKey)->post('merchant/api/v1/identities/ng/bvn', [
            'id' => $bvn,
            'verification_consent' => true,
        ]);
    }

    public function verifyNin(string $nin, ?string $secretKey = null)
    {
        return $this->client($secretKey)->post('merchant/api/v1/identities/ng/nin', [
            'id' => $nin,
            'verification_consent' => true,
        ]);
    }
}
