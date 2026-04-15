<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Frontend;
use App\Models\GeneralSetting;
use App\Rules\FileTypeValidate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Image;

class GeneralSettingController extends Controller
{
    public function index()
    {
        $general = GeneralSetting::first();
        $pageTitle = 'General Setting';
        $timezones = json_decode(file_get_contents(resource_path('views/admin/partials/timezone.json')));
        return view('admin.setting.general_setting', compact('pageTitle', 'general','timezones'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'base_color' => 'nullable', 'regex:/^[a-f0-9]{6}$/i',
            'secondary_color' => 'nullable', 'regex:/^[a-f0-9]{6}$/i',
            'timezone' => 'required',
            'quickteller_mode' => 'nullable|in:TEST,LIVE',
        ]);

        $general = GeneralSetting::first();

        $general->ev = 1;
        $general->en = $request->en ? 1 : 0;
        $general->sv = $request->sv ? 1 : 0;
        $general->sn = $request->sn ? 1 : 0;
        $general->force_ssl = $request->force_ssl ? 1 : 0;
        $general->registration = $request->registration ? 1 : 0;
        $general->agree = $request->agree ? 1 : 0;
        $general->sitename = $request->sitename;
        $general->cur_text = $request->cur_text;
        $general->base_color = $request->base_color;
        $general->secondary_color = $request->secondary_color;
        $general->identity_verification_settings = [
            'auto_create_paystack_customer' => $request->boolean('auto_create_paystack_customer'),
            'auto_create_budpay_customer' => $request->boolean('auto_create_budpay_customer'),
            'auto_prepare_squad_customer' => $request->boolean('auto_prepare_squad_customer'),
            'auto_generate_paystack_account' => $request->boolean('auto_generate_paystack_account'),
            'auto_generate_budpay_account' => $request->boolean('auto_generate_budpay_account'),
            'auto_generate_kora_account' => $request->boolean('auto_generate_kora_account'),
            'require_identity_for_accounts' => $request->boolean('require_identity_for_accounts'),
            'lock_profile_after_identity' => $request->boolean('lock_profile_after_identity'),
            'force_email_verification' => true,
            'phone_verification_enabled' => $request->boolean('phone_verification_enabled'),
            'phone_verification_auth_url' => trim((string) $request->phone_verification_auth_url),
            'phone_verification_send_url' => trim((string) $request->phone_verification_send_url),
            'phone_verification_verify_url' => trim((string) $request->phone_verification_verify_url),
            'phone_verification_client_id' => trim((string) $request->phone_verification_client_id),
            'phone_verification_client_secret' => trim((string) $request->phone_verification_client_secret),
            'phone_verification_sender_id' => trim((string) $request->phone_verification_sender_id),
            'phone_verification_template' => trim((string) $request->phone_verification_template),
            'kora_secret_key' => trim((string) $request->kora_secret_key) ?: config('services.kora.secret_key'),
            'kora_virtual_account_bank_code' => trim((string) $request->kora_virtual_account_bank_code) ?: '035',
        ];
        $general->deposit_checkout_settings = [
            'kora_enabled' => $request->boolean('kora_enabled'),
            'kora_public_key' => trim((string) $request->kora_public_key),
            'quickteller_enabled' => $request->boolean('quickteller_enabled'),
            'quickteller_mode' => strtoupper((string) ($request->quickteller_mode ?: 'TEST')),
            'quickteller_merchant_code' => trim((string) $request->quickteller_merchant_code),
            'quickteller_pay_item_id' => trim((string) $request->quickteller_pay_item_id),
            'quickteller_pay_item_name' => trim((string) $request->quickteller_pay_item_name) ?: 'Wallet Funding',
            'quickteller_client_id' => trim((string) $request->quickteller_client_id),
            'quickteller_client_secret' => trim((string) $request->quickteller_client_secret),
            'quickteller_auth_url' => trim((string) $request->quickteller_auth_url),
            'quickteller_search_url' => trim((string) $request->quickteller_search_url),
        ];
        $general->virtual_card_settings = [
            'enabled' => $request->boolean('virtual_card_enabled'),
            'provider' => 'interswitch',
            'allow_prepaid' => $request->boolean('virtual_card_allow_prepaid'),
            'allow_debit' => $request->boolean('virtual_card_allow_debit'),
            'default_type' => (string) ($request->virtual_card_default_type ?: 'PREPAID_NEW'),
            'require_verified_email' => $request->boolean('virtual_card_require_verified_email'),
            'require_identity' => $request->boolean('virtual_card_require_identity'),
            'auth_url' => trim((string) $request->virtual_card_auth_url) ?: config('services.interswitch.auth_url'),
            'base_url' => trim((string) $request->virtual_card_base_url) ?: config('services.interswitch.card_base_url'),
            'client_id' => trim((string) $request->virtual_card_client_id) ?: config('services.interswitch.client_id'),
            'client_secret' => trim((string) $request->virtual_card_client_secret) ?: config('services.interswitch.client_secret'),
            'issuer_nr' => trim((string) $request->virtual_card_issuer_nr),
            'card_program' => trim((string) $request->virtual_card_card_program),
            'user_id' => trim((string) $request->virtual_card_user_id),
            'branch_code' => trim((string) $request->virtual_card_branch_code),
            'account_type' => trim((string) $request->virtual_card_account_type) ?: '20',
            'default_currency' => strtoupper(trim((string) $request->virtual_card_default_currency) ?: 'NGN'),
            'creation_fee' => (float) ($request->virtual_card_creation_fee ?: $general->cardfee ?: 0),
        ];
        $general->save();

        $timezoneFile = config_path('timezone.php');
        $content = "<?php \$timezone = '{$request->timezone}'; ?>";
        file_put_contents($timezoneFile, $content);
        $notify[] = ['success', 'General setting has been updated. Email verification is enforced for every new registration.'];
        return back()->withNotify($notify);
    }


    public function logoIcon()
    {
        $pageTitle = 'Logo & Favicon';
        return view('admin.setting.logo_icon', compact('pageTitle'));
    }

    public function logoIconUpdate(Request $request)
    {
        $request->validate([
            'logo' => ['image',new FileTypeValidate(['jpg','jpeg','png'])],
            'favicon' => ['image',new FileTypeValidate(['png'])],
        ]);
        if ($request->hasFile('logo')) {
            try {
                $path = imagePath()['logoIcon']['path'];
                if (!file_exists($path)) {
                    mkdir($path, 0755, true);
                }
                Image::make($request->logo)->save($path . '/logo.png');
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Logo could not be uploaded.'];
                return back()->withNotify($notify);
            }
        }

        if ($request->hasFile('favicon')) {
            try {
                $path = imagePath()['logoIcon']['path'];
                if (!file_exists($path)) {
                    mkdir($path, 0755, true);
                }
                $size = explode('x', imagePath()['favicon']['size']);
                Image::make($request->favicon)->resize($size[0], $size[1])->save($path . '/favicon.png');
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Favicon could not be uploaded.'];
                return back()->withNotify($notify);
            }
        }
        $notify[] = ['success', 'Logo & favicon has been updated.'];
        return back()->withNotify($notify);
    }

    public function cookie(){
        $pageTitle = 'GDPR Cookie';
        $cookie = Frontend::where('data_keys','cookie.data')->firstOrFail();
        return view('admin.setting.cookie',compact('pageTitle','cookie'));
    }

    public function cookieSubmit(Request $request){
        $request->validate([
            'link'=>'required',
            'description'=>'required',
        ]);
        $cookie = Frontend::where('data_keys','cookie.data')->firstOrFail();
        $cookie->data_values = [
            'link' => $request->link,
            'description' => $request->description,
            'status' => $request->status ? 1 : 0,
        ];
        $cookie->save();
        $notify[] = ['success','Cookie policy updated successfully'];
        return back()->withNotify($notify);
    }
}
