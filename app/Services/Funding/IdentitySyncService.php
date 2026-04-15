<?php

namespace App\Services\Funding;

use App\Models\Deposit;
use App\Models\User;
use App\Services\KoraService;
use Carbon\Carbon;
use RuntimeException;

class IdentitySyncService
{
    public function __construct(
        protected FundingSettings $settings,
        protected KoraService $kora,
        protected CustomerProvisioningService $provisioning,
    ) {
    }

    public function minimumFundingAmount(): float
    {
        return $this->settings->kyc()['minimum_funded_amount'];
    }

    public function fundedAmount(User $user): float
    {
        return (float) Deposit::where('user_id', $user->id)->where('status', 1)->sum('amount');
    }

    public function userCanStartKyc(User $user): bool
    {
        return $this->fundedAmount($user) >= $this->minimumFundingAmount();
    }

    public function verifyAndSync(User $user, ?string $bvn, ?string $nin): array
    {
        if (! $this->userCanStartKyc($user)) {
            throw new RuntimeException('You need at least ₦'.number_format($this->minimumFundingAmount(), 2).' in successful deposits before starting KYC.');
        }

        if ($user->hasLockedIdentity()) {
            throw new RuntimeException('Your profile is already locked to verified identity data.');
        }

        if (! filled($bvn) && ! filled($nin)) {
            throw new RuntimeException('Provide either a BVN or NIN to continue.');
        }

        $identitySettings = $this->settings->identity();
        $secretKey = (string) ($identitySettings['kora_secret_key'] ?? '');

        if (! filled($secretKey)) {
            throw new RuntimeException('Kora identity verification is not configured yet.');
        }

        $source = filled($nin) ? 'nin' : 'bvn';
        $response = filled($nin)
            ? $this->kora->verifyNin((string) $nin, $secretKey)
            : $this->kora->verifyBvn((string) $bvn, $secretKey);

        $body = $response->json() ?? [];

        if (! $response->successful() || ! data_get($body, 'status')) {
            throw new RuntimeException((string) data_get($body, 'message', 'Identity verification failed.'));
        }

        $data = (array) data_get($body, 'data', []);
        $address = (array) data_get($data, 'address', []);
        $currentAddress = (array) ($user->address ? (array) $user->address : []);
        $phone = (string) data_get($data, 'phone_number', '');

        $user->forceFill([
            'firstname' => data_get($data, 'first_name', $user->firstname),
            'lastname' => data_get($data, 'last_name', $user->lastname),
            'identity_middle_name' => data_get($data, 'middle_name'),
            'identity_gender' => data_get($data, 'gender'),
            'identity_date_of_birth' => data_get($data, 'date_of_birth') ? Carbon::parse((string) data_get($data, 'date_of_birth'))->toDateString() : null,
            'BVN' => filled($bvn) ? $bvn : ($user->BVN ?: data_get($data, 'id')),
            'NIN' => filled($nin) ? $nin : $user->NIN,
            'identity_source' => $source,
            'identity_payload' => $data,
            'identity_verified_at' => now(),
            'identity_locked_at' => $identitySettings['lock_profile_after_identity'] ? now() : null,
            'kyc' => 1,
            'address' => [
                'address' => data_get($address, 'street', data_get($currentAddress, 'address', '')),
                'state' => data_get($address, 'state', data_get($currentAddress, 'state', '')),
                'zip' => data_get($currentAddress, 'zip', ''),
                'country' => data_get($currentAddress, 'country', 'Nigeria'),
                'city' => data_get($address, 'town', data_get($currentAddress, 'city', '')),
            ],
        ])->save();

        if (blank($user->mobile) && filled($phone)) {
            $user->forceFill(['mobile' => $phone])->save();
        }

        $this->provisioning->ensureCustomers($user->refresh());
        $accounts = $this->provisioning->ensureDedicatedAccounts($user->refresh());

        return [
            'source' => $source,
            'payload' => $data,
            'accounts' => $accounts,
        ];
    }
}
