<?php

namespace App\Http\Controllers\Paystack;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\PaystackController;
use Illuminate\Support\Facades\Auth;

class DVAController extends Controller
{
    //
	public function __construct()
    {
        $this->activeTemplate = activeTemplate();
    }

    public function createWema(Request $request)
    {
        $assigned = app(PaystackController::class)->assignAccounts($request->user() ?? Auth::user());

        if ($assigned) {
            $alertx[] = ['success', 'User Details Updated.'];
            return back()->withAlertx($alertx);
        }

        $alertx[] = ['error', 'Account generation failed.'];
        return back()->withAlertx($alertx);
    }
	
    public function createPayTi(Request $request)
    {
        $assigned = app(PaystackController::class)->assignAccounts($request->user() ?? Auth::user());

        if ($assigned) {
            $alertx[] = ['success', 'Titan User Details Updated.'];
            return back()->withAlertx($alertx);
        }

        $alertx[] = ['error', 'Account generation failed.'];
        return back()->withAlertx($alertx);
    }
}
