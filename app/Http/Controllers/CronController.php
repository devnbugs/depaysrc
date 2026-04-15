<?php

namespace App\Http\Controllers;

use App\Models\Cryptotrx;
use App\Models\Cryptowallet;
use App\Models\Investment;
use App\Models\Plan;
use App\Models\Transaction;
use App\Models\GeneralSetting;
use App\Models\Admin;
use App\Models\LoanPlan;
use App\Models\LoanPay;
use App\Models\Loan;
use App\Models\SavingPay;
use App\Models\Savings;
use App\Models\User;
use Carbon\Carbon;

class CronController extends Controller
{


    public function investment(){


        try{
             $general = GeneralSetting::first();

            $investments = Investment::where('status', 0)  // Status: 0=>Running, 1=>Completed
                                         ->whereDate('next_return_date', '<=', Carbon::now())
                                         ->get();

            foreach($investments as $index => $data){
                $plan = Plan::where('id', $data->plan_id)->first();
                $user = $data->user;

                if($data->usd == 1)
                {
                $user->invest_return += $data->interest_amount;
                }
                else
                {
                $user->invest_returnusd += $data->interest_amount;
                }

                $user->save();

                $data->next_return_date = Carbon::parse($data->next_return_date)->addDay($plan->timer ?? 1);
                $data->total_paid += 1;

                if($data->total_paid >= $data->total_return){
                   $data->status = 1;
                }

                $data->save();

                $transaction = new Transaction();
                $transaction->user_id = $data->user_id;
                $transaction->amount = $data->interest_amount;
                $transaction->charge = 0;
                $transaction->post_balance = $user->balance;
                $transaction->trx_type = '+';
                $transaction->trx = getTrx();
                $transaction->details = 'Got Interest From '.$data->plan->name;
                $transaction->save();

                if($general->invest_return_commission == 1)
                {
                $commissionType =  'Commission Rewarded For '. number_format($request->amount) . ' '.$general->cur_text.' Investment Return';
                levelCommision($user->id, $data->interest_amount, $commissionType);
                }



            }

        }catch(\Exception $ex){
            $admin = Admin::first();
            sendGeneralEmail($admin->email, $ex->getMessage(), $ex->getMessage(), '');
            \Log::error('CronController -> investment() line '. __LINE__ .': '.$ex->getMessage() ."\n");
        }

        return 'Investment Cron Successful';


    }



      public function loan(){

            try{
            $general = GeneralSetting::first();
            $loan = Loan::where('status', 1)->whereDate('next_penalty', '<=', Carbon::now())->where('expire', '<=', Carbon::now())->get();
             // Status: 1=>Running, 2=>Completed
             //return $loan;

            foreach($loan as $data)
            {
            $plan = LoanPlan::where('id', $data->plan_id)->first();
            $user = User::where('id', $data->user_id)->first();
            $balance = $data->total - $data->paid;
            $code = getTrx();

            $penalty = $data->amount/100*$plan->penalty;

            if($balance <= $user->balance)
                {
            // Start Deduct Loan Balance From User Wallet
            $user->balance -= $balance;
            $user->save();

            $pay = new LoanPay();
            $pay->user_id = $user->id;
            $pay->loan_id = $data->reference;
            $pay->plan_id = $data->plan_id;
            $pay->amount = $balance;
            $pay->balance = 0;
            $pay->trx = $code;
            $pay->status = 1;
            $pay->save();

            $transaction = new Transaction();
            $transaction->user_id = $user->id;
            $transaction->amount = $balance;
            $transaction->post_balance = $user->balance;
            $transaction->charge = 0;
            $transaction->trx_type = '-';
            $transaction->details = 'Fund Debited From Wallet To Service Loan Repayment';
            $transaction->trx = $code;
            $transaction->save();

            $data->status = 2;
            $data->paid += $balance;
            $data->save();

            //return 'Loan Deducted';
            // End Deduct Loan Balance From User Wallet
            }

            elseif($balance > $user->balance)
            {
            if($user->balance > 0)
            {

            // Start Deduct Loan Balance From User Wallet
            $left = $balance - $user->balance;
            //return $remove;

            $data->paid += $user->balance;
            $data->total += $penalty;
            $data->save();

            $pay = new LoanPay();
            $pay->user_id = $user->id;
            $pay->loan_id = $data->reference;
            $pay->plan_id = $data->plan_id;
            $pay->amount = $user->balance;
            $pay->balance = $left;
            $pay->trx = $code;
            $pay->status = 1;
            $pay->save();

            $transaction = new Transaction();
            $transaction->user_id = $user->id;
            $transaction->amount = $user->balance;
            $transaction->post_balance = 0;
            $transaction->charge = 0;
            $transaction->trx_type = '-';
            $transaction->details = 'Fund Debited From Wallet To Service Loan Repayment';
            $transaction->trx = $code;
            $transaction->save();

            $data->total += $penalty;
            $data->penalty += $penalty;
            $data->next_penalty = Carbon::parse(Carbon::now())->addMonth(1);
            $data->save();


            $user->balance -= $user->balance;
            $user->save();
            //return 'Loan Deducted But Remain Balance';
            // End Deduct Loan Balance From User Wallet
            }
             else
            {

            $data->total += $penalty;
            $data->penalty += $penalty;
            $data->next_penalty = Carbon::parse(Carbon::now())->addMonth(1);
            $data->save();
            //return 'Loan Penalty Applied';


            }
            }

            }

        }
        catch(\Exception $ex){
            $admin = Admin::first();
            sendGeneralEmail($admin->email, $ex->getMessage(), $ex->getMessage(), '');
            \Log::error('CronController -> investment() line '. __LINE__ .': '.$ex->getMessage() ."\n");
        }
         return 'Loan Cron Successful';




    }


      public function savings(){

            try{
            $general = GeneralSetting::first();
            $target = Savings::where('status', 1)->where('mature', '<=', Carbon::now())->get();
            $recurrent = Savings::where('status', 1)->where('next_recurrent', '<=', Carbon::now())->get();
            //return $recurrent;
            foreach($target as $data)
            {
            $user = User::where('id', $data->user_id)->first();
            $user->balance += $data->balance;
            $user->save();


            $transaction = new Transaction();
            $transaction->user_id = $user->id;
            $transaction->amount = $data->balance;
            $transaction->post_balance = $user->balance;
            $transaction->charge = 0;
            $transaction->trx_type = '+';
            $transaction->details = 'Savings Credited To Wallet On Due Date';
            $transaction->trx = getTrx();
            $transaction->save();

            $data->status = 0;
            $data->balance = 0;
            $data->save();
            }

            foreach($recurrent as $recdata)
            {
            //return $recdata;

            $user = User::where('id', $recdata->user_id)->first();
            if($recdata->recurrent > $recdata->recurrent_count)
            {

                 if($user->balance >= $recdata->amount)
                 {
                 $user->balance -= $recdata->amount;
                 $user->save();

                 $recdata->balance += $recdata->amount;
                 $recdata->recurrent_count += 1;
                 $recdata->next_recurrent = Carbon::parse(Carbon::now())->addDays($recdata->cycle);
                 $recdata->save();

                 $code = getTrx();
                 $pay = new SavingPay();
                 $pay->user_id = $user->id;
                 $pay->loan_id = $recdata->reference;
                 $pay->plan_id = $recdata->type;
                 $pay->amount =  $recdata->amount;
                 $pay->balance = $recdata->balance;
                 $pay->trx = $code;
                 $pay->status = 1;
                 $pay->save();

                 $transaction = new Transaction();
                 $transaction->user_id = $user->id;
                 $transaction->amount = $recdata->amount;
                 $transaction->post_balance = $user->balance;
                 $transaction->charge = 0;
                 $transaction->trx_type = '-';
                 $transaction->details = 'Fund Debited From Wallet To Service Recurrent Savings';
                 $transaction->trx = $code;
                 $transaction->save();

                 }

            }
                 if($recdata->recurrent <= $recdata->recurrent_count)
                 {
                 $user->balance += $recdata->balance;
                 $user->save();

                 $recdata->status = 0;
                 $recdata->balance = 0;
                 $recdata->save();
                 }


            }

        }
        catch(\Exception $ex){
            $admin = Admin::first();
            sendGeneralEmail($admin->email, $ex->getMessage(), $ex->getMessage(), '');
            \Log::error('CronController -> investment() line '. __LINE__ .': '.$ex->getMessage() ."\n");
        }
         return 'Savings Cron Successful';

    }



      public function webhook(Request $request)
    {

        return response(['Message'=>'Wallet Credited'], 200);
        $input = $request->all();
        $u = Cryptowallet::where('address', $input['address'])->first();
        $currency = Currency::whereSymbol($input['coin_short_name'])->first();
        $amount = $input['amount'];

        $baseurl = "https://coinremitter.com/api/v3/get-coin-rate";
		$curl = curl_init();
		curl_setopt_array($curl, array(
		  CURLOPT_URL => $baseurl,
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => '',
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 0,
		  CURLOPT_FOLLOWLOCATION => true,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => 'GET',
		));
		$response = curl_exec($curl);
		$reply = json_decode($response,true);
		curl_close($curl);
		$rate = $reply['data'][$currency->symbol]['price'];
        $usd = $rate * $amount;



        if ($u) {
            $receive = Cryptotrx::where('trxid', $input['id'])->whereType('receive')->first();
            //$send = Cryptotrx::where('trxid', $input['id'])->whereType('send')->first();

            if($input['type'] == 'receive')
        {
            if(!$receive)
            {
            $u->balance += $amount;
            $u->save();

            $w['user_id'] = $u->user_id;
            $w['coin_id'] = $u->coin_id;
            $w['amount'] = $input['amount'];
            $w['usd'] = $usd;
            $w['address'] = $input['address'];
            $w['type'] = $input['type'];
            $w['trxid'] = $input['id'];
            $w['hash'] = $input['txid'];
            $w['explorer_url'] = $input['explorer_url'];
            $w['wallet_id'] = $input['wallet_id'];
            $w['status'] = 1;
            $result = Cryptotrx::create($w);
            //return response(['Message'=>'Wallet Credited'], 200);
            }

         }

            return response(['Message'=>'Wallet Credited'], 200);


        }
        elseif(!$us){
            return "Wallet not found";
        }
        else{
            return "Transaction Not Found";
        }
    }




}
