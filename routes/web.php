<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebhookController;
use App\Http\Controllers\UsersController;
use App\Events\WebhookReceived;
use App\Http\Controllers\BetaController;
use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\UssdController;
use App\Http\Controllers\NetworkController;
use App\Http\Controllers\KycSubscriptionController;
use App\Http\Controllers\BudPayWebhookController;
use App\Http\Controllers\DepositCheckoutController;
use App\Http\Controllers\KoraWebhookController;
use App\Http\Controllers\PhoneVerificationController;
use App\Http\Controllers\Admin\BillPaymentSettingsController as AdminBillPaymentSettingsController;
use App\Http\Controllers\Admin\KycServicesController as AdminKycServicesController;
use App\Http\Controllers\PaystackWebhookController;
use App\Http\Controllers\Paystack\ClientController;
use App\Http\Controllers\Paystack\DVAController;
use Spatie\LaravelPasskeys\Http\Controllers\AuthenticateUsingPasskeyController;
use Spatie\LaravelPasskeys\Http\Controllers\GeneratePasskeyAuthenticationOptionsController;






// Root Route Place Holder

Route::post('check', function () {
    return response()->json(['message' => 'POST request works!']);
});

Route::get('/status/easyaccess', [NetworkController::class, 'easyaccess']);
Route::post('/paystack/webhook', [PaystackWebhookController::class, 'handle']);
Route::post('/budpay/webhook', [BudPayWebhookController::class, 'handle']);
Route::post('/kora/webhook', [KoraWebhookController::class, 'handle']);
Route::post('/paystack/register', [ClientController::class, 'createCustomer']);
Route::post('/ussd/callback', [UssdController::class, 'handleUssd']);
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::prefix('ipn')->name('ipn.')->group(function () {
    Route::post('paypal', [\App\Http\Controllers\Gateway\Paypal\ProcessController::class, 'ipn'])->name('Paypal');
    Route::get('paypal-sdk', [\App\Http\Controllers\Gateway\PaypalSdk\ProcessController::class, 'ipn'])->name('PaypalSdk');
    Route::post('perfect-money', [\App\Http\Controllers\Gateway\PerfectMoney\ProcessController::class, 'ipn'])->name('PerfectMoney');
    Route::post('stripe', [\App\Http\Controllers\Gateway\Stripe\ProcessController::class, 'ipn'])->name('Stripe');
    Route::post('stripe-js', [\App\Http\Controllers\Gateway\StripeJs\ProcessController::class, 'ipn'])->name('StripeJs');
    Route::post('stripe-v3', [\App\Http\Controllers\Gateway\StripeV3\ProcessController::class, 'ipn'])->name('StripeV3');
    Route::post('skrill', [\App\Http\Controllers\Gateway\Skrill\ProcessController::class, 'ipn'])->name('Skrill');
    Route::post('paytm', [\App\Http\Controllers\Gateway\Paytm\ProcessController::class, 'ipn'])->name('Paytm');
    Route::post('payeer', [\App\Http\Controllers\Gateway\Payeer\ProcessController::class, 'ipn'])->name('Payeer');
    Route::post('paystack', [\App\Http\Controllers\Gateway\Paystack\ProcessController::class, 'ipn'])->name('Paystack');
    Route::post('voguepay', [\App\Http\Controllers\Gateway\Voguepay\ProcessController::class, 'ipn'])->name('Voguepay');
    Route::get('flutterwave/{trx}/{type}', [\App\Http\Controllers\Gateway\Flutterwave\ProcessController::class, 'ipn'])->name('Flutterwave');
    Route::post('razorpay', [\App\Http\Controllers\Gateway\Razorpay\ProcessController::class, 'ipn'])->name('Razorpay');
    Route::post('instamojo', [\App\Http\Controllers\Gateway\Instamojo\ProcessController::class, 'ipn'])->name('Instamojo');
    Route::get('blockchain', [\App\Http\Controllers\Gateway\Blockchain\ProcessController::class, 'ipn'])->name('Blockchain');
    Route::get('blockio', [\App\Http\Controllers\Gateway\Blockio\ProcessController::class, 'ipn'])->name('Blockio');
    Route::post('coinpayments', [\App\Http\Controllers\Gateway\Coinpayments\ProcessController::class, 'ipn'])->name('Coinpayments');
    Route::post('coinpayments-fiat', [\App\Http\Controllers\Gateway\CoinpaymentsFiat\ProcessController::class, 'ipn'])->name('CoinpaymentsFiat');
    Route::post('coingate', [\App\Http\Controllers\Gateway\Coingate\ProcessController::class, 'ipn'])->name('Coingate');
    Route::post('coinbase-commerce', [\App\Http\Controllers\Gateway\CoinbaseCommerce\ProcessController::class, 'ipn'])->name('CoinbaseCommerce');
    Route::get('mollie', [\App\Http\Controllers\Gateway\Mollie\ProcessController::class, 'ipn'])->name('Mollie');
    Route::post('cashmaal', [\App\Http\Controllers\Gateway\Cashmaal\ProcessController::class, 'ipn'])->name('Cashmaal');
});


/*
|--------------------------------------------------------------------------
| Start Admin Area
|--------------------------------------------------------------------------
*/
Route::group([], function () {
 Route::get('/register/{ref}', [\App\Http\Controllers\Auth\RegisterController::class, 'showform']);
 });


Route::prefix('admin')->name('admin.')->namespace('Admin')->group(function () {
    Route::namespace('Auth')->group(function () {
        Route::get('/login', 'LoginController@showLoginForm')->name('login');
        Route::get('/', 'LoginController@showLoginForm');

        Route::post('/', 'LoginController@login');
        Route::get('logout', 'LoginController@logout')->name('logout');
        // Admin Password Reset
        Route::get('password/reset', 'ForgotPasswordController@showLinkRequestForm')->name('password.reset');
        Route::post('password/reset', 'ForgotPasswordController@sendResetCodeEmail');
        Route::post('password/verify-code', 'ForgotPasswordController@verifyCode')->name('password.verify.code');
        Route::get('password/reset/{token}', 'ResetPasswordController@showResetForm')->name('password.reset.form');
        Route::post('password/reset/change', 'ResetPasswordController@reset')->name('password.change');
    });

    Route::middleware('admin')->group(function () {
        Route::get('dashboard', 'AdminController@dashboard')->name('dashboard');
        Route::get('analytics', 'AnalyticsController@index')->name('analytics');
        Route::get('profile', 'AdminController@profile')->name('profile');
        Route::post('profile', 'AdminController@profileUpdate')->name('profile.update');
        Route::get('password', 'AdminController@password')->name('password');
        Route::post('password', 'AdminController@passwordUpdate')->name('password.update');

         //refer
        Route::get('/referral', 'AdminController@refIndex')->name('referral.index');
        Route::post('/referral', 'AdminController@refStore')->name('store.refer');
        Route::post('/referral/feature', 'AdminController@refupdate')->name('store.feature');
        
        //Bundles Update
        Route::get('/sort/bundles', 'DashboardController@bundles')->name('dashboard.bundles');
        Route::get('/sort/edit/{bundle}', 'DashboardController@edit')->name('dashboard.edit');
        Route::put('/sort/update/{bundle}', 'DashboardController@update')->name('dashboard.update');
        
        
        //Beta Routes
        Route::get('/beta/wallet', 'BetaController@indexWallet')->name('beta.wallet');
        Route::get('/beta/notify', 'BetaController@WebNotify')->name('beta.notify');
        Route::post('/beta/notify/update', 'BetaController@updateNews')->name('beta.notify.update');


        //Notification
        Route::get('notifications','AdminController@notifications')->name('notifications');
        Route::get('notification/read/{id}','AdminController@notificationRead')->name('notification.read');
        Route::get('notifications/read-all','AdminController@readAll')->name('notifications.readAll');

        // Users Manager
        Route::get('create/user', 'ManageUsersController@createUser')->name('users.create');
        Route::post('create/user', 'ManageUsersController@createUserpost');
        Route::get('users', 'ManageUsersController@allUsers')->name('users.all');
        Route::get('users/active', 'ManageUsersController@activeUsers')->name('users.active');
        Route::get('users/banned', 'ManageUsersController@bannedUsers')->name('users.banned');
        Route::get('users/email-verified', 'ManageUsersController@emailVerifiedUsers')->name('users.email.verified');
        Route::get('users/email-unverified', 'ManageUsersController@emailUnverifiedUsers')->name('users.email.unverified');
        Route::get('users/sms-unverified', 'ManageUsersController@smsUnverifiedUsers')->name('users.sms.unverified');
        Route::get('users/sms-verified', 'ManageUsersController@smsVerifiedUsers')->name('users.sms.verified');
        Route::get('users/with-balance', 'ManageUsersController@usersWithBalance')->name('users.with.balance');


        Route::get('kyc-settings', 'ManageUsersController@kycsettings')->name('users.kyc.settings');
        Route::post('kyc-settings', 'ManageUsersController@kycsettingspost');
        Route::post('edit-kyc-settings', 'ManageUsersController@editkycsettings')->name('users.kyc.editsettings');
        
        // KYC Services Management
        Route::get('kyc/services', [AdminKycServicesController::class, 'index'])->name('kyc.services.index');
        Route::post('kyc/services/update', [AdminKycServicesController::class, 'update'])->name('kyc.services.update');
        Route::get('settings/bills', [AdminBillPaymentSettingsController::class, 'index'])->name('bills.settings');
        Route::post('settings/bills', [AdminBillPaymentSettingsController::class, 'update'])->name('bills.settings.update');
        Route::post('settings/bills/sync', [AdminBillPaymentSettingsController::class, 'sync'])->name('bills.settings.sync');
        
        Route::get('users/kyc-verified', 'ManageUsersController@kycVerifiedUsers')->name('users.kyc.verified');
        Route::get('users/kyc-unverified', 'ManageUsersController@kycunVerifiedUsers')->name('users.kyc.unverified');
        Route::get('users/kyc-view/{id}', 'ManageUsersController@viewkyc')->name('users.viewkyc');
        Route::get('users/kyc-verify/{id}', 'ManageUsersController@verifykyc')->name('users.verifykyc');
        Route::get('users/kyc-decline/{id}', 'ManageUsersController@declinekyc')->name('users.declinekyc');

        Route::get('users/{scope}/search', 'ManageUsersController@search')->name('users.search');
        Route::get('user/detail/{id}', 'ManageUsersController@detail')->name('users.detail');
        Route::post('user/update/{id}', 'ManageUsersController@update')->name('users.update');
        Route::post('user/add-investment-plan/{id}', 'ManageUsersController@addplan')->name('users.add.plan');
        Route::post('user/add-sub-balance/{id}', 'ManageUsersController@addSubBalance')->name('users.add.sub.balance');
        Route::post('user/add-compound/{id}', 'ManageUsersController@addcompound')->name('users.add.compound');
        Route::get('user/send-email/{id}', 'ManageUsersController@showEmailSingleForm')->name('users.email.single');
        Route::post('user/send-email/{id}', 'ManageUsersController@sendEmailSingle')->name('users.email.send');
        Route::get('user/login/{id}', 'ManageUsersController@login')->name('users.login');
        Route::get('user/transactions/{id}', 'ManageUsersController@transactions')->name('users.transactions');
        Route::get('user/deposits/{id}', 'ManageUsersController@deposits')->name('users.deposits');
        Route::get('user/deposits/via/{method}/{type?}/{userId}', 'ManageUsersController@depViaMethod')->name('users.deposits.method');
        Route::get('user/withdrawals/{id}', 'ManageUsersController@withdrawals')->name('users.withdrawals');
        Route::get('user/withdrawals/via/{method}/{type?}/{userId}', 'ManageUsersController@withdrawalsViaMethod')->name('users.withdrawals.method');


        // Login History
        Route::get('users/login/history/{id}', 'ManageUsersController@userLoginHistory')->name('users.login.history.single');

        // Support Ticket
        Route::get('users/tickets/open', 'ManageUsersController@openticket')->name('users.open.ticket');
        Route::get('users/tickets/replied', 'ManageUsersController@repliedticket')->name('users.replied.ticket');
        Route::get('users/tickets/closed', 'ManageUsersController@closedticket')->name('users.closed.ticket');
        Route::get('users/tickets/view/{id}', 'ManageUsersController@supportview')->name('user.ticket.view');
        Route::post('users/tickets/reply/{id}', 'ManageUsersController@supportMessageReply')->name('user.ticket.reply');
        Route::get('users/tickets/download/{id}', 'ManageUsersController@ticketDownload')->name('user.ticket.download');

        Route::get('users/send-email', 'ManageUsersController@showEmailAllForm')->name('users.email.all');
        Route::post('users/send-email', 'ManageUsersController@sendEmailAll')->name('users.email.send.all');
        Route::get('users/email-log/{id}', 'ManageUsersController@emailLog')->name('users.email.log');
        Route::get('users/email-details/{id}', 'ManageUsersController@emailDetails')->name('users.email.details');

        Route::get('users/investment/{id}', 'ManageUsersController@investment')->name('users.investment');

        Route::get('timer', 'PlanController@timer')->name('plan.timer');
        Route::post('timer/create', 'PlanController@timercreate')->name('timer.create');
        Route::post('timer/edit', 'PlanController@timeredit')->name('timer.edit');
        Route::get('plans', 'PlanController@index')->name('plan.index');
        Route::post('plan-create', 'PlanController@create')->name('plan.create');
        Route::post('plan-edit', 'PlanController@edit')->name('plan.edit');

        Route::get('investment-log', 'PlanController@investLog')->name('plan.invest.log');
        Route::get('investment-start/{id}', 'PlanController@start')->name('plan.start');



        // Deposit Gateway
        Route::name('gateway.')->prefix('gateway')->group(function(){
            // Automatic Gateway
            Route::get('automatic', 'GatewayController@index')->name('automatic.index');
            Route::get('automatic/edit/{alias}', 'GatewayController@edit')->name('automatic.edit');
            Route::post('automatic/update/{code}', 'GatewayController@update')->name('automatic.update');
            Route::post('automatic/remove/{code}', 'GatewayController@remove')->name('automatic.remove');
            Route::post('automatic/activate', 'GatewayController@activate')->name('automatic.activate');
            Route::post('automatic/deactivate', 'GatewayController@deactivate')->name('automatic.deactivate');


            // Manual Methods
            Route::get('manual', 'ManualGatewayController@index')->name('manual.index');
            Route::get('manual/new', 'ManualGatewayController@create')->name('manual.create');
            Route::post('manual/new', 'ManualGatewayController@store')->name('manual.store');
            Route::get('manual/edit/{alias}', 'ManualGatewayController@edit')->name('manual.edit');
            Route::post('manual/update/{id}', 'ManualGatewayController@update')->name('manual.update');
            Route::post('manual/activate', 'ManualGatewayController@activate')->name('manual.activate');
            Route::post('manual/deactivate', 'ManualGatewayController@deactivate')->name('manual.deactivate');
        });


        // DEPOSIT SYSTEM
        Route::name('deposit.')->prefix('deposit')->group(function(){
            Route::get('/', 'DepositController@deposit')->name('list');
            Route::get('pending', 'DepositController@pending')->name('pending');
            Route::get('rejected', 'DepositController@rejected')->name('rejected');
            Route::get('approved', 'DepositController@approved')->name('approved');
            Route::get('successful', 'DepositController@successful')->name('successful');
            Route::get('details/{id}', 'DepositController@details')->name('details');

            Route::post('reject', 'DepositController@reject')->name('reject');
            Route::post('approve', 'DepositController@approve')->name('approve');
            Route::get('via/{method}/{type?}', 'DepositController@depositViaMethod')->name('method');
            Route::get('/{scope}/search', 'DepositController@search')->name('search');
            Route::get('date-search/{scope}', 'DepositController@dateSearch')->name('dateSearch');

        });


         // LOAN SYSTEM
        Route::name('loan.')->prefix('loan')->group(function(){
        Route::get('/', 'LoanController@index')->name('index');
        Route::post('/create', 'LoanController@create')->name('create');
        Route::post('/edit', 'LoanController@edit')->name('edit');
        Route::get('/request', 'LoanController@request')->name('request');
        Route::get('/approveloan/{id}', 'LoanController@approveloan')->name('approveloan');
        Route::get('/rejectloan/{id}', 'LoanController@rejectloan')->name('rejectloan');
        Route::get('/active', 'LoanController@active')->name('active');
        Route::get('/closed', 'LoanController@closed')->name('closed');
        Route::get('/declined', 'LoanController@declined')->name('declined');
        Route::get('/view/{id}', 'LoanController@view')->name('view');
        Route::post('/pay/{id}', 'LoanController@pay')->name('pay');
        Route::post('/close/{id}', 'LoanController@close')->name('close');
        });


         // SAVINGS SYSTEM
        Route::name('savings.')->prefix('savings')->group(function(){
        Route::get('/target', 'SavingsController@target')->name('target');
        Route::get('/recurrent', 'SavingsController@recurrent')->name('recurrent');
        Route::get('/view/{id}', 'SavingsController@view')->name('view');
        });

         // VIRTUAL CARD SYSTEM
        Route::name('card.')->prefix('card')->group(function(){
        Route::get('/active', 'CardController@active')->name('active');
        Route::get('/inactive', 'CardController@inactive')->name('inactive');
        Route::get('/view/{id}', 'CardController@view')->name('view');
        Route::post('/fund/{id}', 'CardController@fundcard')->name('fundcard');
        Route::get('/block/{id}', 'CardController@block')->name('block');
        Route::get('/unblock/{id}', 'CardController@unblock')->name('unblock');
        Route::get('/terminate/{id}', 'CardController@terminate')->name('terminate');
        Route::post('/card-statement/{id}', 'CardController@trxcard')->name('trxcard');
        });

         // TRANSFER SYSTEM
        Route::name('transfer.')->prefix('transfer')->group(function(){
        Route::get('/user', 'TransferController@user')->name('user');
        Route::get('/other', 'TransferController@other')->name('other');
        Route::get('/view/{id}', 'TransferController@view')->name('view');
        Route::post('/approve', 'TransferController@approve')->name('approve');
        Route::post('/reject', 'TransferController@reject')->name('reject');
        });

        Route::get('settings/local-transfer', [\App\Http\Controllers\Admin\LocalTransferSettingsController::class, 'index'])->name('local-transfer.settings');
        Route::post('settings/local-transfer', [\App\Http\Controllers\Admin\LocalTransferSettingsController::class, 'update'])->name('local-transfer.settings.update');

        // Transaction Split Settings
        Route::get('settings/transaction-split', [\App\Http\Controllers\Admin\TransactionSplitSettingsController::class, 'index'])->name('settings.transaction-split.index');
        Route::put('settings/transaction-split', [\App\Http\Controllers\Admin\TransactionSplitSettingsController::class, 'update'])->name('settings.transaction-split.update');
        Route::post('settings/transaction-split/test', [\App\Http\Controllers\Admin\TransactionSplitSettingsController::class, 'testSplit'])->name('settings.transaction-split.test');


         // COIN SYSTEM
        Route::name('coin.')->prefix('coin')->group(function(){
        Route::get('/currency', 'CoinController@currency')->name('currency');
        Route::get('/edit/{id}', 'CoinController@edit')->name('edit');
        Route::post('/edit/{id}', 'CoinController@apiupdate')->name('update');
        Route::get('/deactivate/{id}', 'CoinController@deactivate')->name('deactivate');
        Route::get('/activate/{id}', 'CoinController@activate')->name('activate');
        Route::get('/wallet', 'CoinController@wallet')->name('wallet');
        Route::get('/wallet/{id}', 'CoinController@viewwallet')->name('viewwallet');
        Route::get('/deactivatewallet/{id}', 'CoinController@deactivatewallet')->name('deactivatewallet');
        Route::get('/activatewallet/{id}', 'CoinController@activatewallet')->name('activatewallet');
        Route::get('/viewwallet/{id}', 'CoinController@viewwalletaddress')->name('viewwalletd');
        Route::post('/creditwallet/{id}', 'CoinController@creditwallet')->name('creditwallet');
        Route::post('/debitwallet/{id}', 'CoinController@debitwallet')->name('debitwallet');
        Route::post('/createwallet/{id}', 'CoinController@createwallet')->name('createwallet');
        Route::get('/swap', 'CoinController@swap')->name('swap');
        });


        // WITHDRAW SYSTEM
        Route::name('withdraw.')->prefix('withdraw')->group(function(){
            Route::get('pending', 'WithdrawalController@pending')->name('pending');
            Route::get('approved', 'WithdrawalController@approved')->name('approved');
            Route::get('rejected', 'WithdrawalController@rejected')->name('rejected');
            Route::get('log', 'WithdrawalController@log')->name('log');
            Route::get('via/{method_id}/{type?}', 'WithdrawalController@logViaMethod')->name('method');
            Route::get('{scope}/search', 'WithdrawalController@search')->name('search');
            Route::get('date-search/{scope}', 'WithdrawalController@dateSearch')->name('dateSearch');
            Route::get('details/{id}', 'WithdrawalController@details')->name('details');
            Route::post('approve', 'WithdrawalController@approve')->name('approve');
            Route::post('reject', 'WithdrawalController@reject')->name('reject');


            // Withdraw Method
            Route::get('method/', 'WithdrawMethodController@methods')->name('method.index');
            Route::get('method/create', 'WithdrawMethodController@create')->name('method.create');
            Route::post('method/create', 'WithdrawMethodController@store')->name('method.store');
            Route::get('method/edit/{id}', 'WithdrawMethodController@edit')->name('method.edit');
            Route::post('method/edit/{id}', 'WithdrawMethodController@update')->name('method.update');
            Route::post('method/activate', 'WithdrawMethodController@activate')->name('method.activate');
            Route::post('method/deactivate', 'WithdrawMethodController@deactivate')->name('method.deactivate');
        });

        // Report
        Route::get('report/transaction', 'ReportController@transaction')->name('report.transaction');
        Route::get('report/transaction/search', 'ReportController@transactionSearch')->name('report.transaction.search');
        Route::get('report/login/history', 'ReportController@loginHistory')->name('report.login.history');
        Route::get('report/login/ipHistory/{ip}', 'ReportController@loginIpHistory')->name('report.login.ipHistory');
        Route::get('report/email/history', 'ReportController@emailHistory')->name('report.email.history');
        Route::get('report/investment/log', 'ReportController@investLog')->name('report.plan.invest.log');


        Route::get('report/airtime', 'ReportController@airtime')->name('report.airtime');
        Route::get('report/internet', 'ReportController@internet')->name('report.internet');
        Route::get('report/cabletv', 'ReportController@cabletv')->name('report.cabletv');
        Route::get('report/utility', 'ReportController@utility')->name('report.utility');
        Route::get('report/waecreg', 'ReportController@waecreg')->name('report.waecreg');
        Route::get('report/waecres', 'ReportController@waecres')->name('report.waecres');


        // Language Manager
        Route::get('/language', 'LanguageController@langManage')->name('language.manage');
        Route::post('/language', 'LanguageController@langStore')->name('language.manage.store');
        Route::post('/language/delete/{id}', 'LanguageController@langDel')->name('language.manage.del');
        Route::post('/language/update/{id}', 'LanguageController@langUpdate')->name('language.manage.update');
        Route::get('/language/edit/{id}', 'LanguageController@langEdit')->name('language.key');
        Route::post('/language/import', 'LanguageController@langImport')->name('language.importLang');



        Route::post('language/store/key/{id}', 'LanguageController@storeLanguageJson')->name('language.store.key');
        Route::post('language/delete/key/{id}', 'LanguageController@deleteLanguageJson')->name('language.delete.key');
        Route::post('language/update/key/{id}', 'LanguageController@updateLanguageJson')->name('language.update.key');



        // General Setting
        Route::get('general-setting', 'GeneralSettingController@index')->name('setting.index');
        Route::post('general-setting', 'GeneralSettingController@update')->name('setting.update');

        // Logo-Icon
        Route::get('setting/logo-icon', 'GeneralSettingController@logoIcon')->name('setting.logo.icon');
        Route::post('setting/logo-icon', 'GeneralSettingController@logoIconUpdate')->name('setting.logo.icon.update');

        //Cookie
        Route::get('cookie','GeneralSettingController@cookie')->name('setting.cookie');
        Route::post('cookie','GeneralSettingController@cookieSubmit');


        // Plugin
        Route::get('live-chat-setup', 'ExtensionController@index')->name('extensions.index');
        Route::post('extensions/update/{id}', 'ExtensionController@update')->name('extensions.update');
        Route::post('extensions/activate', 'ExtensionController@activate')->name('extensions.activate');
        Route::post('extensions/deactivate', 'ExtensionController@deactivate')->name('extensions.deactivate');


        // Email Setting
        Route::get('email-template/global', 'EmailTemplateController@emailTemplate')->name('email.template.global');
        Route::post('email-template/global', 'EmailTemplateController@emailTemplateUpdate')->name('email.template.global.update');
        Route::get('email-template/setting', 'EmailTemplateController@emailSetting')->name('email.template.setting');
        Route::post('email-template/setting', 'EmailTemplateController@emailSettingUpdate')->name('email.template.setting.update');
        Route::get('email-template/index', 'EmailTemplateController@index')->name('email.template.index');
        Route::get('email-template/{id}/edit', 'EmailTemplateController@edit')->name('email.template.edit');
        Route::post('email-template/{id}/update', 'EmailTemplateController@update')->name('email.template.update');
        Route::post('email-template/send-test-mail', 'EmailTemplateController@sendTestMail')->name('email.template.test.mail');


        // SMS Setting
        Route::get('sms-template/global', 'SmsTemplateController@smsTemplate')->name('sms.template.global');
        Route::post('sms-template/global', 'SmsTemplateController@smsTemplateUpdate')->name('sms.template.global.update');
        Route::get('sms-template/setting','SmsTemplateController@smsSetting')->name('sms.template.setting');
        Route::post('sms-template/setting', 'SmsTemplateController@smsSettingUpdate')->name('sms.template.setting.update');
        Route::get('sms-template/index', 'SmsTemplateController@index')->name('sms.template.index');
        Route::get('sms-template/edit/{id}', 'SmsTemplateController@edit')->name('sms.template.edit');
        Route::post('sms-template/update/{id}', 'SmsTemplateController@update')->name('sms.template.update');
        Route::post('email-template/send-test-sms', 'SmsTemplateController@sendTestSMS')->name('sms.template.test.sms');

        // SEO
        Route::get('seo', 'FrontendController@seoEdit')->name('seo');


        // Frontend
        Route::name('frontend.')->prefix('frontend')->group(function () {

            Route::get('frontend-sections/{key}', 'FrontendController@frontendSections')->name('sections');
            Route::post('frontend-content/{key}', 'FrontendController@bufrontendContent')->name('sections.content');
            Route::get('frontend-element/{key}/{id?}', 'FrontendController@frontendElement')->name('sections.element');
            Route::post('remove', 'FrontendController@remove')->name('remove');

        });

        // User Authentication Management
        Route::name('auth.')->prefix('authentication')->group(function () {
            Route::get('/stats', 'UserAuthenticationController@stats')->name('stats');
            Route::get('/logs', 'UserAuthenticationController@logs')->name('logs');
        });

        Route::name('users.')->prefix('users')->group(function () {
            Route::get('/{user}/authentication', 'UserAuthenticationController@showUser')->name('auth');
            Route::post('/{user}/pin/reset', 'UserAuthenticationController@resetPin')->name('pin.reset');
            Route::post('/{user}/pin/unlock', 'UserAuthenticationController@unlockPin')->name('pin.unlock');
            Route::post('/{user}/2fa/disable', 'UserAuthenticationController@disable2fa')->name('2fa.disable');
            Route::post('/{user}/passkeys/disable', 'UserAuthenticationController@disablePasskeys')->name('passkeys.disable');
        });
    });
});




/*
|--------------------------------------------------------------------------
| Start User Area
|--------------------------------------------------------------------------
*/


Route::name('user.')->group(function () {
    
    
    //Route::post('/webhook', 'WebhookController@handle');


    Route::get('/login', 'Auth\LoginController@loginpage')->name('loginpage');
    Route::post('/login', 'Auth\LoginController@login')->name('login');
    Route::get('logout', 'Auth\LoginController@logout')->name('logout');


    Route::get('/register', 'Auth\RegisterController@showform')->name('register');
    Route::post('register', 'Auth\RegisterController@register')->middleware('regStatus');

    // Google OAuth
    Route::middleware('guest')->group(function () {
        Route::get('/auth/google/redirect', 'Auth\GoogleAuthController@redirect')->name('google.redirect');
        Route::get('/auth/google/callback', 'Auth\GoogleAuthController@callback')->name('google.callback');
        Route::get('/auth/google/onboarding', 'Auth\GoogleAuthController@showOnboarding')->name('google.onboarding.show');
        Route::post('/auth/google/onboarding', 'Auth\GoogleAuthController@completeOnboarding')->name('google.onboarding.complete');
    });

    Route::get('/password/reset', 'Auth\ForgotPasswordController@resetpassword')->name('password.request');
    Route::post('password/email', 'Auth\ForgotPasswordController@sendResetCodeEmail')->name('password.email');
    Route::get('password/code-verify', 'Auth\ForgotPasswordController@codeVerify')->name('password.code.verify');
    Route::post('password/reset/page', 'Auth\ResetPasswordController@reset')->name('password.update');
    Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
    Route::post('password/verify-code', 'Auth\ForgotPasswordController@verifyCode')->name('password.verify.code');
});

Route::middleware('guest')->group(function () {
    Route::get('passkeys/authentication-options', [GeneratePasskeyAuthenticationOptionsController::class, '__invoke'])->name('passkeys.authentication_options');
    Route::post('passkeys/authenticate', [AuthenticateUsingPasskeyController::class, '__invoke'])->name('passkeys.login');
});

Route::get('user/deposit/kora/callback', [DepositCheckoutController::class, 'handleKoraCallback'])->name('user.deposit.kora.callback');
Route::get('user/deposit/quickteller/callback', [DepositCheckoutController::class, 'handleQuicktellerCallback'])->name('user.deposit.quickteller.callback');

Route::name('user.')->prefix('user')->group(function () {
    Route::middleware('auth')->group(function () {
        Route::get('authorization', 'AuthorizationController@authorizeForm')->name('authorization');
        Route::get('resend-verify', 'AuthorizationController@sendVerifyCode')->name('send.verify.code');
        Route::post('verify-email', 'AuthorizationController@emailVerification')->name('verify.email');
        Route::post('verify-sms', 'AuthorizationController@smsVerification')->name('verify.sms');
        Route::post('verify-g2fa', 'AuthorizationController@g2faVerification')->name('go2fa.verify');

        Route::middleware(['checkStatus'])->group(function () {
            Route::get('dashboard', 'UserController@home')->name('home');
            Route::get('coming_soon', 'UserController@soon')->name('api');

            Route::get('profile-setting', 'UserController@profile')->name('profile.setting');
            Route::get('security', 'SecuritySettingsController@index')->name('security');
            Route::post('profile-setting', 'UserController@submitProfile');
            Route::post('profile-setting/phone/request-otp', [PhoneVerificationController::class, 'requestOtp'])->name('profile.phone.request-otp');
            Route::post('profile-setting/phone/verify-otp', [PhoneVerificationController::class, 'verifyOtp'])->name('profile.phone.verify-otp');
            Route::get('referral-setting', 'UserController@ref')->name('profile.ref');
            Route::get('change-password', 'UserController@changePassword')->name('change.password');
            Route::post('change-password', 'UserController@submitPassword');
            
            //PIN PAGES
            Route::get('/pin', 'PinController@index')->name('user.pin.index');
            Route::get('/pin/setup', 'PinController@showSetupForm')->name('user.pin.setup');
            Route::post('/pin/set', 'PinController@setPin')->name('pin.set');
            Route::get('/pin/change', 'PinController@showChangeForm')->name('user.pin.change');
            Route::post('/pin/change', 'PinController@changePin')->name('pin.change');
            Route::get('/pin/reset-form', 'PinController@showResetForm')->name('user.pin.reset');
            Route::post('/pin/reset', 'PinController@resetPin')->name('pin.reset');
            Route::get('/pin/disable', 'PinController@showDisableForm')->name('user.pin.disable');
            Route::post('/pin/disable', 'PinController@disablePin')->name('pin.disable');
            Route::post('/pin/toggle', 'PinController@togglePin')->name('toggle.pin');
            
            //Beta Updates Base
            Route::get('/beta/realtime', 'BetaController@index')->name('beta.realtime');
            Route::get('/beta/contacts/create', 'BetaController@create')->name('beta.contacts.create');
            Route::get('/beta/contacts/', 'BetaController@ContactIndex')->name('beta.contacts.index');
            Route::post('/beta/contacts/create', 'BetaController@store')->name('contacts.store');
            Route::delete('/user/beta/contacts/{id}', [BetaController::class, 'destroy'])->name('user.beta.contacts.destroy');
            Route::get('/beta/trx/verifyMonnify', 'BetaController@checkMonnify')->name('beta.trx.verifyMonnify');
            Route::get('/beta/trx/verifyPaystack', 'BetaController@checkPaystack')->name('beta.trx.verifyPaystack');
            Route::get('/beta/trx/monnify', 'BetaController@indexcheckStatus')->name('beta.trx.monnify');
            Route::get('/beta/trx/paystack', 'BetaController@indexPaystack')->name('beta.trx.paystack');
            Route::get('/beta/trx/check', 'BetaController@indexCheck')->name('beta.trx.check');
            Route::get('/beta/trx/airtime', 'BetaController@airTrxLog')->name('beta.trx.airtime');
            Route::get('/beta/trx/data', 'BetaController@dataTrxLog')->name('beta.trx.data');
            Route::get('/beta/trx/all', 'BetaController@allTrxLog')->name('beta.trx.all');
            Route::get('/beta/trx/air2cash', 'BetaController@aircTrxLog')->name('beta.trx.air2cash');
            Route::get('/beta/trx/deposits', 'BetaController@depTrxLog')->name('beta.trx.deposit');
            Route::get('/receipt/{billId}', 'BetaController@showReceipt')->name('beta.receipt');
            Route::get('/receipt/{billId}/print', [BetaController::class, 'printReceipt'])->name('beta.receipt.print');
            Route::get('/receipt/{billId}/download', [BetaController::class, 'downloadReceipt'])->name('beta.receipt.download');
            Route::get('/beta/airpin', 'BetaController@indexAirpin')->name('beta.airpin');
            Route::get('/beta/waec', 'BetaController@indexWaec')->name('beta.waec');
            Route::get('/beta/neco', 'BetaController@indexNeco')->name('beta.neco');
            Route::get('/beta/jamb', 'BetaController@indexJamb')->name('beta.jamb');
            Route::get('/beta/nabteb', 'BetaController@indexNabteb')->name('beta.nabteb');
            Route::get('/beta/nbiss', 'BetaController@indexNbiss')->name('beta.nbiss');
            Route::get('/beta/airsell', 'BetaController@indexAirsell')->name('beta.airsell');
            Route::get('/beta/upgrade', 'BetaController@indexUpgrade')->name('beta.upgrade');
			Route::get('/beta/topup', 'BetaController@topup')->name('beta.topup');
			Route::get('/beta/deposit/', 'Paystack\ClientController@indexMain')->name('beta.get.deposit');
			Route::post('/beta/newcustomer/', 'Paystack\ClientController@createCustomer')->name('beta.get.customer');
			Route::post('/beta/injectac/', 'Paystack\ClientController@getAccounts')->name('beta.get.inject');
            


            //2FA
            Route::get('twofactor', 'UserController@show2faForm')->name('twofactor');
            Route::post('twofactor/enable', 'UserController@create2fa')->name('twofactor.enable');
            Route::post('twofactor/disable', 'UserController@disable2fa')->name('twofactor.disable');

            // PASSKEY
            Route::get('passkey', 'PasskeyController@index')->name('passkey');
            Route::post('passkey/disable', 'PasskeyController@disable')->name('passkey.disable');

            // PAYMENT VERIFICATION
            Route::post('payment/verify-auth', 'PaymentVerificationController@verify')->name('payment.verify-auth');
            Route::get('payment/auth-status', 'PaymentVerificationController@getStatus')->name('payment.auth-status');
            Route::get('payment/verifications', 'PaymentVerificationController@getRecent')->name('payment.verifications');


            // KYC
            Route::get('verification/kyc', 'UserController@kyc')->name('kyc');
            Route::post('verification/kyc', 'UserController@submitKyc')->name('submitkyc');
            Route::get('kyc-services', [KycSubscriptionController::class, 'index'])->name('kyc.services');
            Route::get('kyc/upgrade', [KycSubscriptionController::class, 'upgrade'])->name('kyc.upgrade');
            Route::post('kyc/upgrade', [KycSubscriptionController::class, 'subscribe'])->name('kyc.upgrade.process');
            Route::get('kyc/upgrade/callback', [KycSubscriptionController::class, 'callback'])->name('kyc.upgrade.callback');
            Route::get('kyc/manage-subscription', [KycSubscriptionController::class, 'manage'])->name('kyc.manage');


            // Support
            Route::get('support/request', 'UserController@support')->name('support');
            Route::get('support/create', 'UserController@supportnew')->name('ticket.open');
            Route::post('support/create', 'UserController@supportpost')->name('ticket.create');
            Route::get('support/view/{id}', 'UserController@supportview')->name('ticket.view');
            Route::post('support/reply/{id}', 'UserController@supportMessageStore')->name('ticket.reply');
            Route::get('support/download/{id}', 'UserController@ticketDownload')->name('ticket.download');
            Route::get('support/delete/{id}', 'UserController@ticketDelete')->name('ticket.delete');


            // Deposit
            Route::any('/deposit', 'Gateway\PaymentController@deposit')->name('deposit');
            Route::post('deposit/kora', [DepositCheckoutController::class, 'startKora'])->name('deposit.kora.start');
            Route::post('deposit/quickteller/initialize', [DepositCheckoutController::class, 'initializeQuickteller'])->name('deposit.quickteller.initialize');
            Route::post('deposit/insert', 'Gateway\PaymentController@depositInsert')->name('deposit.insert');
            Route::get('deposit/preview', 'Gateway\PaymentController@depositPreview')->name('deposit.preview');
            Route::get('deposit/confirm', 'Gateway\PaymentController@depositConfirm')->name('deposit.confirm');
            Route::get('deposit/manual', 'Gateway\PaymentController@manualDepositConfirm')->name('deposit.manual.confirm');
            Route::post('deposit/manual', 'Gateway\PaymentController@manualDepositUpdate')->name('deposit.manual.update');
            Route::get('deposit/history', 'UserController@depositHistory')->name('deposit.history');
            Route::get('deposit/accounts', 'UserController@viewaccounts')->name('deposit.accounts');

            // Withdraw
            Route::get('/withdraw', 'UserController@withdrawMoney')->name('withdraw');
            Route::post('/withdraw', 'UserController@withdrawStore')->name('withdraw.money');

            Route::get('/withdraw/compound', 'UserController@withdrawCompound')->name('withdraw.compound');
            Route::post('/withdraw/compound', 'UserController@withdrawCompoundStore');
            Route::get('/withdraw/preview', 'UserController@withdrawPreview')->name('withdraw.preview');
            Route::post('/withdraw/preview', 'UserController@withdrawSubmit')->name('withdraw.submit');
            Route::get('/withdraw/history', 'UserController@withdrawLog')->name('withdraw.history');

            Route::get('/trx/log', 'UserController@trxLog')->name('trx.log');
            Route::post('/trx/log', 'UserController@trxLog');
            Route::get('/new/savings', 'UserController@savings')->name('savings');
            Route::get('/investment', 'UserController@investmentnew')->name('investment.new');
            Route::get('/investment/{id}', 'UserController@newinvestment')->name('newinvestment');
            Route::post('/investment', 'UserController@investment')->name('investment');
            Route::get('/investment/pool/log', 'UserController@investmentLog')->name('investment.log');

            //Loan
            Route::get('/request/loan', 'LoanController@requestloan')->name('loan.request');
            Route::post('/request/loan', 'LoanController@requestsubmit');
            Route::get('/my-loan', 'LoanController@myloan')->name('myloan');
            Route::get('/loan/{id}', 'LoanController@viewloan')->name('viewloan');
            Route::post('/loan-pay/{id}', 'LoanController@loanpay')->name('loan.pay');


            //Savings
            Route::get('/request/savings', 'SavingsController@requestsavings')->name('savings.request');
            Route::post('/request/savings', 'SavingsController@requestsubmit');
            Route::get('/my-savings', 'SavingsController@mysavings')->name('mysavings');
            Route::get('/savings/{id}', 'SavingsController@viewsaved')->name('viewsaved');
            Route::post('/savings/{id}', 'SavingsController@savenow')->name('save.pay');


            //Virtual Card
            Route::get('/virtual-Card', 'VirtualCardController@requestcard')->name('vcard');
            Route::post('/virtual-Card', 'VirtualCardController@requestsubmit');
            Route::get('/virtual-Card/{id}', 'VirtualCardController@viewcard')->name('view.card');
            Route::get('/block-Card/{id}', 'VirtualCardController@blockcard')->name('card.block');
            Route::get('/unblock-Card/{id}', 'VirtualCardController@unblockcard')->name('card.unblock');
            Route::post('/fund-Card/{id}', 'VirtualCardController@fundcard')->name('fundcard');
            Route::post('/Trx-Card/{id}', 'VirtualCardController@trxcard')->name('trxcard');


            //Fund Card
            Route::get('/transfer-Fund', 'UserController@usertransfer')->name('usertransfer');
            Route::post('/transfer-Fund', 'UserController@requestsubmit');
            Route::get('/transfer-Fund/preview', 'UserController@usertransferpreview')->name('usertransfer.preview');
            Route::post('/transfer-Fund/preview', 'UserController@usertransfersend');
            Route::get('/deletebeneficiary/{id}', 'UserController@deletebeneficiary')->name('deletebeneficiary');
            Route::post('/transfer/search-users', 'UserController@searchUsers')->name('search-users');
            Route::get('/transfer/receipt/{id}', 'UserController@downloadReceipt')->name('transfer.receipt.download');
            Route::post('/transfer/search-users', 'UserController@searchUsers')->name('search-users');
            Route::get('/transfer/receipt/{id}', 'UserController@downloadReceipt')->name('transfer.receipt.download');


            Route::get('/other-transfer-Fund', [\App\Http\Controllers\LocalTransferController::class, 'index'])->name('othertransfer');
            Route::post('/other-transfer-Fund/resolve', [\App\Http\Controllers\LocalTransferController::class, 'resolve'])->name('othertransfer.resolve');
            Route::post('/other-transfer-Fund', [\App\Http\Controllers\LocalTransferController::class, 'submit']);
            Route::post('/other-transfer-Fund/split-info', [\App\Http\Controllers\LocalTransferController::class, 'getSplitInfo'])->name('othertransfer.split-info');
            Route::get('/transfer-Other/preview', 'UserController@transferpreviewother')->name('transfer.previewother');
            Route::post('/transfer-Other/preview', 'UserController@transferpreviewothersubmit');

            // Dpay Interbank Transfer
            Route::get('/dpay-transfer', [\App\Http\Controllers\DpayTransferController::class, 'index'])->name('dpay.index');
            Route::post('/dpay-transfer/resolve', [\App\Http\Controllers\DpayTransferController::class, 'resolve'])->name('dpay.resolve');
            Route::post('/dpay-transfer/submit', [\App\Http\Controllers\DpayTransferController::class, 'submit'])->name('dpay.submit');
            Route::get('/dpay-transfer/preview', [\App\Http\Controllers\DpayTransferController::class, 'preview'])->name('dpay.preview');
            Route::post('/dpay-transfer/confirm', [\App\Http\Controllers\DpayTransferController::class, 'confirm'])->name('dpay.confirm');

             //Blockchain Wallet
            Route::get('wallet/{id}','WalletController@wallet')->name('wallet');
            Route::post('create/wallet/{id}','WalletController@createwallet')->name('createwallet');
            Route::post('create/sendfromwallet','WalletController@sendfromwallet')->name('sendfromwallet');
            Route::get('view/wallet/{id}','WalletController@viewwallet')->name('viewwallet');

             //Swap
            Route::get('currency/swapcoin', 'WalletController@swapcoin')->name('swapcoin');
            Route::post('currency/swapcoin', 'WalletController@swapcoinpost');


            // Payment Confirmation
            Route::post('payment/confirm', [\App\Http\Controllers\PaymentConfirmationController::class, 'confirm'])->name('payment.confirm');
            Route::post('payment/validate', [\App\Http\Controllers\PaymentConfirmationController::class, 'validatePayment'])->name('payment.validate');

            //Bill Payment System
            Route::get('bill/airtime', 'BillsController@airtime')->name('airtime');
            Route::post('bill/airtime', 'BillsController@airtimebuy');
            Route::get('bill/internet', 'BillsController@internet')->name('internet');
			Route::get('bill/internetx', 'BillsController@internetx')->name('internetx');
            Route::post('bill/internet', 'BillsController@loadinternet');
            Route::get('bill/cabletv', 'BillsController@cabletv')->name('cabletv');
            Route::post('bill/cabletv', 'BillsController@validatedecoder');
            Route::get('bill/cabletv/pay', 'BillsController@decodervalidated')->name('decodervalidated');
            Route::post('bill/cabletv/pay', 'BillsController@decoderpay');
            Route::get('bill/utility', 'BillsController@utility')->name('utility');
            Route::post('bill/utility', 'BillsController@validatebill');
            Route::get('bill/validated', 'BillsController@billvalidated')->name('billvalidated');
            Route::post('bill/validated', 'BillsController@billpay');
            Route::get('utility-token/{id}', 'BillsController@utilitytoken')->name('utilitytoken');
            Route::get('bill/waec/register', 'BillsController@waecreg')->name('waec.reg');
            Route::post('bill/waec/register/{id}', 'BillsController@waecregpost')->name('registerwaec');
            Route::get('bill/waec/result', 'BillsController@waecresult')->name('waec.result');
            Route::post('bill/waec/result/{id}', 'BillsController@resultwaecpost')->name('resultwaec');
            Route::get('bill/query/data', 'BillsController@queryData');




        });
    });
});

/*
Route::post('/webhook', function (Request $request) {
    $webhookData = json_decode($request->getContent(), true);
    event(new WebhookReceived($webhookData));

    return response('Webhook received', 200); // Return a plain text response
});
*/

Route::prefix('cron')->name('cron.')->group(function(){
    Route::get('/investment', 'CronController@investment')->name('investment');
    Route::get('/loan', 'CronController@loan')->name('loan');
    Route::get('/savings', 'CronController@savings')->name('savings');
});


Route::get('/privacy-policy', 'SiteController@privacyPolicy')->name('legal.privacy');
Route::get('/terms', 'SiteController@terms')->name('legal.terms');
Route::get('/policy/{slug}/{id}', 'SiteController@privacyPage')->name('privacy.page');
Route::get('/contact', 'SiteController@contact')->name('contact');
Route::post('/contact', 'SiteController@contactSubmit');
Route::get('/change/{lang?}', 'SiteController@changeLanguage')->name('lang');



Route::get('/cookie/accept', 'SiteController@cookieAccept')->name('cookie.accept');

Route::get('placeholder-image/{size}', 'SiteController@placeholderImage')->name('placeholder.image');

Route::get('/{slug}', 'SiteController@pages')->name('pages');
Route::get('/', 'SiteController@index')->name('home');





/*
|--------------------------------------------------------------------------
| API Rotes
|--------------------------------------------------------------------------
*/


// Login route for both user and admin
Route::post('/api/login', [LoginController::class, 'login']);

// Protected routes using Sanctum middleware
Route::middleware(['auth:sanctum'])->group(function () {
    // Logout route for both user and admin
    Route::post('/api/logout', [LoginController::class, 'logout']);

    // Transactions route for authenticated users
    Route::get('/api/transactions', [UserController::class, 'transactions']);

    // Other shared routes can be added here
});
