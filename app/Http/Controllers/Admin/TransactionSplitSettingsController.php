<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Transaction Split Settings Controller
 * Manages admin settings for transaction split feature
 */
class TransactionSplitSettingsController extends Controller
{
    /**
     * Display transaction split settings
     */
    public function index()
    {
        $pageTitle = 'Transaction Split Settings';
        $general = gs();

        $settings = [
            'enabled' => (bool) $general->transaction_split_enabled ?? true,
            'threshold' => (float) $general->transaction_split_threshold ?? 10000,
            'description' => $general->transaction_split_description ?? 'Transaction split is automatically applied to transfers exceeding the threshold amount.',
        ];

        return view('admin.settings.transaction-split', compact(
            'pageTitle',
            'settings',
            'general'
        ));
    }

    /**
     * Update transaction split settings
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'transaction_split_enabled' => ['required', 'boolean'],
            'transaction_split_threshold' => ['required', 'numeric', 'min:100', 'max:9999999'],
            'transaction_split_description' => ['nullable', 'string', 'max:1000'],
        ]);

        DB::beginTransaction();

        try {
            $general = gs();

            $general->transaction_split_enabled = $validated['transaction_split_enabled'];
            $general->transaction_split_threshold = round((float) $validated['transaction_split_threshold'], 2);
            $general->transaction_split_description = $validated['transaction_split_description'] ?? $general->transaction_split_description;

            $general->save();

            DB::commit();

            $notify[] = ['success', 'Transaction split settings updated successfully.'];
            return back()->withNotify($notify);

        } catch (\Throwable $exception) {
            DB::rollBack();

            $notify[] = ['error', 'Failed to update settings: ' . $exception->getMessage()];
            return back()->withNotify($notify);
        }
    }

    /**
     * Test transaction split calculation
     */
    public function testSplit(Request $request)
    {
        $request->validate([
            'amount' => ['required', 'numeric', 'min:1'],
        ]);

        $amount = (float) $request->input('amount');
        $general = gs();
        $threshold = (float) $general->transaction_split_threshold ?? 10000;

        $chunks = [];
        $remaining = $amount;

        while ($remaining > 0) {
            if ($remaining > $threshold) {
                $chunks[] = round($threshold, 2);
                $remaining = round($remaining - $threshold, 2);
            } else {
                $chunks[] = round($remaining, 2);
                $remaining = 0;
            }
        }

        return response()->json([
            'success' => true,
            'data' => [
                'original_amount' => round($amount, 2),
                'threshold' => round($threshold, 2),
                'chunks' => $chunks,
                'chunk_count' => count($chunks),
                'requires_split' => $amount > $threshold,
            ],
        ]);
    }
}
