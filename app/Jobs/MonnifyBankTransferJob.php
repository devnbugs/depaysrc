<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use App\Services\MonnifyService;
use Illuminate\Support\Facades\Log; // Import the Log facade for debugging

class MonnifyBankTransferJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;

    /**
     * Create a new job instance.
     *
     * @param  User  $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(MonnifyService $monnifyService): void
    {
        if (empty($this->user->monnify_token)) {
            Log::warning('MonnifyBankTransferJob skipped because the user token is missing.', [
                'user_id' => $this->user->id,
            ]);
            return;
        }

        try {
            $response = $monnifyService->createReservedAccount($this->user, $this->user->monnify_token);
            $responseData = $response->json();

            if (! $response->successful() || ! ($responseData['requestSuccessful'] ?? false)) {
                Log::warning('Monnify reserved account creation failed.', [
                    'user_id' => $this->user->id,
                    'response' => $responseData,
                ]);
                return;
            }

            $suffix = 1;
            foreach (($responseData['responseBody']['accounts'] ?? []) as $account) {
                $this->user->{'bN'.$suffix} = $account['bankName'] ?? null;
                $this->user->{'aN'.$suffix} = $account['accountName'] ?? null;
                $this->user->{'aNo'.$suffix} = $account['accountNumber'] ?? null;
                $suffix++;
            }

            $this->user->save();

            Log::info('MonnifyBankTransferJob: reserved accounts stored.', [
                'user_id' => $this->user->id,
            ]);
        } catch (\Throwable $e) {
            Log::error('MonnifyBankTransferJob failed.', [
                'user_id' => $this->user->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
