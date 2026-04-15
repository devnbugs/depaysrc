<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

class MonnifyService
{
    public function client(): PendingRequest
    {
        return Http::baseUrl(rtrim(config('monnify.base_url', 'https://sandbox.monnify.com'), '/').'/')
            ->acceptJson()
            ->asJson()
            ->timeout(30)
            ->retry(2, 250);
    }

    public function apiKey(): string
    {
        return (string) config('monnify.api_key', '');
    }

    public function secretKey(): string
    {
        return (string) config('monnify.secret_key', '');
    }

    public function contractCode(): string
    {
        return (string) config('monnify.contract_code', '');
    }

    public function walletId(): string
    {
        return (string) config('monnify.wallet_id', '');
    }

    public function bvn(): string
    {
        return (string) config('monnify.bvn', '');
    }

    public function login()
    {
        return $this->client()
            ->withBasicAuth($this->apiKey(), $this->secretKey())
            ->post('api/v1/auth/login');
    }

    public function bearerClient(string $accessToken): PendingRequest
    {
        return $this->client()->withToken($accessToken);
    }

    public function reservedAccountPayload(User $user): array
    {
        return [
            'accountReference' => $user->username ?: $user->email,
            'accountName' => trim(($user->firstname ?? '').' '.($user->lastname ?? '')) ?: ($user->fullname ?? $user->email),
            'currencyCode' => config('monnify.default_currency_code', 'NGN'),
            'contractCode' => $this->contractCode(),
            'customerEmail' => $user->email,
            'customerName' => trim(($user->firstname ?? '').' '.($user->lastname ?? '')) ?: ($user->fullname ?? $user->email),
            'bvn' => $this->bvn(),
            'getAllAvailableBanks' => true,
        ];
    }

    public function createReservedAccount(User $user, string $accessToken)
    {
        return $this->bearerClient($accessToken)
            ->post('api/v2/bank-transfer/reserved-accounts', $this->reservedAccountPayload($user));
    }

    public function queryTransaction(string $transactionReference, string $accessToken)
    {
        return $this->bearerClient($accessToken)
            ->get('api/v2/merchant/transactions/query', [
                'transactionReference' => $transactionReference,
            ]);
    }
}
