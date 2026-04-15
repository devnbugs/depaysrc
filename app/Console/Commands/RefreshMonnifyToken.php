<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\MonnifyService;
use Illuminate\Support\Facades\Cache;

class RefreshMonnifyToken extends Command
{
    protected $signature = 'monnify:refresh-token';
    protected $description = 'Refresh the Monnify access token';

    public function handle(MonnifyService $monnifyService): int
    {
        $response = $monnifyService->login();
        $responseData = $response->json();

        if (! $response->successful() || ! ($responseData['requestSuccessful'] ?? false)) {
            $this->error('Failed to refresh Monnify access token.');
            return self::FAILURE;
        }

        $accessToken = data_get($responseData, 'responseBody.accessToken');
        $expiresIn = (int) data_get($responseData, 'responseBody.expiresIn', 3600);

        Cache::put('monnify.access_token', $accessToken, now()->addSeconds(max(60, $expiresIn - 60)));
        Cache::put('monnify.access_token_expires_at', now()->addSeconds($expiresIn));

        $this->info('Monnify access token refreshed.');
        return self::SUCCESS;
    }
}
