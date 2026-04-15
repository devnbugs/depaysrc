<?php

namespace App\Http\Controllers;

use App\Lib\GoogleAuthenticator;
use App\Models\AdminNotification;
use App\Models\GeneralSetting;
use App\Models\Transaction;
use App\Models\Cryptowallet;
use App\Models\Currency;
use App\Models\Cryptotrx;
use App\Rules\FileTypeValidate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Carbon\Carbon;

class WalletController extends Controller
{
    public function __construct()
    {
        $this->activeTemplate = activeTemplate();
    }

     public function wallet($id)
    {
        $currency = Currency::where('status', '!=', 0)->whereCanwallet(1)->whereSymbol($id)->first();
        $wallets = Cryptowallet::where('user_id', auth()->id())->whereStatus(1)->whereCoin_id($currency->id)->get();
        $unit = Cryptowallet::where('user_id', auth()->id())->whereStatus(1)->whereCoin_id($currency->id)->sum('balance');
        $page_title = $currency->name.' Wallet';
        GetCoinPrice();

        $general = GeneralSetting::first();
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
		  CURLOPT_POSTFIELDS => array('api_key' => $currency->apikey,'password' => $currency->apipass,'fiat_amount' => 1,'fiat_symbol' => 'USD'),
		));

		$response = curl_exec($curl);
		$reply = json_decode($response,true);
		curl_close($curl);
		//return $response;
		$trx = Cryptotrx::where('user_id', auth()->id())->whereCoin_id($currency->id)->take(5)->latest()->get();


		if (!isset($reply['msg'])){
		 $notify[] = ['error', 'An error occur. Contact server admin'];
            return back()->withNotify($notify);
         }
         if ($reply['msg'] != 'success'){
		 $notify[] = ['error', 'An error occur. Contact server admin'];
            return back()->withNotify($notify);
         }
         $rate = $reply['data'][$currency->symbol]['price'];
         $usd = $rate * $unit;
         foreach ($wallets as $dataw)
        {
         $usdrate = $rate * $dataw->balance;
         $dataw->usd = $usdrate;
         $dataw->save();
        }
         $pageTitle = $currency->name." Wallet";

        return view($this->activeTemplate. 'user.wallets.wallet', compact('trx','pageTitle','currency','wallets','unit','rate','usd'));
      }

    public function createwallet(Request $request, $id)
       {
        $this->validate($request, [
            'label' => 'required|max:10',
        ]);
        $page_title = 'Create Wallet';
        $currency = Currency::where('status', '!=', 0)->whereCanwallet(1)->whereSymbol($id)->first();
        $walletcount = Cryptowallet::where('user_id', auth()->id())->whereCoin_id($currency->id)->whereLabel($request->label)->where('status', 1)->count();
        if($walletcount > 0){
         $notify[] = ['error', 'You already have a '.$currency->name.' wallet with this label. Please try another label'];
            return back()->withNotify($notify);
        }
        $general = GeneralSetting::first();
        $label = $request->label;;
        $baseurl = "https://coinremitter.com/api/v3/".$currency->symbol."/get-new-address";
		$curl = curl_init();
		curl_setopt_array($curl, array(
		  CURLOPT_URL => $baseurl,
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => '',
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 0,
		  CURLOPT_FOLLOWLOCATION => true,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => 'POST',
		  CURLOPT_POSTFIELDS => array('api_key' => $currency->apikey,'password' => $currency->apipass,'label' => $label),
		));

		$response = curl_exec($curl);
		$reply = json_decode($response,true);
		curl_close($curl);
		//return $response;

		 if (!isset($reply['flag'])){
		 $notify[] = ['error', 'An error occur. Contact server admin'];
            return back()->withNotify($notify);
         }
         if ($reply['flag'] != '1'){
		 $notify[] = ['error', 'An error occur. Contact server admin'];
            return back()->withNotify($notify);
         }
         $address = $reply['data']['address'];
         $qrcode = $reply['data']['qr_code'];

         $w['address'] = $address;
         $w['qrcode'] = $qrcode;
         $w['coin_id'] = $currency->id;
         $w['label'] = $label;
         $w['user_id'] = Auth::id();
         $w['balance'] = 0;
         $w['status'] = 1;
         $result = Cryptowallet::create($w);

         if($result){
         $notify[] = ['success', 'Your new '.$currency->name.' wallet has been created successfully.'];
         return back()->withNotify($notify);
            }


    }

      public function sendfromwallet(Request $request)
    {
        $this->validate($request, [
            'wallet' => 'required',
            'currency' => 'required',
            'amount' => 'required|numeric'
        ]);
        $id = $request->id;
        $currency = Currency::where('id', $request->currency)->where('status', 1)->whereCanwallet(1)->first();
         if(!$currency){
         $notify[] = ['error', 'Invalid Currency or Currency Not Found'];
            return back()->withNotify($notify);
        }

        $wallet = Cryptowallet::where('user_id', auth()->id())->where('id', $id)->whereCoin_id($currency->id)->where('status', 1)->first();

        if(!$wallet){
         $notify[] = ['error', 'Invalid Wallet'];
            return back()->withNotify($notify);
        }

        $baseurl = "https://coinremitter.com/api/v3/".$currency->symbol."/get-fiat-to-crypto-rate";
		$curl = curl_init();
		curl_setopt_array($curl, array(
		  CURLOPT_URL => $baseurl,
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => '',
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 0,
		  CURLOPT_FOLLOWLOCATION => true,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => 'POST',
		  CURLOPT_POSTFIELDS => array('api_key' => $currency->apikey,'password' => $currency->apipass,'fiat_amount' => $request->amount,'fiat_symbol' => 'USD'),
		));

		$response = curl_exec($curl);
		$reply = json_decode($response,true);
		curl_close($curl);

		//return $response;

		if (!isset($reply['msg'])){
		 $notify[] = ['error', 'An error occur. Contact server admin'];
            return back()->withNotify($notify);
         }
         if ($reply['flag'] != '1'){
		 $notify[] = ['error', 'An error occur. Contact server admin'];
            return back()->withNotify($notify);
         }
         $unit = $reply['data']['crypto_amount'];

        if ($wallet->balance < $unit) {
            $notify[] = ['error', 'You do not have enough fund in your wallet to send.'];
            return back()->withNotify($notify);
        }

        else {
        $trx = getTrx();
        $general = GeneralSetting::first();
        $baseurl = "https://coinremitter.com/api/v3/".$currency->symbol."/validate-address";
		$curl = curl_init();
		curl_setopt_array($curl, array(
		  CURLOPT_URL => $baseurl,
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => '',
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 0,
		  CURLOPT_FOLLOWLOCATION => true,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => 'POST',
		  CURLOPT_POSTFIELDS => array('api_key' => $currency->apikey,'password' => $currency->apipass,'address' => $request->wallet),
		));

		$response = curl_exec($curl);
		$reply = json_decode($response,true);
		curl_close($curl);
		//return $response;

		if (!isset($reply['msg'])){
		 $notify[] = ['error', 'An error occur. Contact server admin'];
            return back()->withNotify($notify);
         }
         if ($reply['flag'] != '1'){
		 $notify[] = ['error', 'Invalid '.$currency->name.' Wallet Address'];
            return back()->withNotify($notify);
         }

          if ($reply['flag'] = '1'){
          $baseurl = "https://coinremitter.com/api/v3/".$currency->symbol."/withdraw";
		  $curl = curl_init();
		  curl_setopt_array($curl, array(
		  CURLOPT_URL => $baseurl,
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => '',
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 0,
		  CURLOPT_FOLLOWLOCATION => true,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => 'POST',
		  CURLOPT_POSTFIELDS => array('api_key' => $currency->apikey,'password' => $currency->apipass,'to_address' => $request->wallet,'amount' => $unit),
		));

		$response = curl_exec($curl);
		$reply = json_decode($response,true);
		curl_close($curl);

		//return $reply;

		if (!isset($reply['msg'])){
		 $notify[] = ['error', 'An error occur. Contact server admin'];
            return back()->withNotify($notify);
         }
         if ($reply['flag'] != '1'){
		 $notify[] = ['error', 'An error occur. Contact server admin'];
            return back()->withNotify($notify);
         }
          if ($reply['flag'] == '1'){

            $w['user_id'] = Auth::id();
            $w['coin_id'] = $currency->id;
            $w['amount'] = $unit;
            $w['to_address'] = $reply['data']['to_address'];
            $w['usd'] = $request->amount;
            $w['address'] = $wallet->address;
            $w['type'] = 'send';
            $w['hash'] = $reply['data']['txid'];
            $w['trxid'] = $reply['data']['id'];
            $w['explorer_url'] = $reply['data']['explorer_url'];
            $w['wallet_id'] = $reply['data']['wallet_id'];
            $w['status'] = 1;
            $result = Cryptotrx::create($w);


            $fee = $currency->fee/100;
            $charge = $fee*$unit;
            $total = $charge + $reply['data']['total_amount'];
            $wallet->balance -= $total;
            $wallet->save();

            if($result){
            $notify[] = ['success', 'You have successfully sent '.$currency->name.' to the wallet address.'];
            return back()->withNotify($notify);
            }
          }

          }

        }
    }

      public function viewwallet($id)
    {
        $pageTitle = 'View Asset';
        $wallet = Cryptowallet::where('user_id', auth()->id())->where('address', $id)->where('status', 1)->first();
         if(!$wallet){
         $notify[] = ['error', 'Invalid Wallet or Wallet Not Found'];
            return back()->withNotify($notify);
         }
        $currency = Currency::where('id', $wallet->coin_id)->where('status', 1)->whereCanwallet(1)->first();
         if(!$currency){
         $notify[] = ['error', 'Invalid Currency or Currency Not Found'];
            return back()->withNotify($notify);
         }

          $baseurl = "https://coinremitter.com/api/v3/".$currency->symbol."/get-transaction-by-address";
		$curl = curl_init();
		curl_setopt_array($curl, array(
		  CURLOPT_URL => $baseurl,
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => '',
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 0,
		  CURLOPT_FOLLOWLOCATION => true,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => 'POST',
		  CURLOPT_POSTFIELDS => array('api_key' => $currency->apikey,'password' => $currency->apipass,'address' => $wallet->address),
		));

		$response = curl_exec($curl);
		$reply = json_decode($response,true);
		curl_close($curl);
		//return $response;

         $general = GeneralSetting::first();
         $sent = Cryptotrx::where('user_id', auth()->id())->where('address', $id)->orderby('id','desc')->where('type', 'send')->get();
         $received = Cryptotrx::where('user_id', auth()->id())->where('address', $id)->orderby('id','desc')->whereType('receive')->get();
         //$trx = $reply['data'];
         $tsent = Cryptotrx::where('user_id', auth()->id())->where('address', $id)->orderby('id','desc')->where('type', 'send')->sum('usd');
         $tsentunit = Cryptotrx::where('user_id', auth()->id())->where('address', $id)->orderby('id','desc')->where('type', 'send')->sum('amount');
         $trec = Cryptotrx::where('user_id', auth()->id())->where('address', $id)->orderby('id','desc')->whereType('receive')->sum('usd');
         $trecunit = Cryptotrx::where('user_id', auth()->id())->where('address', $id)->orderby('id','desc')->whereType('receive')->sum('amount');
         $total = Cryptotrx::where('user_id', auth()->id())->where('address', $id)->orderby('id','desc')->sum('usd');
         $totalunit = Cryptotrx::where('user_id', auth()->id())->where('address', $id)->orderby('id','desc')->sum('amount');
        return view($this->activeTemplate. 'user.wallets.view-wallet', compact('pageTitle','currency','sent','wallet','received','tsent','trec','tsentunit','trecunit','total','totalunit'));
    }


      public function swapcoin()
    {
        $pageTitle = 'Swap Coin';
        $currency = Currency::where('status', '!=', 0)->whereCanswap(1)->orderBy('name','asc')->get();
        $wallets = Cryptowallet::where('user_id', auth()->id())->get();
        $trade = Cryptotrx::whereType('swap')->where('user_id', auth()->id())->get();
        GetCoinPrice();
        return view($this->activeTemplate. 'user.wallets.swap', compact('pageTitle','currency','wallets','trade'));
    }

      public function swapcoinpost(Request $request)
    {
        $this->validate($request, [
            'from' => 'required',
            'to' => 'required',
            'amount' => 'required',
        ]);
        GetCoinPrice();
        $from = Currency::where('id', $request->from)->where('status', 1)->whereCanswap(1)->first();
        $to = Currency::where('id', $request->to)->where('status', 1)->whereCanswap(1)->first();

         if($request->from == $request->to){
         $notify[] = ['error', 'You cant swap same currency'];
         return back()->withNotify($notify);
        }

        if(!$from){
         $notify[] = ['error', 'You cant swap from this currency'];
         return back()->withNotify($notify);
        }
        if(!$to){
         $notify[] = ['error', 'You cant swap to this currency'];
         return back()->withNotify($notify);
        }
        $fromwallet = Cryptowallet::where('coin_id', $request->from)->where('user_id', Auth::id())->first();
        $towallet = Cryptowallet::where('coin_id', $request->to)->where('user_id', Auth::id())->first();



        if(!$fromwallet){
         $notify[] = ['error', 'You dont have '.$from->name.' wallet yet. Please create one first'];
            return back()->withNotify($notify);
        }
        if(!$towallet){
         $notify[] = ['error', 'You dont have '.$to->name.' wallet yet. Please create one first'];
         return back()->withNotify($notify);
        }

        $charge = $request->amount/100*$from->swap;
        $total = $request->amount - $charge;
        $totalunit = $request->amount/$from->price;
        $fromunit = $total/$from->price;
        $tounit = $total/$to->price;

        $get = $tounit + $towallet->balance;
        $getunit = number_format($get,8);



        if ($totalunit > $fromwallet->balance) {
            $notify[] = ['error', 'Insufficient '.$from->name.' Balance'];
            return back()->withNotify($notify);
        }
         else {

         $tounit =  number_format($tounit,8);


            $fromwallet->balance -= $totalunit;
            $fromwallet->save();

            $towallet->balance += $getunit;
            $towallet->save();

            $w['user_id'] = Auth::id();
            $w['coin_id'] = $request->from;
            $w['amount'] = $total;
            $w['to_address'] = $towallet->address;
            $w['usd'] = $request->amount;
            $w['address'] = $fromwallet->address;
            $w['type'] = 'swap';
            $w['hash'] = $from->swap;
            $w['trxid'] = getTrx();
            $w['explorer_url'] = $from->name;
            $w['wallet_id'] = $to->name;
            $w['status'] = 1;
            $result = Cryptotrx::create($w);


             $notify[] = ['success', 'Your Swap was succcessful'];
            return back()->withNotify($notify);
        }
    }







}
