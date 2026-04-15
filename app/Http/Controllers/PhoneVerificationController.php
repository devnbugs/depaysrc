<?php

namespace App\Http\Controllers;

use App\Services\Funding\PhoneVerificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PhoneVerificationController extends Controller
{
    public function __construct(protected PhoneVerificationService $verifications)
    {
    }

    public function requestOtp()
    {
        try {
            $this->verifications->requestOtp(Auth::user());

            return back()->withNotify([['success', 'WhatsApp OTP requested successfully.']]);
        } catch (\Throwable $e) {
            return back()->withNotify([['error', $e->getMessage()]]);
        }
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => ['required', 'string', 'min:4', 'max:10'],
        ]);

        try {
            $this->verifications->verifyOtp(Auth::user(), (string) $request->otp);

            return back()->withNotify([['success', 'Phone number verified successfully.']]);
        } catch (\Throwable $e) {
            return back()->withNotify([['error', $e->getMessage()]]);
        }
    }
}
