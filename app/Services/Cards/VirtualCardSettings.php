<?php

namespace App\Services\Cards;

use App\Models\GeneralSetting;
use App\Models\User;

class VirtualCardSettings
{
    public function general(): GeneralSetting
    {
        return GeneralSetting::firstOrFail();
    }

    public function settings(?GeneralSetting $general = null): array
    {
        $general ??= $this->general();
        $stored = $general->virtual_card_settings ?? [];

        return [
            'enabled' => (bool) data_get($stored, 'enabled', false),
            'provider' => (string) data_get($stored, 'provider', 'interswitch'),
            'allow_prepaid' => (bool) data_get($stored, 'allow_prepaid', true),
            'allow_debit' => (bool) data_get($stored, 'allow_debit', false),
            'default_type' => (string) data_get($stored, 'default_type', 'PREPAID_NEW'),
            'require_verified_email' => (bool) data_get($stored, 'require_verified_email', true),
            'require_identity' => (bool) data_get($stored, 'require_identity', true),
            'auth_url' => (string) data_get($stored, 'auth_url', config('services.interswitch.auth_url', 'https://passport-v2.k8.isw.la/passport/oauth/token')),
            'base_url' => (string) data_get($stored, 'base_url', config('services.interswitch.card_base_url', 'https://fintech-card-management.k8.isw.la/')),
            'client_id' => (string) data_get($stored, 'client_id', config('services.interswitch.client_id', '')),
            'client_secret' => (string) data_get($stored, 'client_secret', config('services.interswitch.client_secret', '')),
            'issuer_nr' => (string) data_get($stored, 'issuer_nr', ''),
            'card_program' => (string) data_get($stored, 'card_program', ''),
            'user_id' => (string) data_get($stored, 'user_id', ''),
            'branch_code' => (string) data_get($stored, 'branch_code', ''),
            'account_type' => (string) data_get($stored, 'account_type', '20'),
            'default_currency' => (string) data_get($stored, 'default_currency', 'NGN'),
            'creation_fee' => (float) data_get($stored, 'creation_fee', (float) ($general->cardfee ?? 0)),
        ];
    }

    public function availableTypes(?GeneralSetting $general = null): array
    {
        $settings = $this->settings($general);
        $types = [];

        if ($settings['allow_prepaid']) {
            $types['PREPAID_NEW'] = 'Virtual prepaid card';
        }

        if ($settings['allow_debit']) {
            $types['DEBIT_EXISTING_ACCOUNT'] = 'Virtual debit card';
        }

        if ($types === []) {
            $types['PREPAID_NEW'] = 'Virtual prepaid card';
        }

        return $types;
    }

    public function isConfigured(?GeneralSetting $general = null): bool
    {
        $settings = $this->settings($general);

        return filled($settings['client_id'])
            && filled($settings['client_secret'])
            && filled($settings['issuer_nr'])
            && filled($settings['card_program'])
            && filled($settings['user_id']);
    }

    public function blockersForUser(User $user, ?GeneralSetting $general = null): array
    {
        $settings = $this->settings($general);
        $blockers = [];

        if (! $settings['enabled']) {
            $blockers[] = 'Card service is currently disabled by the admin team.';
        }

        if (! $this->isConfigured($general)) {
            $blockers[] = 'Card service is not fully configured yet.';
        }

        if ($settings['require_verified_email'] && (int) $user->ev !== 1 && is_null($user->email_verified_at)) {
            $blockers[] = 'Verify your email address before requesting a card.';
        }

        if ($settings['require_identity']) {
            if (! filled($user->BVN) && ! filled($user->NIN)) {
                $blockers[] = 'A BVN or NIN must be attached to your profile first.';
            }

            if (! $user->hasLockedIdentity()) {
                $blockers[] = 'Complete identity verification so the BVN or NIN is locked on your profile.';
            }
        }

        return array_values(array_unique($blockers));
    }

    public function canUserCreate(User $user, ?GeneralSetting $general = null): bool
    {
        return $this->blockersForUser($user, $general) === [];
    }
}
