<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

class QuicktellerService
{
    public function tokenClient(string $authUrl): PendingRequest
    {
        $client = Http::asForm()
            ->acceptJson()
            ->timeout(30)
            ->retry(1, 250);

        if (app()->environment('local')) {
            $client = $client->withoutVerifying();
        }

        return $client->baseUrl(rtrim($authUrl, '/'));
    }

    public function apiClient(string $searchUrl, string $token, string $clientId): PendingRequest
    {
        $client = Http::acceptJson()
            ->asJson()
            ->timeout(30)
            ->retry(1, 250)
            ->withToken($token)
            ->withHeaders([
                'ClientId' => $clientId,
            ]);

        if (app()->environment('local')) {
            $client = $client->withoutVerifying();
        }

        return $client->baseUrl(rtrim($searchUrl, '/'));
    }

    public function accessToken(string $authUrl, string $clientId, string $clientSecret)
    {
        return $this->tokenClient($authUrl)->post('', [
            'grant_type' => 'client_credentials',
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
        ]);
    }

    public function referenceSearch(string $searchUrl, string $token, string $clientId, array $payload)
    {
        return $this->apiClient($searchUrl, $token, $clientId)->post('', $payload);
    }
}
