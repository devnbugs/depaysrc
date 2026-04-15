<?php

namespace App\Services\Funding;

use App\Models\GeneralSetting;

class FundingSettings
{
    public function general(): GeneralSetting
    {
        return GeneralSetting::firstOrFail();
    }

    public function identity(?GeneralSetting $general = null): array
    {
        $general ??= $this->general();
        $saved = $general->identity_verification_settings ?? [];

        return [
            'auto_create_paystack_customer' => (bool) data_get($saved, 'auto_create_paystack_customer', true),
            'auto_create_budpay_customer' => (bool) data_get($saved, 'auto_create_budpay_customer', true),
            'auto_prepare_squad_customer' => (bool) data_get($saved, 'auto_prepare_squad_customer', true),
            'auto_generate_paystack_account' => (bool) data_get($saved, 'auto_generate_paystack_account', true),
            'auto_generate_budpay_account' => (bool) data_get($saved, 'auto_generate_budpay_account', true),
            'auto_generate_kora_account' => (bool) data_get($saved, 'auto_generate_kora_account', false),
            'require_identity_for_accounts' => (bool) data_get($saved, 'require_identity_for_accounts', true),
            'lock_profile_after_identity' => (bool) data_get($saved, 'lock_profile_after_identity', true),
            'force_email_verification' => (bool) data_get($saved, 'force_email_verification', true),
            'phone_verification_enabled' => (bool) data_get($saved, 'phone_verification_enabled', false),
            'phone_verification_auth_url' => (string) data_get($saved, 'phone_verification_auth_url', ''),
            'phone_verification_send_url' => (string) data_get($saved, 'phone_verification_send_url', ''),
            'phone_verification_verify_url' => (string) data_get($saved, 'phone_verification_verify_url', ''),
            'phone_verification_client_id' => (string) data_get($saved, 'phone_verification_client_id', ''),
            'phone_verification_client_secret' => (string) data_get($saved, 'phone_verification_client_secret', ''),
            'phone_verification_sender_id' => (string) data_get($saved, 'phone_verification_sender_id', ''),
            'phone_verification_template' => (string) data_get($saved, 'phone_verification_template', ''),
            'kora_secret_key' => (string) data_get($saved, 'kora_secret_key', config('services.kora.secret_key', '')),
            'kora_virtual_account_bank_code' => (string) data_get($saved, 'kora_virtual_account_bank_code', '035'),
        ];
    }

    public function deposit(?GeneralSetting $general = null): array
    {
        $general ??= $this->general();
        $saved = $general->deposit_checkout_settings ?? [];

        return [
            'kora_enabled' => (bool) data_get($saved, 'kora_enabled', false),
            'kora_public_key' => (string) data_get($saved, 'kora_public_key', env('KORA_PUBLIC_KEY', '')),
            'quickteller_enabled' => (bool) data_get($saved, 'quickteller_enabled', false),
            'quickteller_mode' => strtoupper((string) data_get($saved, 'quickteller_mode', 'TEST')),
            'quickteller_merchant_code' => (string) data_get($saved, 'quickteller_merchant_code', ''),
            'quickteller_pay_item_id' => (string) data_get($saved, 'quickteller_pay_item_id', ''),
            'quickteller_pay_item_name' => (string) data_get($saved, 'quickteller_pay_item_name', 'Wallet Funding'),
            'quickteller_client_id' => (string) data_get($saved, 'quickteller_client_id', ''),
            'quickteller_client_secret' => (string) data_get($saved, 'quickteller_client_secret', ''),
            'quickteller_auth_url' => (string) data_get($saved, 'quickteller_auth_url', 'https://passport.k8.isw.la/passport/oauth/token'),
            'quickteller_search_url' => (string) data_get($saved, 'quickteller_search_url', 'https://switch-online-gateway-service.k9.isw.la/switch-online-gateway-service/api/v1/gateway/reference-search'),
        ];
    }

    public function kyc(?GeneralSetting $general = null): array
    {
        $general ??= $this->general();
        $stored = $general->kyc_subscription_settings ?? [];

        return [
            'minimum_funded_amount' => (float) data_get($stored, 'minimum_funded_amount', 500),
        ];
    }
}
