<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Deposit;
use App\Models\EmailLog;
use App\Models\Gateway;
use App\Models\GeneralSetting;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Kyc;
use App\Models\Kycsetting;
use App\Models\UserLogin;
use App\Models\SupportTicket;
use App\Models\Desk;
use App\Models\Plan;
use Carbon\Carbon;
use App\Models\SupportMessage;
use App\Models\SupportAttachment;
use App\Models\WithdrawMethod;
use App\Models\Withdrawal;
use App\Models\Investment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Auth;
use App\Jobs\MonnifyTokenJob;
use App\Jobs\MonnifyBankTransferJob;



class ManageUsersController extends Controller
{
    public function createUser()
    {
        $pageTitle = 'Manage Users';
        return view('admin.users.create', compact('pageTitle'));
    }


     public function createUserpost(Request $request)
    {
    $countryData = (array)json_decode(file_get_contents(resource_path('views/partials/country.json')));
    $countryCodes = implode(',', array_keys($countryData));
    $mobileCodes = implode(',',array_column($countryData, 'dial_code'));
    $countries = implode(',',array_column($countryData, 'country'));

      $request->validate([
            'firstname' => 'sometimes|required|string|max:50',
            'lastname' => 'sometimes|required|string|max:50',
            'email' => 'required|string|email|max:90|unique:users',
            'mobile' => 'required|string|max:50|unique:users',
            'password' => ['required','confirmed'],
            'username' => 'required|alpha_num|unique:users|min:6',
            'captcha' => 'sometimes|required',
            'referBy' => 'sometimes',
            'country' => 'required|in:'.$countries
        ]);

        if (User::where('firstname', $request->firstname)
            ->where('lastname', $request->lastname)
            ->exists()) {
            $notify[] = ['error', 'A user with the same first and last name already exists'];
            return back()->withNotify($notify)->withInput();
        }

        $general = GeneralSetting::first();


        $user = new User();
        $user->firstname = $request->firstname;
        $user->lastname = $request->lastname;
        $user->email = strtolower(trim($request->email));
        $user->password = Hash::make($request->password);
        $user->username = trim($request->username);
        $user->account_number = mt_rand(1000000000, 9999999999);
        $user->ref_by = 0;
        $user->country_code = 234;
        $user->mobile = $request->mobile;
        $user->address = [
            'address' => '',
            'state' => '',
            'zip' => '',
            'country' => $request->country,
            'city' => ''
        ];
        $user->status = 1;
        $user->ev = $general->ev ? 0 : 1;
        $user->sv = $general->sv ? 0 : 1;
        $user->ts = 0;
        $user->tv = 1;
        $user->save();

        dispatch(new MonnifyTokenJob($user));

        $notify[] = ['success', 'New user created successfully'];
        return redirect()->route('admin.users.create')->withNotify($notify);

    }


      public function addplan(Request $request, $id){

        $request->validate([
            'amount'=> 'required|numeric|gt:0',
            'plan'=> 'required|numeric|gt:0'
        ]);

        $findPlan = Plan::where('id', $request->plan)->where('status', 1)->firstOrFail();

        if($findPlan->min_amount > $request->amount || $findPlan->max_amount < $request->amount){
            $notify[] = ['error', 'Amount must be between'.showAmount($findPlan->min_amount).' and '.showAmount($findPlan->max_amount)];
            return redirect()->back()->withNotify($notify);
        }


        $perAnnuityInterest = 0;
        $nextReturn = Carbon::now()->addDay(1);

        if($findPlan->interest_type == 0){
            $perAnnuityInterest = $findPlan->interest_amount;
        }else{
            $perAnnuityInterest = ($request->amount * $findPlan->interest_amount) / 100;
        }

        $newInvest = new Investment();
        $newInvest->trx = getTrx();
        $newInvest->plan_id = $findPlan->id;
        $newInvest->user_id = $id;
        $newInvest->amount = $request->amount;
        $newInvest->interest_type = $findPlan->interest_type;
        $newInvest->interest_amount = $perAnnuityInterest;
        $newInvest->total_return = $findPlan->total_return;
        $newInvest->next_return_date = $nextReturn;
        $newInvest->compound = $request->compound;

        if($findPlan->usd == 1)
        {
        $newInvest->usd = 1;
        }
        else
        {
        $newInvest->usd = 0;
        }

        $newInvest->status = 2;
        $newInvest->save();


        $transaction = new Transaction();
        $transaction->user_id = $id;
        $transaction->amount = $request->amount;
        $transaction->post_balance = 0;
        $transaction->charge = 0;
        $transaction->trx_type = '-';
        $transaction->details = 'Investment in '.$findPlan->name;
        $transaction->trx =  $newInvest->trx;
        $transaction->save();


       $notify[] = ['success', 'User Investment has been created'];
        return redirect()->back()->withNotify($notify);

    }

    public function allUsers()
    {
        $pageTitle = 'Manage Users';
        $emptyMessage = 'No user found';
        $users = User::orderBy('id','desc')->paginate(getPaginate());
        return view('admin.users.list', compact('pageTitle', 'emptyMessage', 'users'));
    }

    public function activeUsers()
    {
        $pageTitle = 'Manage Active Users';
        $emptyMessage = 'No active user found';
        $users = User::active()->orderBy('id','desc')->paginate(getPaginate());
        return view('admin.users.list', compact('pageTitle', 'emptyMessage', 'users'));
    }

    public function bannedUsers()
    {
        $pageTitle = 'Banned Users';
        $emptyMessage = 'No banned user found';
        $users = User::banned()->orderBy('id','desc')->paginate(getPaginate());
        return view('admin.users.list', compact('pageTitle', 'emptyMessage', 'users'));
    }

    public function emailUnverifiedUsers()
    {
        $pageTitle = 'Email Unverified Users';
        $emptyMessage = 'No email unverified user found';
        $users = User::emailUnverified()->orderBy('id','desc')->paginate(getPaginate());
        return view('admin.users.list', compact('pageTitle', 'emptyMessage', 'users'));
    }
    public function emailVerifiedUsers()
    {
        $pageTitle = 'Email Verified Users';
        $emptyMessage = 'No email verified user found';
        $users = User::emailVerified()->orderBy('id','desc')->paginate(getPaginate());
        return view('admin.users.list', compact('pageTitle', 'emptyMessage', 'users'));
    }


    public function smsUnverifiedUsers()
    {
        $pageTitle = 'SMS Unverified Users';
        $emptyMessage = 'No sms unverified user found';
        $users = User::smsUnverified()->orderBy('id','desc')->paginate(getPaginate());
        return view('admin.users.list', compact('pageTitle', 'emptyMessage', 'users'));
    }


    public function smsVerifiedUsers()
    {
        $pageTitle = 'SMS Verified Users';
        $emptyMessage = 'No sms verified user found';
        $users = User::smsVerified()->orderBy('id','desc')->paginate(getPaginate());
        return view('admin.users.list', compact('pageTitle', 'emptyMessage', 'users'));
    }


    public function usersWithBalance()
    {
        $pageTitle = 'Users with balance';
        $emptyMessage = 'No sms verified user found';
        $users = User::where('balance','!=',0)->orderBy('id','desc')->paginate(getPaginate());
        return view('admin.users.list', compact('pageTitle', 'emptyMessage', 'users'));
    }



    public function search(Request $request, $scope)
    {
        $search = $request->search;
        $users = User::where(function ($user) use ($search) {
            $user->where('username', 'like', "%$search%")
                ->orWhere('email', 'like', "%$search%");
        });
        $pageTitle = '';
        if ($scope == 'active') {
            $pageTitle = 'Active ';
            $users = $users->where('status', 1);
        }elseif($scope == 'banned'){
            $pageTitle = 'Banned';
            $users = $users->where('status', 0);
        }elseif($scope == 'emailUnverified'){
            $pageTitle = 'Email Unverified ';
            $users = $users->where('ev', 0);
        }elseif($scope == 'smsUnverified'){
            $pageTitle = 'SMS Unverified ';
            $users = $users->where('sv', 0);
        }elseif($scope == 'withBalance'){
            $pageTitle = 'With Balance ';
            $users = $users->where('balance','!=',0);
        }

        $users = $users->paginate(getPaginate());
        $pageTitle .= 'User Search - ' . $search;
        $emptyMessage = 'No search result found';
        return view('admin.users.list', compact('pageTitle', 'search', 'scope', 'emptyMessage', 'users'));
    }


    public function detail($id)
    {
        $pageTitle = 'User Detail';
        $user = User::findOrFail($id);
        $totalInvest = Investment::where('user_id', $user->id)->sum('amount');
        $totalDeposit = Deposit::where('user_id',$user->id)->where('status',1)->sum('amount');
        $totalWithdraw = Withdrawal::where('user_id',$user->id)->where('status',1)->sum('amount');
        $totalTransaction = Transaction::where('user_id',$user->id)->count();
        $countries = json_decode(file_get_contents(resource_path('views/partials/country.json')));
        return view('admin.users.detail', compact('pageTitle', 'user','totalDeposit','totalWithdraw','totalTransaction','countries', 'totalInvest'));
    }


    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $countryData = json_decode(file_get_contents(resource_path('views/partials/country.json')));

        $request->validate([
            'firstname' => 'required|max:50',
            'lastname' => 'required|max:50',
            'email' => 'required|email|max:90|unique:users,email,' . $user->id,
            'mobile' => 'required|unique:users,mobile,' . $user->id,
            'country' => 'required',
        ]);
        $countryCode = $request->country;
        $user->mobile = $request->mobile;
        $user->country_code = $countryCode;
        $user->firstname = $request->firstname;
        $user->lastname = $request->lastname;
        $user->email = $request->email;
        $user->address = [
                            'address' => $request->address,
                            'city' => $request->city,
                            'state' => $request->state,
                            'zip' => $request->zip,
                            'country' => @$countryData->$countryCode->country,
                        ];
        $user->status = $request->status ? 1 : 0;
        $user->ev = $request->ev ? 1 : 0;
        $user->sv = $request->sv ? 1 : 0;
        $user->ts = $request->ts ? 1 : 0;
        $user->tv = $request->tv ? 1 : 0;
        $user->save();

        $notify[] = ['success', 'User detail has been updated'];
        return redirect()->back()->withNotify($notify);
    }

    public function addSubBalance(Request $request, $id)
    {
        $request->validate(['amount' => 'required|numeric|gt:0']);

        $user = User::findOrFail($id);
        $amount = $request->amount;
        $general = GeneralSetting::first(['cur_text','cur_sym']);
        $trx = getTrx();

        if ($request->act) {
            $user->balance += $amount;
            $user->save();
            $notify[] = ['success', $general->cur_sym . $amount . ' has been added to ' . $user->username . '\'s balance'];

            $transaction = new Transaction();
            $transaction->user_id = $user->id;
            $transaction->amount = $amount;
            $transaction->post_balance = $user->balance;
            $transaction->charge = 0;
            $transaction->trx_type = '+';
            $transaction->details = 'Added Balance Via Admin';
            $transaction->trx =  $trx;
            $transaction->save();

            notify($user, 'BAL_ADD', [
                'trx' => $trx,
                'amount' => showAmount($amount),
                'currency' => $general->cur_text,
                'post_balance' => showAmount($user->balance),
            ]);

        } else {
            if ($amount > $user->balance) {
                $notify[] = ['error', $user->username . '\'s has insufficient balance.'];
                return back()->withNotify($notify);
            }
            $user->balance -= $amount;
            $user->save();



            $transaction = new Transaction();
            $transaction->user_id = $user->id;
            $transaction->amount = $amount;
            $transaction->post_balance = $user->balance;
            $transaction->charge = 0;
            $transaction->trx_type = '-';
            $transaction->details = 'Subtract Balance Via Admin';
            $transaction->trx =  $trx;
            $transaction->save();


            notify($user, 'BAL_SUB', [
                'trx' => $trx,
                'amount' => showAmount($amount),
                'currency' => $general->cur_text,
                'post_balance' => showAmount($user->balance)
            ]);
            $notify[] = ['success', $general->cur_sym . $amount . ' has been subtracted from ' . $user->username . '\'s balance'];
        }
        return back()->withNotify($notify);
    }




    public function addcompound(Request $request, $id)
    {
        $request->validate(['amount' => 'required|numeric|gt:0']);

        $user = User::findOrFail($id);
        $amount = $request->amount;
        $general = GeneralSetting::first(['cur_text','cur_sym']);
        $trx = getTrx();


            $transaction = new Transaction();

            if ($request->act)
            {
            $notify[] = ['success', $general->cur_sym . $amount . ' has been credited to ' . $user->username . '\'s compounding wallet'];
            $user->compound += $amount;
            $transaction->trx_type = '+';
            $transaction->details = 'Credited Compounding Balance By Admin';

            }
            else
            {

             if ($amount > $user->compound)
                {
                $notify[] = ['error', $user->username . '\'s has insufficient compounding balance.'];
                return back()->withNotify($notify);
                }

            $notify[] = ['success', $general->cur_sym . $amount . ' has been debited from ' . $user->username . '\'s compounding wallet'];
            $user->compound -= $amount;
            $transaction->trx_type = '-';
            $transaction->details = 'Debited Compounding Balance By Admin';

            }

            $user->save();

            $transaction->user_id = $user->id;
            $transaction->amount = $amount;
            $transaction->post_balance = $user->balance;
            $transaction->charge = 0;
            $transaction->trx =  $trx;
            $transaction->save();

        return back()->withNotify($notify);
    }



    public function userLoginHistory($id)
    {
        $user = User::findOrFail($id);
        $pageTitle = 'User Login History - ' . $user->username;
        $emptyMessage = 'No users login found.';
        $login_logs = $user->login_logs()->orderBy('id','desc')->with('user')->paginate(getPaginate());
        return view('admin.users.logins', compact('pageTitle', 'emptyMessage', 'login_logs'));
    }



    public function showEmailSingleForm($id)
    {
        $user = User::findOrFail($id);
        $pageTitle = 'Send Email To: ' . $user->username;
        return view('admin.users.email_single', compact('pageTitle', 'user'));
    }

    public function sendEmailSingle(Request $request, $id)
    {
        $request->validate([
            'message' => 'required|string|max:65000',
            'subject' => 'required|string|max:190',
        ]);

        $user = User::findOrFail($id);
        sendGeneralEmail($user->email, $request->subject, $request->message, $user->username);
        $notify[] = ['success', $user->username . ' will receive an email shortly.'];
        return back()->withNotify($notify);
    }

    public function transactions(Request $request, $id)
    {
        $user = User::findOrFail($id);
        if ($request->search) {
            $search = $request->search;
            $pageTitle = 'Search User Transactions : ' . $user->username;
            $transactions = $user->transactions()->where('trx', $search)->with('user')->orderBy('id','desc')->paginate(getPaginate());
            $emptyMessage = 'No transactions';
            return view('admin.reports.transactions', compact('pageTitle', 'search', 'user', 'transactions', 'emptyMessage'));
        }
        $pageTitle = 'User Transactions : ' . $user->username;
        $transactions = $user->transactions()->with('user')->orderBy('id','desc')->paginate(getPaginate());
        $emptyMessage = 'No transactions';
        return view('admin.reports.transactions', compact('pageTitle', 'user', 'transactions', 'emptyMessage'));
    }

    public function deposits(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $userId = $user->id;
        if ($request->search) {
            $search = $request->search;
            $pageTitle = 'Search User Deposits : ' . $user->username;
            $deposits = $user->deposits()->where('trx', $search)->orderBy('id','desc')->paginate(getPaginate());
            $emptyMessage = 'No deposits';
            return view('admin.deposit.log', compact('pageTitle', 'search', 'user', 'deposits', 'emptyMessage','userId'));
        }

        $pageTitle = 'User Deposit : ' . $user->username;
        $deposits = $user->deposits()->orderBy('id','desc')->paginate(getPaginate());
        $successful = $user->deposits()->orderBy('id','desc')->sum('amount');
        $pending = $user->deposits()->orderBy('id','desc')->sum('amount');
        $rejected = $user->deposits()->orderBy('id','desc')->sum('amount');
        $emptyMessage = 'No deposits';
        $scope = 'all';
        return view('admin.deposit.log', compact('pageTitle', 'user', 'deposits', 'emptyMessage','userId','scope','successful','pending','rejected'));
    }


    public function depViaMethod($method, $segment2, $segment3 = null){
        if ($segment3 === null) {
            $type = null;
            $userId = $segment2;
        } else {
            $type = $segment2;
            $userId = $segment3;
        }
        $method = Gateway::where('alias',$method)->firstOrFail();
        $user = User::findOrFail($userId);
        if ($type == 'approved') {
            $pageTitle = 'Approved Payment Via '.$method->name;
            $deposits = Deposit::where('method_code','>=',1000)->where('user_id',$user->id)->where('method_code',$method->code)->where('status', 1)->orderBy('id','desc')->with(['user', 'gateway'])->paginate(getPaginate());
        }elseif($type == 'rejected'){
            $pageTitle = 'Rejected Payment Via '.$method->name;
            $deposits = Deposit::where('method_code','>=',1000)->where('user_id',$user->id)->where('method_code',$method->code)->where('status', 3)->orderBy('id','desc')->with(['user', 'gateway'])->paginate(getPaginate());
        }elseif($type == 'successful'){
            $pageTitle = 'Successful Payment Via '.$method->name;
            $deposits = Deposit::where('status', 1)->where('user_id',$user->id)->where('method_code',$method->code)->orderBy('id','desc')->with(['user', 'gateway'])->paginate(getPaginate());
        }elseif($type == 'pending'){
            $pageTitle = 'Pending Payment Via '.$method->name;
            $deposits = Deposit::where('method_code','>=',1000)->where('user_id',$user->id)->where('method_code',$method->code)->where('status', 2)->orderBy('id','desc')->with(['user', 'gateway'])->paginate(getPaginate());
        }else{
            $pageTitle = 'Payment Via '.$method->name;
            $deposits = Deposit::where('status','!=',0)->where('user_id',$user->id)->where('method_code',$method->code)->orderBy('id','desc')->with(['user', 'gateway'])->paginate(getPaginate());
        }
        $pageTitle = 'Deposit History: '.$user->username.' Via '.$method->name;
        $methodAlias = $method->alias;
        $emptyMessage = 'Deposit Log';
        return view('admin.deposit.log', compact('pageTitle', 'emptyMessage', 'deposits','methodAlias','userId'));
    }



    public function withdrawals(Request $request, $id)
    {
        $user = User::findOrFail($id);
        if ($request->search) {
            $search = $request->search;
            $pageTitle = 'Search User Withdrawals : ' . $user->username;
            $withdrawals = $user->withdrawals()->where('trx', 'like',"%$search%")->orderBy('id','desc')->paginate(getPaginate());
            $emptyMessage = 'No withdrawals';
            return view('admin.withdraw.withdrawals', compact('pageTitle', 'user', 'search', 'withdrawals', 'emptyMessage'));
        }
        $pageTitle = 'User Withdrawals : ' . $user->username;
        $withdrawals = $user->withdrawals()->orderBy('id','desc')->paginate(getPaginate());
        $emptyMessage = 'No withdrawals';
        $userId = $user->id;
        return view('admin.withdraw.withdrawals', compact('pageTitle', 'user', 'withdrawals', 'emptyMessage','userId'));
    }

    public  function withdrawalsViaMethod($method,$type,$userId){
        $method = WithdrawMethod::findOrFail($method);
        $user = User::findOrFail($userId);
        if ($type == 'approved') {
            $pageTitle = 'Approved Withdrawal of '.$user->username.' Via '.$method->name;
            $withdrawals = Withdrawal::where('status', 1)->where('user_id',$user->id)->with(['user','method'])->orderBy('id','desc')->paginate(getPaginate());
        }elseif($type == 'rejected'){
            $pageTitle = 'Rejected Withdrawals of '.$user->username.' Via '.$method->name;
            $withdrawals = Withdrawal::where('status', 3)->where('user_id',$user->id)->with(['user','method'])->orderBy('id','desc')->paginate(getPaginate());

        }elseif($type == 'pending'){
            $pageTitle = 'Pending Withdrawals of '.$user->username.' Via '.$method->name;
            $withdrawals = Withdrawal::where('status', 2)->where('user_id',$user->id)->with(['user','method'])->orderBy('id','desc')->paginate(getPaginate());
        }else{
            $pageTitle = 'Withdrawals of '.$user->username.' Via '.$method->name;
            $withdrawals = Withdrawal::where('status', '!=', 0)->where('user_id',$user->id)->with(['user','method'])->orderBy('id','desc')->paginate(getPaginate());
        }
        $emptyMessage = 'Withdraw Log Not Found';
        return view('admin.withdraw.withdrawals', compact('pageTitle', 'withdrawals', 'emptyMessage','method'));
    }

    public function showEmailAllForm()
    {
        $pageTitle = 'Send Email To All Users';
        return view('admin.users.email_all', compact('pageTitle'));
    }

    public function sendEmailAll(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:65000',
            'subject' => 'required|string|max:190',
        ]);

        foreach (User::where('status', 1)->cursor() as $user) {
            sendGeneralEmail($user->email, $request->subject, $request->message, $user->username);
        }

        $notify[] = ['success', 'All users will receive an email shortly.'];
        return back()->withNotify($notify);
    }

    public function login($id){
        $user = User::findOrFail($id);
        Auth::login($user);
        return redirect()->route('user.home');
    }

    public function emailLog($id){
        $user = User::findOrFail($id);
        $pageTitle = 'Email log of '.$user->username;
        $logs = EmailLog::where('user_id',$id)->with('user')->orderBy('id','desc')->paginate(getPaginate());
        $emptyMessage = 'No data found';
        return view('admin.users.email_log', compact('pageTitle','logs','emptyMessage','user'));
    }

    public function emailDetails($id){
        $email = EmailLog::findOrFail($id);
        $pageTitle = 'Email details';
        return view('admin.users.email_details', compact('pageTitle','email'));
    }

    public function investment($id){
        $user = User::findOrFail($id);
        $pageTitle = 'Investment History of '.$user->fullname;
        $investments = Investment::where('user_id', $id)->latest()->paginate(getPaginate());
        $emptyMessage = 'Data Not Found';
        return view('admin.investment', compact('pageTitle', 'investments', 'emptyMessage'));
    }

      public function kycsettings()
    {
        $pageTitle = 'KYC Settings';
        $empty_message = 'No document found';
        $kyc = Kycsetting::get();
        return view('admin.kyc.settings', compact('pageTitle', 'empty_message', 'kyc'));
    }

        public function kycsettingspost(Request $request)
    {
         $request->validate([
            'type' => 'required|string|max:190',
        ]);
         $document = new Kycsetting();
         $document->type = $request->type;
         $document->save();
         $notify[] = ['success', 'KYC Document Added'];
         return back()->withNotify($notify);
    }

         public function editkycsettings(Request $request)
    {
         $request->validate([
            'type' => 'required|string|max:190',
        ]);
         $document = Kycsetting::whereId($request->id)->first();
         $document->type = $request->type;
         $document->status = $request->status;
         $document->save();
         $notify[] = ['success', 'KYC Document Updated'];
         return back()->withNotify($notify);
    }




      public function kycunVerifiedUsers()
    {
        $pageTitle = 'Unverified KYC';
        $empty_message = 'No document found';
        $kyc = Kyc::whereStatus(0)->orderBy('id','asc')->paginate(config('constants.table.default'));
        return view('admin.kyc.kyc', compact('pageTitle', 'empty_message', 'kyc'));
    }

    public function kycVerifiedUsers()
    {
        $pageTitle = 'Verified KYC';
        $empty_message = 'No document found';
        $kyc = Kyc::whereStatus(1)->orderBy('id','asc')->paginate(config('constants.table.default'));
        return view('admin.kyc.kyc', compact('pageTitle', 'empty_message', 'kyc'));
    }
    public function dkyc()
    {
        $pageTitle = 'Verified KYC';
        $empty_message = 'No document found';
        $kyc = Kyc::whereStatus(2)->orderBy('id','asc')->paginate(config('constants.table.default'));
        return view('admin.kyc.kyc', compact('pageTitle', 'empty_message', 'kyc'));
    }


       public function verifykyc($id)
    {
        $pageTitle = 'Pending KYC';
        $empty_message = 'No document found';
        $kyc = Kyc::whereId($id)->first();
        $user = User::whereId($kyc->user_id)->first();
        $kyc->status = 1;
        $kyc->save();

        $user->kyc = 1;
        $user->save();
        $notify[] = ['success', 'KYC Verified'];
        return back()->withNotify($notify);
    }
       public function declinekyc($id)
    {
        $pageTitle = 'Declined KYC';
        $empty_message = 'No document found';
        $kyc = Kyc::whereId($id)->first();
        $kyc->status = 2;
        $kyc->save();
        $notify[] = ['error', 'KYC Unverified'];
        return back()->withNotify($notify);
    }

     public function viewkyc($id)
    {
        $pageTitle = 'View KYC';
        $empty_message = 'No document found';
        $kyc = Kyc::whereId($id)->first();
        return view('admin.kyc.detail', compact('pageTitle', 'empty_message', 'kyc'));
    }


     //Support Ticket
    public function openticket()
    {
        $pageTitle = "Open Ticket";
        $supports = SupportTicket::whereStatus(0)->latest()->paginate(10);
        return view('admin.support.index', compact('supports', 'pageTitle'));
    }

    public function repliedticket()
    {
        $pageTitle = "Replied Ticket";
        $supports = SupportTicket::whereStatus(2)->orWhere('status', 1)->latest()->paginate(10);
        return view('admin.support.index', compact('supports', 'pageTitle'));
    }

    public function closedticket()
    {
        $pageTitle = "Closed Ticket";
        $supports = SupportTicket::whereStatus(3)->latest()->paginate(10);
        return view('admin.support.index', compact('supports', 'pageTitle'));
    }

     public function supportview($ticket)
    {
        $pageTitle = "View Ticket";
        $my_ticket = SupportTicket::where('ticket', $ticket)->latest()->first();
        $messages = SupportMessage::where('supportticket_id', $my_ticket->id)->latest()->get();
        $user = User::whereId($my_ticket->user_id)->first();
        $topics = Desk::all();
        // if ($my_ticket->user_id == Auth::id()) {
            return view('admin.support.view', compact('my_ticket', 'messages', 'pageTitle', 'user', 'topics'));
        // } else {
          //  return abort(404);
       //  }

    }


    public function supportMessageReply(Request $request, $id)
    {
        $ticket = SupportTicket::findOrFail($id);
        $message = new SupportMessage();

        if ($ticket->status != 3) {

            if ($request->replayTicket == 1) {
                $imgs = $request->file('attachments');
                $allowedExts = array('jpg', 'png', 'jpeg', 'pdf');

                $this->validate($request, [
                    'attachments' => [
                        'max:4096',
                        function ($attribute, $value, $fail) use ($imgs, $allowedExts) {
                            foreach ($imgs as $img) {
                                $ext = strtolower($img->getClientOriginalExtension());
                                if (($img->getClientSize() / 1000000) > 2) {
                                    return $fail("Images MAX  2MB ALLOW!");
                                }
                                if (!in_array($ext, $allowedExts)) {
                                    return $fail("Only png, jpg, jpeg, pdf images are allowed");
                                }
                            }
                            if (count($imgs) > 5) {
                                return $fail("Maximum 5 images can be uploaded");
                            }
                        },
                    ],
                    'message' => 'required',
                ]);

                $ticket->status = 2;
                $ticket->save();

                $message->supportticket_id = $ticket->id;
                $message->type = 2;
                $message->message = $request->message;
                $message->save();

                if ($request->hasFile('attachments')) {
                    foreach ($request->file('attachments') as $image) {
                        $filename = rand(1000, 9999) . time() . '.' . $image->getClientOriginalExtension();
                        $image->move(public_path('assets/images/support'), $filename);
                        SupportAttachment::create([
                            'support_message_id' => $message->id,
                            'image' => $filename,
                        ]);
                    }
                }

                 $notify[] = ['success', 'Support ticket replied successfully!'];
                 return back()->withNotify($notify);


            } elseif ($request->replayTicket == 2) {
                $ticket->status = 3;
                $ticket->save();
                $notify = ['success', 'Support ticket closed successfully!'];
                return back()->withNotify($notify);
            }

        } else {
            $notify = ['error', 'Support ticket already closed!'];
            return back()->withNotify($notify);
        }
         $notify[] = ['success', 'Support ticket replied successfully!'];
                 return back()->withNotify($notify);

    }


      public function ticketDownload($ticket_id)
    {
        $attachment = SupportAttachment::findOrFail(decrypt($ticket_id));
        $file = $attachment->image;
        $full_path = public_path('assets/images/support/' . $file);

        $title = str_slug($attachment->supportMessage->ticket->subject);
        $ext = pathinfo($file, PATHINFO_EXTENSION);
        $mimetype = mime_content_type($full_path);


        header('Content-Disposition: attachment; filename="' . $title . '.' . $ext . '";');
        header("Content-Type: " . $mimetype);
        return readfile($full_path);
    }

    public function ticketDelete(Request $request)
    {
        $message = SupportMessage::where('id', $request->message_id)->latest()->firstOrFail();

        if ($message->ticket->user_id != Auth::id()) {
            $notify[] = ['error', 'Unauthorized!'];
            return back()->withNotify($notify);
        }

        if ($message->attachments()->count() > 0) {
            foreach ($message->attachments as $img) {
                @unlink(public_path('assets/images/support/' . $img->image));
                $img->delete();
            }
        }
        $message->delete();

        $notify[] = ['success', 'Deleted successfully.'];
        return back()->withNotify($notify);
    }








}
