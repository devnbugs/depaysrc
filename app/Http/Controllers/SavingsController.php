<?php

namespace App\Http\Controllers;

use App\Lib\GoogleAuthenticator;
use App\Models\AdminNotification;
use App\Models\GeneralSetting;
use App\Models\Transaction;
use App\Models\WithdrawMethod;
use App\Models\Withdrawal;
use App\Models\Deposit;
use App\Models\SavingPay;
use App\Models\Savings;
use App\Models\Investment;
use App\Rules\FileTypeValidate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Carbon\Carbon;

class SavingsController extends Controller
{
    public function __construct()
    {
        $this->activeTemplate = activeTemplate();
    }

    public function requestsavings()
    {
        $pageTitle = 'New Savings';
        $user = Auth::user();
        return view($this->activeTemplate . 'user.savings.request', compact(
            'pageTitle',
            'user'
        ));
    }



    public function requestsubmit(Request $request)
    {

    if($request->type == 1)
    {

        $request->validate([
            'ramount' => 'required|int|min:100',
            'cycle' => 'required|int|max:30',
            'recurrent' => 'required|int|max:30',
        ],[
            'ramount.required'=>'Please Enter An Amount',
            'cycle.required'=>'Please Please Select Recurrent Cycle',
            'recurrent.required'=>'Please Enter Recurrent Cycle'
        ]);
        $user = Auth::user();
        if ($user->balance < $request->ramount) {
            $notify[] = ['error', 'You dont have enough balance to start this recurrent savings plan.'];
            return back()->withNotify($notify);
        }
    }
    else
    {

    $request->validate([
            'tamount' => 'required|int|min:100',
            'mature' => 'required|string',
        ],[
            'tamount.required'=>'Please Enter Targeted Amount',
            'mature.required'=>'Please Set Maturity Date',
        ]);

    }

        $user = Auth::user();

        $save = new Savings();
        $save->type = $request->type; // Plan method ID
        $save->user_id = $user->id;
        if($request->type == 1)
         {
        $user->balance -= $request->amount;
        $user->save();

        $save->balance += $request->amount;

        $save->amount = $request->ramount;
        $save->cycle = $request->cycle ?? 0;
        $save->next_recurrent = Carbon::parse(Carbon::now())->addDay($request->cycle ?? 1);
        $save->recurrent = $request->recurrent ?? 0;
         }
        else
        {
        $save->amount = $request->tamount;
        $save->mature = $request->mature ?? 0;
        }
        $save->status = 1;
        $save->reference = getTrx();

        $save->save();

        $notify[] = ['success', 'Saving Plan Created Successfully.'];
        return back()->withNotify($notify);
    }

    public function mysavings()
    {
        $pageTitle = 'My Savings Plan';
        $user = Auth::user();
        $saved = Savings::where('user_id', $user->id)->paginate(10);
        $emptyMessage = "Data Not Found";

        return view($this->activeTemplate . 'user.savings.mysavings', compact('pageTitle','saved','emptyMessage'));
    }



    public function viewsaved($id)
    {

        $user = Auth::user();
        $saved = Savings::where('user_id', $user->id)->whereReference($id)->first();
         if (!$saved) {
            $notify[] = ['error', 'Invalid Savings Request.'];
            return back()->withNotify($notify);
        }


        $pageTitle = 'My Savings Log';


        $year = date('Y');
        $month = date('m');
        $day = date('d');
        $jan = '01';
        $feb = '02';
        $mar = '03';
        $apr = '04';
        $may = '05';
        $jun = '06';
        $jul = '07';
        $aug = '08';
        $sep = '09';
        $oct = '10';
        $nov = '11';
        $dec = '12';

        $data['jan'] = SavingPay::where('user_id', $user->id)->whereStatus(1)->whereYear('created_at', $year)->whereLoanId($id)->whereMonth('created_at', $jan)->sum('amount');
        $data['feb'] = SavingPay::where('user_id', $user->id)->whereStatus(1)->whereYear('created_at', $year)->whereLoanId($id)->whereMonth('created_at', $feb)->sum('amount');
        $data['mar'] = SavingPay::where('user_id', $user->id)->whereStatus(1)->whereYear('created_at', $year)->whereLoanId($id)->whereMonth('created_at', $mar)->sum('amount');
        $data['apr'] = SavingPay::where('user_id', $user->id)->whereStatus(1)->whereYear('created_at', $year)->whereLoanId($id)->whereMonth('created_at', $apr)->sum('amount');
        $data['may'] = SavingPay::where('user_id', $user->id)->whereStatus(1)->whereYear('created_at', $year)->whereLoanId($id)->whereMonth('created_at', $may)->sum('amount');
        $data['jun'] = SavingPay::where('user_id', $user->id)->whereStatus(1)->whereYear('created_at', $year)->whereLoanId($id)->whereMonth('created_at', $jun)->sum('amount');
        $data['jul'] = SavingPay::where('user_id', $user->id)->whereStatus(1)->whereYear('created_at', $year)->whereLoanId($id)->whereMonth('created_at', $jul)->sum('amount');
        $data['aug'] = SavingPay::where('user_id', $user->id)->whereStatus(1)->whereYear('created_at', $year)->whereLoanId($id)->whereMonth('created_at', $aug)->sum('amount');
        $data['sep'] = SavingPay::where('user_id', $user->id)->whereStatus(1)->whereYear('created_at', $year)->whereLoanId($id)->whereMonth('created_at', $sep)->sum('amount');
        $data['oct'] = SavingPay::where('user_id', $user->id)->whereStatus(1)->whereYear('created_at', $year)->whereLoanId($id)->whereMonth('created_at', $oct)->sum('amount');
        $data['nov'] = SavingPay::where('user_id', $user->id)->whereStatus(1)->whereYear('created_at', $year)->whereLoanId($id)->whereMonth('created_at', $nov)->sum('amount');
        $data['dec'] = SavingPay::where('user_id', $user->id)->whereStatus(1)->whereYear('created_at', $year)->whereLoanId($id)->whereMonth('created_at', $dec)->sum('amount');


        $pay = SavingPay::where('user_id', $user->id)->whereLoanId($id)->get();
        $sum = SavingPay::where('user_id', $user->id)->whereLoanId($id)->sum('amount');
        $data['count'] = SavingPay::where('user_id', $user->id)->whereLoanId($id)->count();
        return view($this->activeTemplate.'user.savings.view',$data, compact('pageTitle','saved','pay','sum'));
    }

    public function savenow(Request $request,$id)
    {
        $this->validate($request, [
            'amount' => 'required|numeric'
        ]);

        $user = auth()->user();
        $save = Savings::where('user_id', $user->id)->whereReference($id)->first();
        if (!$save) {
            $notify[] = ['error', 'Invalid Savings Request.'];
            return back()->withNotify($notify);
        }

        if ($save->status == 0) {
            $notify[] = ['error', 'Savings plan has already been closed'];
            return back()->withNotify($notify);
        }



       if($save->type == 1)
       {

       if ($request->amount < $save->amount) {
            $notify[] = ['error', 'Amount Smaller Than Recurrent Amount'];
            return back()->withNotify($notify);
        }

       }



        if ($request->amount > $user->balance)
        {
            $notify[] = ['error', 'You do not have sufficient balance to Save On ThisPlan.'];
            return back()->withNotify($notify);
        }

         if ($user->balance >= $request->amount)
         {
            $save->balance += $request->amount;
            $save->save();


            $user->balance -= $request->amount;
            $user->save();

        $code = getTrx();
        $pay = new SavingPay();
        $pay->user_id = $user->id;
        $pay->loan_id = $save->reference;
        $pay->plan_id = $save->type;
        $pay->amount = $request->amount;
        $pay->balance = $save->balance;
        $pay->trx = $code;
        $pay->status = 1;
        $pay->save();

        $transaction = new Transaction();
            $transaction->user_id = $user->id;
            $transaction->amount = $request->amount;
            $transaction->post_balance = $user->balance;
            $transaction->charge = 0;
            $transaction->trx_type = '-';
            $transaction->details = 'Fund Debited From Wallet To Service Ongoing Savings';
            $transaction->trx = $code;
            $transaction->save();

        $notify[] = ['success', 'Payment Was Successful'];
        return back()->withNotify($notify);

         }
          $notify[] = ['error', 'Sorry we cant process this payment at the moment.'];
          return back()->withNotify($notify);
    }





}
