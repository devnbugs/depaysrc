<?php

namespace App\Http\Controllers;

use App\Lib\GoogleAuthenticator;
use App\Models\AdminNotification;
use App\Models\GeneralSetting;
use App\Models\Transaction;
use App\Models\WithdrawMethod;
use App\Models\Withdrawal;
use App\Models\Deposit;
use App\Models\LoanPlan;
use App\Models\LoanPay;
use App\Models\Loan;
use App\Models\Investment;
use App\Rules\FileTypeValidate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Carbon\Carbon;

class LoanController extends Controller
{
    public function __construct()
    {
        $this->activeTemplate = activeTemplate();
    }

    public function requestloan()
    {
        $pageTitle = 'Request Loan';
        $user = Auth::user();
        $plans = LoanPlan::latest()->where('status', 1)->get();
        $emptyMessage = 'No Loan Plan Found At The Moment. Please Check Back Later';
        return view($this->activeTemplate . 'user.loan.request', compact(
            'pageTitle',
            'user',
            'plans'
        ));
    }



    public function requestsubmit(Request $request)
    {
        $request->validate([
            'amount' => 'required|string|max:50',
            'plan' => 'required|string|max:50',
            'duration' => 'required|string',
        ],[
            'amount.required'=>'Please Enter Loan Amount',
            'duration.required'=>'Please Enter Loan Duration',
            'plan.required'=>'Please Select A Loan Plan'
        ]);

        $user = Auth::user();

        $plan = LoanPlan::where('id', $request->plan)->where('status', 1)->firstOrFail();
        $running = Loan::where('user_id', $user->id)->where('status', 1)->first();
        $pending = Loan::where('user_id', $user->id)->where('status', 0)->first();
        $user = auth()->user();
        if ($request->amount < $plan->min) {
            $notify[] = ['error', 'Your requested amount is smaller than minimum amount.'];
            return back()->withNotify($notify);
        }
        if ($request->amount > $plan->max) {
            $notify[] = ['error', 'Your requested amount is larger than maximum amount.'];
            return back()->withNotify($notify);
        }

        if ($request->duration > $plan->duration) {
            $notify[] = ['error', 'Requested duration is greater than the maximum duration'];
            return back()->withNotify($notify);
        }

        if ($running) {
            $notify[] = ['error', 'You have a running loan. Please Pay Up Loan'];
            return back()->withNotify($notify);
        }

        if ($pending) {
            $notify[] = ['error', 'You have a pending loan request. Please wait for processing'];
            return back()->withNotify($notify);
        }


        $interest = 0 + ($request->amount * $plan->fee / 100);
        $total = $request->amount + $interest;
        $now = Carbon::now();
        $expire = Carbon::parse($now)->addMonths($request->duration);

        $loan = new Loan();
        $loan->plan_id = $plan->id; // Plan method ID
        $loan->user_id = $user->id;
        $loan->amount = $request->amount;
        $loan->interest = $interest;
        $loan->total = $total;
        $loan->status = 0;
        $loan->reference = getTrx();
        $loan->expire = $expire;
        $loan->duration = $request->duration;
        $loan->save();

        $notify[] = ['success', 'Loan Request Submitted Successfully.'];
        return back()->withNotify($notify);
    }

    public function myloan()
    {
        $pageTitle = 'My Loan Requests';
        $user = Auth::user();
        $loan = Loan::latest()->where('user_id', $user->id)->paginate(10);
        $emptyMessage = "Data Not Found";

        return view($this->activeTemplate . 'user.loan.myloan', compact('pageTitle','loan','emptyMessage'));
    }


    public function viewloan($id)
    {

        $user = Auth::user();
        $loan = Loan::where('user_id', $user->id)->whereReference($id)->first();
         if (!$loan) {
            $notify[] = ['error', 'Invalid Loan Request.'];
            return back()->withNotify($notify);
        }

        if ($loan->status == 0) {
            $notify[] = ['error', 'Please wait while we process this loan'];
            return back()->withNotify($notify);
        }
        if ($loan->status == 3) {
            $notify[] = ['error', 'Loan was rejected'];
            return back()->withNotify($notify);
        }

        $pageTitle = 'My Loan';
        $plan = LoanPlan::where('id', $loan->plan_id)->where('status', 1)->first();
        if (!$plan) {
            $notify[] = ['error', 'Invalid Loan Plan.'];
            return back()->withNotify($notify);
        }

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

        $data['jan'] = LoanPay::where('user_id', $user->id)->whereStatus(1)->whereYear('created_at', $year)->whereLoanId($id)->whereMonth('created_at', $jan)->sum('amount');
        $data['feb'] = LoanPay::where('user_id', $user->id)->whereStatus(1)->whereYear('created_at', $year)->whereLoanId($id)->whereMonth('created_at', $feb)->sum('amount');
        $data['mar'] = LoanPay::where('user_id', $user->id)->whereStatus(1)->whereYear('created_at', $year)->whereLoanId($id)->whereMonth('created_at', $mar)->sum('amount');
        $data['apr'] = LoanPay::where('user_id', $user->id)->whereStatus(1)->whereYear('created_at', $year)->whereLoanId($id)->whereMonth('created_at', $apr)->sum('amount');
        $data['may'] = LoanPay::where('user_id', $user->id)->whereStatus(1)->whereYear('created_at', $year)->whereLoanId($id)->whereMonth('created_at', $may)->sum('amount');
        $data['jun'] = LoanPay::where('user_id', $user->id)->whereStatus(1)->whereYear('created_at', $year)->whereLoanId($id)->whereMonth('created_at', $jun)->sum('amount');
        $data['jul'] = LoanPay::where('user_id', $user->id)->whereStatus(1)->whereYear('created_at', $year)->whereLoanId($id)->whereMonth('created_at', $jul)->sum('amount');
        $data['aug'] = LoanPay::where('user_id', $user->id)->whereStatus(1)->whereYear('created_at', $year)->whereLoanId($id)->whereMonth('created_at', $aug)->sum('amount');
        $data['sep'] = LoanPay::where('user_id', $user->id)->whereStatus(1)->whereYear('created_at', $year)->whereLoanId($id)->whereMonth('created_at', $sep)->sum('amount');
        $data['oct'] = LoanPay::where('user_id', $user->id)->whereStatus(1)->whereYear('created_at', $year)->whereLoanId($id)->whereMonth('created_at', $oct)->sum('amount');
        $data['nov'] = LoanPay::where('user_id', $user->id)->whereStatus(1)->whereYear('created_at', $year)->whereLoanId($id)->whereMonth('created_at', $nov)->sum('amount');
        $data['dec'] = LoanPay::where('user_id', $user->id)->whereStatus(1)->whereYear('created_at', $year)->whereLoanId($id)->whereMonth('created_at', $dec)->sum('amount');


        $pay = LoanPay::where('user_id', $user->id)->whereLoanId($id)->get();
        $sum = LoanPay::where('user_id', $user->id)->whereLoanId($id)->sum('amount');
        return view($this->activeTemplate.'user.loan.view',$data, compact('pageTitle','loan','pay','sum'));
    }

    public function loanpay(Request $request,$id)
    {
        $this->validate($request, [
            'amount' => 'required|numeric'
        ]);

        $user = auth()->user();
        $loan = Loan::where('user_id', $user->id)->whereReference($id)->first();
        if (!$loan) {
            $notify[] = ['error', 'Invalid Loan Request.'];
            return back()->withNotify($notify);
        }

        if ($loan->status == 2) {
            $notify[] = ['error', 'Loan has already been cleared'];
            return back()->withNotify($notify);
        }
        if ($loan->status == 3) {
            $notify[] = ['error', 'Loan was rejected'];
            return back()->withNotify($notify);
        }

        $balance = $loan->total - $loan->paid;

         if ($request->amount > $balance) {
            $notify[] = ['error', 'Amount Greater Than Loan Balance'];
            return back()->withNotify($notify);
        }

        if ($balance < 1) {
            $loan->status = 2;
            $loan->save();
            $notify[] = ['success', 'Loan has been cleared.'];
            return back()->withNotify($notify);
        }

        if ($request->amount > $user->balance)
        {
            $notify[] = ['error', 'You do not have sufficient balance to pay this loan.'];
            return back()->withNotify($notify);
        }

         if ($user->balance >= $request->amount)
         {
            $loan->paid += $request->amount;
            $loan->save();

            $user->balance -= $request->amount;
            $user->save();

            $balance = $loan->total - $loan->paid;

            if ($balance < 1)
            {
            $loan->status = 2;
            $loan->save();
            }

        $code = getTrx();
        $pay = new LoanPay();
        $pay->user_id = $user->id;
        $pay->loan_id = $loan->reference;
        $pay->plan_id = $loan->plan_id;
        $pay->amount = $request->amount;
        $pay->balance = $balance;
        $pay->trx = $code;
        $pay->status = 1;
        $pay->save();



        $transaction = new Transaction();
            $transaction->user_id = $user->id;
            $transaction->amount = $request->amount;
            $transaction->post_balance = $user->balance;
            $transaction->charge = 0;
            $transaction->trx_type = '-';
            $transaction->details = 'Fund Debited From Wallet To Service Loan Repayment';
            $transaction->trx = $code;
            $transaction->save();

        $notify[] = ['success', 'Payment Was Successful'];
        return back()->withNotify($notify);

         }
          $notify[] = ['error', 'Sorry we cant process this payment at the moment.'];
          return back()->withNotify($notify);
    }





}
