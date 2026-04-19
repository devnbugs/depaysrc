<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\PasswordResetEmailRequest;
use App\Models\PasswordReset;
use App\Models\User;
use Illuminate\Http\Request;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    public function __construct()
    {
        $this->middleware('guest');
    }


    public function resetpassword()
    {
        return view(activeTemplate() . 'user.auth.passwords.email')->with(
            ['pageTitle' => 'Reset Password']
        );
    }



    public function sendResetCodeEmail(PasswordResetEmailRequest $request)
    {
        // PasswordResetEmailRequest automatically validates email/username and Turnstile
        // with rate limiting of 5 attempts per 1 minute

        \Session::flash('modal', '#resetModal');

        // Determine field type (email, username, or mobile)
        $inputValue = $request->email;
        $fieldType = 'username'; // default
        
        if (filter_var($inputValue, FILTER_VALIDATE_EMAIL)) {
            $fieldType = 'email';
        } elseif (preg_match('/^[0-9+\-\s\(\)]+$/', $inputValue) && strlen(preg_replace('/\D/', '', $inputValue)) >= 10) {
            $fieldType = 'mobile';
        }
        
        $user = User::where($fieldType, $inputValue)->first();

        if (!$user) {
            $fieldTypeLabel = $fieldType === 'email' ? 'Email' : ($fieldType === 'mobile' ? 'Phone Number' : 'Username');
            $notify[] = ['error', "User not found with this {$fieldTypeLabel}."];
            \Session::flash('modalType', '#resetModal');
            return back()->withNotify($notify);
        }

        PasswordReset::where('email', $user->email)->delete();
        $code = verificationCode(6);
        $password = new PasswordReset();
        $password->email = $user->email;
        $password->token = $code;
        $password->created_at = \Carbon\Carbon::now();
        $password->save();

        $userIpInfo = getIpInfo();
        $userBrowserInfo = osBrowser();
        sendEmail($user, 'PASS_RESET_CODE', [
            'code' => $code,
            'operating_system' => @$userBrowserInfo['os_platform'],
            'browser' => @$userBrowserInfo['browser'],
            'ip' => @$userIpInfo['ip'],
            'time' => @$userIpInfo['time']
        ]);

        $pageTitle = 'Account Recovery';
        $email = $user->email;
        session()->put('pass_res_mail',$email);
        $notify[] = ['success', 'Password reset email sent successfully'];
        return redirect()->route('user.password.code.verify')->withNotify($notify);
    }

    public function codeVerify(){
        $pageTitle = 'Account Recovery';
        $email = session()->get('pass_res_mail');
        if (!$email) {
            $notify[] = ['error', 'Invalid Request'];
            \Session::flash('modalType', '#resetModal');
            \Session::flash('modal', '#resetModal');
            return redirect()->route('home')->withNotify($notify);
        }
        return view(activeTemplate().'user.auth.passwords.code_verify',compact('pageTitle','email'));
    }

    public function verifyCode(Request $request)
    {
        $request->validate([
            'code' => 'required',
            'email' => 'required'
        ]);
        $code =  str_replace(' ', '', $request->code);

        if (PasswordReset::where('token', $code)->where('email', $request->email)->count() != 1) {
             $notify[] = ['error','Invalid Reset Code'];
            return back()->withNotify($notify);
        }
        $notify[] = ['success', 'You can change your password now.'];
        session()->flash('fpass_email', $request->email);
        return redirect()->route('user.password.reset', $code)->withNotify($notify);
    }

}
