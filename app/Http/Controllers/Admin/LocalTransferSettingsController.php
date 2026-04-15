<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\LocalTransfers\LocalTransferSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class LocalTransferSettingsController extends Controller
{
    public function __construct(protected LocalTransferSettings $transferSettings)
    {
    }

    public function index()
    {
        $pageTitle = 'Local Transfer Settings';
        $general = $this->transferSettings->general();
        $settings = $this->transferSettings->values($general);
        $providers = $this->transferSettings->providerLabels();

        return view('admin.local-transfer.settings', compact('pageTitle', 'general', 'settings', 'providers'));
    }

    public function update(Request $request)
    {
        $providers = array_keys($this->transferSettings->providerLabels());

        $validated = $request->validate([
            'local_transfer_min' => ['nullable', 'numeric', 'min:0'],
            'local_transfer_max' => ['nullable', 'numeric', 'min:0'],
            'local_transfer_directory_provider' => ['required', 'in:'.implode(',', $providers)],
            'local_transfer_resolve_order' => ['required', 'array', 'min:1'],
            'local_transfer_resolve_order.*' => ['required', 'in:'.implode(',', $providers)],
            'local_transfer_transfer_order' => ['required', 'array', 'min:1'],
            'local_transfer_transfer_order.*' => ['required', 'in:'.implode(',', $providers)],
        ]);

        $general = $this->transferSettings->general();
        $general->local_transfer_enabled = $request->boolean('local_transfer_enabled');
        $general->local_transfer_require_pin = $request->boolean('local_transfer_require_pin');
        $general->local_transfer_min = $validated['local_transfer_min'] ?? 0;
        $general->local_transfer_max = $validated['local_transfer_max'] ?? 0;
        $general->local_transfer_directory_provider = $validated['local_transfer_directory_provider'];
        $general->local_transfer_resolve_order = $this->transferSettings->normalizeOrder($validated['local_transfer_resolve_order']);
        $general->local_transfer_transfer_order = $this->transferSettings->normalizeOrder($validated['local_transfer_transfer_order']);
        $general->local_transfer_settings = [
            'providers' => [
                'paystack' => [
                    'enabled' => $request->boolean('providers.paystack.enabled'),
                    'base_url' => trim((string) $request->input('providers.paystack.base_url')),
                    'secret_key' => trim((string) $request->input('providers.paystack.secret_key')),
                ],
                'kora' => [
                    'enabled' => $request->boolean('providers.kora.enabled'),
                    'base_url' => trim((string) $request->input('providers.kora.base_url')),
                    'secret_key' => trim((string) $request->input('providers.kora.secret_key')),
                ],
                'squad' => [
                    'enabled' => $request->boolean('providers.squad.enabled'),
                    'base_url' => trim((string) $request->input('providers.squad.base_url')),
                    'secret_key' => trim((string) $request->input('providers.squad.secret_key')),
                    'merchant_id' => trim((string) $request->input('providers.squad.merchant_id')),
                ],
                'budpay' => [
                    'enabled' => $request->boolean('providers.budpay.enabled'),
                    'base_url' => trim((string) $request->input('providers.budpay.base_url')),
                    'secret_key' => trim((string) $request->input('providers.budpay.secret_key')),
                    'public_key' => trim((string) $request->input('providers.budpay.public_key')),
                ],
            ],
        ];
        $general->save();

        Cache::forget('general-setting');

        return back()->withNotify([['success', 'Local transfer settings updated successfully.']]);
    }
}
