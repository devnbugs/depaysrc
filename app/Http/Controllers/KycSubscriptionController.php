<?php

namespace App\Http\Controllers;

use App\Models\KycPlan;
use App\Services\Kyc\KycSubscriptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class KycSubscriptionController extends Controller
{
    protected string $activeTemplate;

    public function __construct(protected KycSubscriptionService $kycSubscriptions)
    {
        $this->activeTemplate = activeTemplate();
    }

    public function index()
    {
        $this->kycSubscriptions->syncCatalog();

        $pageTitle = 'KYC & Verification Services';
        $user = Auth::user();
        $activePlan = $this->kycSubscriptions->planForUser($user);
        $enabledPlans = $this->kycSubscriptions->plans(true);
        $enabledServices = $this->kycSubscriptions->servicesForUser($user);
        $hasActiveAccess = $this->kycSubscriptions->hasActiveAccess($user);
        $canManageBilling = filled($user->kyc_paystack_subscription_code);
        $fundedAmount = $this->kycSubscriptions->fundedAmount($user);
        $minimumFundingAmount = $this->kycSubscriptions->settings()['minimum_funded_amount'];
        $isFundingEligible = $fundedAmount >= $minimumFundingAmount;

        return view($this->activeTemplate.'user.kyc.index', compact(
            'pageTitle',
            'user',
            'activePlan',
            'enabledPlans',
            'enabledServices',
            'hasActiveAccess',
            'canManageBilling',
            'fundedAmount',
            'minimumFundingAmount',
            'isFundingEligible',
        ));
    }

    public function upgrade()
    {
        $this->kycSubscriptions->syncCatalog();

        $pageTitle = 'KYC Services Upgrade';
        $user = Auth::user();
        $plans = $this->kycSubscriptions->plans(true);
        $activePlan = $this->kycSubscriptions->planForUser($user);
        $settings = $this->kycSubscriptions->settings();
        $fundedAmount = $this->kycSubscriptions->fundedAmount($user);
        $minimumFundingAmount = $settings['minimum_funded_amount'];
        $isFundingEligible = $fundedAmount >= $minimumFundingAmount;

        return view($this->activeTemplate.'user.kyc.upgrade', compact('pageTitle', 'user', 'plans', 'activePlan', 'settings', 'fundedAmount', 'minimumFundingAmount', 'isFundingEligible'));
    }

    public function subscribe(Request $request)
    {
        $this->kycSubscriptions->syncCatalog();

        $validated = $request->validate([
            'plan' => ['required', 'string', 'exists:kyc_plans,key'],
        ]);

        $plan = KycPlan::query()->where('key', $validated['plan'])->where('enabled', true)->firstOrFail();
        $user = Auth::user();

        try {
            $checkout = $this->kycSubscriptions->initializeCheckout($user, $plan);

            return redirect()->away((string) data_get($checkout, 'authorization_url'));
        } catch (\Throwable $e) {
            Log::warning('Unable to initialize KYC Paystack subscription.', [
                'user_id' => $user->id,
                'plan' => $plan->key,
                'error' => $e->getMessage(),
            ]);

            return back()->withNotify([['error', $e->getMessage()]]);
        }
    }

    public function callback(Request $request)
    {
        $reference = (string) ($request->query('reference') ?: $request->query('trxref'));

        if (! filled($reference)) {
            return redirect()->route('user.kyc.upgrade')->withNotify([['error', 'Missing Paystack transaction reference.']]);
        }

        try {
            $user = $this->kycSubscriptions->verifyAndApply($reference);

            if (! $user) {
                return redirect()->route('user.kyc.services')->withNotify([['warning', 'Payment was received, but the subscription is still awaiting confirmation. Refresh shortly.']]);
            }

            return redirect()->route('user.kyc.services')->withNotify([['success', 'KYC subscription activated successfully.']]);
        } catch (\Throwable $e) {
            Log::warning('Unable to verify KYC Paystack subscription callback.', [
                'reference' => $reference,
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('user.kyc.upgrade')->withNotify([['error', $e->getMessage()]]);
        }
    }

    public function manage()
    {
        $user = Auth::user();
        $url = $this->kycSubscriptions->managementLink($user);

        if (! $url) {
            return back()->withNotify([['error', 'Subscription management link is not available yet.']]);
        }

        return redirect()->away($url);
    }
}
