<?php

namespace App\Services\Bills;

use App\Models\Cabletvbundle;
use App\Models\GeneralSetting;
use App\Models\Internetbundle;
use App\Models\Network;
use App\Models\Power;
use App\Services\Bills\Providers\AbstractBillPaymentProvider;
use App\Services\Bills\Providers\BudPayBillPaymentProvider;
use App\Services\Bills\Providers\SquadBillPaymentProvider;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use RuntimeException;

class BillPaymentManager
{
    public function __construct(
        protected BillPaymentSettings $settingsManager,
        protected BudPayBillPaymentProvider $budpay,
        protected SquadBillPaymentProvider $squad,
    ) {
    }

    public function general(): GeneralSetting
    {
        return $this->settingsManager->general();
    }

    public function settings(?GeneralSetting $general = null): array
    {
        return $this->settingsManager->values($general);
    }

    public function providerLabels(): array
    {
        return $this->settingsManager->providerLabels();
    }

    public function serviceLabels(): array
    {
        return $this->settingsManager->serviceLabels();
    }

    public function providerChoicesFor(string $service): array
    {
        return $this->settingsManager->providerChoicesFor($service);
    }

    public function syncIfDue(): void
    {
        $settings = $this->settings();

        if (! $settings['enabled']) {
            return;
        }

        $lastSynced = $settings['last_synced_at'];
        $hours = $settings['auto_sync_hours'];
        $isDue = ! $lastSynced || $lastSynced->diffInHours(now()) >= $hours || $this->catalogLooksEmpty($settings);

        if (! $isDue) {
            return;
        }

        $this->syncCatalog(false);
    }

    public function syncCatalog(bool $force = true): array
    {
        $settings = $this->settings();
        $results = [];
        $synced = false;

        if (! $settings['enabled']) {
            return ['synced' => false, 'results' => ['general' => ['status' => 'skipped', 'message' => 'Bill payment module is disabled.']]];
        }

        if (! $force && ! $settings['auto_sync_enabled']) {
            return ['synced' => false, 'results' => ['general' => ['status' => 'skipped', 'message' => 'Auto sync is disabled.']]];
        }

        foreach (array_keys($this->serviceLabels()) as $service) {
            $method = 'sync'.Str::studly($service).'Catalog';

            try {
                $results[$service] = $this->{$method}($settings);
            } catch (\Throwable $e) {
                $results[$service] = [
                    'status' => 'failed',
                    'message' => $e->getMessage(),
                ];
            }

            if (($results[$service]['status'] ?? null) === 'synced') {
                $synced = true;
            }
        }

        if ($synced) {
            $general = $this->general();
            $general->bill_payment_catalog_last_synced_at = now();
            $general->save();
            Cache::forget('general-setting');
        }

        return ['synced' => $synced, 'results' => $results];
    }

    public function airtimeNetworks(): Collection
    {
        return Network::query()
            ->where('airtime', 1)
            ->where('status', 1)
            ->get()
            ->sortByDesc(fn (Network $network) => $this->airtimeNetworkPriority($network))
            ->unique(fn (Network $network) => $this->normalizeAirtimeNetworkName((string) $network->name))
            ->sortBy(fn (Network $network) => $this->normalizeAirtimeNetworkName((string) $network->name))
            ->values();
    }

    public function dataBundles(): Collection
    {
        $providerKey = $this->catalogProviderKey('data');

        $query = Internetbundle::query()
            ->where('status', 1)
            ->when($providerKey, fn ($query) => $query->where('providers', $providerKey))
            ->orderBy('network')
            ->orderBy('cost');
        $bundles = $query->get();

        if ($bundles->isNotEmpty()) {
            return $bundles;
        }

        return Internetbundle::query()
            ->where('status', 1)
            ->orderBy('network')
            ->orderBy('cost')
            ->get();
    }

    public function dataCatalog(): array
    {
        return $this->dataBundles()
            ->groupBy(fn (Internetbundle $bundle) => strtolower((string) ($bundle->networkcode ?: Str::slug($bundle->network))))
            ->map(function (Collection $plans, string $networkCode) {
                return [
                    'network' => (string) $plans->first()->network,
                    'network_code' => $networkCode,
                    'validities' => $plans
                        ->groupBy(fn (Internetbundle $bundle) => $this->normalizeValidity((string) $bundle->validity))
                        ->map(fn (Collection $items, string $validity) => [
                            'label' => $validity,
                            'plans' => $items->map(fn (Internetbundle $bundle) => [
                                'id' => (string) $bundle->plan,
                                'name' => (string) $bundle->name,
                                'amount' => (float) $bundle->cost,
                                'validity' => $this->normalizeValidity((string) $bundle->validity),
                                'category' => (string) ($bundle->datatype ?: 'Bundle'),
                            ])->values()->all(),
                        ])
                        ->values()
                        ->all(),
                ];
            })
            ->values()
            ->all();
    }

    public function cableBundles(): Collection
    {
        $providerKey = $this->catalogProviderKey('tv');

        $query = Cabletvbundle::query()
            ->where('status', 1)
            ->when(Schema::hasColumn('cabletvbundles', 'provider') && $providerKey, fn ($query) => $query->where('provider', $providerKey))
            ->orderBy('network')
            ->orderBy('cost');
        $bundles = $query->get();

        if ($bundles->isNotEmpty()) {
            return $bundles;
        }

        return Cabletvbundle::query()
            ->where('status', 1)
            ->orderBy('network')
            ->orderBy('cost')
            ->get();
    }

    public function electricityProviders(): Collection
    {
        $providerKey = $this->catalogProviderKey('electricity');

        $query = Power::query()
            ->where('status', 1)
            ->when(Schema::hasColumn('powers', 'provider') && $providerKey, fn ($query) => $query->where('provider', $providerKey))
            ->orderBy('name');
        $providers = $query->get();

        if ($providers->isNotEmpty()) {
            return $providers;
        }

        return Power::query()
            ->where('status', 1)
            ->orderBy('name')
            ->get();
    }

    public function purchaseAirtime(string $network, string $phone, float $amount): array
    {
        [$provider] = $this->providerForService('airtime');

        return $provider->purchaseAirtime([
            'network' => $network,
            'phone' => $phone,
            'amount' => $amount,
            'reference' => $this->reference('AIR'),
        ], $this->providerSettings($provider->key()));
    }

    public function purchaseData(Internetbundle $bundle, string $phone): array
    {
        [$provider] = $this->providerForService('data');

        return $provider->purchaseData([
            'network' => (string) ($bundle->networkcode ?: Str::slug($bundle->network)),
            'phone' => $phone,
            'amount' => (float) $bundle->cost,
            'plan_id' => (string) $bundle->plan,
            'reference' => $this->reference('DATA'),
        ], $this->providerSettings($provider->key()));
    }

    protected function normalizeAirtimeNetworkName(string $name): string
    {
        $normalized = strtoupper(trim(preg_replace('/\s+/', ' ', $name)));

        return match ($normalized) {
            'ETISALAT', '9 MOBILE', '9MOBILE' => '9MOBILE',
            default => $normalized,
        };
    }

    protected function airtimeNetworkPriority(Network $network): int
    {
        $score = 0;
        $name = $this->normalizeAirtimeNetworkName((string) $network->name);
        $type = strtolower(trim((string) ($network->type ?? '')));
        $symbol = strtolower(trim((string) ($network->symbol ?? '')));

        if ($type === 'network') {
            $score += 100;
        }

        if ($name === '9MOBILE' && $symbol === '9mobile') {
            $score += 50;
        }

        if ($symbol === Str::slug($name)) {
            $score += 25;
        }

        return $score;
    }

    public function validateTv(Cabletvbundle $bundle, string $number): array
    {
        [$provider] = $this->providerForService('tv');

        return $provider->validateTv([
            'provider' => (string) ($bundle->networkcode ?: $bundle->code ?: Str::slug($bundle->network)),
            'number' => $number,
        ], $this->providerSettings($provider->key()));
    }

    public function purchaseTv(Cabletvbundle $bundle, string $number): array
    {
        [$provider] = $this->providerForService('tv');

        return $provider->purchaseTv([
            'provider' => (string) ($bundle->networkcode ?: $bundle->code ?: Str::slug($bundle->network)),
            'number' => $number,
            'package_id' => (string) $bundle->plan,
            'reference' => $this->reference('TV'),
        ], $this->providerSettings($provider->key()));
    }

    public function validateElectricity(Power $power, string $type, string $number): array
    {
        [$provider] = $this->providerForService('electricity');

        return $provider->validateElectricity([
            'provider' => (string) ($power->billercode ?: $power->code ?: Str::slug($power->name)),
            'type' => strtolower($type),
            'number' => $number,
        ], $this->providerSettings($provider->key()));
    }

    public function purchaseElectricity(Power $power, string $type, string $number, float $amount, string $phone, string $email, ?string $validationReference = null): array
    {
        [$provider] = $this->providerForService('electricity');

        return $provider->purchaseElectricity([
            'provider' => (string) ($power->billercode ?: $power->code ?: Str::slug($power->name)),
            'type' => strtolower($type),
            'number' => $number,
            'amount' => $amount,
            'phone' => $phone,
            'email' => $email,
            'reference' => $this->reference('PWR'),
            'validation_reference' => $validationReference,
        ], $this->providerSettings($provider->key()));
    }

    protected function syncAirtimeCatalog(array $settings): array
    {
        [$provider, $providerKey] = $this->providerForService('airtime', $settings);
        $networks = $provider->listAirtimeProviders($this->providerSettings($providerKey, $settings));

        foreach ($networks as $network) {
            Network::query()->updateOrCreate(
                ['code' => strtolower((string) ($network['provider'] ?? $network['symbol'] ?? Str::slug($network['name'] ?? 'network')))],
                [
                    'name' => strtoupper((string) ($network['name'] ?? $network['provider'] ?? 'NETWORK')),
                    'symbol' => strtolower((string) ($network['symbol'] ?? $network['provider'] ?? Str::slug($network['name'] ?? 'network'))),
                    'type' => 'network',
                    'image' => (string) ($network['image'] ?? ''),
                    'min' => (float) ($network['min'] ?? 50),
                    'max' => (float) ($network['max'] ?? 500000),
                    'airtime' => 1,
                    'status' => 1,
                ]
            );
        }

        return [
            'status' => 'synced',
            'provider' => $providerKey,
            'count' => count($networks),
            'message' => 'Airtime providers refreshed successfully.',
        ];
    }

    protected function syncDataCatalog(array $settings): array
    {
        [$provider, $providerKey] = $this->providerForService('data', $settings);
        $networks = $provider->listDataProviders($this->providerSettings($providerKey, $settings));
        $bundleCount = 0;

        foreach ($networks as $network) {
            $networkCode = strtolower((string) ($network['provider'] ?? $network['symbol'] ?? Str::slug($network['name'] ?? 'network')));
            $networkName = strtoupper((string) ($network['name'] ?? $networkCode));

            Network::query()->updateOrCreate(
                ['code' => $networkCode],
                [
                    'name' => $networkName,
                    'symbol' => $networkCode,
                    'type' => 'network',
                    'image' => (string) ($network['image'] ?? ''),
                    'internet' => 1,
                    'status' => 1,
                ]
            );

            foreach ($provider->listDataPlans($networkCode, $this->providerSettings($providerKey, $settings)) as $plan) {
                Internetbundle::query()->updateOrCreate(
                    [
                        'providers' => $providerKey,
                        'networkcode' => $networkCode,
                        'plan' => (string) $plan['id'],
                    ],
                    [
                        'name' => (string) $plan['name'],
                        'datatype' => (string) ($plan['category'] ?: 'Bundle'),
                        'network' => $networkName,
                        'code' => (string) $plan['id'],
                        'validity' => $this->normalizeValidity((string) $plan['validity']),
                        'cost' => (float) $plan['amount'],
                        'image' => (string) ($plan['image'] ?? ''),
                        'status' => 1,
                    ]
                );

                $bundleCount++;
            }
        }

        return [
            'status' => 'synced',
            'provider' => $providerKey,
            'count' => $bundleCount,
            'message' => 'Data providers and plans refreshed successfully.',
        ];
    }

    protected function syncTvCatalog(array $settings): array
    {
        [$provider, $providerKey] = $this->providerForService('tv', $settings);
        $providers = $provider->listTvProviders($this->providerSettings($providerKey, $settings));
        $packageCount = 0;

        foreach ($providers as $tvProvider) {
            $providerCode = strtolower((string) ($tvProvider['provider'] ?? Str::slug($tvProvider['name'] ?? 'tv')));
            $providerName = strtoupper((string) ($tvProvider['name'] ?? $providerCode));

            Network::query()->updateOrCreate(
                ['code' => $providerCode],
                [
                    'name' => $providerName,
                    'symbol' => $providerCode,
                    'type' => 'tv',
                    'image' => (string) ($tvProvider['image'] ?? ''),
                    'tv' => 1,
                    'status' => 1,
                ]
            );

            foreach ($provider->listTvPackages($providerCode, $this->providerSettings($providerKey, $settings)) as $package) {
                Cabletvbundle::query()->updateOrCreate(
                    [
                        'provider' => $providerKey,
                        'networkcode' => $providerCode,
                        'plan' => (string) $package['id'],
                    ],
                    [
                        'name' => (string) $package['name'],
                        'network' => $providerName,
                        'code' => $providerCode,
                        'cost' => (float) $package['amount'],
                        'status' => 1,
                    ]
                );

                $packageCount++;
            }
        }

        return [
            'status' => 'synced',
            'provider' => $providerKey,
            'count' => $packageCount,
            'message' => 'Cable TV providers and plans refreshed successfully.',
        ];
    }

    protected function syncElectricityCatalog(array $settings): array
    {
        [$provider, $providerKey] = $this->providerForService('electricity', $settings);
        $providers = $provider->listElectricityProviders($this->providerSettings($providerKey, $settings));

        foreach ($providers as $item) {
            $providerCode = strtolower((string) ($item['provider'] ?? Str::slug($item['name'] ?? 'electricity')));
            $providerName = strtoupper((string) ($item['name'] ?? $providerCode));

            Power::query()->updateOrCreate(
                [
                    'provider' => $providerKey,
                    'billercode' => $providerCode,
                ],
                [
                    'name' => $providerName,
                    'symbol' => $providerCode,
                    'code' => $providerCode,
                    'type' => 'electricity',
                    'image' => (string) ($item['image'] ?? ''),
                    'status' => 1,
                ]
            );
        }

        return [
            'status' => 'synced',
            'provider' => $providerKey,
            'count' => count($providers),
            'message' => 'Electricity providers refreshed successfully.',
        ];
    }

    protected function providerForService(string $service, ?array $settings = null): array
    {
        $settings ??= $this->settings();
        $preferred = $settings['service_providers'][$service] ?? $settings['default_provider'];
        $candidates = array_unique(array_filter([$preferred, $settings['default_provider'], 'budpay', 'squad']));

        foreach ($candidates as $key) {
            $provider = $this->provider($key);
            if (! $provider || ! $provider->supports($service)) {
                continue;
            }

            if (! $provider->isConfigured($this->providerSettings($key, $settings))) {
                continue;
            }

            return [$provider, $key];
        }

        throw new RuntimeException('No configured bill payment provider is available for '.$this->serviceLabels()[$service].'.');
    }

    protected function provider(string $key): ?AbstractBillPaymentProvider
    {
        return match ($key) {
            'budpay' => $this->budpay,
            'squad' => $this->squad,
            default => null,
        };
    }

    protected function providerSettings(string $key, ?array $settings = null): array
    {
        $settings ??= $this->settings();

        return $settings['providers'][$key] ?? [];
    }

    protected function catalogProviderKey(string $service): ?string
    {
        $settings = $this->settings();

        return $settings['service_providers'][$service] ?? $settings['default_provider'] ?? null;
    }

    protected function catalogLooksEmpty(array $settings): bool
    {
        $dataProvider = $settings['service_providers']['data'] ?? $settings['default_provider'];
        $tvProvider = $settings['service_providers']['tv'] ?? $settings['default_provider'];
        $electricityProvider = $settings['service_providers']['electricity'] ?? $settings['default_provider'];

        return Network::query()->where('airtime', 1)->count() === 0
            || Internetbundle::query()->where('providers', $dataProvider)->count() === 0
            || Cabletvbundle::query()->when(Schema::hasColumn('cabletvbundles', 'provider'), fn ($query) => $query->where('provider', $tvProvider))->count() === 0
            || Power::query()->when(Schema::hasColumn('powers', 'provider'), fn ($query) => $query->where('provider', $electricityProvider))->count() === 0;
    }

    protected function normalizeValidity(string $value): string
    {
        $value = trim($value);

        if ($value === '') {
            return 'Other plans';
        }

        return preg_replace('/\s+/', ' ', $value) ?: 'Other plans';
    }

    protected function reference(string $prefix): string
    {
        return strtoupper($prefix).'-'.now()->format('YmdHis').'-'.strtoupper(Str::random(6));
    }
}
