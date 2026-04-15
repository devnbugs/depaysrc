<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Cabletvbundle;
use App\Models\Contact;
use App\Models\Extension;
use App\Models\GeneralSetting;
use App\Models\Internetbundle;
use App\Models\Network;
use App\Models\Power;
use App\Models\Transaction;
use App\Models\User;
use App\Services\Bills\BillPaymentManager;
use App\Services\VtpassService;
use App\Services\WalletService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class BillsController extends Controller
{
    public function __construct(
        protected WalletService $walletService,
        protected BillPaymentManager $billPayments,
        protected VtpassService $vtpassService,
    ) {
        $this->activeTemplate = activeTemplate();
    }

    public function airtime()
    {
        $this->safeSyncBillCatalog();

        $user = Auth::user();
        $pageTitle = 'Airtime Recharge';
        $network = $this->billPayments->airtimeNetworks();
        $bills = Bill::whereUserId($user->id)->whereType(1)->latest()->get();
        $contacts = Contact::all();
        $trxcount = Bill::whereUserId($user->id)->whereType(1)->count();

        return view($this->activeTemplate.'user.bills.airtime', compact(
            'pageTitle',
            'network',
            'bills',
            'user',
            'contacts',
            'trxcount'
        ));
    }

    public function airtimebuy(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'phone' => ['required', 'numeric'],
            'network' => ['required', 'string'],
            'amount' => ['required', 'numeric', 'min:50'],
        ]);

        // Validate security verification
        if ((int) $user->pin_state === 1) {
            $request->validate([
                'pin_code' => ['required', 'string', 'size:4'],
            ]);

            if (! hash_equals((string) $user->pin, (string) $request->pin_code)) {
                return back()->withAlertx([['error', 'Incorrect PIN.']]);
            }
        } elseif ((int) $user->two_factor_enabled === 1) {
            $request->validate([
                'authenticator_code' => ['required', 'string', 'size:6'],
            ]);

            $response = verifyG2fa($user, $request->authenticator_code);
            if (! $response) {
                return back()->withAlertx([['error', 'Invalid 2FA code. Please try again.']]);
            }
        }

        $amount = (float) $request->amount;
        if ($user->balance < $amount) {
            return back()->withAlertx([['error', 'You do not have enough balance.']]);
        }

        try {
            $result = $this->billPayments->purchaseAirtime((string) $request->network, (string) $request->phone, $amount);
        } catch (\Throwable $e) {
            $this->storeBill($user, [
                'amount' => $amount,
                'token' => getTrx(),
                'bundle' => 'VTU Airtime',
                'profit' => 0,
                'trx' => getTrx(),
                'phone' => $request->phone,
                'network' => strtoupper((string) $request->network),
                'newbalance' => $user->balance,
                'type' => 1,
                'status' => 0,
                'response' => ['message' => $e->getMessage()],
            ]);

            return back()->withAlertx([['error', $e->getMessage()]]);
        }

        if (($result['status'] ?? 'failed') === 'failed') {
            return back()->withAlertx([['error', $result['message'] ?? 'Purchase declined.']]);
        }

        $bill = $this->walletService->purchaseBill($user, $amount, 'VTU Airtime', $result['reference'] ?: getTrx(), [
            'token' => $result['reference'] ?: getTrx(),
            'debit_amount' => $amount,
            'charge' => 0,
            'profit' => 0,
            'phone' => $request->phone,
            'network' => strtoupper((string) $request->network),
            'type' => 1,
            'status' => ($result['status'] ?? 'success') === 'pending' ? 0 : 1,
            'response' => $result['meta'] ?? [],
            'transaction_details' => 'Airtime purchase for '.strtoupper((string) $request->network),
            'bywho' => ucfirst((string) data_get($result, 'provider', 'BudPay')),
        ]);
        $this->markBillGateway($bill, (string) data_get($result, 'provider', 'budpay'));

        session()->flash('receipt_url', route('user.beta.receipt', ['billId' => $bill->id]));

        return back()->withAlertx([['trxsuccess', $result['message'] ?? 'Airtime purchase completed successfully.']]);
    }

    public function internet()
    {
        $this->safeSyncBillCatalog();

        $user = Auth::user();
        $pageTitle = 'Buy Data';
        $bills = Bill::whereUserId($user->id)->whereType(2)->latest()->get();
        $trxcount = Bill::whereUserId($user->id)->whereType(2)->where('status', 1)->count();
        $contacts = Contact::all();
        $dataCatalog = $this->billPayments->dataCatalog();

        return view($this->activeTemplate.'user.bills.internet', compact(
            'pageTitle',
            'bills',
            'user',
            'contacts',
            'dataCatalog',
            'trxcount'
        ));
    }

    public function internetx()
    {
        return $this->internet();
    }

    public function loadinternet(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'phone' => ['required', 'string', 'min:11'],
            'network' => ['required', 'string'],
            'plan' => ['required'],
        ]);

        // Validate security verification
        if ((int) $user->pin_state === 1) {
            $request->validate([
                'pin_code' => ['required', 'string', 'size:4'],
            ]);

            if (! hash_equals((string) $user->pin, (string) $request->pin_code)) {
                return back()->withAlertx([['error', 'Incorrect PIN.']]);
            }
        } elseif ((int) $user->two_factor_enabled === 1) {
            $request->validate([
                'authenticator_code' => ['required', 'string', 'size:6'],
            ]);

            $response = verifyG2fa($user, $request->authenticator_code);
            if (! $response) {
                return back()->withAlertx([['error', 'Invalid 2FA code. Please try again.']]);
            }
        }

        $internet = Internetbundle::wherePlan($request->plan)
            ->where('status', 1)
            ->where('networkcode', strtolower((string) $request->network))
            ->first();
        if (! $internet) {
            return back()->withAlertx([['error', 'Invalid data plan selected.']]);
        }

        if ($user->balance < $internet->cost) {
            return back()->withAlertx([['error', 'Insufficient Balance.']]);
        }

        try {
            $result = $this->billPayments->purchaseData($internet, (string) $request->phone);
        } catch (\Throwable $e) {
            $this->storeBill($user, [
                'amount' => $internet->cost,
                'token' => getTrx(),
                'bundle' => $internet->name,
                'plan' => $internet->plan,
                'btype' => $internet->datatype,
                'validity' => $internet->validity,
                'profit' => 0,
                'trx' => getTrx(),
                'phone' => $request->phone,
                'network' => $internet->network,
                'newbalance' => $user->balance,
                'type' => 2,
                'status' => 0,
                'response' => ['message' => $e->getMessage()],
            ]);

            return back()->withAlertx([['error', $e->getMessage()]]);
        }

        $bill = $this->walletService->purchaseBill($user, $internet->cost, $internet->name, $result['reference'] ?: getTrx(), [
            'token' => $result['reference'] ?: getTrx(),
            'debit_amount' => $internet->cost,
            'charge' => 0,
            'profit' => 0,
            'phone' => $request->phone,
            'network' => $internet->network,
            'plan' => $internet->plan,
            'btype' => $internet->datatype,
            'validity' => $internet->validity,
            'type' => 2,
            'status' => ($result['status'] ?? 'success') === 'pending' ? 0 : 1,
            'response' => $result['meta'] ?? [],
            'transaction_details' => 'Data purchase for '.$internet->network,
            'bywho' => ucfirst((string) data_get($result, 'provider', 'BudPay')),
        ]);
        $this->markBillGateway($bill, (string) data_get($result, 'provider', $internet->providers ?: 'budpay'));

        session()->flash('receipt_url', route('user.beta.receipt', ['billId' => $bill->id]));

        return back()->withAlertx([['trxsuccess', $result['message'] ?? 'Data purchase successful']]);
    }

    public function queryData(Request $request)
    {
        $network = $request->input('network');
        $dataType = $request->input('dataType');

        $options = Internetbundle::where('network', $network)
            ->where('pxcode', $dataType)
            ->pluck('plan', 'name');

        return response()->json($options);
    }

    public function cabletv()
    {
        $this->safeSyncBillCatalog();

        $pageTitle = 'Cable TV Subscription';
        $user = Auth::user();
        $bills = Bill::whereUserId($user->id)->whereType(3)->latest()->get();
        $bill = $this->billPayments->cableBundles();
        $network = $bill->groupBy('network')->map(fn ($items) => (object) [
            'name' => (string) $items->first()->network,
            'code' => (string) ($items->first()->networkcode ?: $items->first()->code),
        ])->values();

        return view($this->activeTemplate.'user.bills.cabletv', compact(
            'pageTitle',
            'network',
            'bills',
            'bill',
            'user'
        ));
    }

    public function validatedecoder(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'number' => ['required'],
            'decoder' => ['required', 'string'],
            'plan' => ['required'],
        ]);

        $tvProvider = data_get($this->billPayments->settings(), 'service_providers.tv');
        $decoderQuery = Cabletvbundle::wherePlan($request->plan)
            ->when(Schema::hasColumn('cabletvbundles', 'provider') && $tvProvider, fn ($query) => $query->where('provider', $tvProvider));
        $decoder = $decoderQuery->first() ?: Cabletvbundle::wherePlan($request->plan)->first();
        if (! $decoder) {
            return back()->withAlertx([['error', 'Invalid decoder plan selected.']]);
        }

        if ((float) $decoder->cost > $user->balance) {
            return back()->withAlertx([['error', 'Insufficient Balance']]);
        }

        try {
            $result = $this->billPayments->validateTv($decoder, (string) $request->number);
        } catch (\Throwable $e) {
            return back()->withAlertx([['warning', $e->getMessage()]]);
        }

        Session::put('customer', $result['customer_name'] ?? 'Validated customer');
        Session::put('number', $request->number);
        Session::put('planname', $decoder->name);
        Session::put('plancode', $request->plan);
        Session::put('decoder', $decoder->network);
        Session::put('cost', $decoder->cost);
        Session::put('charge', 0);

        return redirect()->route('user.decodervalidated');
    }

    public function decodervalidated()
    {
        $pageTitle = 'Cable TV Validation';
        $customer = Session::get('customer');
        $planname = Session::get('planname');
        $number = Session::get('number');
        $plancode = Session::get('plancode');
        $decoder = Session::get('decoder');
        $cost = Session::get('cost');
        $charge = Session::get('charge', 0);

        return view($this->activeTemplate.'user.bills.tv-validated', compact(
            'pageTitle',
            'customer',
            'planname',
            'number',
            'plancode',
            'decoder',
            'cost',
            'charge'
        ));
    }

    public function decoderpay(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'number' => ['required'],
            'customer' => ['required'],
            'plan' => ['required'],
        ]);

        $tvProvider = data_get($this->billPayments->settings(), 'service_providers.tv');
        $decoderQuery = Cabletvbundle::wherePlan($request->plan)
            ->when(Schema::hasColumn('cabletvbundles', 'provider') && $tvProvider, fn ($query) => $query->where('provider', $tvProvider));
        $decoder = $decoderQuery->first() ?: Cabletvbundle::wherePlan($request->plan)->first();
        if (! $decoder) {
            return back()->withAlertx([['error', 'Invalid decoder plan selected.']]);
        }

        if ((float) $decoder->cost > $user->balance) {
            return back()->withAlertx([['error', 'Insufficient Balance']]);
        }

        try {
            $result = $this->billPayments->purchaseTv($decoder, (string) $request->number);
        } catch (\Throwable $e) {
            return back()->withAlertx([['warning', $e->getMessage()]]);
        }

        $bill = $this->walletService->purchaseBill($user, $decoder->cost, $decoder->name, $result['reference'] ?: getTrx(), [
            'debit_amount' => (float) $decoder->cost,
            'charge' => 0,
            'profit' => 0,
            'phone' => $request->number,
            'network' => $decoder->network,
            'plan' => $decoder->name,
            'accountnumber' => $request->number,
            'accountname' => $request->customer,
            'type' => 3,
            'status' => ($result['status'] ?? 'success') === 'pending' ? 0 : 1,
            'response' => $result['meta'] ?? [],
            'transaction_details' => 'Cable TV subscription for '.$decoder->name,
            'bywho' => ucfirst((string) data_get($result, 'provider', 'BudPay')),
        ]);
        $this->markBillGateway($bill, (string) data_get($result, 'provider', 'budpay'));

        session()->flash('receipt_url', route('user.beta.receipt', ['billId' => $bill->id]));

        return redirect()->route('user.cabletv')->withAlertx([['success', $result['message'] ?? 'Payment was successful.']]);
    }

    public function utility()
    {
        $this->safeSyncBillCatalog();

        $pageTitle = 'Utility Bills Payment';
        $user = Auth::user();
        $bills = Bill::whereUserId($user->id)->whereType(4)->latest()->get();
        $network = $this->billPayments->electricityProviders();

        return view($this->activeTemplate.'user.bills.utility', compact(
            'pageTitle',
            'network',
            'bills',
            'user'
        ));
    }

    public function validatebill(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'number' => ['required'],
            'company' => ['required', 'string'],
            'type' => ['required'],
            'amount' => ['required', 'integer', 'min:500'],
        ]);

        $electricityProvider = data_get($this->billPayments->settings(), 'service_providers.electricity');
        $meterQuery = Power::whereBillercode($request->company)
            ->when(Schema::hasColumn('powers', 'provider') && $electricityProvider, fn ($query) => $query->where('provider', $electricityProvider));
        $meter = $meterQuery->first() ?: Power::whereBillercode($request->company)->first();
        if (! $meter) {
            return back()->withAlertx([['error', 'Invalid meter company selected.']]);
        }

        $total = (float) $request->amount;
        if ($total > $user->balance) {
            return back()->withAlertx([['error', 'Insufficient Balance']]);
        }

        try {
            $result = $this->billPayments->validateElectricity($meter, (string) $request->type, (string) $request->number);
        } catch (\Throwable $e) {
            return back()->withAlertx([['warning', $e->getMessage()]]);
        }

        if (($result['minimum_amount'] ?? 0) > 0 && $total < (float) $result['minimum_amount']) {
            return back()->withAlertx([['warning', 'Minimum amount for this meter is '.showAmount((float) $result['minimum_amount']).'.']]);
        }

        Session::put('customer', $result['customer_name'] ?: 'Validated customer');
        Session::put('address', $result['address'] ?? null);
        Session::put('number', $request->number);
        Session::put('type', $request->type);
        Session::put('plancode', $meter->billercode);
        Session::put('meter', $meter->name);
        Session::put('cost', $request->amount);
        Session::put('charge', 0);
        Session::put('validation_reference', $result['reference'] ?? null);

        return redirect()->route('user.billvalidated');
    }

    public function billvalidated()
    {
        $pageTitle = 'Utility Bill Validation';
        $customer = Session::get('customer');
        $number = Session::get('number');
        $address = Session::get('address');
        $plancode = Session::get('plancode');
        $meter = Session::get('meter');
        $cost = Session::get('cost');
        $type = Session::get('type');
        $charge = Session::get('charge', 0);

        return view($this->activeTemplate.'user.bills.bill-validated', compact(
            'pageTitle',
            'customer',
            'number',
            'plancode',
            'meter',
            'cost',
            'type',
            'address',
            'charge'
        ));
    }

    public function billpay(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'number' => ['required'],
            'customer' => ['required'],
            'plan' => ['required'],
            'type' => ['required'],
            'amount' => ['required'],
        ]);

        $electricityProvider = data_get($this->billPayments->settings(), 'service_providers.electricity');
        $meterQuery = Power::whereBillercode($request->plan)
            ->when(Schema::hasColumn('powers', 'provider') && $electricityProvider, fn ($query) => $query->where('provider', $electricityProvider));
        $meter = $meterQuery->first() ?: Power::whereBillercode($request->plan)->first();
        if (! $meter) {
            return back()->withAlertx([['error', 'Invalid meter selected.']]);
        }

        $amount = (float) $request->amount;
        if ($amount > $user->balance) {
            return back()->withAlertx([['error', 'Insufficient Balance']]);
        }

        try {
            $result = $this->billPayments->purchaseElectricity(
                $meter,
                (string) $request->type,
                (string) $request->number,
                $amount,
                (string) ($user->mobile ?: $request->number),
                (string) $user->email,
                Session::get('validation_reference')
            );
        } catch (\Throwable $e) {
            return back()->withAlertx([['warning', $e->getMessage()]]);
        }

        $bill = $this->walletService->purchaseBill($user, $amount, $meter->name, $result['reference'] ?: getTrx(), [
            'debit_amount' => $amount,
            'charge' => 0,
            'profit' => 0,
            'phone' => $request->number,
            'network' => $meter->name,
            'plan' => $meter->billercode,
            'accountnumber' => $result['token'] ?: $request->number,
            'accountname' => $request->customer ?: ('Meter: '.$meter->name),
            'type' => 4,
            'status' => ($result['status'] ?? 'success') === 'pending' ? 0 : 1,
            'response' => $result['meta'] ?? [],
            'transaction_details' => 'Payment for '.$meter->name.' utility bill',
            'bywho' => ucfirst((string) data_get($result, 'provider', 'BudPay')),
        ]);
        $this->markBillGateway($bill, (string) data_get($result, 'provider', 'budpay'));

        if (! empty($result['token'])) {
            $bill->accountnumber = trim($result['token'].($result['units'] ? '<br> Units: '.$result['units'] : ''));
            $bill->accountname = 'Meter: '.$meter->name.'<br>Meter Number: '.$request->number;
            $bill->save();
        }

        session()->flash('receipt_url', route('user.beta.receipt', ['billId' => $bill->id]));

        return redirect()->route('user.utility')->withAlertx([['success', $result['message'] ?? 'Payment was successful.']]);
    }

    public function utilitytoken($id)
    {
        $user = Auth::user();
        $bill = Bill::whereTrx($id)->whereUserId($user->id)->first();
        if (! $bill) {
            return back()->withAlertx([['error', 'Sorry, Order Not Found']]);
        }

        $reply = $this->decodeBillResponse($bill);

        $pageTitle = 'Utility Token';
        $token = data_get($reply, 'data.purchased_code') ?: data_get($reply, 'data.token') ?: data_get($reply, 'purchased_code');
        $customer = data_get($reply, 'data.customer_name') ?: data_get($reply, 'customerName') ?: $bill->accountname;
        $address = data_get($reply, 'data.customer_address') ?: data_get($reply, 'customerAddress');
        $unit = data_get($reply, 'data.units') ?: data_get($reply, 'data.token_units') ?: data_get($reply, 'mainTokenUnits');
        $status = data_get($reply, 'data.status') ?: data_get($reply, 'content.transactions.status');
        $meter = data_get($reply, 'data.number') ?: data_get($reply, 'content.transactions.unique_element') ?: $bill->phone;
        $disco = data_get($reply, 'data.provider') ?: data_get($reply, 'content.transactions.product_name') ?: $bill->network;
        $amount = data_get($reply, 'data.amount') ?: data_get($reply, 'content.transactions.unit_price') ?: $bill->amount;

        return view($this->activeTemplate.'user.bills.utility-token', compact(
            'pageTitle',
            'address',
            'token',
            'status',
            'meter',
            'unit',
            'disco',
            'amount',
            'customer'
        ));
    }

    public function waecreg()
    {
        $user = Auth::user();
        $pageTitle = 'WAEC Registration';
        $bills = Bill::whereUserId($user->id)->whereType(5)->latest()->get();
        $charge = $this->vtpassCharge('waec_charge');
        $response = $this->vtpassService->serviceVariations('waec-registration');
        $reply = $response->json() ?? [];

        if (! isset($reply['response_description']) || $reply['response_description'] !== '000') {
            return back()->withAlertx([['error', $reply['content']['errors'] ?? 'Sorry, we cannot process this registration right now.']]);
        }

        $network = $reply['content'] ?? [];
        $forms = $reply['content']['varations'] ?? ($reply['content']['variations'] ?? []);

        return view($this->activeTemplate.'user.bills.waec-register', compact(
            'pageTitle',
            'network',
            'bills',
            'forms',
            'charge',
            'user'
        ));
    }

    public function waecregpost(Request $request, $id)
    {
        $user = Auth::user();

        $request->validate([
            'phone' => ['required', 'numeric'],
            'variant' => ['required'],
            'amount' => ['required'],
        ]);

        $amount = (float) $request->amount;
        if ($user->balance < $amount) {
            return back()->withAlertx([['error', 'You do not have enough balance to start this transaction.']]);
        }

        $charge = $this->vtpassCharge('waec_charge');
        $requestId = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ01234567890'), 0, 10);
        $response = $this->vtpassService->pay([
            'amount' => $amount,
            'phone' => $request->phone,
            'serviceID' => $id,
            'variation_code' => $request->variant,
            'request_id' => $requestId,
        ]);

        $reply = $response->json() ?? [];
        if (! isset($reply['code']) || isset($reply['content']['errors']) || $reply['code'] != 000 || ! isset($reply['content']['transactions']['product_name'])) {
            return back()->withAlertx([['error', $reply['content']['errors'] ?? 'WAEC Registration Token Purchase Was Not Successful']]);
        }

        $bill = $this->walletService->purchaseBill($user, $amount, $reply['content']['transactions']['product_name'], $requestId, [
            'debit_amount' => $amount,
            'charge' => $charge,
            'profit' => 0,
            'phone' => $request->phone,
            'network' => $reply['content']['transactions']['product_name'],
            'accountnumber' => $reply['purchased_code'] ?? null,
            'accountname' => $request->name ?? null,
            'type' => 5,
            'status' => 1,
            'response' => $reply,
            'transaction_details' => 'Payment for '.$reply['content']['transactions']['product_name'].' with transaction number '.$requestId,
            'bywho' => 'VTPass',
        ]);

        session()->flash('receipt_url', route('user.beta.receipt', ['billId' => $bill->id]));

        return back()->withAlertx([['success', 'WAEC Registration Token Purchase Was Successful']]);
    }

    public function waecresult()
    {
        $user = Auth::user();
        $pageTitle = 'WAEC Result Checker';
        $bills = Bill::whereUserId($user->id)->whereType(6)->latest()->get();
        $charge = $this->vtpassCharge('waec_result_charge');
        $response = $this->vtpassService->serviceVariations('waec');
        $reply = $response->json() ?? [];

        if (! isset($reply['response_description']) || $reply['response_description'] !== '000') {
            return back()->withAlertx([['error', $reply['content']['errors'] ?? 'Sorry, we cannot process this registration right now.']]);
        }

        $network = $reply['content'] ?? [];
        $forms = $reply['content']['varations'] ?? ($reply['content']['variations'] ?? []);

        return view($this->activeTemplate.'user.bills.waec-result', compact(
            'pageTitle',
            'network',
            'bills',
            'forms',
            'charge',
            'user'
        ));
    }

    public function resultwaecpost(Request $request, $id)
    {
        $user = Auth::user();

        $request->validate([
            'phone' => ['required', 'numeric'],
            'variant' => ['required'],
            'amount' => ['required'],
        ]);

        $amount = (float) $request->amount;
        if ($user->balance < $amount) {
            return back()->withAlertx([['error', 'You do not have enough balance to start this transaction.']]);
        }

        $charge = $this->vtpassCharge('waec_result_charge');
        $requestId = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ01234567890'), 0, 10);
        $response = $this->vtpassService->pay([
            'amount' => $amount,
            'phone' => $request->phone,
            'serviceID' => $id,
            'variation_code' => $request->variant,
            'request_id' => $requestId,
        ]);

        $reply = $response->json() ?? [];
        if (! isset($reply['code']) || isset($reply['content']['errors']) || $reply['code'] != 000 || ! isset($reply['content']['transactions']['product_name'])) {
            return back()->withAlertx([['error', $reply['content']['errors'] ?? 'WAEC Result Checker purchase was not successful']]);
        }

        $bill = $this->walletService->purchaseBill($user, $amount, $reply['content']['transactions']['product_name'], $requestId, [
            'debit_amount' => $amount,
            'charge' => $charge,
            'profit' => 0,
            'phone' => $request->phone,
            'network' => $reply['content']['transactions']['product_name'],
            'accountnumber' => $reply['purchased_code'] ?? null,
            'accountname' => $request->name ?? null,
            'type' => 6,
            'status' => 1,
            'response' => $reply,
            'transaction_details' => 'Payment for '.$reply['content']['transactions']['product_name'].' with transaction number '.$requestId,
            'bywho' => 'VTPass',
        ]);

        session()->flash('receipt_url', route('user.beta.receipt', ['billId' => $bill->id]));

        return back()->withAlertx([['success', 'WAEC Result Checker Purchase Was Successful']]);
    }

    private function safeSyncBillCatalog(): void
    {
        try {
            $this->billPayments->syncIfDue();
        } catch (\Throwable) {
            // Keep bill pages available even when provider sync is temporarily unavailable.
        }
    }

    private function markBillGateway(Bill $bill, string $provider): void
    {
        if (! Schema::hasColumn('bills', 'gateway')) {
            return;
        }

        $bill->gateway = strtoupper($provider);
        $bill->save();
    }

    private function decodeBillResponse(Bill $bill): array
    {
        if (! filled($bill->response)) {
            return [];
        }

        if (is_array($bill->response)) {
            return $bill->response;
        }

        return json_decode((string) $bill->response, true) ?? [];
    }

    private function vtpassCharge(string $key): float
    {
        return (float) config('services.vtpass.'.$key, 0);
    }

    private function storeBill(User $user, array $attributes): Bill
    {
        $bill = new Bill();
        $bill->user_id = $user->id;

        foreach (['amount', 'token', 'bundle', 'profit', 'trx', 'phone', 'network', 'newbalance', 'type', 'status', 'plan', 'btype', 'validity', 'accountnumber', 'accountname', 'api'] as $field) {
            if (array_key_exists($field, $attributes)) {
                $bill->{$field} = $attributes[$field];
            }
        }

        if (array_key_exists('response', $attributes)) {
            $bill->response = is_string($attributes['response']) ? $attributes['response'] : json_encode($attributes['response']);
        }

        $bill->save();

        return $bill;
    }
}
