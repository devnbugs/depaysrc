@extends('admin.layouts.app')
@section('panel')
    <div class="space-y-6">
        <!-- Header -->
        <div>
            <p class="section-kicker">@lang('Settings')</p>
            <h2 class="mt-2 section-title">@lang('General Configuration')</h2>
            <p class="mt-2 section-copy max-w-2xl">@lang('Configure core settings for your platform including site information, currencies, and features.')</p>
        </div>

        <!-- Settings Form -->
        <div class="panel-card rounded-2xl border border-slate-200 dark:border-white/10 p-6">
            <form action="" method="POST" class="space-y-8">
                @csrf

                <!-- Site Information -->
                <div class="space-y-4 border-b border-slate-200 dark:border-white/10 pb-8">
                    <h3 class="text-lg font-semibold text-slate-950 dark:text-white">@lang('Site Information')</h3>
                    <div class="grid gap-6 sm:grid-cols-2">
                        <div class="form-group">
                            <label class="block text-sm font-semibold text-slate-700 dark:text-zinc-300 mb-2">@lang('Site Title')</label>
                            <input class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-slate-900 placeholder-slate-500 transition focus:border-sky-500 focus:outline-none dark:border-white/10 dark:bg-white/5 dark:text-white dark:placeholder-zinc-400" type="text" name="sitename" value="{{$general->sitename}}">
                        </div>
                        <div class="form-group">
                            <label class="block text-sm font-semibold text-slate-700 dark:text-zinc-300 mb-2">@lang('Site Timezone')</label>
                            <select class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-slate-900 transition focus:border-sky-500 focus:outline-none dark:border-white/10 dark:bg-white/5 dark:text-white" name="timezone">
                                @foreach($timezones as $timezone)
                                    <option value="{{ $timezone }}" @if(config('app.timezone') == $timezone) selected @endif>{{ __($timezone) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Currency Settings -->
                <div class="space-y-4 border-b border-slate-200 dark:border-white/10 pb-8">
                    <h3 class="text-lg font-semibold text-slate-950 dark:text-white">@lang('Currency Settings')</h3>
                    <div class="grid gap-6 sm:grid-cols-2">
                        <div class="form-group">
                            <label class="block text-sm font-semibold text-slate-700 dark:text-zinc-300 mb-2">@lang('Currency Name')</label>
                            <input class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-slate-900 placeholder-slate-500 transition focus:border-sky-500 focus:outline-none dark:border-white/10 dark:bg-white/5 dark:text-white dark:placeholder-zinc-400" type="text" name="cur_text" value="{{$general->cur_text}}">
                        </div>
                        <div class="form-group">
                            <label class="block text-sm font-semibold text-slate-700 dark:text-zinc-300 mb-2">@lang('Currency Symbol')</label>
                            <input class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-slate-900 placeholder-slate-500 transition focus:border-sky-500 focus:outline-none dark:border-white/10 dark:bg-white/5 dark:text-white dark:placeholder-zinc-400" type="text" name="cur_sym" value="{{$general->cur_sym}}">
                        </div>
                    </div>
                </div>

                <!-- Color Settings -->
                <div class="space-y-4 border-b border-slate-200 dark:border-white/10 pb-8">
                    <h3 class="text-lg font-semibold text-slate-950 dark:text-white">@lang('Theme Colors')</h3>
                    <div class="grid gap-6 sm:grid-cols-2">
                        <div class="form-group">
                            <label class="block text-sm font-semibold text-slate-700 dark:text-zinc-300 mb-2">@lang('Primary Color')</label>
                            <div class="flex gap-3">
                                <input type='text' class="colorPicker flex-shrink-0 h-10 w-16 rounded-lg border border-slate-300" value="{{$general->base_color}}"/>
                                <input type="text" class="colorCode flex-grow rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-slate-900 transition focus:border-sky-500 focus:outline-none dark:border-white/10 dark:bg-white/5 dark:text-white" name="base_color" value="{{ $general->base_color }}"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="block text-sm font-semibold text-slate-700 dark:text-zinc-300 mb-2">@lang('Secondary Color')</label>
                            <div class="flex gap-3">
                                <input type='text' class="colorPicker flex-shrink-0 h-10 w-16 rounded-lg border border-slate-300" value="{{$general->secondary_color}}"/>
                                <input type="text" class="colorCode flex-grow rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-slate-900 transition focus:border-sky-500 focus:outline-none dark:border-white/10 dark:bg-white/5 dark:text-white" name="secondary_color" value="{{ $general->secondary_color }}"/>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Feature Toggles -->
                <div class="space-y-4 pb-8">
                    <h3 class="text-lg font-semibold text-slate-950 dark:text-white">@lang('Features & Security')</h3>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between p-4 rounded-lg border border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/5">
                            <div>
                                <label class="text-sm font-semibold text-slate-700 dark:text-zinc-300">@lang('Agree Policy')</label>
                                <p class="mt-1 text-xs text-slate-600 dark:text-zinc-400">@lang('Require users to agree to policies')</p>
                            </div>
                            <input type="checkbox" class="h-5 w-5 rounded border-slate-300 text-sky-600 transition focus:ring-sky-500" name="agree" @if($general->agree) checked @endif />
                        </div>

                        <div class="flex items-center justify-between p-4 rounded-lg border border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/5">
                            <div>
                                <label class="text-sm font-semibold text-slate-700 dark:text-zinc-300">@lang('User Registration')</label>
                                <p class="mt-1 text-xs text-slate-600 dark:text-zinc-400">@lang('Allow new users to register')</p>
                            </div>
                            <input type="checkbox" class="h-5 w-5 rounded border-slate-300 text-sky-600 transition focus:ring-sky-500" name="registration" @if($general->registration) checked @endif />
                        </div>

                        <div class="flex items-center justify-between p-4 rounded-lg border border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/5">
                            <div>
                                <label class="text-sm font-semibold text-slate-700 dark:text-zinc-300">@lang('Force SSL')</label>
                                <p class="mt-1 text-xs text-slate-600 dark:text-zinc-400">@lang('Require HTTPS connections')</p>
                            </div>
                            <input type="checkbox" class="h-5 w-5 rounded border-slate-300 text-sky-600 transition focus:ring-sky-500" name="force_ssl" @if($general->force_ssl) checked @endif />
                        </div>

                        <div class="flex items-center justify-between p-4 rounded-lg border border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/5">
                            <div>
                                <label class="text-sm font-semibold text-slate-700 dark:text-zinc-300">@lang('Email Verification')</label>
                                <p class="mt-1 text-xs text-slate-600 dark:text-zinc-400">@lang('Verify email addresses on registration. This is now enforced for every new user.')</p>
                            </div>
                            <input type="checkbox" class="h-5 w-5 rounded border-slate-300 text-sky-600 transition focus:ring-sky-500" name="ev" checked disabled />
                        </div>

                        <div class="flex items-center justify-between p-4 rounded-lg border border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/5">
                            <div>
                                <label class="text-sm font-semibold text-slate-700 dark:text-zinc-300">@lang('Email Notifications')</label>
                                <p class="mt-1 text-xs text-slate-600 dark:text-zinc-400">@lang('Send email notifications to users')</p>
                            </div>
                            <input type="checkbox" class="h-5 w-5 rounded border-slate-300 text-sky-600 transition focus:ring-sky-500" name="en" @if($general->en) checked @endif />
                        </div>

                        <div class="flex items-center justify-between p-4 rounded-lg border border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/5">
                            <div>
                                <label class="text-sm font-semibold text-slate-700 dark:text-zinc-300">@lang('SMS Verification')</label>
                                <p class="mt-1 text-xs text-slate-600 dark:text-zinc-400">@lang('Verify phone numbers via SMS')</p>
                            </div>
                            <input type="checkbox" class="h-5 w-5 rounded border-slate-300 text-sky-600 transition focus:ring-sky-500" name="sv" @if($general->sv) checked @endif />
                        </div>

                        <div class="flex items-center justify-between p-4 rounded-lg border border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/5">
                            <div>
                                <label class="text-sm font-semibold text-slate-700 dark:text-zinc-300">@lang('SMS Notifications')</label>
                                <p class="mt-1 text-xs text-slate-600 dark:text-zinc-400">@lang('Send SMS notifications to users')</p>
                            </div>
                            <input type="checkbox" class="h-5 w-5 rounded border-slate-300 text-sky-600 transition focus:ring-sky-500" name="sn" @if($general->sn) checked @endif />
                        </div>
                    </div>
                </div>

                <div class="space-y-4 border-t border-slate-200 dark:border-white/10 pt-8">
                    <h3 class="text-lg font-semibold text-slate-950 dark:text-white">@lang('Identity & Topup Accounts')</h3>
                    <p class="section-copy max-w-3xl">@lang('Control when customers are created on Paystack, BudPay, Squad, and Kora, and require BVN or NIN verification before automatic top-up account numbers are generated.')</p>

                    <div class="grid gap-4 md:grid-cols-2">
                        @foreach ([
                            'auto_create_paystack_customer' => 'Auto-create Paystack customer',
                            'auto_create_budpay_customer' => 'Auto-create BudPay customer',
                            'auto_prepare_squad_customer' => 'Auto-create Squad customer model',
                            'auto_generate_paystack_account' => 'Generate Paystack top-up account',
                            'auto_generate_budpay_account' => 'Generate BudPay top-up account',
                            'auto_generate_kora_account' => 'Generate Kora top-up account',
                            'require_identity_for_accounts' => 'Require BVN/NIN before accounts',
                            'lock_profile_after_identity' => 'Lock profile after identity sync',
                            'phone_verification_enabled' => 'Enable WhatsApp OTP verification',
                        ] as $field => $label)
                            <label class="flex items-center justify-between p-4 rounded-lg border border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/5">
                                <span class="text-sm font-semibold text-slate-700 dark:text-zinc-300">{{ $label }}</span>
                                <input type="checkbox" class="h-5 w-5 rounded border-slate-300 text-sky-600 transition focus:ring-sky-500" name="{{ $field }}" value="1" @checked(data_get($general->identity_verification_settings, $field, in_array($field, ['auto_create_paystack_customer', 'auto_create_budpay_customer', 'auto_prepare_squad_customer', 'auto_generate_paystack_account', 'auto_generate_budpay_account', 'require_identity_for_accounts', 'lock_profile_after_identity']))) />
                            </label>
                        @endforeach
                    </div>

                    <div class="grid gap-6 sm:grid-cols-2">
                        <div class="form-group">
                            <label class="block text-sm font-semibold text-slate-700 dark:text-zinc-300 mb-2">Kora secret key override</label>
                            <input class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-slate-900 transition focus:border-sky-500 focus:outline-none dark:border-white/10 dark:bg-white/5 dark:text-white" type="text" name="kora_secret_key" value="{{ data_get($general->identity_verification_settings, 'kora_secret_key') }}">
                        </div>
                        <div class="form-group">
                            <label class="block text-sm font-semibold text-slate-700 dark:text-zinc-300 mb-2">Kora virtual account bank code</label>
                            <input class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-slate-900 transition focus:border-sky-500 focus:outline-none dark:border-white/10 dark:bg-white/5 dark:text-white" type="text" name="kora_virtual_account_bank_code" value="{{ data_get($general->identity_verification_settings, 'kora_virtual_account_bank_code', '035') }}">
                        </div>
                        <div class="form-group">
                            <label class="block text-sm font-semibold text-slate-700 dark:text-zinc-300 mb-2">WhatsApp OTP auth URL</label>
                            <input class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-slate-900 transition focus:border-sky-500 focus:outline-none dark:border-white/10 dark:bg-white/5 dark:text-white" type="text" name="phone_verification_auth_url" value="{{ data_get($general->identity_verification_settings, 'phone_verification_auth_url') }}">
                        </div>
                        <div class="form-group">
                            <label class="block text-sm font-semibold text-slate-700 dark:text-zinc-300 mb-2">WhatsApp OTP send URL</label>
                            <input class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-slate-900 transition focus:border-sky-500 focus:outline-none dark:border-white/10 dark:bg-white/5 dark:text-white" type="text" name="phone_verification_send_url" value="{{ data_get($general->identity_verification_settings, 'phone_verification_send_url') }}">
                        </div>
                        <div class="form-group">
                            <label class="block text-sm font-semibold text-slate-700 dark:text-zinc-300 mb-2">WhatsApp OTP verify URL</label>
                            <input class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-slate-900 transition focus:border-sky-500 focus:outline-none dark:border-white/10 dark:bg-white/5 dark:text-white" type="text" name="phone_verification_verify_url" value="{{ data_get($general->identity_verification_settings, 'phone_verification_verify_url') }}">
                        </div>
                        <div class="form-group">
                            <label class="block text-sm font-semibold text-slate-700 dark:text-zinc-300 mb-2">WhatsApp client ID</label>
                            <input class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-slate-900 transition focus:border-sky-500 focus:outline-none dark:border-white/10 dark:bg-white/5 dark:text-white" type="text" name="phone_verification_client_id" value="{{ data_get($general->identity_verification_settings, 'phone_verification_client_id') }}">
                        </div>
                        <div class="form-group">
                            <label class="block text-sm font-semibold text-slate-700 dark:text-zinc-300 mb-2">WhatsApp client secret</label>
                            <input class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-slate-900 transition focus:border-sky-500 focus:outline-none dark:border-white/10 dark:bg-white/5 dark:text-white" type="text" name="phone_verification_client_secret" value="{{ data_get($general->identity_verification_settings, 'phone_verification_client_secret') }}">
                        </div>
                        <div class="form-group">
                            <label class="block text-sm font-semibold text-slate-700 dark:text-zinc-300 mb-2">WhatsApp sender ID</label>
                            <input class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-slate-900 transition focus:border-sky-500 focus:outline-none dark:border-white/10 dark:bg-white/5 dark:text-white" type="text" name="phone_verification_sender_id" value="{{ data_get($general->identity_verification_settings, 'phone_verification_sender_id') }}">
                        </div>
                        <div class="form-group">
                            <label class="block text-sm font-semibold text-slate-700 dark:text-zinc-300 mb-2">WhatsApp template</label>
                            <input class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-slate-900 transition focus:border-sky-500 focus:outline-none dark:border-white/10 dark:bg-white/5 dark:text-white" type="text" name="phone_verification_template" value="{{ data_get($general->identity_verification_settings, 'phone_verification_template') }}">
                        </div>
                    </div>
                </div>

                <div class="space-y-4 border-t border-slate-200 dark:border-white/10 pt-8">
                    <h3 class="text-lg font-semibold text-slate-950 dark:text-white">@lang('Deposit Checkout')</h3>
                    <p class="section-copy max-w-3xl">@lang('Configure Kora hosted checkout and Quickteller Business inline checkout for the user deposit page.')</p>

                    <div class="grid gap-4 md:grid-cols-2">
                        <label class="flex items-center justify-between p-4 rounded-lg border border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/5">
                            <span class="text-sm font-semibold text-slate-700 dark:text-zinc-300">Enable Kora checkout</span>
                            <input type="checkbox" class="h-5 w-5 rounded border-slate-300 text-sky-600 transition focus:ring-sky-500" name="kora_enabled" value="1" @checked(data_get($general->deposit_checkout_settings, 'kora_enabled', false))>
                        </label>
                        <label class="flex items-center justify-between p-4 rounded-lg border border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/5">
                            <span class="text-sm font-semibold text-slate-700 dark:text-zinc-300">Enable Quickteller inline</span>
                            <input type="checkbox" class="h-5 w-5 rounded border-slate-300 text-sky-600 transition focus:ring-sky-500" name="quickteller_enabled" value="1" @checked(data_get($general->deposit_checkout_settings, 'quickteller_enabled', false))>
                        </label>
                    </div>

                    <div class="grid gap-6 sm:grid-cols-2">
                        <div class="form-group">
                            <label class="block text-sm font-semibold text-slate-700 dark:text-zinc-300 mb-2">Kora public key</label>
                            <input class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-slate-900 transition focus:border-sky-500 focus:outline-none dark:border-white/10 dark:bg-white/5 dark:text-white" type="text" name="kora_public_key" value="{{ data_get($general->deposit_checkout_settings, 'kora_public_key') }}">
                        </div>
                        <div class="form-group">
                            <label class="block text-sm font-semibold text-slate-700 dark:text-zinc-300 mb-2">Quickteller mode</label>
                            <select class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-slate-900 transition focus:border-sky-500 focus:outline-none dark:border-white/10 dark:bg-white/5 dark:text-white" name="quickteller_mode">
                                @foreach(['TEST', 'LIVE'] as $mode)
                                    <option value="{{ $mode }}" @selected(data_get($general->deposit_checkout_settings, 'quickteller_mode', 'TEST') === $mode)>{{ $mode }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="block text-sm font-semibold text-slate-700 dark:text-zinc-300 mb-2">Quickteller merchant code</label>
                            <input class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-slate-900 transition focus:border-sky-500 focus:outline-none dark:border-white/10 dark:bg-white/5 dark:text-white" type="text" name="quickteller_merchant_code" value="{{ data_get($general->deposit_checkout_settings, 'quickteller_merchant_code') }}">
                        </div>
                        <div class="form-group">
                            <label class="block text-sm font-semibold text-slate-700 dark:text-zinc-300 mb-2">Quickteller pay item ID</label>
                            <input class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-slate-900 transition focus:border-sky-500 focus:outline-none dark:border-white/10 dark:bg-white/5 dark:text-white" type="text" name="quickteller_pay_item_id" value="{{ data_get($general->deposit_checkout_settings, 'quickteller_pay_item_id') }}">
                        </div>
                        <div class="form-group">
                            <label class="block text-sm font-semibold text-slate-700 dark:text-zinc-300 mb-2">Quickteller pay item name</label>
                            <input class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-slate-900 transition focus:border-sky-500 focus:outline-none dark:border-white/10 dark:bg-white/5 dark:text-white" type="text" name="quickteller_pay_item_name" value="{{ data_get($general->deposit_checkout_settings, 'quickteller_pay_item_name', 'Wallet Funding') }}">
                        </div>
                        <div class="form-group">
                            <label class="block text-sm font-semibold text-slate-700 dark:text-zinc-300 mb-2">Quickteller client ID</label>
                            <input class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-slate-900 transition focus:border-sky-500 focus:outline-none dark:border-white/10 dark:bg-white/5 dark:text-white" type="text" name="quickteller_client_id" value="{{ data_get($general->deposit_checkout_settings, 'quickteller_client_id') }}">
                        </div>
                        <div class="form-group">
                            <label class="block text-sm font-semibold text-slate-700 dark:text-zinc-300 mb-2">Quickteller client secret</label>
                            <input class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-slate-900 transition focus:border-sky-500 focus:outline-none dark:border-white/10 dark:bg-white/5 dark:text-white" type="text" name="quickteller_client_secret" value="{{ data_get($general->deposit_checkout_settings, 'quickteller_client_secret') }}">
                        </div>
                        <div class="form-group">
                            <label class="block text-sm font-semibold text-slate-700 dark:text-zinc-300 mb-2">Quickteller auth URL</label>
                            <input class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-slate-900 transition focus:border-sky-500 focus:outline-none dark:border-white/10 dark:bg-white/5 dark:text-white" type="text" name="quickteller_auth_url" value="{{ data_get($general->deposit_checkout_settings, 'quickteller_auth_url', 'https://passport.k8.isw.la/passport/oauth/token') }}">
                        </div>
                        <div class="form-group">
                            <label class="block text-sm font-semibold text-slate-700 dark:text-zinc-300 mb-2">Quickteller reference search URL</label>
                            <input class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-slate-900 transition focus:border-sky-500 focus:outline-none dark:border-white/10 dark:bg-white/5 dark:text-white" type="text" name="quickteller_search_url" value="{{ data_get($general->deposit_checkout_settings, 'quickteller_search_url', 'https://switch-online-gateway-service.k9.isw.la/switch-online-gateway-service/api/v1/gateway/reference-search') }}">
                        </div>
                    </div>

                    <div class="rounded-2xl border border-dashed border-slate-300 px-4 py-4 text-sm text-slate-500 dark:border-white/10 dark:text-zinc-500">
                        BudPay webhook URL: <span class="font-medium text-slate-700 dark:text-zinc-300">{{ url('/budpay/webhook') }}</span><br>
                        Paystack webhook URL: <span class="font-medium text-slate-700 dark:text-zinc-300">{{ url('/paystack/webhook') }}</span><br>
                        Kora webhook URL: <span class="font-medium text-slate-700 dark:text-zinc-300">{{ url('/kora/webhook') }}</span><br>
                        Kora return URL: <span class="font-medium text-slate-700 dark:text-zinc-300">{{ route('user.deposit.kora.callback') }}</span><br>
                        Quickteller return URL: <span class="font-medium text-slate-700 dark:text-zinc-300">{{ route('user.deposit.quickteller.callback') }}</span>
                    </div>
                </div>

                <div class="space-y-4 border-t border-slate-200 dark:border-white/10 pt-8">
                    <h3 class="text-lg font-semibold text-slate-950 dark:text-white">@lang('Virtual Cards')</h3>
                    <p class="section-copy max-w-3xl">@lang('Manage the Interswitch virtual prepaid and debit card service, including access rules, API credentials, issuer settings, and the default card type visible in the user dashboard.')</p>

                    <div class="grid gap-4 md:grid-cols-2">
                        @foreach ([
                            'virtual_card_enabled' => 'Enable virtual cards',
                            'virtual_card_allow_prepaid' => 'Allow prepaid cards',
                            'virtual_card_allow_debit' => 'Allow debit cards',
                            'virtual_card_require_verified_email' => 'Require verified email',
                            'virtual_card_require_identity' => 'Require BVN or NIN identity lock',
                        ] as $field => $label)
                            <label class="flex items-center justify-between rounded-lg border border-slate-200 bg-slate-50 p-4 dark:border-white/10 dark:bg-white/5">
                                <span class="text-sm font-semibold text-slate-700 dark:text-zinc-300">{{ $label }}</span>
                                <input type="checkbox" class="h-5 w-5 rounded border-slate-300 text-sky-600 transition focus:ring-sky-500" name="{{ $field }}" value="1" @checked(data_get($general->virtual_card_settings, str_replace('virtual_card_', '', $field), in_array($field, ['virtual_card_allow_prepaid', 'virtual_card_require_verified_email', 'virtual_card_require_identity'], true)))>
                            </label>
                        @endforeach
                    </div>

                    <div class="grid gap-6 sm:grid-cols-2">
                        <div class="form-group">
                            <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-zinc-300">Default card type</label>
                            <select class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-slate-900 transition focus:border-sky-500 focus:outline-none dark:border-white/10 dark:bg-white/5 dark:text-white" name="virtual_card_default_type">
                                @foreach (['PREPAID_NEW' => 'Virtual prepaid card', 'DEBIT_EXISTING_ACCOUNT' => 'Virtual debit card'] as $value => $label)
                                    <option value="{{ $value }}" @selected(data_get($general->virtual_card_settings, 'default_type', 'PREPAID_NEW') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-zinc-300">Default currency</label>
                            <input class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-slate-900 transition focus:border-sky-500 focus:outline-none dark:border-white/10 dark:bg-white/5 dark:text-white" type="text" name="virtual_card_default_currency" value="{{ data_get($general->virtual_card_settings, 'default_currency', 'NGN') }}">
                        </div>
                        <div class="form-group">
                            <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-zinc-300">Creation fee</label>
                            <input class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-slate-900 transition focus:border-sky-500 focus:outline-none dark:border-white/10 dark:bg-white/5 dark:text-white" type="number" min="0" step="0.01" name="virtual_card_creation_fee" value="{{ data_get($general->virtual_card_settings, 'creation_fee', $general->cardfee) }}">
                        </div>
                        <div class="form-group">
                            <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-zinc-300">Debit account type</label>
                            <input class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-slate-900 transition focus:border-sky-500 focus:outline-none dark:border-white/10 dark:bg-white/5 dark:text-white" type="text" name="virtual_card_account_type" value="{{ data_get($general->virtual_card_settings, 'account_type', '20') }}">
                        </div>
                        <div class="form-group">
                            <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-zinc-300">Interswitch auth URL</label>
                            <input class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-slate-900 transition focus:border-sky-500 focus:outline-none dark:border-white/10 dark:bg-white/5 dark:text-white" type="text" name="virtual_card_auth_url" value="{{ data_get($general->virtual_card_settings, 'auth_url', 'https://passport-v2.k8.isw.la/passport/oauth/token') }}">
                        </div>
                        <div class="form-group">
                            <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-zinc-300">Interswitch card base URL</label>
                            <input class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-slate-900 transition focus:border-sky-500 focus:outline-none dark:border-white/10 dark:bg-white/5 dark:text-white" type="text" name="virtual_card_base_url" value="{{ data_get($general->virtual_card_settings, 'base_url', 'https://fintech-card-management.k8.isw.la/') }}">
                        </div>
                        <div class="form-group">
                            <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-zinc-300">Interswitch client ID</label>
                            <input class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-slate-900 transition focus:border-sky-500 focus:outline-none dark:border-white/10 dark:bg-white/5 dark:text-white" type="text" name="virtual_card_client_id" value="{{ data_get($general->virtual_card_settings, 'client_id') }}">
                        </div>
                        <div class="form-group">
                            <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-zinc-300">Interswitch client secret</label>
                            <input class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-slate-900 transition focus:border-sky-500 focus:outline-none dark:border-white/10 dark:bg-white/5 dark:text-white" type="text" name="virtual_card_client_secret" value="{{ data_get($general->virtual_card_settings, 'client_secret') }}">
                        </div>
                        <div class="form-group">
                            <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-zinc-300">Issuer number</label>
                            <input class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-slate-900 transition focus:border-sky-500 focus:outline-none dark:border-white/10 dark:bg-white/5 dark:text-white" type="text" name="virtual_card_issuer_nr" value="{{ data_get($general->virtual_card_settings, 'issuer_nr') }}">
                        </div>
                        <div class="form-group">
                            <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-zinc-300">Card program</label>
                            <input class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-slate-900 transition focus:border-sky-500 focus:outline-none dark:border-white/10 dark:bg-white/5 dark:text-white" type="text" name="virtual_card_card_program" value="{{ data_get($general->virtual_card_settings, 'card_program') }}">
                        </div>
                        <div class="form-group">
                            <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-zinc-300">Provider user ID</label>
                            <input class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-slate-900 transition focus:border-sky-500 focus:outline-none dark:border-white/10 dark:bg-white/5 dark:text-white" type="text" name="virtual_card_user_id" value="{{ data_get($general->virtual_card_settings, 'user_id') }}">
                        </div>
                        <div class="form-group">
                            <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-zinc-300">Branch code</label>
                            <input class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-slate-900 transition focus:border-sky-500 focus:outline-none dark:border-white/10 dark:bg-white/5 dark:text-white" type="text" name="virtual_card_branch_code" value="{{ data_get($general->virtual_card_settings, 'branch_code') }}">
                        </div>
                    </div>

                    <div class="rounded-2xl border border-dashed border-slate-300 px-4 py-4 text-sm text-slate-500 dark:border-white/10 dark:text-zinc-500">
                        Provider: <span class="font-medium text-slate-700 dark:text-zinc-300">Interswitch Card 360</span><br>
                        Create card endpoint: <span class="font-medium text-slate-700 dark:text-zinc-300">/card-management/api/v1/card/request</span><br>
                        Prepaid balance endpoint: <span class="font-medium text-slate-700 dark:text-zinc-300">/card-management/api/v1/card/prepaid/balance</span><br>
                        Debit balance endpoint: <span class="font-medium text-slate-700 dark:text-zinc-300">/card-management/api/v1/card/debit/balance</span>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end">
                    <button type="submit" class="inline-flex h-11 items-center rounded-full bg-sky-600 px-6 text-sm font-semibold text-white transition hover:bg-sky-700 dark:bg-sky-700 dark:hover:bg-sky-800">
                        @lang('Save Changes')
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('script-lib')
    <script src="{{ asset('assets/admin/js/spectrum.js') }}"></script>
@endpush

@push('style-lib')
    <link rel="stylesheet" href="{{ asset('assets/admin/css/spectrum.css') }}">
@endpush

@push('style')
    <style>
        .sp-replacer {
            padding: 0;
            border: 1px solid rgba(0, 0, 0, .125);
            border-radius: 5px;
        }

        .sp-preview {
            width: 100%;
            height: 40px;
            border: 0;
        }

        .sp-preview-inner {
            width: 100%;
        }

        .sp-dd {
            display: none;
        }
    </style>
@endpush

@push('script')
    <script>
        (function ($) {
            "use strict";
            $('.colorPicker').spectrum({
                color: $(this).data('color'),
                change: function (color) {
                    $(this).siblings('.colorCode').val(color.toHexString().replace(/^#?/, ''));
                }
            });

            $('.colorCode').on('input', function () {
                var clr = $(this).val();
                $(this).siblings('.colorPicker').spectrum({
                    color: clr,
                });
            });
        })(jQuery);

    </script>
@endpush
