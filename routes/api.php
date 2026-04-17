<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API V1 Routes (NEW - Recommended for Flutter & Mobile Apps)
|--------------------------------------------------------------------------
*/
Route::prefix('v1')->name('v1.')->group(function () {
    // Public Routes
    Route::post('/auth/login', [\App\Http\Controllers\Api\Auth\ApiAuthController::class, 'login']);
    Route::post('/auth/register', [\App\Http\Controllers\Api\Auth\ApiAuthController::class, 'register']);
    Route::post('/auth/forgot-password', [\App\Http\Controllers\Api\Auth\ApiAuthController::class, 'forgotPassword'])->name('auth.forgot-password');

    // Protected Routes
    Route::middleware('auth:sanctum')->group(function () {
        // Auth Routes
        Route::post('/auth/logout', [\App\Http\Controllers\Api\Auth\ApiAuthController::class, 'logout']);
        Route::post('/auth/refresh-token', [\App\Http\Controllers\Api\Auth\ApiAuthController::class, 'refreshToken']);
        Route::post('/auth/change-password', [\App\Http\Controllers\Api\Auth\ApiAuthController::class, 'changePassword']);
        Route::get('/auth/me', [\App\Http\Controllers\Api\Auth\ApiAuthController::class, 'getCurrentUser']);

        // User Profile Routes
        Route::prefix('user')->group(function () {
            Route::get('/profile', [\App\Http\Controllers\Api\ApiUserController::class, 'getProfile']);
            Route::put('/profile', [\App\Http\Controllers\Api\ApiUserController::class, 'updateProfile']);
            Route::get('/wallet', [\App\Http\Controllers\Api\ApiUserController::class, 'getWallet']);
            Route::get('/verification', [\App\Http\Controllers\Api\ApiUserController::class, 'getVerificationStatus']);
            Route::get('/transactions', [\App\Http\Controllers\Api\ApiUserController::class, 'getTransactions']);
            Route::post('/check-permission', [\App\Http\Controllers\Api\ApiUserController::class, 'checkPermission']);
        });

        // Payment Routes
        Route::prefix('payments')->group(function () {
            Route::get('/options', [\App\Http\Controllers\Api\ApiPaymentController::class, 'getPaymentOptions']);
            Route::get('/networks', [\App\Http\Controllers\Api\ApiPaymentController::class, 'getNetworks']);
            Route::post('/validate', [\App\Http\Controllers\Api\ApiPaymentController::class, 'validatePayment']);
            Route::post('/process', [\App\Http\Controllers\Api\ApiPaymentController::class, 'processPayment']);
            Route::get('/history', [\App\Http\Controllers\Api\ApiPaymentController::class, 'getPaymentHistory']);
            Route::get('/statistics', [\App\Http\Controllers\Api\ApiPaymentController::class, 'getStatistics']);
            Route::get('/{reference}', [\App\Http\Controllers\Api\ApiPaymentController::class, 'getPaymentDetails']);
        });
    });
});

/*
|--------------------------------------------------------------------------
| Legacy API Routes (Maintained for Backward Compatibility)
|--------------------------------------------------------------------------
*/
Route::namespace('Api')->name('api.')->group(function(){
	Route::get('general-setting','BasicController@generalSetting');
	Route::get('unauthenticate','BasicController@unauthenticate')->name('unauthenticate');
	Route::get('languages','BasicController@languages');
	Route::get('language-data/{code}','BasicController@languageData');

	Route::namespace('Auth')->group(function(){
		Route::post('login', 'LoginController@login');
		Route::post('register', 'RegisterController@register');
		
	    Route::post('password/email', 'ForgotPasswordController@sendResetCodeEmail');
	    Route::post('password/verify-code', 'ForgotPasswordController@verifyCode');
	    
	    Route::post('password/reset', 'ResetPasswordController@reset');
	});


	Route::middleware('auth.api:sanctum')->name('user.')->prefix('user')->group(function(){
		Route::get('logout', 'Auth\LoginController@logout');
		Route::get('authorization', 'AuthorizationController@authorization')->name('authorization');
	    Route::get('resend-verify', 'AuthorizationController@sendVerifyCode')->name('send.verify.code');
	    Route::post('verify-email', 'AuthorizationController@emailVerification')->name('verify.email');
	    Route::post('verify-sms', 'AuthorizationController@smsVerification')->name('verify.sms');
	    Route::post('verify-g2fa', 'AuthorizationController@g2faVerification')->name('go2fa.verify');

	    Route::middleware(['checkStatusApi'])->group(function(){
	    	Route::get('dashboard',function(){
	    		return auth()->user();
	    	});

            Route::post('profile-setting', 'UserController@submitProfile');
            Route::post('change-password', 'UserController@submitPassword');

            // Withdraw
            Route::get('withdraw/methods', 'UserController@withdrawMethods');
            Route::post('withdraw/store', 'UserController@withdrawStore');
            Route::post('withdraw/confirm', 'UserController@withdrawConfirm');
            Route::get('withdraw/history', 'UserController@withdrawLog');
            
            
            // Bills
            Route::post('buydata', 'PaymentController@loadinternet');
            Route::post('buyairtime', 'PaymentController@airtimebuy');
            Route::post('buywaec', 'PaymentController@waec');
            Route::post('buyneco', 'PaymentController@neco');


            // Deposit
            Route::get('deposit/methods', 'PaymentController@depositMethods');
            Route::post('deposit/insert', 'PaymentController@depositInsert');
            Route::get('deposit/confirm', 'PaymentController@depositConfirm');

            Route::get('deposit/manual', 'PaymentController@manualDepositConfirm');
            Route::post('deposit/manual', 'PaymentController@manualDepositUpdate');

            Route::get('deposit/history', 'UserController@depositHistory');

            Route::get('transactions', 'UserController@transactions');

	    });
	});
});