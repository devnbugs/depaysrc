<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Contact;
use Illuminate\Support\Facades\Auth;
use App\Models\Transaction;
use App\Models\WithdrawMethod;
use App\Models\Power;
use App\Models\Deposit;
use App\Models\Network;
use App\Models\Bill;
use App\Models\User;
use App\Models\Internetbundle;
use Illuminate\Support\Facades\Log;
use App\Models\Cabletvbundle;
use Illuminate\Support\Facades\Cache;
use App\Rules\FileTypeValidate;
use App\Rules\MonnifyTransactionReference;
use Barryvdh\DomPDF\Facade as PDF;



class BetaController extends Controller
{
    //
    public function index()
    {
        
        $pageTitle = 'Network';
        $user = auth()->user();

        return view('user.user.beta.realtime', compact(
            'pageTitle',
            'user'
        ));
        // Your code for the real-time functionality goes here
        //return view('realtime.index'); // Replace 'realtime.index' with your actual view name
    }
	
	//
    public function topup()
    {
        
        $pageTitle = 'Topup';
        $user = auth()->user();

        return view('user.user.beta.topup', compact(
            'pageTitle',
            'user'
        ));
        // Your code for the real-time functionality goes here
        //return view('realtime.index'); // Replace 'realtime.index' with your actual view name
    }
	
    public function create()
    {
        $pageTitle = 'Add Contact';
        $user = auth()->user();

        return view('user.user.beta.contacts.create', compact(
            'pageTitle',
            'user'
        ));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $contact = new Contact();
        $contact->name = $request->input('name');
        $contact->phone = $request->input('phone');
        $contact->remark = $request->input('remark');

        $user->contacts()->save($contact);

        $alertx[] = ['success', 'Contact Saved Successfully.'];
        return redirect()->route('user.beta.contacts.index')->withAlertx($alertx);
    }
    public function ContactIndex()
    {
        $user = auth()->user();
        $contacts = $user->contacts()->latest()->get();
        $pageTitle = 'Saved Contacts';

        return view('user.user.beta.contacts.index', compact(
            'pageTitle',
            'user',
            'contacts'
        ));
    }
    public function destroy($id)
    {
        $user = auth()->user();
        $deletedRows = $user->contacts()->whereKey($id)->delete();

        if ($deletedRows) {
            $alertx[] = ['success', 'Contact Deleted Successfully.'];
        } else {
            $alertx[] = ['error', 'Contact Not Found.'];
        }

        return redirect()->route('user.beta.contacts.index')->withAlertx($alertx);
    }
    public function checkMonnify(Request $request)
    {
        // Validate the request input
        $request->validate([
            'trx' => ['required'], // Assuming 'MonnifyTransactionReference' is a custom rule
        ]);

        $user = Auth::user();

        // Check if the transaction exists in the user's transactions
        $trxFound = Deposit::where('user_id', $user->id)
            ->where('trx', $request->input('trx'))
            ->where('status', 1)
            ->exists();

        if ($trxFound) {
            // The transaction exists in the user's transaction table
            // Handle it as needed
            $alertx[] = ['error', 'Transaction Already Verified.'];
            return back()->with('alertx', $alertx);
        }

        // Generate or retrieve the access token
        $accessToken = $user->monnify_token;

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Basic TUtfUFJPRF9aRVlaVVpEUkY2OlNVQ1RETFdSMjNLVjg2SkpBNFRHRFhZMVVaTFhVUktG',
        ])->post('https://api.monnify.com/api/v1/auth/login');

        // Log the raw response content of the access token request
        //Log::info('MonnifyTokenJob: Raw Access Token Response Content: ' . $response->body());

        $responseData = $response->json();

        if ($responseData['requestSuccessful']) {
            $accessToken = $responseData['responseBody']['accessToken'];

            // Save the access token in the user's 'monnify_token' attribute
            $user->monnify_token = $accessToken;
            $user->save();

            // Cache the access token for a specific duration (e.g., 1 hour)
            // Cache::put('monnify_access_token', $accessToken, 60); // 60 minutes
            //Log::info('CustomLogEntry: Raw Info - ' . json_encode($accessToken));
        }

        // Use the access token in the request headers for the 'checkStatus' request
        $transactionReference = $request->input('trx'); // Add this line to retrieve the transaction reference

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => "Bearer $accessToken",
        ])->get('https://api.monnify.com/api/v2/merchant/transactions/query', [
            'transactionReference' => $transactionReference,
        ]);

        //Log::info('CustomLogEntry: Raw Info - ' . json_encode($response));

        $responseData = $response->json();

        if (isset($responseData['requestSuccessful']) && $responseData['requestSuccessful'] === true) {
            if (isset($responseData['responseBody']['paymentStatus']) && $responseData['responseBody']['paymentStatus'] === 'PAID') {
                // Transaction is successful, handle it as needed
                $amountReceived = $responseData['responseBody']['amountPaid'];
                $amountToCreditUser = $amountReceived - 50;

                $user = User::where('email', $responseData['responseBody']['customer']['email'])->first();
                $user->username = $responseData['responseBody']['product']['reference'];

                $deposit = new Deposit();
                $deposit->user_id = $user->id;
                $deposit->amount = $amountToCreditUser;
                $deposit->charge = 50;
                $deposit->trx = $responseData['responseBody']['transactionReference'];
                $deposit->method_code = '104';
                $deposit->method_currency = 'NGN';
                $deposit->status = 1;
                $deposit->rate = 1;
                $deposit->admin_feedback = 'Automatic Funding Account';
                $deposit->final_amo += $amountToCreditUser;
                $deposit->save();

                $transaction = new Transaction();
                $transaction->user_id = $user->id;
                $transaction->trx = $responseData['responseBody']['transactionReference'];
                $transaction->amount = $amountToCreditUser;
                $transaction->trx_type = '+';
                $transaction->charge = 50;
                $transaction->details = 'Automatic Account Number Funding';
                $transaction->post_balance += $amountToCreditUser;
                $transaction->save();

                $user->balance += $amountToCreditUser;
                $user->save();

                $alertx[] = ['success', 'Transaction Verified and Credited.'];
                return redirect()->route('user.beta.trx.monnify')->with('alertx', $alertx);
                //Log::info('CustomLogEntry: Raw Info - ' . json_encode($responseData));
            } else {
                // Handle the case when the paymentStatus is not 'PAID'
            }
        } else {
            // Handle the case when 'requestSuccessful' is not true
            $alertx[] = ['error', 'Transaction Verification Failed.'];
            return redirect()->route('user.beta.trx.monnify')->with('alertx', $alertx);
        }
    }
    public function checkPaystack(Request $request)
    {
        // Validate the request input
        $request->validate([
            'trx' => ['required'], // Assuming 'MonnifyTransactionReference' is a custom rule
        ]);

        $user = Auth::user();

        // Check if the transaction exists in the user's transactions
        $trxFound = Deposit::where('user_id', $user->id)
            ->where('trx', $request->input('trx'))
            ->where('status', 1)
            ->exists();

        if ($trxFound) {
            // The transaction exists in the user's transaction table
            // Handle it as needed
            $alertx[] = ['error', 'Transaction Already Verified.'];
            return back()->with('alertx', $alertx);
        }

        // Generate or retrieve the access token
        $accessToken = $user->monnify_token;

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Basic TUtfUFJPRF9aRVlaVVpEUkY2OlNVQ1RETFdSMjNLVjg2SkpBNFRHRFhZMVVaTFhVUktG',
        ])->post('https://api.monnify.com/api/v1/auth/login');

        // Log the raw response content of the access token request
        //Log::info('MonnifyTokenJob: Raw Access Token Response Content: ' . $response->body());

        $responseData = $response->json();

        if ($responseData['requestSuccessful']) {
            $accessToken = $responseData['responseBody']['accessToken'];

            // Save the access token in the user's 'monnify_token' attribute
            $user->monnify_token = $accessToken;
            $user->save();

            // Cache the access token for a specific duration (e.g., 1 hour)
            // Cache::put('monnify_access_token', $accessToken, 60); // 60 minutes
            //Log::info('CustomLogEntry: Raw Info - ' . json_encode($accessToken));
        }

        // Use the access token in the request headers for the 'checkStatus' request
        $transactionReference = $request->input('trx'); // Add this line to retrieve the transaction reference

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => "Bearer $accessToken",
        ])->get('https://api.monnify.com/api/v2/merchant/transactions/query', [
            'transactionReference' => $transactionReference,
        ]);

        //Log::info('CustomLogEntry: Raw Info - ' . json_encode($response));

        $responseData = $response->json();

        if (isset($responseData['requestSuccessful']) && $responseData['requestSuccessful'] === true) {
            if (isset($responseData['responseBody']['paymentStatus']) && $responseData['responseBody']['paymentStatus'] === 'PAID') {
                // Transaction is successful, handle it as needed
                $amountReceived = $responseData['responseBody']['amountPaid'];
                $amountToCreditUser = $amountReceived - 50;

                $user = User::where('email', $responseData['responseBody']['customer']['email'])->first();
                $user->username = $responseData['responseBody']['product']['reference'];

                $deposit = new Deposit();
                $deposit->user_id = $user->id;
                $deposit->amount = $amountToCreditUser;
                $deposit->charge = 50;
                $deposit->trx = $responseData['responseBody']['transactionReference'];
                $deposit->method_code = '107';
                $deposit->method_currency = 'NGN';
                $deposit->status = 1;
                $deposit->rate = 1;
                $deposit->admin_feedback = 'Paystack Fund';
                $deposit->final_amo += $amountToCreditUser;
                $deposit->save();

                $transaction = new Transaction();
                $transaction->user_id = $user->id;
                $transaction->trx = $responseData['responseBody']['transactionReference'];
                $transaction->amount = $amountToCreditUser;
                $transaction->trx_type = '+';
                $transaction->charge = 50;
                $transaction->details = 'Automatic Account Number Funding';
                $transaction->post_balance += $amountToCreditUser;
                $transaction->save();

                $user->balance += $amountToCreditUser;
                $user->save();

                $alertx[] = ['success', 'Transaction Verified and Credited.'];
                return redirect()->route('user.beta.trx.paystack')->with('alertx', $alertx);
                //Log::info('CustomLogEntry: Raw Info - ' . json_encode($responseData));
            } else {
                // Handle the case when the paymentStatus is not 'PAID'
            }
        } else {
            // Handle the case when 'requestSuccessful' is not true
            $alertx[] = ['error', 'Transaction Verification Failed.'];
            return redirect()->route('user.beta.trx.paystack')->with('alertx', $alertx);
        }
    }

    public function indexcheckStatus()
    {
        $pageTitle = 'Verify Monnify';
        $user = auth()->user();

        return view('user.user.beta.trx.monnify', compact(
            'pageTitle',
            'user'
        ));
    }
    public function indexPaystack()
    {
        $pageTitle = 'Verify Paystack';
        $user = auth()->user();

        return view('user.user.beta.trx.paystack', compact(
            'pageTitle',
            'user'
        ));
    }
    public function indexCheck()
    {
        $pageTitle = 'Verify Paystack';
        $user = auth()->user();

        return view('user.user.beta.trx.check', compact(
            'pageTitle',
            'user'
        ));
    }
    public function showReceipt($billId)
    {
        $pageTitle = 'Transaction Receipt';
        $user = Auth::user();
        $bill = Bill::where('user_id', $user->id)->findOrFail($billId);
        $trx = Bill::all();
        $internet = Internetbundle::where('cost', $bill->amount)->where('network', $bill->network)->first();
        $name = $internet ? $internet->name : null;

        return view('user.user.beta.receipt', compact('pageTitle', 'user', 'bill', 'internet', 'name', 'trx'));
    }

    public function printReceipt($billId)
    {
        $bill = Bill::findOrFail($billId);

        return view('receipt', compact('bill'));
    }

    private function generatePdf($html)
    {
    $pdf = new \Mpdf\Mpdf();
    $pdf->WriteHTML($html);
    return $pdf->Output('', 'S'); // 'S' option returns the PDF as a string
    }
    public function dataTrxLog()
    {
        
        $pageTitle = 'Data History';
        $user = auth()->user();
        $bills = Bill::where('user_id', $user->id)->where('type', 2)->get();

        return view('user.user.beta.trx.data', compact(
            'pageTitle',
            'user',
            'bills'
        ));
        // Your code for the real-time functionality goes here
        //return view('realtime.index'); // Replace 'realtime.index' with your actual view name
    }
    public function airTrxLog()
    {
        
        $pageTitle = 'Airtime History';
        $user = auth()->user();
        $bills = Bill::where('user_id', $user->id)->where('type', 1)->get();

        return view('user.user.beta.trx.airtime', compact(
            'pageTitle',
            'user',
            'bills'
        ));
        // Your code for the real-time functionality goes here
        //return view('realtime.index'); // Replace 'realtime.index' with your actual view name
    }
    public function aircTrxLog()
    {
        
        $pageTitle = 'Airtime2Cash History';
        $user = auth()->user();
        $bills = Bill::where('user_id', $user->id)->where('type', 9)->get();

        return view('user.user.beta.trx.air2cash', compact(
            'pageTitle',
            'user',
            'bills'
        ));
        // Your code for the real-time functionality goes here
        //return view('realtime.index'); // Replace 'realtime.index' with your actual view name
    }
    public function allTrxLog()
    {
        
        $pageTitle = 'All Bills';
        $user = auth()->user();
        $bills = Bill::where('user_id', $user->id)->get();

        return view('user.user.beta.trx.all', compact(
            'pageTitle',
            'user',
            'bills'
        ));
        // Your code for the real-time functionality goes here
        //return view('realtime.index'); // Replace 'realtime.index' with your actual view name
    }
    public function depTrxLog(Request $request)
    {
        
        $pageTitle = 'Deposits History';
        $user = Auth::user();
        $bills = Deposit::where('user_id', $user->id)->where('status', 1)->get();
        if(!empty($request->from))
        {
         $logs = Transaction::where('user_id', $user->id)->whereBetween('created_at',[$request->from,$request->to])->latest()->get();
        }
        else
        {
         $logs = Transaction::where('user_id', $user->id)->latest()->get();
        }

        $emptyMessage = 'Data Not Found';

        return view('user.user.beta.trx.deposits', compact(
            'pageTitle',
            'user',
            'bills',
            'logs',
            'emptyMessage'
        ));
        // Your code for the real-time functionality goes here
        //return view('realtime.index'); // Replace 'realtime.index' with your actual view name
    }

    public function indexAirpin()
    {
        $pageTitle = 'Airpin';
        $user = auth()->user();

        return view('user.user.beta.airpin', compact(
            'pageTitle',
            'user'
        ));
    }

    public function indexWaec()
    {
        $pageTitle = 'WAEC';
        $user = auth()->user();

        return view('user.user.beta.waec', compact(
            'pageTitle',
            'user'
        ));
    }

    public function indexNeco()
    {
        $pageTitle = 'NECO';
        $user = auth()->user();

        return view('user.user.beta.neco', compact(
            'pageTitle',
            'user'
        ));
    }

    public function indexJamb()
    {
        $pageTitle = 'JAMB';
        $user = auth()->user();

        return view('user.user.beta.jamb', compact(
            'pageTitle',
            'user'
        ));
    }

    public function indexNabteb()
    {
        $pageTitle = 'NABTEB';
        $user = auth()->user();

        return view('user.user.beta.nabteb', compact(
            'pageTitle',
            'user'
        ));
    }

    public function indexNbiss()
    {
        $pageTitle = 'NBISS';
        $user = auth()->user();

        return view('user.user.beta.nbiss', compact(
            'pageTitle',
            'user'
        ));
    }

    public function indexAirsell()
    {
        $pageTitle = 'Airsell';
        $user = auth()->user();

        return view('user.user.beta.airsell', compact(
            'pageTitle',
            'user'
        ));
    }

    public function indexUpgrade()
    {
        $pageTitle = 'Upgrade';
        $user = auth()->user();

        return view('user.user.beta.upgrade', compact(
            'pageTitle',
            'user'
        ));
    }

    public function downloadReceipt($billId)
    {
        $user = auth()->user();
        $bill = Bill::where('id', $billId)->where('user_id', $user->id)->firstOrFail();
        
        // Generate PDF
        $pdf = PDF::loadView('user.user.beta.receipt', compact('bill', 'user'));
        
        // Download PDF
        return $pdf->download('receipt-' . $billId . '.pdf');
    }

}
