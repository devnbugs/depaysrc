<?php

namespace App\Services\Funding;

use App\Models\User;
use App\Services\KoraService;
use App\Services\QuicktellerService;
use App\Services\WalletService;
use Illuminate\Support\Facades\Cache;
use RuntimeException;

class DepositCheckoutService
{
    public function __construct(
        protected FundingSettings $settings,
        protected KoraService $kora,
        protected QuicktellerService $quickteller,
        protected WalletService $wallets,
    ) {
    }

    public function depositSettings(): array
    {
        return $this->settings->deposit();
    }

    public function initializeKora(User $user, float $amount): array
    {
        $settings = $this->depositSettings();

        if (! $settings['kora_enabled']) {
            throw new RuntimeException('Kora checkout is not enabled right now.');
        }

        if (! filled(config('services.kora.secret_key'))) {
            throw new RuntimeException('Kora secret key is not configured.');
        }

        $reference = 'KORA-DEP-'.date('ymdHis').getTrx(6);
        Cache::put($this->cacheKey($reference), [
            'user_id' => $user->id,
            'amount' => $amount,
            'provider' => 'kora',
        ], now()->addHours(6));

        $response = $this->kora->initializeCheckout([
            'amount' => (int) round($amount),
            'currency' => 'NGN',
            'reference' => $reference,
            'redirect_url' => route('user.deposit.kora.callback'),
            'narration' => 'Wallet funding',
            'default_channel' => 'pay_with_bank',
            'channels' => ['pay_with_bank', 'card'],
            'customer' => [
                'email' => $user->email,
                'name' => trim($user->fullname) ?: $user->email,
            ],
            'metadata' => [
                'user_id' => $user->id,
                'deposit_type' => 'wallet',
            ],
        ]);

        $body = $response->json() ?? [];

        if (! $response->successful() || ! data_get($body, 'status')) {
            throw new RuntimeException((string) data_get($body, 'message', 'Unable to initialize Kora checkout.'));
        }

        return [
            'reference' => $reference,
            'checkout_url' => (string) data_get($body, 'data.checkout_url'),
        ];
    }

    public function confirmKora(string $reference): array
    {
        $cached = Cache::get($this->cacheKey($reference));
        $response = $this->kora->verifyCharge($reference);
        $body = $response->json() ?? [];

        if (! $response->successful() || ! data_get($body, 'status')) {
            throw new RuntimeException((string) data_get($body, 'message', 'Unable to verify Kora payment.'));
        }

        $status = strtolower((string) data_get($body, 'data.status', 'pending'));
        if (! in_array($status, ['success', 'successful'], true)) {
            return ['status' => $status, 'credited' => false];
        }

        $userId = (int) ($cached['user_id'] ?? data_get($body, 'data.metadata.user_id'));
        $user = User::findOrFail($userId);
        $amount = ((float) data_get($body, 'data.amount')) / 100;
        $fee = ((float) data_get($body, 'data.fee')) / 100;

        $result = $this->wallets->creditDeposit($user, $amount, $fee, $reference, 'Kora');
        Cache::forget($this->cacheKey($reference));

        return ['status' => 'success', 'credited' => ! ($result['duplicate'] ?? false)];
    }

    public function quicktellerPayload(User $user, float $amount): array
    {
        $settings = $this->depositSettings();

        if (! $settings['quickteller_enabled']) {
            throw new RuntimeException('Quickteller checkout is not enabled right now.');
        }

        if (! filled($settings['quickteller_merchant_code']) || ! filled($settings['quickteller_pay_item_id'])) {
            throw new RuntimeException('Quickteller merchant settings are incomplete.');
        }

        $reference = 'QT-DEP-'.date('ymdHis').getTrx(6);
        Cache::put($this->cacheKey($reference), [
            'user_id' => $user->id,
            'amount' => $amount,
            'provider' => 'quickteller',
        ], now()->addHours(6));

        return [
            'merchant_code' => $settings['quickteller_merchant_code'],
            'pay_item_id' => $settings['quickteller_pay_item_id'],
            'pay_item_name' => $settings['quickteller_pay_item_name'],
            'txn_ref' => $reference,
            'site_redirect_url' => route('user.deposit.quickteller.callback'),
            'amount' => (int) round($amount * 100),
            'currency' => 566,
            'cust_name' => trim($user->fullname) ?: $user->email,
            'cust_email' => $user->email,
            'cust_id' => (string) $user->id,
            'cust_mobile_no' => (string) $user->mobile,
            'mode' => $settings['quickteller_mode'],
        ];
    }

    public function confirmQuickteller(string $reference): array
    {
        $settings = $this->depositSettings();
        $cached = Cache::get($this->cacheKey($reference));

        if (! $cached) {
            throw new RuntimeException('Quickteller checkout session has expired. Please start again.');
        }

        $tokenResponse = $this->quickteller->accessToken(
            $settings['quickteller_auth_url'],
            $settings['quickteller_client_id'],
            $settings['quickteller_client_secret'],
        );

        $tokenBody = $tokenResponse->json() ?? [];
        $token = (string) data_get($tokenBody, 'access_token');

        if (! $tokenResponse->successful() || blank($token)) {
            throw new RuntimeException('Unable to authenticate Quickteller verification request.');
        }

        $searchResponse = $this->quickteller->referenceSearch(
            $settings['quickteller_search_url'],
            $token,
            $settings['quickteller_client_id'],
            [
                'merchant_code' => $settings['quickteller_merchant_code'],
                'txn_ref' => $reference,
            ],
        );

        $searchBody = $searchResponse->json() ?? [];

        if (! $searchResponse->successful() || ! in_array((string) data_get($searchBody, 'responseCode'), ['200', '202'], true)) {
            throw new RuntimeException((string) data_get($searchBody, 'responseMessage', 'Unable to verify Quickteller payment.'));
        }

        $first = (array) collect(data_get($searchBody, 'data', []))->first();
        $responseCode = (string) data_get($first, 'response_code', data_get($first, 'transaction_response_code', '00'));
        if ($responseCode && $responseCode !== '00') {
            return ['status' => 'pending', 'credited' => false];
        }

        $user = User::findOrFail((int) $cached['user_id']);
        $amount = (float) ($cached['amount'] ?? 0);
        $result = $this->wallets->creditDeposit($user, $amount, 0, $reference, 'Quickteller');
        Cache::forget($this->cacheKey($reference));

        return ['status' => 'success', 'credited' => ! ($result['duplicate'] ?? false)];
    }

    protected function cacheKey(string $reference): string
    {
        return 'deposit_checkout:'.$reference;
    }
}
