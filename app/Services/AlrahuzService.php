<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

class AlrahuzService
{
    public function client(): PendingRequest
    {
        return Http::baseUrl(rtrim(config('services.alrahuz.base_url', 'https://alrahuzdata.com/api/'), '/').'/')
            ->acceptJson()
            ->asJson()
            ->timeout(30)
            ->retry(2, 250)
            ->withHeaders([
                'Authorization' => 'Token '.(string) config('services.alrahuz.api_key', ''),
            ]);
    }

    public function topup(array $payload)
    {
        return $this->client()->post('topup/', $payload);
    }
}
