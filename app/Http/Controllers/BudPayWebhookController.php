<?php

namespace App\Http\Controllers;

use App\Models\Deposit;
use App\Models\Transaction;
use App\Models\User;
use App\Services\WalletService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BudPayWebhookController extends Controller
{
    public function __construct(protected WalletService $wallets)
    {
    }

    public function handle(Request $request): JsonResponse
    {
        $payload = $request->all();
        $notify = (string) data_get($payload, 'notify', '');
        $notifyType = strtolower((string) data_get($payload, 'notifyType', ''));

        if ($notify !== 'transaction' || $notifyType !== 'successful') {
            return response()->json(['status' => 'ignored'], 200);
        }

        $reference = (string) data_get($payload, 'data.reference', '');

        if (! filled($reference) || Transaction::where('trx', $reference)->exists() || Deposit::where('trx', $reference)->exists()) {
            return response()->json(['status' => 'duplicate'], 200);
        }

        $customerCode = (string) data_get($payload, 'data.customer.customer_code', '');
        $email = (string) data_get($payload, 'data.customer.email', '');

        $user = User::query()
            ->when(filled($customerCode), fn ($query) => $query->where('budpay_customer_code', $customerCode))
            ->when(blank($customerCode) && filled($email), fn ($query) => $query->where('email', $email))
            ->first();

        if (! $user) {
            Log::warning('BudPay webhook user not found.', ['reference' => $reference, 'payload' => $payload]);
            return response()->json(['error' => 'user_not_found'], 404);
        }

        $grossAmount = (float) data_get($payload, 'data.requested_amount', data_get($payload, 'data.amount', 0));
        $fee = (float) data_get($payload, 'data.fees', 0);

        $this->wallets->creditDeposit($user, $grossAmount, $fee, $reference, 'BudPay');

        return response()->json(['status' => 'ok'], 200);
    }
}
