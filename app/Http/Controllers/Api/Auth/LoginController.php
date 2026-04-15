<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\Extension;
use App\Models\UserLogin;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        $this->username = $this->findUsername();
    }

    public function login(Request $request)
    {

        $validator = $this->validateLogin($request);

        if ($validator->fails()) {
            return response()->json([
                'code'=>200,
                'status'=>'ok',
                'message'=>['error'=>$validator->errors()->all()],
            ]);
        }

        $credentials = request([$this->username, 'password']);
        if(!Auth::attempt($credentials)){
            $response[] = 'Unauthorized user';
            return response()->json([
                'code'=>401,
                'status'=>'unauthorized',
                'message'=>['error'=>$response],
            ]);
        }

        $user = $request->user();
        $user = Auth::user();
        if (! $user) {
            return response()->json([
                'code'=>401,
                'status'=>'unauthorized',
                'message'=>['error'=>['Authentication failed']],
            ]);
        }

        $authResponse = $this->authenticated($request, $user);
        if ($authResponse instanceof JsonResponse) {
            return $authResponse;
        }

        $tokenResult = $user->createToken('auth_token')->plainTextToken;
        $response[] = 'Login Succesfull';
        return response()->json([
            'code'=>200,
            'status'=>'ok',
            'message'=>['success'=>$response],
            'data'=>[
                'user' => $user,
                'access_token'=>$tokenResult,
                'token_type'=>'Bearer'
            ]
        ]);

        
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

    protected function validateLogin(Request $request)
    {
        $validation_rule = [
            $this->username() => 'required|string',
            'password' => 'required|string',
        ];

        $validate = Validator::make($request->all(),$validation_rule);
        return $validate;

    }

    public function logout()
    {
        if ($user = Auth::user()) {
            $user->tokens()->delete();
        }

        $notify[] = 'Logout Succesfull';
        return response()->json([
            'code'=>200,
            'status'=>'ok',
            'message'=>['success'=>$notify],
        ]);
    }

    public function authenticated(Request $request, $user): ?JsonResponse
    {
        if ($user->status == 0) {
            if ($authUser = Auth::user()) {
                $authUser->tokens()->delete();
            }
            $notify[] = 'Your account has been deactivated';
            return response()->json([
                'code'=>200,
                'status'=>'ok',
                'message'=>['success'=>$notify],
            ]);
        }


        $user->tv = $user->ts == 1 ? 0 : 1;
        $user->save();

        $ip = $request->ip();
        $exist = UserLogin::where('user_id', $user->id)->where('user_ip', $ip)->first();
        $userLogin = new UserLogin();
        $userLogin->countx = ($exist->countx ?? 0) + 1;
        if ($exist) {
            $userLogin->longitude =  $exist->longitude;
            $userLogin->latitude =  $exist->latitude;
            $userLogin->city =  $exist->city;
            $userLogin->country_code = $exist->country_code;
            $userLogin->country =  $exist->country;
        }else{
            $info = json_decode(json_encode(getIpInfo()), true);
            $userLogin->longitude =  @implode(',',$info['long']);
            $userLogin->latitude =  @implode(',',$info['lat']);
            $userLogin->city =  @implode(',',$info['city']);
            $userLogin->country_code = @implode(',',$info['code']);
            $userLogin->country =  @implode(',', $info['country']);
        }

        $userAgent = osBrowser();
        $userLogin->user_id = $user->id;
        $userLogin->user_ip =  $ip;
        
        $userLogin->browser = @$userAgent['browser'];
        $userLogin->os = @$userAgent['os_platform'];
        $userLogin->save();
    }


}
