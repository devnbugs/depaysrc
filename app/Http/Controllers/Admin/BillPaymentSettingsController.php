<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Bills\BillPaymentManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class BillPaymentSettingsController extends Controller
{
    public function __construct(protected BillPaymentManager $billPayments)
    {
    }

    public function index()
    {
        $pageTitle = 'Bill Payment Settings';
        $general = $this->billPayments->general();
        $settings = $this->billPayments->settings($general);
        $providers = $this->billPayments->providerLabels();
        $services = $this->billPayments->serviceLabels();
        $serviceChoices = collect(array_keys($services))
            ->mapWithKeys(fn ($service) => [$service => $this->billPayments->providerChoicesFor($service)])
            ->all();

        return view('admin.bills.settings', compact('pageTitle', 'general', 'settings', 'providers', 'services', 'serviceChoices'));
    }

    public function update(Request $request)
    {
        $providers = array_keys($this->billPayments->providerLabels());

        $validated = $request->validate([
            'bill_payment_default_provider' => ['required', 'in:'.implode(',', $providers)],
            'bill_payment_auto_sync_hours' => ['required', 'integer', 'min:1', 'max:168'],
        ]);

        $serviceProviders = [];
        foreach (array_keys($this->billPayments->serviceLabels()) as $service) {
            $choices = array_keys($this->billPayments->providerChoicesFor($service));
            $serviceProviders[$service] = $request->validate([
                "bill_payment_service_providers.$service" => ['required', 'in:'.implode(',', $choices)],
            ])["bill_payment_service_providers"][$service];
        }

        $general = $this->billPayments->general();
        $general->bill_payment_enabled = $request->boolean('bill_payment_enabled');
        $general->bill_payment_default_provider = $validated['bill_payment_default_provider'];
        $general->bill_payment_service_providers = $serviceProviders;
        $general->bill_payment_auto_sync_enabled = $request->boolean('bill_payment_auto_sync_enabled');
        $general->bill_payment_auto_sync_hours = $validated['bill_payment_auto_sync_hours'];
        $general->bill_payment_settings = [
            'providers' => [
                'budpay' => [
                    'enabled' => $request->boolean('providers.budpay.enabled'),
                    'base_url' => trim((string) $request->input('providers.budpay.base_url')),
                    'secret_key' => trim((string) $request->input('providers.budpay.secret_key')),
                    'public_key' => trim((string) $request->input('providers.budpay.public_key')),
                ],
                'squad' => [
                    'enabled' => $request->boolean('providers.squad.enabled'),
                    'base_url' => trim((string) $request->input('providers.squad.base_url')),
                    'secret_key' => trim((string) $request->input('providers.squad.secret_key')),
                    'merchant_id' => trim((string) $request->input('providers.squad.merchant_id')),
                ],
            ],
        ];
        $general->save();

        Cache::forget('general-setting');

        return back()->withNotify([['success', 'Bill payment settings updated successfully.']]);
    }

    public function sync()
    {
        $result = $this->billPayments->syncCatalog(true);
        $notify = [];

        foreach ($result['results'] ?? [] as $service => $payload) {
            $level = ($payload['status'] ?? null) === 'synced' ? 'success' : 'warning';
            $notify[] = [$level, ucfirst($service).': '.($payload['message'] ?? 'Sync completed.')];
        }

        if ($notify === []) {
            $notify[] = ['warning', 'No bill catalogs were synced.'];
        }

        return back()->withNotify($notify);
    }
}
