<?php

namespace App\Services\LocalTransfers;

use App\Services\LocalTransfers\Providers\BudPayLocalTransferProvider;
use App\Services\LocalTransfers\Providers\KoraLocalTransferProvider;
use App\Services\LocalTransfers\Providers\PaystackLocalTransferProvider;
use App\Services\LocalTransfers\Providers\SquadLocalTransferProvider;
use RuntimeException;

class LocalTransferManager
{
    protected array $providers;
    protected array $fallbackBanks = [
        ['name' => 'Access Bank', 'code' => '044'],
        ['name' => 'Citibank Nigeria', 'code' => '023'],
        ['name' => 'Ecobank Nigeria', 'code' => '050'],
        ['name' => 'Fidelity Bank', 'code' => '070'],
        ['name' => 'First Bank of Nigeria', 'code' => '011'],
        ['name' => 'First City Monument Bank', 'code' => '214'],
        ['name' => 'Globus Bank', 'code' => '00103'],
        ['name' => 'Guaranty Trust Bank', 'code' => '058'],
        ['name' => 'Jaiz Bank', 'code' => '301'],
        ['name' => 'Keystone Bank', 'code' => '082'],
        ['name' => 'Kuda Bank', 'code' => '50211'],
        ['name' => 'Moniepoint Microfinance Bank', 'code' => '50515'],
        ['name' => 'Opay', 'code' => '999992'],
        ['name' => 'Palmpay', 'code' => '999991'],
        ['name' => 'Parallex Bank', 'code' => '526'],
        ['name' => 'Polaris Bank', 'code' => '076'],
        ['name' => 'Providus Bank', 'code' => '101'],
        ['name' => 'Stanbic IBTC Bank', 'code' => '221'],
        ['name' => 'Sterling Bank', 'code' => '232'],
        ['name' => 'Suntrust Bank', 'code' => '100'],
        ['name' => 'Titan Trust Bank', 'code' => '102'],
        ['name' => 'Union Bank of Nigeria', 'code' => '032'],
        ['name' => 'United Bank For Africa', 'code' => '033'],
        ['name' => 'Unity Bank', 'code' => '215'],
        ['name' => 'Wema Bank', 'code' => '035'],
        ['name' => 'Zenith Bank', 'code' => '057'],
    ];

    public function __construct(
        protected LocalTransferSettings $settings,
        PaystackLocalTransferProvider $paystack,
        KoraLocalTransferProvider $kora,
        SquadLocalTransferProvider $squad,
        BudPayLocalTransferProvider $budpay,
    ) {
        $this->providers = [
            'paystack' => $paystack,
            'kora' => $kora,
            'squad' => $squad,
            'budpay' => $budpay,
        ];
    }

    public function banks(?array $config = null): array
    {
        $config ??= $this->settings->values();
        $directoryProvider = $config['directory_provider'];
        $order = array_unique(array_merge([$directoryProvider], $config['resolve_order']));

        foreach ($order as $providerKey) {
            $provider = $this->providers[$providerKey] ?? null;
            $providerSettings = $config['providers'][$providerKey] ?? [];

            if (! $provider || ! $provider->canListBanks() || ! $provider->isConfigured($providerSettings)) {
                continue;
            }

            try {
                return $provider->listBanks($providerSettings);
            } catch (\Throwable) {
                continue;
            }
        }

        return $this->fallbackBanks;
    }

    public function resolve(array $bank, string $accountNumber, ?array $config = null): array
    {
        $config ??= $this->settings->values();
        $errors = [];

        foreach ($config['resolve_order'] as $providerKey) {
            $provider = $this->providers[$providerKey] ?? null;
            $providerSettings = $config['providers'][$providerKey] ?? [];

            if (! $provider || ! $provider->isConfigured($providerSettings)) {
                continue;
            }

            try {
                $resolved = $provider->resolveAccount($bank, $accountNumber, $providerSettings);
                $resolved['resolved_by'] = $providerKey;
                $resolved['bank_name'] = $resolved['bank_name'] ?: ($bank['name'] ?? '');
                $resolved['bank_code'] = $resolved['bank_code'] ?: ($bank['code'] ?? '');

                return $resolved;
            } catch (\Throwable $exception) {
                $errors[] = $provider->label().': '.$exception->getMessage();
            }
        }

        throw new RuntimeException($errors !== [] ? implode(' ', $errors) : 'No live account resolve provider is configured yet. Please add at least one provider key in admin settings.');
    }

    public function transfer(array $payload, ?array $config = null): array
    {
        $config ??= $this->settings->values();
        $errors = [];

        foreach ($config['transfer_order'] as $providerKey) {
            $provider = $this->providers[$providerKey] ?? null;
            $providerSettings = $config['providers'][$providerKey] ?? [];

            if (! $provider || ! $provider->isConfigured($providerSettings)) {
                continue;
            }

            try {
                $response = $provider->transfer($payload, $providerSettings);

                if (($response['status'] ?? 'failed') !== 'failed') {
                    return $response;
                }

                $errors[] = $provider->label().': '.($response['message'] ?? 'Transfer failed.');
            } catch (\Throwable $exception) {
                $errors[] = $provider->label().': '.$exception->getMessage();
            }
        }

        return [
            'status' => 'failed',
            'message' => $errors !== [] ? implode(' ', $errors) : 'No transfer provider is configured right now.',
            'provider' => null,
            'meta' => [
                'attempts' => $errors,
            ],
        ];
    }
}
