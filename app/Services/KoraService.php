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

    /**
     * Initiate Kora Liveness verification
     * 
     * @param string|int $userId Unique identifier for the user
     * @param string $fullName User's full name
     * @param string|null $secretKey
     * @return mixed
     */
    public function initiateLiveness(string|int $userId, string $fullName, ?string $secretKey = null)
    {
        return $this->client($secretKey)->post('merchant/api/v1/liveness/initiate', [
            'client_id' => (string) $userId,
            'client_name' => $fullName,
            'verification_consent' => true,
        ]);
    }

    /**
     * Check status of Kora Liveness verification
     * 
     * @param string $livenessId The liveness ID returned from initiate
     * @param string|null $secretKey
     * @return mixed
     */
    public function checkLivenessStatus(string $livenessId, ?string $secretKey = null)
    {
        return $this->client($secretKey)->get("merchant/api/v1/liveness/{$livenessId}/status");
    }

    /**
     * Get liveness verification details
     * 
     * @param string $livenessId The liveness ID
     * @param string|null $secretKey
     * @return mixed
     */
    public function getLivenessDetails(string $livenessId, ?string $secretKey = null)
    {
        return $this->client($secretKey)->get("merchant/api/v1/liveness/{$livenessId}");
    }
}
