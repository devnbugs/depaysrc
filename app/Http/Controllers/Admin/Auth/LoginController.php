<?php
namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    public $redirectTo = 'admin';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('admin.guest')->except('logout');
    }

    /**
     * Show the application's login form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showLoginForm()
    {
        $pageTitle = "Admin Login";
        return view('admin.auth.login', compact('pageTitle'));
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard('admin');
    }

    public function username()
    {
        return 'username';
    }

    protected function validateLogin(Request $request)
    {
        return Validator::make($request->all(), [
            $this->username() => 'required|string',
            'password' => 'required|string',
        ]);
    }

    public function login(Request $request)
    {
        $this->validateLogin($request)->validate();

        $key = $this->throttleKey($request);
        if (RateLimiter::tooManyAttempts($key, $this->maxAttempts())) {
            $seconds = RateLimiter::availableIn($key);
            $notify[] = ['error', "Too many login attempts. Try again in {$seconds} seconds."];
            return back()->withNotify($notify)->withInput();
        }

        $credentials = $request->only($this->username(), 'password');

        if (Auth::guard('admin')->attempt($credentials, $request->boolean('remember'))) {
            RateLimiter::clear($key);
            $request->session()->regenerate();

            return redirect()->intended('/admin');
        }

        RateLimiter::hit($key, $this->decaySeconds());
        $notify[] = ['error', 'Invalid credentials'];
        return back()->withNotify($notify)->withInput();
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


    public function logout(Request $request)
    {
        $this->guard()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        $notify[] = ['success', 'You have been logged out.'];
        return redirect('/admin')->withNotify($notify);
    }

    public function resetPassword()
    {
        $pageTitle = 'Account Recovery';
        return view('admin.reset', compact('pageTitle'));
    }
}
