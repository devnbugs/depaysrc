<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

class EasyAccessService
{
    public function client(): PendingRequest
    {
        return Http::baseUrl(rtrim(config('services.easyaccess.base_url', 'https://easyaccess.com.ng/api/'), '/').'/')
            ->acceptJson()
            ->asJson()
            ->timeout(30)
            ->retry(2, 250)
            ->withHeaders([
                'AuthorizationToken' => (string) config('services.easyaccess.auth_token', ''),
            ]);
    }

    public function airtime(array $payload)
    {
        return $this->client()->post('airtime.php', $payload);
    }

    public function data(array $payload)
    {
        return $this->client()->post('data.php', $payload);
    }
}
