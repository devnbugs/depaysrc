<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KycPlan;
use App\Models\KycService;
use App\Services\Kyc\KycSubscriptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class KycServicesController extends Controller
{
    public function __construct(protected KycSubscriptionService $kycSubscriptions)
    {
    }

    public function index()
    {
        $this->kycSubscriptions->syncCatalog();

        $pageTitle = 'KYC Services Management';
        $general = $this->kycSubscriptions->general();
        $settings = $this->kycSubscriptions->settings($general);
        $plans = $this->kycSubscriptions->plans();
        $groupedServices = $this->kycSubscriptions->groupedServices();
        $planLevels = [
            'kyc_basic' => 'Basic',
            'kyc_premium' => 'Premium',
            'kyc_enterprise' => 'Enterprise',
        ];

        return view('admin.kyc.services', compact('pageTitle', 'general', 'settings', 'plans', 'groupedServices', 'planLevels'));
    }

    public function update(Request $request)
    {
        $this->kycSubscriptions->syncCatalog();

        $validated = $request->validate([
            'kyc_subscription_currency' => ['required', 'string', 'size:3'],
            'kyc_subscription_interval' => ['required', 'in:hourly,daily,weekly,monthly,quarterly,biannually,annually'],
            'kyc_subscription_reference_prefix' => ['required', 'string', 'max:20'],
            'kyc_minimum_funded_amount' => ['required', 'numeric', 'min:0'],
            'plans' => ['required', 'array', 'min:1'],
            'services' => ['required', 'array', 'min:1'],
        ]);

        foreach ($request->input('plans', []) as $planId => $planData) {
            validator($planData, [
                'name' => ['required', 'string', 'max:100'],
                'description' => ['nullable', 'string', 'max:1000'],
                'price' => ['required', 'numeric', 'min:0'],
                'monthly_limit' => ['required', 'numeric', 'min:0'],
                'sort_order' => ['required', 'integer', 'min:0'],
                'paystack_interval' => ['required', 'in:hourly,daily,weekly,monthly,quarterly,biannually,annually'],
                'paystack_currency' => ['required', 'string', 'size:3'],
                'invoice_limit' => ['nullable', 'integer', 'min:0'],
            ])->validate();
        }

        foreach ($request->input('services', []) as $serviceId => $serviceData) {
            validator($serviceData, [
                'price' => ['required', 'numeric', 'min:0'],
                'minimum_plan' => ['required', 'in:kyc_basic,kyc_premium,kyc_enterprise'],
            ])->validate();
        }

        $general = $this->kycSubscriptions->general();
        $general->kyc_subscription_enabled = $request->boolean('kyc_subscription_enabled');
        $general->kyc_subscription_settings = [
            'currency' => strtoupper($validated['kyc_subscription_currency']),
            'interval' => $validated['kyc_subscription_interval'],
            'sync_plans' => $request->boolean('kyc_subscription_sync_plans'),
            'reference_prefix' => strtoupper(trim($validated['kyc_subscription_reference_prefix'])),
            'minimum_funded_amount' => (float) $validated['kyc_minimum_funded_amount'],
        ];
        $general->save();

        foreach ($request->input('plans', []) as $planId => $planData) {
            $plan = KycPlan::query()->find($planId);

            if (! $plan) {
                continue;
            }

            $plan->forceFill([
                'name' => trim((string) $planData['name']),
                'description' => trim((string) ($planData['description'] ?? '')),
                'price' => $planData['price'],
                'monthly_limit' => $planData['monthly_limit'],
                'sort_order' => $planData['sort_order'],
                'badge' => trim((string) ($planData['badge'] ?? '')) ?: null,
                'enabled' => array_key_exists('enabled', $planData),
                'paystack_plan_code' => trim((string) ($planData['paystack_plan_code'] ?? '')) ?: null,
                'paystack_plan_name' => trim((string) ($planData['paystack_plan_name'] ?? '')) ?: null,
                'paystack_interval' => $planData['paystack_interval'],
                'paystack_currency' => strtoupper((string) $planData['paystack_currency']),
                'invoice_limit' => (int) ($planData['invoice_limit'] ?? 0),
                'features' => collect(preg_split('/\r\n|\r|\n/', (string) ($planData['features'] ?? '')))
                    ->map(fn ($item) => trim($item))
                    ->filter()
                    ->values()
                    ->all(),
            ])->save();
        }

        foreach ($request->input('services', []) as $serviceId => $serviceData) {
            $service = KycService::query()->find($serviceId);

            if (! $service) {
                continue;
            }

            $service->forceFill([
                'price' => $serviceData['price'],
                'enabled' => array_key_exists('enabled', $serviceData),
                'minimum_plan' => $serviceData['minimum_plan'],
            ])->save();
        }

        Cache::forget('general-setting');

        $notify = [['success', 'KYC subscription settings updated successfully.']];
        $settings = $this->kycSubscriptions->settings($general->refresh());

        if ($general->kyc_subscription_enabled && $settings['sync_plans']) {
            foreach (KycPlan::query()->where('enabled', true)->orderBy('sort_order')->get() as $plan) {
                try {
                    $this->kycSubscriptions->syncPlanToPaystack($plan, $settings);
                } catch (\Throwable $e) {
                    Log::warning('Failed to sync KYC plan to Paystack.', [
                        'plan_id' => $plan->id,
                        'plan_key' => $plan->key,
                        'error' => $e->getMessage(),
                    ]);

                    $notify[] = ['warning', 'Saved settings, but Paystack sync failed for '.$plan->name.'. '.$e->getMessage()];
                }
            }
        }

        return back()->withNotify($notify);
    }
}
