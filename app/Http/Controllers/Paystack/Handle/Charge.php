<?php

namespace App\Http\Controllers\Paystack\Handle;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Deposit;
use App\Models\User;
use App\Services\WalletService;
use Illuminate\Support\Facades\Log;

class Charge extends Controller
{
    public function __construct(protected WalletService $walletService)
    {
    }

    public function Success(Request $request)
    {
        $validated = $request->validate([
            'event' => ['required', 'in:charge.success'],
            'data.id' => ['required'],
            'data.status' => ['required'],
            'data.reference' => ['required', 'string'],
            'data.amount' => ['required', 'numeric'],
            'data.customer.email' => ['required', 'email'],
            'data.customer.id' => ['required'],
        ]);

        $payload = $request->input('data');
        $user = User::where([
            'email' => data_get($payload, 'customer.email'),
            'psid' => data_get($payload, 'customer.id'),
        ])->first();

        if (! $user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $reference = (string) data_get($payload, 'reference');
        if (Transaction::where('trx', $reference)->exists() || Deposit::where('trx', $reference)->exists()) {
            return response()->json(['message' => 'Transaction already processed'], 200);
        }

        $amountReceived = ((float) data_get($payload, 'amount')) / 100;
        $transactionCharge = round($amountReceived * 0.05, 2);

        $this->walletService->creditDeposit(
            $user,
            $amountReceived,
            $transactionCharge,
            $reference,
            'Paystack'
        );

        Log::info('Successfully processed Paystack payment.', [
            'user_id' => $user->id,
            'reference' => $reference,
        ]);

        return response()->json(['message' => 'Transaction successful'], 200);
    }
}
