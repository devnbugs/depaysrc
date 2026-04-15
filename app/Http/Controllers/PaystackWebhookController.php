<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Paystack\Handle\Charge;
use App\Services\Kyc\KycSubscriptionService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class PaystackWebhookController extends Controller
{
    public function __construct(
        protected Charge $charge,
        protected KycSubscriptionService $kycSubscriptions,
    )
    {
    }

    public function handle(Request $request): JsonResponse
    {
        $signature = (string) $request->header('x-paystack-signature', '');
        $secret = (string) config('services.paystack.secret_key', '');
        $computedSignature = hash_hmac('sha512', $request->getContent(), $secret);

        if (! hash_equals($computedSignature, $signature)) {
            Log::warning('Invalid Paystack webhook signature received.');
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        $event = (string) $request->input('event');
        $data = (array) $request->input('data', []);

        return match ($event) {
            'charge.success' => $this->handleChargeSuccess($request, $data),
            'subscription.create' => $this->handleSubscriptionCreated($data),
            'subscription.not_renew' => $this->handleSubscriptionNotRenewing($data),
            'subscription.disable' => $this->handleSubscriptionDisabled($data),
            'invoice.update' => $this->handleInvoiceUpdated($data),
            'invoice.payment_failed' => $this->handleInvoiceFailed($data),
            default => $this->unhandledEvent($request),
        };
    }

    protected function handleChargeSuccess(Request $request, array $data): JsonResponse
    {
        if ($this->kycSubscriptions->isKycSubscriptionPayload($data)) {
            $this->kycSubscriptions->markChargeSuccessful($data);

            return response()->json(['message' => 'KYC subscription charge processed'], 200);
        }

        return $this->charge->Success($request);
    }

    protected function handleSubscriptionCreated(array $data): JsonResponse
    {
        $this->kycSubscriptions->recordSubscriptionCreated($data);

        return response()->json(['status' => 'ok'], 200);
    }

    protected function handleSubscriptionNotRenewing(array $data): JsonResponse
    {
        $this->kycSubscriptions->recordSubscriptionNotRenewing($data);

        return response()->json(['status' => 'ok'], 200);
    }

    protected function handleSubscriptionDisabled(array $data): JsonResponse
    {
        $this->kycSubscriptions->recordSubscriptionDisabled($data);

        return response()->json(['status' => 'ok'], 200);
    }

    protected function handleInvoiceUpdated(array $data): JsonResponse
    {
        $this->kycSubscriptions->recordInvoiceUpdated($data);

        return response()->json(['status' => 'ok'], 200);
    }

    protected function handleInvoiceFailed(array $data): JsonResponse
    {
        $this->kycSubscriptions->recordInvoiceFailed($data);

        return response()->json(['status' => 'ok'], 200);
    }

    protected function unhandledEvent(Request $request): JsonResponse
    {
        Log::info('Unhandled Paystack webhook event.', [
            'event' => $request->input('event'),
        ]);

        return response()->json(['status' => 'ok'], 200);
    }
}
