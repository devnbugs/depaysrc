<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AdminNotification;
use App\Models\GatewayCurrency;
use Illuminate\Support\Facades\Validator;
use App\Lib\GoogleAuthenticator;
use App\Models\GeneralSetting;
use App\Models\Transaction;
use App\Models\WithdrawMethod;
use App\Models\Power;
use App\Models\Deposit;
use App\Models\Extension;
use App\Models\Network;
use App\Models\Bill;
use App\Models\User;
use App\Models\Contact;
use App\Models\Internetbundle;
use App\Models\Cabletvbundle;
use App\Rules\FileTypeValidate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Carbon\Carbon;
use Session;
use Illuminate\Support\Str;
use App\Services\AlrahuzService;
use App\Services\EasyAccessService;
use App\Services\WalletService;

class PaymentController extends Controller
{
    public function __construct(
        protected WalletService $walletService,
        protected AlrahuzService $alrahuzService,
        protected EasyAccessService $easyAccessService,
    ) {
    }

	public function depositMethods()
    {
        $gatewayCurrency = GatewayCurrency::whereHas('method', function ($gate) {
            $gate->where('status', 1);
        })->with('method')->orderby('method_code')->get();
        $notify[] = 'Payment Methods';
        return response()->json([
            'code'=>200,
            'status'=>'ok',
            'message'=>['error'=>$notify],
            'data'=>[
            	'methods'=>$gatewayCurrency,
            	'image_path'=>imagePath()['gateway']['path']
            ],
        ]);
    }

    public function depositInsert(Request $request){
    	$validator = Validator::make($request->all(),[
            'amount' => 'required|numeric|gt:0',
            'method_code' => 'required',
            'currency' => 'required',
        ]);

        if ($validator->fails()) {
        	return response()->json([
                'code'=>200,
                'status'=>'ok',
        		'message'=>['error'=>$validator->errors()->all()],
        	]);
        }

        $user = auth()->user();
        $gate = GatewayCurrency::whereHas('method', function ($gate) {
            $gate->where('status', 1);
        })->where('method_code', $request->method_code)->where('currency', $request->currency)->first();
        if (!$gate) {
            $notify[] = 'Invalid gateway';
            return response()->json([
                'code'=>200,
                'status'=>'ok',
        		'message'=>['error'=>$notify],
        	]);
        }

        if ($gate->min_amount > $request->amount || $gate->max_amount < $request->amount) {
            $notify[] = 'Please follow deposit limit';
            return response()->json([
                'code'=>200,
                'status'=>'ok',
        		'message'=>['error'=>$notify],
        	]);
        }

        $charge = $gate->fixed_charge + ($request->amount * $gate->percent_charge / 100);
        $payable = $request->amount + $charge;
        $final_amo = $payable * $gate->rate;

        $data = new Deposit();
        $data->user_id = $user->id;
        $data->method_code = $gate->method_code;
        $data->method_currency = strtoupper($gate->currency);
        $data->amount = $request->amount;
        $data->charge = $charge;
        $data->rate = $gate->rate;
        $data->final_amo = $final_amo;
        $data->btc_amo = 0;
        $data->btc_wallet = "";
        $data->trx = getTrx();
        $data->try = 0;
        $data->status = 0;
        $data->from_api = 1;
        $data->save();

        $notify[] = 'Deposit Created';
        return response()->json([
            'code'=>202,
            'status'=>'created',
        	'message'=>['success'=>$notify],
        	'data'=>[
        		'deposit'=>$data
        	],
        ]);
    }

    public function depositConfirm(Request $request){
    	$validator = Validator::make($request->all(),[
            'transaction' => 'required',
        ]);

        if ($validator->fails()) {
        	return response()->json([
                'code'=>200,
                'status'=>'ok',
        		'message'=>['error'=>$validator->errors()->all()],
        	]);
        }
    	$deposit = Deposit::where('trx', $request->transaction)->where('status',0)->orderBy('id', 'DESC')->with('gateway')->first();
        if (!$deposit) {
            $notify[] = 'Deposit not found';
            return response()->json([
                'code'=>404,
                'status'=>'error',
                'message'=>['error'=>$notify],
            ]);
        }
    	$dirName = $deposit->gateway->alias;
        $new = substr(__NAMESPACE__,0,-4).'\\Gateway'. '\\' . $dirName . '\\ProcessController';
        $data = (array)json_decode($new::process($deposit));
        if (array_key_exists('view', $data)) {
        	unset($data['view']);
        }
        return response()->json([
            'code'=>200,
            'status'=>'ok',
        	'data'=>[
        		'gateway_data'=>$data
        	],
        ]);
    }


    public function manualDepositConfirm(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'transaction' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code'=>200,
                'status'=>'ok',
                'message'=>['error'=>$validator->errors()->all()],
            ]);
        }
        $data = Deposit::with('gateway')->where('status', 0)->where('trx', $request->transaction)->where('method_code','>=',1000)->first();
        if (!$data) {
            $notify[] = 'Deposit not found';
            return response()->json([
                'code'=>404,
                'status'=>'error',
                'message'=>['error'=>$notify],
            ]);
        }
        $method = $data->gatewayCurrency();
        $notify[] = 'Manual payment details';
        return response()->json([
            'code'=>200,
            'status'=>'ok',
            'message'=>['success'=>$notify],
            'data'=>[
                'deposit'=>$data,
                'payment_method'=>$method
            ]
        ]);
    }

    public function manualDepositUpdate(Request $request)
    {
        $data = Deposit::with('gateway')->where('status', 0)->where('trx', $request->transaction)->where('method_code','>=',1000)->first();
        if (!$data) {
            $notify[] = 'Deposit not found';
            return response()->json([
                'code'=>404,
                'status'=>'error',
                'message'=>['error'=>$notify],
            ]);
        }

        $params = json_decode($data->gatewayCurrency()->gateway_parameter);

        $rules = [];
        $inputField = [];
        $verifyImages = [];

        if ($params != null) {
            foreach ($params as $key => $custom) {
                $rules[$key] = [$custom->validation];
                if ($custom->type == 'file') {
                    array_push($rules[$key], 'image');
                    array_push($rules[$key], new FileTypeValidate(['jpg','jpeg','png']));
                    array_push($rules[$key], 'max:2048');

                    array_push($verifyImages, $key);
                }
                if ($custom->type == 'text') {
                    array_push($rules[$key], 'max:191');
                }
                if ($custom->type == 'textarea') {
                    array_push($rules[$key], 'max:300');
                }
                $inputField[] = $key;
            }
        }
        $validator = Validator::make($request->all(),$rules);
        if ($validator->fails()) {
            return response()->json([
                'code'=>200,
                'status'=>'ok',
                'message'=>['error'=>$validator->errors()->all()],
            ]);
        }


        $directory = date("Y")."/".date("m")."/".date("d");
        $path = imagePath()['verify']['deposit']['path'].'/'.$directory;
        $collection = collect($request);
        $reqField = [];
        if ($params != null) {
            foreach ($collection as $k => $v) {
                foreach ($params as $inKey => $inVal) {
                    if ($k != $inKey) {
                        continue;
                    } else {
                        if ($inVal->type == 'file') {
                            if ($request->hasFile($inKey)) {
                                try {
                                    $reqField[$inKey] = [
                                        'field_name' => $directory.'/'.uploadImage($request[$inKey], $path),
                                        'type' => $inVal->type,
                                    ];
                                } catch (\Exception $exp) {
                                    $notify[] = ['error', 'Could not upload your ' . $inKey];
                                    return back()->withNotify($notify)->withInput();
                                }
                            }
                        } else {
                            $reqField[$inKey] = $v;
                            $reqField[$inKey] = [
                                'field_name' => $v,
                                'type' => $inVal->type,
                            ];
                        }
                    }
                }
            }
            $data->detail = $reqField;
        } else {
            $data->detail = null;
        }



        $data->status = 2; // pending
        $data->save();


        $adminNotification = new AdminNotification();
        $adminNotification->user_id = $data->user->id;
        $adminNotification->title = 'Deposit request from '.$data->user->username;
        $adminNotification->click_url = urlPath('admin.deposit.details',$data->id);
        $adminNotification->save();

        $general = GeneralSetting::first();
        notify($data->user, 'DEPOSIT_REQUEST', [
            'method_name' => $data->gatewayCurrency()->name,
            'method_currency' => $data->method_currency,
            'method_amount' => showAmount($data->final_amo),
            'amount' => showAmount($data->amount),
            'charge' => showAmount($data->charge),
            'currency' => $general->cur_text,
            'rate' => showAmount($data->rate),
            'trx' => $data->trx
        ]);

        $notify[] = 'Deposit request sent successfully';
        return response()->json([
            'code'=>200,
            'status'=>'ok',
            'message'=>['error'=>$notify],
        ]);
    }
    public function airtimebuy(Request $request)
    {
        $user = Auth::user();
        $this->validate($request, [
            'phone' => 'required|numeric',
            'network' => 'required',
            'amount' => 'required|numeric|min:50',
            'pin_code' => 'required|string|min:4|max:4',
        ]);

        $network = Network::whereSymbol($request->network)->first();
        if (! $network) {
            return response()->json(['error' => 'Invalid Network'], 400);
        }

        if (! hash_equals((string) $user->pin, (string) $request->input('pin_code'))) {
            return response()->json(['error' => 'Incorrect PIN.'], 400);
        }

        $amount = (float) $request->amount;
        if ($user->balance < $amount) {
            return response()->json(['error' => 'You dont have enough balance.'], 400);
        }

        $token = (string) Str::uuid();
        $trx = 'TRANX'.substr(str_shuffle('0123456789'), 0, 10);
        $response = $this->alrahuzService->topup([
            'network' => $network->symbol,
            'amount' => $amount,
            'mobile_number' => $request->phone,
            'Ported_number' => true,
            'airtime_type' => 'VTU',
            'client_reference' => $trx,
        ]);

        $reply = $response->json() ?? [];
        if (Transaction::where('token', $token)->exists()) {
            return response()->json(['error' => 'Duplicate Transaction Found'], 400);
        }

        if (! $response->successful() || ! isset($reply['Status'])) {
            $this->storeApiBill($user, [
                'amount' => $amount,
                'token' => $token,
                'bundle' => 'VTU Airtime',
                'trx' => $trx,
                'phone' => $request->phone,
                'network' => $network->name,
                'newbalance' => $user->balance,
                'type' => 1,
                'status' => 0,
                'response' => $reply,
            ]);

            return response()->json(['error' => $reply['message'] ?? 'Purchase Decline'], 400);
        }

        $bill = $this->walletService->purchaseBill($user, $amount, 'VTU Airtime', $reply['ident'] ?? $trx, [
            'token' => $token,
            'debit_amount' => $amount,
            'charge' => 0,
            'profit' => 0,
            'phone' => $request->phone,
            'network' => $network->name,
            'type' => 1,
            'status' => 1,
            'response' => $reply,
            'transaction_details' => 'Airtime purchase for '.$network->name,
            'bywho' => 'Alrahuz',
        ]);

        return response()->json([
            'success' => 'Airtime Purchase was Successful',
            'data' => [
                'bill' => $bill,
                'provider' => $reply,
            ],
        ]);
    }

    public function loadinternet(Request $request)
    {
        $user = Auth::user();
        $this->validate($request, [
            'phone' => 'required|string|min:11',
            'network' => 'required|string',
            'plan' => 'required',
            'pin_code' => 'required|string|min:4|max:4',
        ]);

        $network = Network::whereSymbol($request->network)->first();
        if (! $network) {
            return response()->json(['error' => 'Invalid Network'], 400);
        }

        if (! hash_equals((string) $user->pin, (string) $request->input('pin_code'))) {
            return response()->json(['error' => 'Incorrect PIN.'], 400);
        }

        $internet = Internetbundle::wherePlan($request->plan)->first();
        if (! $internet) {
            return response()->json(['error' => 'Invalid data plan selected.'], 400);
        }

        if ($user->balance < $internet->cost) {
            return response()->json(['error' => 'You dont have enough balance.'], 400);
        }

        $reference = 'TRANX'.substr(str_shuffle('0123456789'), 0, 10);
        $response = $this->easyAccessService->data([
            'network' => $network->symbol,
            'mobileno' => $request->phone,
            'dataplan' => $internet->plan,
            'client_reference' => $reference,
        ]);

        $reply = $response->json() ?? [];
        $success = $response->successful() && filter_var($reply['success'] ?? false, FILTER_VALIDATE_BOOLEAN);

        if (! $success) {
            $this->storeApiBill($user, [
                'amount' => $internet->cost,
                'token' => $reference,
                'bundle' => $internet->name,
                'plan' => $internet->plan,
                'btype' => $internet->datatype,
                'validity' => $internet->validity,
                'trx' => $reference,
                'phone' => $request->phone,
                'network' => $network->name,
                'newbalance' => $user->balance,
                'type' => 2,
                'status' => 0,
                'response' => $reply,
            ]);

            return response()->json(['error' => $reply['message'] ?? 'Purchase Decline'], 400);
        }

        $bill = $this->walletService->purchaseBill($user, $internet->cost, $internet->name, $reply['reference_no'] ?? $reference, [
            'token' => $reference,
            'debit_amount' => $internet->cost,
            'charge' => 0,
            'profit' => 2,
            'phone' => $request->phone,
            'network' => $network->name,
            'plan' => $internet->plan,
            'btype' => $internet->datatype,
            'validity' => $internet->validity,
            'type' => 2,
            'status' => 1,
            'response' => $reply,
            'transaction_details' => 'Data purchase for '.$network->name,
            'bywho' => 'EasyAccess',
        ]);

        return response()->json([
            'success' => 'Data purchase was successful',
            'data' => [
                'bill' => $bill,
                'provider' => $reply,
            ],
        ]);
    }

    public function waec(Request $request)
    {
        return response()->json([
            'success' => false,
            'message' => 'WAEC purchase is available in the web dashboard while we complete the API flow.',
        ], 422);
    }

    public function neco(Request $request)
    {
        return response()->json([
            'success' => false,
            'message' => 'NECO purchase is available in the web dashboard while we complete the API flow.',
        ], 422);
    }

    private function storeApiBill(User $user, array $attributes): Bill
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
