<?php

namespace App\Http\Controllers\Gateway\Mollie;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Gateway\PaymentController;
use App\Models\Deposit;
use App\Models\GeneralSetting;
use Mollie\Api\MollieApiClient;

class ProcessController extends Controller
{
    public static function process($deposit)
    {
        $basic = GeneralSetting::first();
        $mollieAcc = json_decode($deposit->gatewayCurrency()->gateway_parameter);

        $mollie = new MollieApiClient();
        $mollie->setApiKey(trim($mollieAcc->api_key));

        $payment = $mollie->payments->create([
            'amount' => [
                'currency' => $deposit->method_currency,
                'value' => number_format((float) round($deposit->final_amo, 2), 2, '.', ''),
            ],
            'description' => "Payment To $basic->sitename Account",
            'redirectUrl' => route('ipn.'.$deposit->gateway->alias),
            'metadata' => [
                'order_id' => $deposit->trx,
            ],
        ]);

        $payment = $mollie->payments->get($payment->id);

        session()->put('payment_id', $payment->id);
        session()->put('deposit_id', $deposit->id);

        return json_encode([
            'redirect' => true,
            'redirect_url' => $payment->getCheckoutUrl(),
        ]);
    }

    public function ipn()
    {
        $deposit_id = session()->get('deposit_id');
        if ($deposit_id === null) {
            return redirect()->route('home');
        }

        $deposit = Deposit::where('id', $deposit_id)->where('status', 0)->first();
        if (! $deposit) {
            session()->forget('deposit_id');
            session()->forget('payment_id');

            $notify[] = ['error', 'Invalid request.'];
            return redirect()->route('home')->withNotify($notify);
        }

        $mollieAcc = json_decode($deposit->gatewayCurrency()->gateway_parameter);
        $mollie = new MollieApiClient();
        $mollie->setApiKey(trim($mollieAcc->api_key));

        $payment = $mollie->payments->get(session()->get('payment_id'));
        $deposit->detail = $payment->details;
        $deposit->save();

        if ($payment->status === 'paid') {
            PaymentController::userDataUpdate($deposit->trx);
            $notify[] = ['success', 'Transaction was successful.'];
            return redirect()->route(gatewayRedirectUrl(true))->withNotify($notify);
        }

        session()->forget('deposit_id');
        session()->forget('payment_id');

        $notify[] = ['error', 'Invalid request.'];
        return redirect()->route(gatewayRedirectUrl())->withNotify($notify);
    }
}
