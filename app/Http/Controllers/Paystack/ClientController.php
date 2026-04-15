<?php

namespace App\Http\Controllers\Paystack;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\PaystackController;


class ClientController extends Controller
{
    //
	public function __construct()
    {
        $this->activeTemplate = activeTemplate();
    }
	
	public function indexMain()
    {
        $pageTitle = 'Funding Accounts';
        $user = Auth::user();

        return view($this->activeTemplate . 'user.beta.get.deposit', compact(
            'pageTitle',
            'user',
        ));
    }

    public function createCustomer(Request $request)
    {
        $created = app(PaystackController::class)->createCustomer($request);

        if ($created) {
            $alertx[] = ['trxsuccess', 'Account Activated'];
            return back()->withAlertx($alertx);
        }

        $alertx[] = ['error', 'Activation Error'];
        return back()->withAlertx($alertx);
    }
	
	public function getAccounts(Request $request)
    {
        $assigned = app(PaystackController::class)->assignAccounts($request);

        if ($assigned) {
            $alertx[] = ['success', 'Account Generated'];
            return back()->with('alertx', $alertx);
        }

        $alertx[] = ['error', 'Generating Error'];
        return back()->with('alertx', $alertx);
    }

	
}
