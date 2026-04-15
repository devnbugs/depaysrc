<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\User; // Make sure to import the User model
use App\Jobs\MonnifyBankTransferJob;
use App\Services\MonnifyService;
use Illuminate\Support\Facades\Log;


class MonnifyTokenJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function handle(MonnifyService $monnifyService): void
    {
        try {
            $response = $monnifyService->login();
            $responseData = $response->json();

            if ($response->successful() && ($responseData['requestSuccessful'] ?? false) && isset($responseData['responseBody']['accessToken'])) {
                $accessToken = $responseData['responseBody']['accessToken'];

                $this->user->forceFill([
                    'monnify_token' => $accessToken,
                ])->save();

                Log::info('MonnifyTokenJob: refreshed access token for user '.$this->user->id);
                dispatch(new MonnifyBankTransferJob($this->user));
                return;
            }

            Log::warning('MonnifyTokenJob: login failed or no access token returned.', [
                'user_id' => $this->user->id,
                'response' => $responseData,
            ]);
        } catch (\Exception $e) {
            Log::error('MonnifyTokenJob failed.', [
                'user_id' => $this->user->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
