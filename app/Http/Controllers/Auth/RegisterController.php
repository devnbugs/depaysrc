<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Models\AdminNotification;
use App\Models\GeneralSetting;
use App\Models\User;
use App\Models\UserLogin;
use App\Services\Funding\FundingSettings;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Auth;
use App\Jobs\MonnifyTokenJob;
use App\Events\MonnifyTokenEvent;
use App\Listeners\MonnifyTokenListener;
use App\Jobs\MonnifyBankTransferJob;



class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
        $this->middleware('regStatus')->except('registrationNotAllowed');

        $this->activeTemplate = activeTemplate();
    }


        protected function showform(Request $request, $ref = null)
    {
        $pageTitle = 'Register';
        $reference = $ref;
        return view($this->activeTemplate . 'user.auth.register', compact('pageTitle','reference'));

    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        $general = GeneralSetting::first();
        $password_validation = Password::min(6);

        //$agree = 'nullable';
        //if ($general->agree) {
        //    $agree = 'required';
        //}

        \Session::flash('modal', '#registerModal');

        $countryData = (array)json_decode(file_get_contents(resource_path('views/partials/country.json')));
        $countryCodes = implode(',', array_keys($countryData));
        $mobileCodes = implode(',',array_column($countryData, 'dial_code'));
        $countries = implode(',',array_column($countryData, 'country'));
        $validate = Validator::make($data, [
            'firstname' => 'sometimes|required|string|max:50',
            'lastname' => 'sometimes|required|string|max:50',
            'email' => 'required|string|email|max:90|unique:users',
            'mobile' => 'required|unique:users',
            'password' => ['required','confirmed',$password_validation],
            'username' => 'required|alpha_num|unique:users|min:6',
            'captcha' => 'sometimes|required',
            'referBy' => 'sometimes',
        ]);
        return $validate;
    }

    public function register(RegisterRequest $request)
    {
        // RegisterRequest automatically validates all fields including Turnstile
        // with rate limiting of 5 attempts per 1 minute
        
        if (User::where('firstname', $request->firstname)
            ->where('lastname', $request->lastname)
            ->exists()) {
            $notify[] = ['error', 'A user with the same first and last name already exists'];
            return back()->withNotify($notify)->withInput();
        }
        $exist = User::where('mobile', $request->mobile_code . $request->mobile)->first();
        if ($exist) {
            $notify[] = ['error', 'The mobile number already exists'];
            return back()->withNotify($notify)->withInput();
        }

        event(new Registered($user = $this->create($request->all())));

        // Dispatch the CreateCustomerJobt job here
        
        // Dispatch the CreateDedicatedAccountJob job here
        //dispatch(new CreateDedicatedAccountJob($user));
        //dispatch(new MonnifyTokenJob($user));

        $this->guard()->login($user);

        return $this->registered($request, $user)
            ?: redirect($this->redirectPath());
    }


    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array $data
     * @return \App\User
     */
    protected function create(array $data)
{
    $general = GeneralSetting::first();

    if (isset($data['referBy'])) {
        $referUser = User::where('username', $data['referBy'])->first();
    } else {
        $referUser = null;
    }

    // User Create
    $user = new User();
    $user->firstname = isset($data['firstname']) ? $data['firstname'] : null;
    $user->lastname = isset($data['lastname']) ? $data['lastname'] : null;
    $user->email = strtolower(trim($data['email']));
    $user->password = Hash::make($data['password']);
    $user->username = trim($data['username']);
    $user->account_number = '24' . mt_rand(1000000000, 9999999999);
    $user->ref_by = $referUser ? $referUser->id : 0;
    $user->country_code = 'NG'; // Assigning the country code 'NG'
    $user->mobile = $data['mobile'];
    $user->address = [
        'address' => '',
        'state' => '',
        'zip' => '',
        'country' => isset($data['country']) ? $data['country'] : null,
        'city' => ''
    ];
    $user->status = 1;
    $user->ev = 0;
    $user->sv = $general->sv ? 0 : 1;
    $user->ts = 0;
    $user->tv = 1;
	$user->BVN = '';
	$user->NIN = '';
    $user->save();
    
    //dispatch(new MonnifyTokenJob($user)); //Configured
    //event(new MonnifyTokenEvent($user)); 
    


    $adminNotification = new AdminNotification();
    $adminNotification->user_id = $user->id;
    $adminNotification->title = 'New User registered';
    $adminNotification->click_url = urlPath('admin.users.detail', $user->id);
    $adminNotification->save();


    

    
    // Log the user in after registration (optional)
    Auth::login($user);
	
	// Check if the user's KYC Approved
    //if ($user->psid == null || $user->paystackcode == null || $user->kyc == 0) {
    //    return app(ClientController::class)->createCustomer($request);
    //}
    
    return $user;
    }

    protected function guard()
    {
        return Auth::guard();
    }


    public function registered()
    {
        
        $general = app(FundingSettings::class)->general();

        if (! $general->ev) {
            $general->ev = 1;
            $general->save();
        }

        return redirect()->route('user.authorization');
    }
    

}
