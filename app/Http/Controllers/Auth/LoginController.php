<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Models\Extension;
use App\Models\UserLogin;
use App\Services\Funding\CustomerProvisioningService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;


class LoginController extends Controller
{


    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    /**
     * Where to redirect users after login.
     *
     * @var string
     */

    protected $username;

    /**
     * Create a new controller instance.
     *
     * @return void
     */


    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->username = $this->findUsername();
        $this->activeTemplate = activeTemplate();
    }

    public function loginpage(Request $request)
    {
        $pageTitle = 'Login';
        return view($this->activeTemplate . 'user.auth.login', compact('pageTitle'));

    }

    public function login(LoginRequest $request)
    {
        \Session::flash('modal', '#loginModal');

        // LoginRequest automatically validates username, password, and Turnstile token
        // with rate limiting of 5 attempts per 1 minute

        $key = $this->throttleKey($request);
        if (RateLimiter::tooManyAttempts($key, $this->maxAttempts())) {
            $seconds = RateLimiter::availableIn($key);
            $notify[] = ['error', "Too many login attempts. Try again in {$seconds} seconds."];
            return back()->withNotify($notify)->withInput();
        }

        $credentials = $request->only($this->username(), 'password');
        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            RateLimiter::clear($key);
            $request->session()->regenerate();

            $user = Auth::user();
            return $this->authenticated($request, $user) ?: redirect()->route('user.home');
        }

        RateLimiter::hit($key, $this->decaySeconds());
        $notify[] = ['error', 'Invalid credentials'];
        return back()->withNotify($notify)->withInput();
    }

    public function findUsername()
    {
        $login = request()->input('username');

        $fieldType = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        request()->merge([$fieldType => $login]);
        return $fieldType;
    }

    public function username()
    {
        return $this->username;
    }

    protected function guard()
    {
        return Auth::guard();
    }

    protected function throttleKey(Request $request): string
    {
        return Str::transliterate(Str::lower($request->input($this->username())) . '|' . $request->ip());
    }

    protected function maxAttempts(): int
    {
        return 5;
    }

    protected function decaySeconds(): int
    {
        return 60;
    }

    protected function validateLogin(Request $request)
    {
        $customRecaptcha = Extension::where('act', 'custom-captcha')->where('status', 1)->first();
        $validation_rule = [
            $this->username() => 'required|string',
            'password' => 'required|string',
        ];

        if ($customRecaptcha) {
            $validation_rule['captcha'] = 'required';
        }

        $request->validate($validation_rule);

    }

    public function logout()
    {
        $this->guard()->logout();

        request()->session()->invalidate();
        request()->session()->regenerateToken();

        $notify[] = ['success', 'You have been logged out.'];
        return redirect()->route('user.login')->withNotify($notify);
    }





    public function authenticated(Request $request, $user)
    {
        if ($user->status == 0) {
            $this->guard()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            $notify[] = ['error','Your account has been deactivated, Contact Support.'];
            return redirect()->route('user.login')->withNotify($notify);
        }

        $user->tv = $this->shouldRequireLegacyTwoFactorChallenge($request, $user) ? 0 : 1;
        $user->save();

        $ip = $request->ip();
        $exist = UserLogin::where('user_id', $user->id)->where('user_ip', $ip)->first();
        $userLogin = new UserLogin();
        $userLogin->countx = ($exist->countx ?? 0) + 1;
        if ($exist) {
            $userLogin->longitude = $exist->longitude;
            $userLogin->latitude = $exist->latitude;
            $userLogin->city = $exist->city;
            $userLogin->country_code = $exist->country_code;
            $userLogin->country = $exist->country;
        } else {
            $info = json_decode(json_encode(getIpInfo()), true);
            $userLogin->longitude = isset($info['long']) ? implode(',', $info['long']) : '';
            $userLogin->latitude = isset($info['lat']) ? implode(',', $info['lat']) : '';
            $userLogin->city = isset($info['city']) ? implode(',', $info['city']) : '';
            $userLogin->country_code = isset($info['code']) ? implode(',', $info['code']) : '';
            $userLogin->country = isset($info['country']) ? implode(',', $info['country']) : '';
        }

        $userAgent = osBrowser();
        $userLogin->user_id = $user->id;
        $userLogin->user_ip =  $ip;

        $userLogin->browser = @$userAgent['browser'];
        $userLogin->os = @$userAgent['os_platform'];
        $userLogin->save();
        
        if ($user->ev) {
            app(CustomerProvisioningService::class)->ensureCustomers($user);

            if ($user->hasLockedIdentity()) {
                app(CustomerProvisioningService::class)->ensureDedicatedAccounts($user);
            }
        }

        return redirect()->route('user.home');
    }

    protected function shouldRequireLegacyTwoFactorChallenge(Request $request, $user): bool
    {
        return (int) ($user->ts ?? 0) === 1 && ! $this->wasAuthenticatedWithPasskey($request);
    }

    protected function wasAuthenticatedWithPasskey(Request $request): bool
    {
        return (bool) $request->attributes->get('authenticated_via_passkey')
            || $request->routeIs('passkeys.login');
    }


}
