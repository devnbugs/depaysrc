<?php

namespace App\Services\LocalTransfers;

use App\Models\GeneralSetting;

class LocalTransferSettings
{
    public const PROVIDERS = ['paystack', 'kora', 'squad', 'budpay'];

    public function general(): GeneralSetting
    {
        return GeneralSetting::firstOrFail();
    }

    public function values(?GeneralSetting $general = null): array
    {
        $general ??= $this->general();
        $savedSettings = $general->local_transfer_settings ?? [];
        $savedProviders = $savedSettings['providers'] ?? [];

        return [
            'enabled' => (bool) $general->local_transfer_enabled,
            'require_pin' => (bool) $general->local_transfer_require_pin,
            'minimum' => $general->local_transfer_min !== null ? (float) $general->local_transfer_min : 100,
            'maximum' => $general->local_transfer_max !== null ? (float) $general->local_transfer_max : 1000000,
            'directory_provider' => $general->local_transfer_directory_provider ?: 'paystack',
            'resolve_order' => $this->normalizeOrder($general->local_transfer_resolve_order ?? self::PROVIDERS),
            'transfer_order' => $this->normalizeOrder($general->local_transfer_transfer_order ?? self::PROVIDERS),
            'providers' => [
                'paystack' => [
                    'enabled' => (bool) data_get($savedProviders, 'paystack.enabled', filled(config('services.paystack.secret_key'))),
                    'base_url' => (string) data_get($savedProviders, 'paystack.base_url', config('services.paystack.base_url')),
                    'secret_key' => (string) data_get($savedProviders, 'paystack.secret_key', config('services.paystack.secret_key')),
                ],
                'kora' => [
                    'enabled' => (bool) data_get($savedProviders, 'kora.enabled', filled(config('services.kora.secret_key'))),
                    'base_url' => (string) data_get($savedProviders, 'kora.base_url', config('services.kora.base_url')),
                    'secret_key' => (string) data_get($savedProviders, 'kora.secret_key', config('services.kora.secret_key')),
                ],
                'squad' => [
                    'enabled' => (bool) data_get($savedProviders, 'squad.enabled', filled(config('services.squad.secret_key'))),
                    'base_url' => (string) data_get($savedProviders, 'squad.base_url', config('services.squad.base_url')),
                    'secret_key' => (string) data_get($savedProviders, 'squad.secret_key', config('services.squad.secret_key')),
                    'merchant_id' => (string) data_get($savedProviders, 'squad.merchant_id', config('services.squad.merchant_id')),
                ],
                'budpay' => [
                    'enabled' => (bool) data_get($savedProviders, 'budpay.enabled', filled(config('services.budpay.secret_key'))),
                    'base_url' => (string) data_get($savedProviders, 'budpay.base_url', config('services.budpay.base_url')),
                    'secret_key' => (string) data_get($savedProviders, 'budpay.secret_key', config('services.budpay.secret_key')),
                    'public_key' => (string) data_get($savedProviders, 'budpay.public_key', config('services.budpay.public_key')),
                ],
            ],
        ];
    }

    public function providerLabels(): array
    {
        return [
            'paystack' => 'Paystack',
            'kora' => 'Kora',
            'squad' => 'Squad',
            'budpay' => 'BudPay',
        ];
    }

    public function normalizeOrder(array $order): array
    {
        $order = array_values(array_filter($order, fn ($provider) => in_array($provider, self::PROVIDERS, true)));

        foreach (self::PROVIDERS as $provider) {
            if (! in_array($provider, $order, true)) {
                $order[] = $provider;
            }
        }

        return $order;
    }
}
