<?php

namespace App\Http\Controllers;

use App\Models\Deposit;
use App\Models\Transaction;
use App\Models\User;
use App\Services\Funding\FundingSettings;
use App\Services\KoraService;
use App\Services\WalletService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class KoraWebhookController extends Controller
{
    public function __construct(
        protected KoraService $kora,
        protected WalletService $wallets,
        protected FundingSettings $settings,
    ) {
    }

    public function handle(Request $request): JsonResponse
    {
        $event = strtolower((string) $request->input('event', ''));
        $data = (array) $request->input('data', []);
        $reference = (string) data_get($data, 'reference', '');

        if ($event && ! in_array($event, ['charge.success', 'bank_transfer.success'], true)) {
            return response()->json(['status' => 'ignored'], 200);
        }

        if (! filled($reference) || Transaction::where('trx', $reference)->exists() || Deposit::where('trx', $reference)->exists()) {
            return response()->json(['status' => 'duplicate'], 200);
        }

        $identitySettings = $this->settings->identity();
        $secretKey = (string) ($identitySettings['kora_secret_key'] ?? '');

        if (blank($secretKey)) {
            return response()->json(['error' => 'kora_not_configured'], 422);
        }

        $verification = $this->kora->verifyCharge($reference, $secretKey);
        $body = $verification->json() ?? [];

        if (! $verification->successful() || ! data_get($body, 'status')) {
            Log::warning('Kora webhook verification failed.', ['reference' => $reference, 'payload' => $request->all(), 'response' => $body]);
            return response()->json(['error' => 'verification_failed'], 422);
        }

        $status = strtolower((string) data_get($body, 'data.status', 'pending'));
        if (! in_array($status, ['success', 'successful'], true)) {
            return response()->json(['status' => $status], 202);
        }

        $user = User::query()
            ->where('kora_account_reference', (string) data_get($body, 'data.virtual_bank_account.account_reference'))
            ->orWhere('aNo3', (string) data_get($body, 'data.virtual_bank_account.account_number'))
            ->orWhere('email', (string) data_get($body, 'data.customer.email'))
            ->first();

        if (! $user) {
            Log::warning('Kora webhook user not found.', ['reference' => $reference, 'payload' => $body]);
            return response()->json(['error' => 'user_not_found'], 404);
        }

        $amount = (float) data_get($body, 'data.amount');
        $fee = (float) data_get($body, 'data.fee');

        $this->wallets->creditDeposit($user, $amount, $fee, $reference, 'Kora Virtual Account');

        return response()->json(['status' => 'ok'], 200);
    }
}
