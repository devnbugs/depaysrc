<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\Transaction;
use App\Models\Extension;
use App\Models\Internetbundle;
use App\Models\GeneralSetting;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use App\Models\Deposit;
use App\Models\Gateway;

class BetaController extends Controller
{
    //
    public function handle()
    {
        $client = new Client();

        $url = 'https://api.monnify.com/api/v1/auth/login';
        $headers = [
            'Content-Type' => 'application/json',
            'Authorization' => 'Basic TUtfUFJPRF9aRVlaVVpEUkY2OlNVQ1RETFdSMjNLVjg2SkpBNFRHRFhZMVVaTFhVUktG',
        ];
            
        $response = $client->request('POST', $url, [
                'headers' => $headers,
            ]);

            // Check if the HTTP response status is 200 (OK)
            if ($response->getStatusCode() === 200) {
                $responseData = json_decode($response->getBody(), true);

                // Check if the request was successful and the accessToken is present
                if ($responseData['requestSuccessful'] && isset($responseData['responseBody']['accessToken'])) {
                    $accessToken = $responseData['responseBody']['accessToken'];

                    // Update the user's monnify_token field with the accessToken
                    $this->user->update([
                        'monnify_token' => $accessToken,
                    ]);
                } 
        
        } 
    }
    
    public function indexWallet()
    {
        
        $pageTitle = 'Wallet';
        $user = auth()->user();
        $emptyMessage = 'No deposit history available.';
        $deposits = Deposit::with(['user', 'gateway'])->where('status','!=',0)->orderBy('id','desc')->paginate(getPaginate());
        $successful = Deposit::where('status',1)->sum('amount');
        $pending = Deposit::where('status',2)->sum('amount');
        $rejected = Deposit::where('status',3)->sum('amount');

        return view('admin.beta.wallet', compact(
            'pageTitle',
            'user',
            'emptyMessage',
            'deposits',
            'successful',
            'pending',
            'rejected'
        ));
        // Your code for the real-time functionality goes here
        //return view('realtime.index'); // Replace 'realtime.index' with your actual view name
    }
    
    public function WebNotify()
    {
        
        $pageTitle = 'Website Notify';
        $webnotify = Extension::where('id', 2)->get();
        return view('admin.beta.notify', compact(
            'pageTitle',
            'webnotify'
        ));
        // Your code for the real-time functionality goes here
        //return view('realtime.index'); // Replace 'realtime.index' with your actual view name
    }
    public function updateNews(Request $request, $id)
    {
        $webnotify = Extension::findOrFail($id);

        $webnotify->description = $request->latest;
        $webnotify->script = $request->news;

        $webnotify->save();

        $alertx[] = ['success', 'News Updated Successfully'];
        return redirect()->back()->withAlertx($alertx);
    }
	
	public function maindev(Request $request)
    {
        // Toggle all records' status between 1 and 0
        $newStatus = InternetBundle::where('status', 1)->exists() ? 0 : 1;
        
        Internetbundle::query()->update(['status' => $newStatus]);
		// Get the general setting record (assuming a single row for settings)
		$generalSetting = GeneralSetting::first();

		// Set `devmode` to 1 if the new status is 0 (meaning maintenance mode), otherwise set to 0
		$generalSetting->devmode = $newStatus === 0 ? 1 : 0;
		$generalSetting->save();

        $alertx[] = ['success', 'Maintainance Mode'];
        return redirect()->back()->withAlertx($alertx);
    }
	
	public function checkDataDev()
    {
        // Check if all records have status set to 0
        $allStatusZero = InternetBundle::where('status', '!=', 0)->doesntExist();

        if ($allStatusZero) {
            return view('maintenance'); // Create a maintenance.blade.php view with a message
        }
    }
    
    
}