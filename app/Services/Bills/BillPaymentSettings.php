<?php

namespace App\Services\Bills;

use App\Models\GeneralSetting;
use Carbon\Carbon;

class BillPaymentSettings
{
    public const PROVIDERS = ['budpay', 'squad'];

    public const SERVICES = ['airtime', 'data', 'tv', 'electricity'];

    public function general(): GeneralSetting
    {
        return GeneralSetting::query()->firstOrFail();
    }

    public function values(?GeneralSetting $general = null): array
    {
        $general ??= $this->general();
        $savedSettings = $general->bill_payment_settings ?? [];
        $savedProviders = $savedSettings['providers'] ?? [];
        $savedServiceProviders = $general->bill_payment_service_providers ?? [];
        $defaultProvider = in_array($general->bill_payment_default_provider, self::PROVIDERS, true)
            ? $general->bill_payment_default_provider
            : 'budpay';

        $serviceProviders = [];
        foreach (self::SERVICES as $service) {
            $serviceProviders[$service] = $this->normalizeServiceProvider(
                $service,
                (string) ($savedServiceProviders[$service] ?? $defaultProvider),
                $defaultProvider
            );
        }

        return [
            'enabled' => $general->bill_payment_enabled ?? true,
            'default_provider' => $defaultProvider,
            'service_providers' => $serviceProviders,
            'auto_sync_enabled' => $general->bill_payment_auto_sync_enabled ?? true,
            'auto_sync_hours' => max(1, (int) ($general->bill_payment_auto_sync_hours ?: 8)),
            'last_synced_at' => $general->bill_payment_catalog_last_synced_at instanceof Carbon
                ? $general->bill_payment_catalog_last_synced_at
                : ($general->bill_payment_catalog_last_synced_at ? Carbon::parse($general->bill_payment_catalog_last_synced_at) : null),
            'providers' => [
                'budpay' => [
                    'enabled' => (bool) data_get($savedProviders, 'budpay.enabled', filled(config('services.budpay.secret_key')) && filled(config('services.budpay.public_key'))),
                    'base_url' => (string) data_get($savedProviders, 'budpay.base_url', config('services.budpay.base_url')),
                    'secret_key' => (string) data_get($savedProviders, 'budpay.secret_key', config('services.budpay.secret_key')),
                    'public_key' => (string) data_get($savedProviders, 'budpay.public_key', config('services.budpay.public_key')),
                ],
                'squad' => [
                    'enabled' => (bool) data_get($savedProviders, 'squad.enabled', filled(config('services.squad.secret_key'))),
                    'base_url' => (string) data_get($savedProviders, 'squad.base_url', config('services.squad.base_url')),
                    'secret_key' => (string) data_get($savedProviders, 'squad.secret_key', config('services.squad.secret_key')),
                    'merchant_id' => (string) data_get($savedProviders, 'squad.merchant_id', config('services.squad.merchant_id')),
                ],
            ],
        ];
    }

    public function providerLabels(): array
    {
        return [
            'budpay' => 'BudPay',
            'squad' => 'Squad',
        ];
    }

    public function serviceLabels(): array
    {
        return [
            'airtime' => 'Airtime',
            'data' => 'Data',
            'tv' => 'Cable TV',
            'electricity' => 'Electricity',
        ];
    }

    public function providerChoicesFor(string $service): array
    {
        $labels = $this->providerLabels();

        return match ($service) {
            'tv' => ['budpay' => $labels['budpay']],
            default => $labels,
        };
    }

    public function normalizeServiceProvider(string $service, string $provider, string $defaultProvider = 'budpay'): string
    {
        $choices = array_keys($this->providerChoicesFor($service));

        if (in_array($provider, $choices, true)) {
            return $provider;
        }

        return in_array($defaultProvider, $choices, true) ? $defaultProvider : $choices[0];
    }
}
