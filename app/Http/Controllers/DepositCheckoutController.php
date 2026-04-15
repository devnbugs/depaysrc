<?php

namespace App\Http\Controllers;

use App\Services\Funding\DepositCheckoutService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DepositCheckoutController extends Controller
{
    protected string $activeTemplate;

    public function __construct(protected DepositCheckoutService $checkouts)
    {
        $this->activeTemplate = activeTemplate();
    }

    public function startKora(Request $request)
    {
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:100'],
        ]);

        try {
            $checkout = $this->checkouts->initializeKora(Auth::user(), (float) $validated['amount']);

            return redirect()->away($checkout['checkout_url']);
        } catch (\Throwable $e) {
            return back()->withNotify([['error', $e->getMessage()]]);
        }
    }

    public function initializeQuickteller(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:100'],
        ]);

        try {
            return response()->json([
                'success' => true,
                'data' => $this->checkouts->quicktellerPayload(Auth::user(), (float) $validated['amount']),
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function handleKoraCallback(Request $request)
    {
        $reference = (string) $request->query('reference', '');

        if (! filled($reference)) {
            return redirect()->route('user.deposit')->withNotify([['error', 'Missing Kora payment reference.']]);
        }

        try {
            $result = $this->checkouts->confirmKora($reference);

            if ($result['status'] !== 'success') {
                return redirect()->route('user.deposit')->withNotify([['warning', 'Kora payment is still pending confirmation.']]);
            }

            return redirect()->route('user.deposit')->withNotify([['success', 'Wallet funded successfully with Kora.']]);
        } catch (\Throwable $e) {
            Log::warning('Kora deposit verification failed.', ['reference' => $reference, 'error' => $e->getMessage()]);

            return redirect()->route('user.deposit')->withNotify([['error', $e->getMessage()]]);
        }
    }

    public function handleQuicktellerCallback(Request $request)
    {
        $reference = (string) ($request->query('txn_ref') ?: $request->query('txnRef') ?: $request->query('reference'));

        if (! filled($reference)) {
            return redirect()->route('user.deposit')->withNotify([['error', 'Missing Quickteller transaction reference.']]);
        }

        try {
            $result = $this->checkouts->confirmQuickteller($reference);

            if ($result['status'] !== 'success') {
                return redirect()->route('user.deposit')->withNotify([['warning', 'Quickteller payment is still pending confirmation.']]);
            }

            return redirect()->route('user.deposit')->withNotify([['success', 'Wallet funded successfully with Quickteller.']]);
        } catch (\Throwable $e) {
            Log::warning('Quickteller deposit verification failed.', ['reference' => $reference, 'error' => $e->getMessage()]);

            return redirect()->route('user.deposit')->withNotify([['error', $e->getMessage()]]);
        }
    }
}
