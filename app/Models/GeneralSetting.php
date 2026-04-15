<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GeneralSetting extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'mail_config' => 'object',
        'sms_config' => 'object',
        'local_transfer_enabled' => 'boolean',
        'local_transfer_require_pin' => 'boolean',
        'local_transfer_min' => 'decimal:2',
        'local_transfer_max' => 'decimal:2',
        'local_transfer_resolve_order' => 'array',
        'local_transfer_transfer_order' => 'array',
        'local_transfer_settings' => 'array',
        'kyc_subscription_enabled' => 'boolean',
        'kyc_subscription_settings' => 'array',
        'identity_verification_settings' => 'array',
        'deposit_checkout_settings' => 'array',
        'virtual_card_settings' => 'array',
        'bill_payment_enabled' => 'boolean',
        'bill_payment_service_providers' => 'array',
        'bill_payment_settings' => 'array',
        'bill_payment_auto_sync_enabled' => 'boolean',
        'bill_payment_catalog_last_synced_at' => 'datetime',
    ];

    public function scopeSitename($query, $pageTitle)
    {
        $pageTitle = empty($pageTitle) ? '' : ' - ' . $pageTitle;
        return $this->sitename . $pageTitle;
    }
}
