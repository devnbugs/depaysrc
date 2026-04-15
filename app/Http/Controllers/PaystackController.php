<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\PaystackService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PaystackController extends Controller
{
    public function __construct(protected PaystackService $paystackService)
    {
    }

    protected function resolveUser(User|Request|null $input = null): ?User
    {
        if ($input instanceof User) {
            return $input;
        }

        if ($input instanceof Request) {
            return $input->user() ?? Auth::user();
        }

        return Auth::user();
    }

    public function createCustomer(User|Request|null $input = null): bool
    {
        $user = $this->resolveUser($input);

        if (! $user) {
            Log::warning('Paystack customer creation skipped because no user was available.');
            return false;
        }

        try {
            $response = $this->paystackService->createCustomer([
                'email' => $user->email,
                'first_name' => $user->firstname,
                'last_name' => $user->lastname,
                'phone' => $user->mobile,
            ]);
            $payload = $response->json();

            if (! $response->successful() || ! ($payload['status'] ?? false)) {
                Log::warning('Paystack customer creation failed.', [
                    'user_id' => $user->id,
                    'response' => $payload,
                ]);
                return false;
            }

            $user->forceFill([
                'paystackcode' => data_get($payload, 'data.customer_code'),
                'psid' => data_get($payload, 'data.id'),
                'pslinked' => 1,
            ])->save();

            Log::info('Paystack customer created.', ['user_id' => $user->id]);
            return true;
        } catch (\Throwable $e) {
            Log::error('Paystack customer creation failed.', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    public function assignAccounts(User|Request|null $input = null): bool
    {
        $user = $this->resolveUser($input);

        if (! $user) {
            Log::warning('Paystack account assignment skipped because no user was available.');
            return false;
        }

        if (! method_exists($user, 'hasLockedIdentity') || ! $user->hasLockedIdentity()) {
            Log::info('Paystack account assignment skipped because identity is not locked yet.', ['user_id' => $user->id]);
            return false;
        }

        if (empty($user->psid)) {
            if (! $this->createCustomer($user)) {
                return false;
            }
            $user->refresh();
        }

        if (! empty($user->aNo1)) {
            return true;
        }

        $splitCode = config('services.paystack.split_code', 'SPL_tIaVNyQ7LX');
        $banks = [
            [
                'bank' => 'wema-bank',
                'bankField' => 'bN1',
                'nameField' => 'aN1',
                'numberField' => 'aNo1',
                'idField' => 'aNid1',
            ],
        ];

        $created = false;

        foreach ($banks as $bank) {
            if (! empty($user->{$bank['numberField']})) {
                continue;
            }

            try {
                $response = $this->paystackService->createDedicatedAccount([
                    'customer' => $user->psid,
                    'split_code' => $splitCode,
                    'preferred_bank' => $bank['bank'],
                ]);
                $payload = $response->json();

                if (! $response->successful() || ! ($payload['status'] ?? false)) {
                    Log::warning('Paystack dedicated account creation failed.', [
                        'user_id' => $user->id,
                        'bank' => $bank['bank'],
                        'response' => $payload,
                    ]);
                    continue;
                }

                $user->{$bank['bankField']} = data_get($payload, 'data.bank.name');
                $user->{$bank['nameField']} = data_get($payload, 'data.account_name');
                $user->{$bank['numberField']} = data_get($payload, 'data.account_number');
                $user->{$bank['idField']} = data_get($payload, 'data.id');
                $created = true;
            } catch (\Throwable $e) {
                Log::error('Paystack dedicated account creation failed.', [
                    'user_id' => $user->id,
                    'bank' => $bank['bank'],
                    'error' => $e->getMessage(),
                ]);
            }
        }

        if (! $created) {
            return false;
        }

        $user->pslinked = 1;
        $user->psverified = 1;
        $user->save();

        return true;
    }

    public function verifyTrx(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'reference' => ['required', 'string'],
        ]);

        $response = $this->paystackService->verifyTransaction($validated['reference']);

        return response()->json([
            'success' => $response->successful(),
            'data' => $response->json(),
        ], $response->successful() ? 200 : 422);
    }
}
