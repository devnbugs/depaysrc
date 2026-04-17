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
        
        // Use ONLY the configured resolution provider without fallback
        if (! isset($config['resolve_order']) || empty($config['resolve_order'])) {
            throw new RuntimeException('No account resolution provider is configured.');
        }

        $primaryProvider = $config['resolve_order'][0] ?? null;
        if (! $primaryProvider) {
            throw new RuntimeException('No primary account resolution provider is configured.');
        }

        $provider = $this->providers[$primaryProvider] ?? null;
        $providerSettings = $config['providers'][$primaryProvider] ?? [];

        if (! $provider) {
            throw new RuntimeException('Account resolution provider "'.$primaryProvider.'" is not available.');
        }

        if (! $provider->isConfigured($providerSettings)) {
            throw new RuntimeException('Account resolution provider "'.$primaryProvider.'" is not properly configured.');
        }

        try {
            $resolved = $provider->resolveAccount($bank, $accountNumber, $providerSettings);
            $resolved['resolved_by'] = $primaryProvider;
            $resolved['bank_name'] = $resolved['bank_name'] ?: ($bank['name'] ?? '');
            $resolved['bank_code'] = $resolved['bank_code'] ?: ($bank['code'] ?? '');

            return $resolved;
        } catch (\Throwable $exception) {
            throw new RuntimeException($provider->label().': '.$exception->getMessage());
        }
    }

    public function transfer(array $payload, ?array $config = null): array
    {
        $config ??= $this->settings->values();
        
        // Use ONLY the configured transfer provider without fallback
        if (! isset($config['transfer_order']) || empty($config['transfer_order'])) {
            return [
                'status' => 'failed',
                'message' => 'No transfer provider is configured. Please configure at least one provider in settings.',
                'provider' => null,
                'meta' => [],
            ];
        }

        $primaryProvider = $config['transfer_order'][0] ?? null;
        if (! $primaryProvider) {
            return [
                'status' => 'failed',
                'message' => 'No primary transfer provider is configured.',
                'provider' => null,
                'meta' => [],
            ];
        }

        $provider = $this->providers[$primaryProvider] ?? null;
        $providerSettings = $config['providers'][$primaryProvider] ?? [];

        if (! $provider) {
            return [
                'status' => 'failed',
                'message' => 'Transfer provider "'.$primaryProvider.'" is not available.',
                'provider' => null,
                'meta' => [],
            ];
        }

        if (! $provider->isConfigured($providerSettings)) {
            return [
                'status' => 'failed',
                'message' => 'Transfer provider "'.$primaryProvider.'" is not properly configured.',
                'provider' => null,
                'meta' => [],
            ];
        }

        try {
            $response = $provider->transfer($payload, $providerSettings);
            
            if (! is_array($response)) {
                return [
                    'status' => 'failed',
                    'message' => 'Transfer provider returned invalid response.',
                    'provider' => $primaryProvider,
                    'meta' => [],
                ];
            }

            return $response;
        } catch (\Throwable $exception) {
            return [
                'status' => 'failed',
                'message' => $exception->getMessage() ?: 'Transfer failed.',
                'provider' => $primaryProvider,
                'meta' => [
                    'error' => $exception->getMessage(),
                ],
            ];
        }
    }
}
