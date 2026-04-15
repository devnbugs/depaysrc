<?php

namespace App\Services\Kyc;

use App\Models\GeneralSetting;
use App\Models\KycPlan;
use App\Models\KycService;
use App\Models\User;
use App\Services\PaystackService;
use App\Support\Kyc\KycCatalog;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use RuntimeException;

class KycSubscriptionService
{
    public function __construct(protected PaystackService $paystackService)
    {
    }

    public function general(): GeneralSetting
    {
        return GeneralSetting::firstOrFail();
    }

    public function settings(?GeneralSetting $general = null): array
    {
        $general ??= $this->general();
        $stored = $general->kyc_subscription_settings ?? [];

        return [
            'enabled' => (bool) $general->kyc_subscription_enabled,
            'currency' => strtoupper((string) data_get($stored, 'currency', 'NGN')),
            'interval' => (string) data_get($stored, 'interval', 'monthly'),
            'sync_plans' => (bool) data_get($stored, 'sync_plans', true),
            'reference_prefix' => strtoupper(trim((string) data_get($stored, 'reference_prefix', 'KYC-SUB'))),
            'minimum_funded_amount' => (float) data_get($stored, 'minimum_funded_amount', 500),
            'paystack_public_key' => (string) config('services.paystack.public_key', ''),
            'paystack_secret_configured' => filled($this->paystackService->secretKey()),
        ];
    }

    public function fundedAmount(User $user): float
    {
        return (float) $user->deposits()->where('status', 1)->sum('amount');
    }

    public function eligibleForUpgrade(User $user): bool
    {
        return $this->fundedAmount($user) >= $this->settings()['minimum_funded_amount'];
    }

    public function assertEligibleForUpgrade(User $user): void
    {
        $settings = $this->settings();

        if ($this->fundedAmount($user) < $settings['minimum_funded_amount']) {
            throw new RuntimeException('You need at least ₦'.number_format((float) $settings['minimum_funded_amount'], 2).' in successful deposits before starting KYC.');
        }
    }

    public function syncCatalog(): void
    {
        if (! Schema::hasTable('kyc_plans') || ! Schema::hasTable('kyc_services')) {
            return;
        }

        foreach (KycCatalog::plans() as $plan) {
            KycPlan::firstOrCreate(['key' => $plan['key']], $plan);
        }

        foreach (KycCatalog::services() as $service) {
            KycService::firstOrCreate(['service_id' => $service['service_id']], $service);
        }
    }

    public function plans(bool $enabledOnly = false): Collection
    {
        $query = KycPlan::query()->orderBy('sort_order')->orderBy('id');

        if ($enabledOnly) {
            $query->where('enabled', true);
        }

        return $query->get();
    }

    public function services(): Collection
    {
        return KycService::query()
            ->orderByRaw("FIELD(provider, 'korapay', 'interswitch')")
            ->orderBy('name')
            ->get();
    }

    public function groupedServices(): Collection
    {
        return $this->services()->groupBy('provider');
    }

    public function planForUser(User $user): ?KycPlan
    {
        if (! filled($user->kyc_plan)) {
            return null;
        }

        return KycPlan::query()->where('key', $user->kyc_plan)->first();
    }

    public function hasActiveAccess(User $user): bool
    {
        if (! $user->is_kyc_upgraded || ! filled($user->kyc_plan)) {
            return false;
        }

        return ! in_array((string) $user->kyc_subscription_status, ['cancelled', 'completed', 'disabled'], true);
    }

    public function servicesForUser(User $user): Collection
    {
        $plan = $this->planForUser($user);

        if (! $plan) {
            return collect();
        }

        $currentLevel = KycCatalog::levelFor($plan->key);

        return $this->services()
            ->filter(fn (KycService $service) => $service->enabled && KycCatalog::levelFor($service->minimum_plan) <= $currentLevel)
            ->values();
    }

    public function syncPlanToPaystack(KycPlan $plan, ?array $settings = null): KycPlan
    {
        $settings ??= $this->settings();

        if (! $settings['paystack_secret_configured']) {
            throw new RuntimeException('Paystack secret key is not configured.');
        }

        $payload = [
            'name' => $plan->paystack_plan_name ?: $plan->name,
            'amount' => (int) round(((float) $plan->price) * 100),
            'interval' => $plan->paystack_interval ?: $settings['interval'],
            'invoice_limit' => (int) ($plan->invoice_limit ?? 0),
        ];

        $response = $plan->paystack_plan_code
            ? $this->paystackService->updatePlan($plan->paystack_plan_code, $payload)
            : $this->paystackService->createPlan($payload);

        $body = $response->json();

        if (! $response->successful() || ! data_get($body, 'status')) {
            throw new RuntimeException((string) data_get($body, 'message', 'Unable to sync plan with Paystack.'));
        }

        $plan->forceFill([
            'paystack_plan_code' => data_get($body, 'data.plan_code', $plan->paystack_plan_code),
            'paystack_plan_id' => data_get($body, 'data.id', $plan->paystack_plan_id),
            'paystack_plan_name' => data_get($body, 'data.name', $payload['name']),
            'paystack_interval' => data_get($body, 'data.interval', $payload['interval']),
            'paystack_currency' => data_get($body, 'data.currency', $plan->paystack_currency ?: $settings['currency']),
            'paystack_last_synced_at' => now(),
        ])->save();

        return $plan->refresh();
    }

    public function initializeCheckout(User $user, KycPlan $plan): array
    {
        $settings = $this->settings();

        if (! $settings['enabled']) {
            throw new RuntimeException('KYC subscriptions are currently disabled.');
        }

        $this->assertEligibleForUpgrade($user);

        $plan = $settings['sync_plans'] ? $this->syncPlanToPaystack($plan, $settings) : $plan;

        if (! filled($plan->paystack_plan_code)) {
            throw new RuntimeException('This plan is missing a Paystack plan code. Please contact support.');
        }

        $reference = $settings['reference_prefix'].'-'.strtoupper(Str::random(12));
        $response = $this->paystackService->initializeTransaction([
            'email' => $user->email,
            'amount' => (int) round(((float) $plan->price) * 100),
            'plan' => $plan->paystack_plan_code,
            'reference' => $reference,
            'callback_url' => route('user.kyc.upgrade.callback'),
            'metadata' => [
                'purpose' => 'kyc_subscription',
                'user_id' => $user->id,
                'kyc_plan_key' => $plan->key,
            ],
        ]);

        $body = $response->json();

        if (! $response->successful() || ! data_get($body, 'status')) {
            throw new RuntimeException((string) data_get($body, 'message', 'Unable to initialize Paystack checkout.'));
        }

        $user->forceFill([
            'kyc_pending_plan' => $plan->key,
            'kyc_paystack_reference' => $reference,
            'kyc_subscription_status' => 'pending',
            'kyc_subscription_channel' => 'paystack',
            'kyc_paystack_plan_code' => $plan->paystack_plan_code,
        ])->save();

        return (array) data_get($body, 'data', []);
    }

    public function verifyAndApply(string $reference): ?User
    {
        $response = $this->paystackService->verifyTransaction($reference);
        $body = $response->json();

        if (! $response->successful() || ! data_get($body, 'status')) {
            throw new RuntimeException((string) data_get($body, 'message', 'Unable to verify Paystack transaction.'));
        }

        $payload = (array) data_get($body, 'data', []);

        if ((string) data_get($payload, 'status') !== 'success' || ! $this->isKycSubscriptionPayload($payload)) {
            return null;
        }

        return $this->markChargeSuccessful($payload);
    }

    public function isKycSubscriptionPayload(array $payload): bool
    {
        if ((string) data_get($payload, 'metadata.purpose') === 'kyc_subscription') {
            return true;
        }

        $subscriptionCode = (string) (data_get($payload, 'subscription.subscription_code') ?: data_get($payload, 'subscription_code'));
        if ($subscriptionCode !== '' && User::query()->where('kyc_paystack_subscription_code', $subscriptionCode)->exists()) {
            return true;
        }

        $planCode = (string) (data_get($payload, 'plan.plan_code') ?: data_get($payload, 'plan'));
        if ($planCode !== '' && KycPlan::query()->where('paystack_plan_code', $planCode)->exists()) {
            return true;
        }

        $reference = (string) data_get($payload, 'reference', '');
        return $reference !== '' && str_starts_with($reference, $this->settings()['reference_prefix']);
    }

    public function markChargeSuccessful(array $payload): ?User
    {
        [$user, $plan] = $this->resolveContext($payload);

        if (! $user || ! $plan) {
            return null;
        }

        $subscriptionCode = (string) (data_get($payload, 'subscription.subscription_code') ?: data_get($payload, 'subscription_code'));
        $status = (string) (data_get($payload, 'subscription.status') ?: data_get($payload, 'status') ?: 'active');

        $user->forceFill([
            'is_kyc_upgraded' => true,
            'kyc_plan' => $plan->key,
            'kyc_pending_plan' => null,
            'kyc_upgrade_date' => $user->kyc_upgrade_date ?: now(),
            'kyc_expiry_date' => null,
            'kyc_monthly_limit' => $plan->monthly_limit,
            'kyc_paystack_reference' => (string) data_get($payload, 'reference', $user->kyc_paystack_reference),
            'kyc_paystack_plan_code' => $plan->paystack_plan_code,
            'kyc_paystack_subscription_code' => $subscriptionCode ?: $user->kyc_paystack_subscription_code,
            'kyc_paystack_email_token' => (string) data_get($payload, 'email_token', $user->kyc_paystack_email_token),
            'kyc_subscription_status' => $status,
            'kyc_subscription_channel' => 'paystack',
            'kyc_subscription_started_at' => $this->parseDate(data_get($payload, 'paid_at') ?: data_get($payload, 'created_at') ?: data_get($payload, 'createdAt')) ?: $user->kyc_subscription_started_at ?: now(),
            'kyc_subscription_next_payment_at' => $this->parseDate(data_get($payload, 'subscription.next_payment_date') ?: data_get($payload, 'next_payment_date')),
            'kyc_subscription_cancelled_at' => null,
            'kyc_subscription_last_payment_at' => $this->parseDate(data_get($payload, 'paid_at')) ?: now(),
            'kyc_subscription_last_invoice_code' => (string) data_get($payload, 'invoice.invoice_code', $user->kyc_subscription_last_invoice_code),
        ])->save();

        return $user->refresh();
    }

    public function recordSubscriptionCreated(array $payload): ?User
    {
        [$user, $plan] = $this->resolveContext($payload);

        if (! $user || ! $plan) {
            return null;
        }

        $user->forceFill([
            'is_kyc_upgraded' => true,
            'kyc_plan' => $plan->key,
            'kyc_pending_plan' => null,
            'kyc_upgrade_date' => $user->kyc_upgrade_date ?: now(),
            'kyc_monthly_limit' => $plan->monthly_limit,
            'kyc_paystack_plan_code' => $plan->paystack_plan_code,
            'kyc_paystack_subscription_code' => (string) data_get($payload, 'subscription_code', $user->kyc_paystack_subscription_code),
            'kyc_paystack_email_token' => (string) data_get($payload, 'email_token', $user->kyc_paystack_email_token),
            'kyc_subscription_status' => (string) data_get($payload, 'status', 'active'),
            'kyc_subscription_channel' => 'paystack',
            'kyc_subscription_started_at' => $this->parseDate(data_get($payload, 'createdAt') ?: data_get($payload, 'created_at')) ?: $user->kyc_subscription_started_at ?: now(),
            'kyc_subscription_next_payment_at' => $this->parseDate(data_get($payload, 'next_payment_date')),
            'kyc_subscription_cancelled_at' => null,
        ])->save();

        return $user->refresh();
    }

    public function recordInvoiceUpdated(array $payload): ?User
    {
        [$user, $plan] = $this->resolveContext($payload);

        if (! $user) {
            return null;
        }

        $status = (string) data_get($payload, 'status', $user->kyc_subscription_status ?: 'active');
        $paid = (bool) data_get($payload, 'paid', false);

        if ($paid) {
            $user->forceFill([
                'is_kyc_upgraded' => true,
                'kyc_plan' => $plan?->key ?: $user->kyc_plan,
                'kyc_monthly_limit' => $plan?->monthly_limit ?: $user->kyc_monthly_limit,
                'kyc_subscription_status' => $status === 'success' ? 'active' : $status,
                'kyc_subscription_last_payment_at' => $this->parseDate(data_get($payload, 'paid_at')) ?: now(),
                'kyc_subscription_last_invoice_code' => (string) data_get($payload, 'invoice_code', $user->kyc_subscription_last_invoice_code),
                'kyc_subscription_next_payment_at' => $this->parseDate(data_get($payload, 'next_payment_date') ?: data_get($payload, 'subscription.next_payment_date')),
            ])->save();
        } else {
            $user->forceFill([
                'kyc_subscription_status' => $status,
                'kyc_subscription_last_invoice_code' => (string) data_get($payload, 'invoice_code', $user->kyc_subscription_last_invoice_code),
            ])->save();
        }

        return $user->refresh();
    }

    public function recordInvoiceFailed(array $payload): ?User
    {
        [$user] = $this->resolveContext($payload);

        if (! $user) {
            return null;
        }

        $user->forceFill([
            'kyc_subscription_status' => 'attention',
            'kyc_subscription_last_invoice_code' => (string) data_get($payload, 'invoice_code', $user->kyc_subscription_last_invoice_code),
        ])->save();

        return $user->refresh();
    }

    public function recordSubscriptionDisabled(array $payload): ?User
    {
        [$user] = $this->resolveContext($payload);

        if (! $user) {
            return null;
        }

        $user->forceFill([
            'is_kyc_upgraded' => false,
            'kyc_expiry_date' => now(),
            'kyc_subscription_status' => (string) data_get($payload, 'status', 'cancelled'),
            'kyc_subscription_cancelled_at' => now(),
        ])->save();

        return $user->refresh();
    }

    public function recordSubscriptionNotRenewing(array $payload): ?User
    {
        [$user] = $this->resolveContext($payload);

        if (! $user) {
            return null;
        }

        $user->forceFill([
            'kyc_subscription_status' => 'non-renewing',
            'kyc_subscription_next_payment_at' => $this->parseDate(data_get($payload, 'next_payment_date') ?: data_get($payload, 'subscription.next_payment_date')),
        ])->save();

        return $user->refresh();
    }

    public function managementLink(User $user): ?string
    {
        if (! filled($user->kyc_paystack_subscription_code) || ! $this->settings()['paystack_secret_configured']) {
            return null;
        }

        try {
            $response = $this->paystackService->subscriptionManageLink($user->kyc_paystack_subscription_code);
            $body = $response->json();
        } catch (\Throwable) {
            return null;
        }

        if (! $response->successful() || ! data_get($body, 'status')) {
            return null;
        }

        return (string) data_get($body, 'data.link');
    }

    protected function resolveContext(array $payload): array
    {
        $user = $this->resolveUser($payload);
        $plan = $this->resolvePlan($payload, $user);

        return [$user, $plan];
    }

    protected function resolveUser(array $payload): ?User
    {
        $userId = data_get($payload, 'metadata.user_id');
        if ($userId && $user = User::query()->find($userId)) {
            return $user;
        }

        $reference = (string) data_get($payload, 'reference', '');
        if ($reference !== '' && $user = User::query()->where('kyc_paystack_reference', $reference)->first()) {
            return $user;
        }

        $subscriptionCode = (string) (data_get($payload, 'subscription.subscription_code') ?: data_get($payload, 'subscription_code'));
        if ($subscriptionCode !== '' && $user = User::query()->where('kyc_paystack_subscription_code', $subscriptionCode)->first()) {
            return $user;
        }

        $email = (string) (data_get($payload, 'customer.email') ?: data_get($payload, 'customer.customer_email'));
        if ($email !== '') {
            return User::query()->where('email', $email)->first();
        }

        return null;
    }

    protected function resolvePlan(array $payload, ?User $user = null): ?KycPlan
    {
        $planKey = (string) data_get($payload, 'metadata.kyc_plan_key', '');
        if ($planKey !== '' && $plan = KycPlan::query()->where('key', $planKey)->first()) {
            return $plan;
        }

        $planCode = (string) (data_get($payload, 'plan.plan_code') ?: data_get($payload, 'plan'));
        if ($planCode !== '' && $plan = KycPlan::query()->where('paystack_plan_code', $planCode)->first()) {
            return $plan;
        }

        if ($user && filled($user->kyc_pending_plan) && $plan = KycPlan::query()->where('key', $user->kyc_pending_plan)->first()) {
            return $plan;
        }

        if ($user && filled($user->kyc_plan)) {
            return KycPlan::query()->where('key', $user->kyc_plan)->first();
        }

        return null;
    }

    protected function parseDate(mixed $value): ?Carbon
    {
        if (! filled($value)) {
            return null;
        }

        try {
            return Carbon::parse($value);
        } catch (\Throwable) {
            return null;
        }
    }
}
