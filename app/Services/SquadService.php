<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

class SquadService
{
    public function client(): PendingRequest
    {
        $client = Http::baseUrl(rtrim(config('services.squad.base_url', 'https://api-d.squadco.com/'), '/').'/')
            ->acceptJson()
            ->asJson()
            ->withHeaders([
                'Authorization' => 'Bearer '.config('services.squad.secret_key', ''),
            ])
            ->timeout(30)
            ->retry(1, 250);

        if (app()->environment('local')) {
            $client = $client->withoutVerifying();
        }

        return $client;
    }

    public function createVirtualAccount(array $payload)
    {
        return $this->client()->post('virtual-account', $payload);
    }
}
