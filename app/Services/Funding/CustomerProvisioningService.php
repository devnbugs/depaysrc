<?php

namespace App\Services\Funding;

use App\Models\User;
use App\Services\BudPayService;
use App\Services\KoraService;
use App\Services\PaystackService;
use App\Services\SquadService;
use Illuminate\Support\Facades\Log;

class CustomerProvisioningService
{
    public function __construct(
        protected FundingSettings $settings,
        protected PaystackService $paystack,
        protected BudPayService $budpay,
        protected KoraService $kora,
        protected SquadService $squad,
    ) {
    }

    public function ensureCustomers(User $user): array
    {
        $settings = $this->settings->identity();
        $result = [
            'paystack' => false,
            'budpay' => false,
            'squad' => false,
        ];

        if (! $user->ev && $settings['force_email_verification']) {
            return $result;
        }

        if ($settings['auto_create_paystack_customer']) {
            $result['paystack'] = $this->ensurePaystackCustomer($user);
        }

        if ($settings['auto_create_budpay_customer']) {
            $result['budpay'] = $this->ensureBudPayCustomer($user);
        }

        if ($settings['auto_prepare_squad_customer']) {
            $result['squad'] = $this->ensureSquadCustomer($user);
        }

        return $result;
    }

    public function ensureDedicatedAccounts(User $user): array
    {
        $settings = $this->settings->identity();
        $result = [
            'paystack_account' => false,
            'budpay_account' => false,
            'kora_account' => false,
        ];

        if ($settings['require_identity_for_accounts'] && ! $user->hasLockedIdentity()) {
            return $result;
        }

        $this->ensureCustomers($user);
        $user->refresh();

        if ($settings['auto_generate_paystack_account']) {
            $result['paystack_account'] = $this->ensurePaystackDedicatedAccount($user);
        }

        if ($settings['auto_generate_budpay_account']) {
            $result['budpay_account'] = $this->ensureBudPayDedicatedAccount($user);
        }

        if ($settings['auto_generate_kora_account']) {
            $result['kora_account'] = $this->ensureKoraDedicatedAccount($user);
        }

        return $result;
    }

    public function ensurePaystackCustomer(User $user): bool
    {
        if (filled($user->psid) && filled($user->paystackcode)) {
            return true;
        }

        if (! filled($this->paystack->secretKey())) {
            return false;
        }

        try {
            $response = $this->paystack->createCustomer([
                'email' => $user->email,
                'first_name' => $user->firstname ?: $user->name,
                'last_name' => $user->lastname ?: 'User',
                'phone' => $user->mobile,
            ]);
            $body = $response->json() ?? [];

            if (! $response->successful() || ! data_get($body, 'status')) {
                Log::warning('Paystack customer provisioning failed.', ['user_id' => $user->id, 'response' => $body]);
                return false;
            }

            $user->forceFill([
                'paystackcode' => data_get($body, 'data.customer_code'),
                'psid' => data_get($body, 'data.id'),
                'pslinked' => true,
            ])->save();

            return true;
        } catch (\Throwable $e) {
            Log::warning('Paystack customer provisioning crashed.', ['user_id' => $user->id, 'error' => $e->getMessage()]);
            return false;
        }
    }

    public function ensurePaystackDedicatedAccount(User $user): bool
    {
        if (filled($user->aNo1) && filled($user->aN1)) {
            return true;
        }

        if (! filled($user->psid) && ! $this->ensurePaystackCustomer($user)) {
            return false;
        }

        try {
            $response = $this->paystack->createDedicatedAccount([
                'customer' => $user->psid,
                'preferred_bank' => 'wema-bank',
                'split_code' => config('services.paystack.split_code', ''),
            ]);
            $body = $response->json() ?? [];

            if (! $response->successful() || ! data_get($body, 'status')) {
                Log::warning('Paystack dedicated account provisioning failed.', ['user_id' => $user->id, 'response' => $body]);
                return false;
            }

            $user->forceFill([
                'bN1' => data_get($body, 'data.bank.name'),
                'aN1' => data_get($body, 'data.account_name'),
                'aNo1' => (string) data_get($body, 'data.account_number'),
                'aNid1' => (string) data_get($body, 'data.id'),
                'pslinked' => true,
                'psverified' => 1,
            ])->save();

            return true;
        } catch (\Throwable $e) {
            Log::warning('Paystack dedicated account provisioning crashed.', ['user_id' => $user->id, 'error' => $e->getMessage()]);
            return false;
        }
    }

    public function ensureBudPayCustomer(User $user): bool
    {
        if (filled($user->budpay_customer_code)) {
            return true;
        }

        if (! filled($this->budpay->secretKey())) {
            return false;
        }

        try {
            $response = $this->budpay->createCustomer([
                'email' => $user->email,
                'first_name' => $user->firstname ?: $user->name,
                'last_name' => $user->lastname ?: 'User',
                'phone' => $user->mobile,
            ]);
            $body = $response->json() ?? [];

            if (! $response->successful() || ! data_get($body, 'status')) {
                Log::warning('BudPay customer provisioning failed.', ['user_id' => $user->id, 'response' => $body]);
                return false;
            }

            $user->forceFill([
                'budpay_customer_code' => data_get($body, 'data.customer_code'),
                'budpay_customer_id' => (string) data_get($body, 'data.id'),
                'budpay_linked' => true,
            ])->save();

            return true;
        } catch (\Throwable $e) {
            Log::warning('BudPay customer provisioning crashed.', ['user_id' => $user->id, 'error' => $e->getMessage()]);
            return false;
        }
    }

    public function ensureBudPayDedicatedAccount(User $user): bool
    {
        if ($this->hasBudPayAccount($user)) {
            return true;
        }

        if (! filled($user->budpay_customer_code) && ! $this->ensureBudPayCustomer($user)) {
            return false;
        }

        try {
            $response = $this->budpay->createDedicatedVirtualAccount([
                'customer' => $user->budpay_customer_code,
            ]);
            $body = $response->json() ?? [];

            if (! $response->successful() || ! data_get($body, 'status')) {
                Log::warning('BudPay dedicated account provisioning failed.', ['user_id' => $user->id, 'response' => $body]);
                return false;
            }

            $user->forceFill([
                'bN2' => data_get($body, 'data.bank.name'),
                'aN2' => data_get($body, 'data.account_name'),
                'aNo2' => (string) data_get($body, 'data.account_number'),
                'aNid2' => (string) data_get($body, 'data.id'),
                'budpay_virtual_account_id' => (string) data_get($body, 'data.id'),
                'budpay_virtual_account_reference' => (string) data_get($body, 'data.reference'),
                'budpay_linked' => true,
                'budpay_verified' => 1,
            ])->save();

            return true;
        } catch (\Throwable $e) {
            Log::warning('BudPay dedicated account provisioning crashed.', ['user_id' => $user->id, 'error' => $e->getMessage()]);
            return false;
        }
    }

    public function ensureKoraDedicatedAccount(User $user): bool
    {
        if ($this->hasKoraAccount($user)) {
            return true;
        }

        $settings = $this->settings->identity();
        $secretKey = (string) ($settings['kora_secret_key'] ?? '');

        if (! filled($secretKey)) {
            return false;
        }

        try {
            $accountReference = 'KVA-'.$user->id.'-'.date('ymdHis');
            $response = $this->kora->createVirtualBankAccount([
                'account_reference' => $accountReference,
                'permanent' => true,
                'bank_code' => (string) ($settings['kora_virtual_account_bank_code'] ?: '035'),
                'account_name' => trim($user->fullname) ?: $user->email,
                'customer' => [
                    'name' => trim($user->fullname) ?: $user->email,
                    'email' => $user->email,
                ],
                'kyc' => array_filter([
                    'bvn' => $user->BVN,
                    'nin' => $user->NIN,
                ]),
            ], $secretKey);
            $body = $response->json() ?? [];

            if (! $response->successful() || ! data_get($body, 'status')) {
                Log::warning('Kora virtual account provisioning failed.', ['user_id' => $user->id, 'response' => $body]);
                return false;
            }

            $user->forceFill([
                'bN3' => data_get($body, 'data.bank_name', 'Kora Virtual Account'),
                'aN3' => data_get($body, 'data.account_name', trim($user->fullname) ?: $user->email),
                'aNo3' => (string) data_get($body, 'data.account_number'),
                'kora_account_reference' => (string) data_get($body, 'data.account_reference', $accountReference),
                'kora_virtual_account_id' => (string) data_get($body, 'data.unique_id', data_get($body, 'data.id')),
                'kora_bank_code' => (string) data_get($body, 'data.bank_code', $settings['kora_virtual_account_bank_code']),
                'kora_linked' => true,
                'kora_verified' => 1,
            ])->save();

            return true;
        } catch (\Throwable $e) {
            Log::warning('Kora virtual account provisioning crashed.', ['user_id' => $user->id, 'error' => $e->getMessage()]);
            return false;
        }
    }

    public function prepareSquadCustomerReference(User $user): bool
    {
        if (filled($user->squad_customer_reference)) {
            return true;
        }

        $reference = 'SQUAD-'.$user->id.'-'.strtoupper(substr(md5($user->email), 0, 8));
        $user->forceFill([
            'squad_customer_reference' => $reference,
            'squad_customer_status' => 'prepared',
        ])->save();

        return true;
    }

    public function ensureSquadCustomer(User $user): bool
    {
        $this->prepareSquadCustomerReference($user);

        if ((string) $user->squad_customer_status === 'provisioned') {
            return true;
        }

        if (! $user->hasLockedIdentity() || blank($user->BVN)) {
            return true;
        }

        if (! filled(config('services.squad.secret_key'))) {
            return false;
        }

        try {
            $response = $this->squad->createVirtualAccount(array_filter([
                'customer_identifier' => $user->squad_customer_reference,
                'first_name' => $user->firstname,
                'last_name' => $user->lastname,
                'middle_name' => $user->identity_middle_name ?: 'N/A',
                'mobile_num' => $user->mobile,
                'dob' => optional($user->identity_date_of_birth)->format('m/d/Y'),
                'email' => $user->email,
                'bvn' => $user->BVN,
                'gender' => strtolower((string) $user->identity_gender) === 'female' ? '2' : '1',
                'address' => data_get((array) $user->address, 'address'),
            ]));
            $body = $response->json() ?? [];

            if (! $response->successful() || ! data_get($body, 'success')) {
                Log::warning('Squad customer provisioning failed.', ['user_id' => $user->id, 'response' => $body]);
                return false;
            }

            $user->forceFill([
                'squad_customer_status' => 'provisioned',
            ])->save();

            return true;
        } catch (\Throwable $e) {
            Log::warning('Squad customer provisioning crashed.', ['user_id' => $user->id, 'error' => $e->getMessage()]);
            return false;
        }
    }

    protected function hasBudPayAccount(User $user): bool
    {
        if (filled($user->budpay_virtual_account_id) || (int) $user->budpay_verified === 1) {
            return filled($user->aNo2) && filled($user->aN2);
        }

        $bankName = strtolower((string) $user->bN2);
        $accountName = strtolower((string) $user->aN2);

        if (str_contains($bankName, 'paystack') || str_contains($bankName, 'titan')) {
            return false;
        }

        if (str_contains($accountName, 'paystack')) {
            return false;
        }

        return false;
    }

    protected function hasKoraAccount(User $user): bool
    {
        if (filled($user->kora_virtual_account_id) || (int) $user->kora_verified === 1) {
            return filled($user->aNo3) && filled($user->aN3);
        }

        return false;
    }
}
