<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Network;
use App\Models\Transaction;
use App\Models\User;
use App\Services\WalletService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class ApiController extends Controller
{
    public function __construct(protected WalletService $walletService)
    {
    }

    public function getWalletBalance(string $phoneNumber): JsonResponse
    {
        $user = $this->walletService->findByPhone($phoneNumber);

        if (! $user) {
            return response()->json(['message' => 'Unable to get the wallet balance'], 404);
        }

        return response()->json([
            'balance' => $user->balance,
            'name' => $user->fullname,
        ]);
    }

    public function validatePin(string $phoneNumber, string $pin): JsonResponse
    {
        $user = $this->walletService->findByPhone($phoneNumber);

        if (! $user) {
            return response()->json(['valid' => false], 404);
        }

        return response()->json([
            'valid' => $this->walletService->validatePin($user, $pin),
        ]);
    }

    public function buyAirtime(string $phoneNumber, string $networkSymbol, string $amount, ?string $pin = null): JsonResponse
    {
        $user = $this->walletService->findByPhone($phoneNumber);

        if (! $user) {
            return response()->json(['success' => false, 'message' => 'Unable to locate the customer account'], 404);
        }

        if ($pin !== null && ! $this->walletService->validatePin($user, $pin)) {
            return response()->json(['success' => false, 'message' => 'Incorrect PIN'], 422);
        }

        $network = Network::whereSymbol($networkSymbol)->first();
        if (! $network) {
            return response()->json(['success' => false, 'message' => 'Invalid Network'], 422);
        }

        $amountValue = (float) $amount;
        if ($amountValue <= 0) {
            return response()->json(['success' => false, 'message' => 'Invalid amount'], 422);
        }

        $deduction = round($amountValue * 0.98, 2);
        if ($user->balance < $deduction) {
            return response()->json(['success' => false, 'message' => 'Insufficient funds'], 422);
        }

        $reference = 'CTPR'.date('ymdHi').str_pad((string) random_int(1, 999999), 6, '0', STR_PAD_LEFT);

        $response = Http::acceptJson()
            ->asJson()
            ->timeout(30)
            ->withHeaders([
                'Authorization' => 'Token '.config('services.alrahuz.api_key', ''),
            ])
            ->post(rtrim(config('services.alrahuz.base_url', 'https://alrahuzdata.com/api/'), '/').'/topup/', [
                'network' => $network->symbol,
                'amount' => $amountValue,
                'mobile_number' => $phoneNumber,
                'Ported_number' => true,
                'airtime_type' => 'VTU',
                'client_reference' => $reference,
            ]);

        $reply = $response->json();
        if (! $response->successful() || ! isset($reply['Status'])) {
            return response()->json([
                'success' => false,
                'message' => $reply['message'] ?? 'Purchase Decline',
                'provider' => $reply,
            ], 422);
        }

        $bill = DB::transaction(function () use ($user, $network, $amountValue, $deduction, $reference, $reply, $phoneNumber) {
            $lockedUser = User::whereKey($user->id)->lockForUpdate()->firstOrFail();
            if ($lockedUser->balance < $deduction) {
                return null;
            }

            $lockedUser->balance -= $deduction;
            $lockedUser->save();

            $bill = new Bill();
            $bill->user_id = $lockedUser->id;
            $bill->amount = $amountValue;
            $bill->token = $reference;
            $bill->bundle = 'VTU Airtime';
            $bill->profit = round($amountValue - $deduction, 2);
            $bill->trx = $reply['ident'] ?? $reference;
            $bill->phone = $phoneNumber;
            $bill->network = $network->name;
            $bill->newbalance = $lockedUser->balance;
            $bill->type = 1;
            $bill->status = 1;
            $bill->response = json_encode($reply);
            $bill->save();

            $transaction = new Transaction();
            $transaction->user_id = $lockedUser->id;
            $transaction->amount = $amountValue;
            $transaction->charge = round($amountValue - $deduction, 2);
            $transaction->trx_type = '-';
            $transaction->details = 'Airtime purchase for '.$network->name;
            $transaction->trx = $bill->trx;
            $transaction->save();

            return $bill;
        });

        if (! $bill) {
            return response()->json(['success' => false, 'message' => 'Unable to complete purchase'], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Airtime Purchase was Successful',
            'data' => [
                'bill' => $bill,
                'provider' => $reply,
            ],
        ]);
    }

    public function buyData(string $phoneNumber, string $amount): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'USSD data purchase is temporarily unavailable. Please use the dashboard.',
        ], 422);
    }
}
