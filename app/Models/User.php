<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\LaravelPasskeys\Models\Concerns\HasPasskeys;
use Spatie\LaravelPasskeys\Models\Concerns\InteractsWithPasskeys;

class User extends Authenticatable implements HasPasskeys
{
    use InteractsWithPasskeys;
    use Notifiable, HasApiTokens;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = [
        'name',
        'email',
        'password',
        'pin',
        'psbank',
        'psacname',
        'psnuban',
        'psid',
        'monnify_token',
        'bankName',
        'accountNumber',
        'accountName',
        'bN1',
        'bN2',
        'bN3',
        'aN1',
        'aN2',
        'aN3',
        'aNo1',
        'aNo2',
        'aNo3',
        'aNid1',
        'aNid2',
        'ussd',
        'mobile',
        'whatsapp_phone',
        'NIN',
        'BVN',
        'mother_maiden_name',
        'kyc_additional_data',
        'paystackcode',
        'pslinked',
        'psverified',
        'budpay_customer_code',
        'budpay_customer_id',
        'budpay_virtual_account_id',
        'budpay_virtual_account_reference',
        'budpay_linked',
        'budpay_verified',
        'kora_account_reference',
        'kora_virtual_account_id',
        'kora_bank_code',
        'kora_linked',
        'kora_verified',
        'squad_customer_reference',
        'squad_customer_status',
        'identity_source',
        'identity_locked_at',
        'identity_verified_at',
        'identity_payload',
        'identity_date_of_birth',
        'identity_gender',
        'identity_middle_name',
        'phone_verification_channel',
        'phone_verification_reference',
        'phone_verification_requested_at',
        'phone_verified_externally_at',
        'pin_state',
        'pin_enabled_at',
        'pin_failed_attempts',
        'pin_locked_until',
        'two_factor_secret',
        'two_factor_enabled_at',
        'two_factor_enabled',
        'passkey_enabled',
        'passkey_credentials',
        'passkey_created_at',
    ];
    protected $guarded = ['id'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'address' => 'object',
        'ver_code_send_at' => 'datetime',
        'is_kyc_upgraded' => 'boolean',
        'kyc_upgrade_date' => 'datetime',
        'kyc_expiry_date' => 'datetime',
        'kyc_monthly_limit' => 'decimal:2',
        'kyc_monthly_spent' => 'decimal:2',
        'kyc_subscription_started_at' => 'datetime',
        'kyc_subscription_next_payment_at' => 'datetime',
        'kyc_subscription_cancelled_at' => 'datetime',
        'kyc_subscription_last_payment_at' => 'datetime',
        'pslinked' => 'boolean',
        'budpay_linked' => 'boolean',
        'kora_linked' => 'boolean',
        'identity_locked_at' => 'datetime',
        'identity_verified_at' => 'datetime',
        'identity_payload' => 'array',
        'kyc_additional_data' => 'array',
        'identity_date_of_birth' => 'date',
        'phone_verification_requested_at' => 'datetime',
        'phone_verified_externally_at' => 'datetime',
        'pin_enabled_at' => 'datetime',
        'pin_locked_until' => 'datetime',
        'two_factor_enabled_at' => 'datetime',
        'two_factor_enabled' => 'boolean',
        'passkey_enabled' => 'boolean',
        'passkey_credentials' => 'array',
        'passkey_created_at' => 'datetime',
    ];

    protected $data = [
        'data'=>1
    ];




    public function login_logs()
    {
        return $this->hasMany(UserLogin::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class)->orderBy('id','desc');
    }

    public function deposits()
    {
        return $this->hasMany(Deposit::class)->where('status','!=',0);
    }

    public function withdrawals()
    {
        return $this->hasMany(Withdrawal::class)->where('status','!=',0);
    }


    // SCOPES

    public function getFullnameAttribute()
    {
        return $this->firstname . ' ' . $this->lastname;
    }

    public function scopeActive()
    {
        return $this->where('status', 1);
    }

    public function scopeBanned()
    {
        return $this->where('status', 0);
    }

    public function scopeEmailUnverified()
    {
        return $this->where('ev', 0);
    }

    public function scopeSmsUnverified()
    {
        return $this->where('sv', 0);
    }
    public function scopeEmailVerified()
    {
        return $this->where('ev', 1);
    }

    public function scopeSmsVerified()
    {
        return $this->where('sv', 1);
    }
    public function contacts()
    {
        return $this->hasMany(Contact::class);
    }

    public function authenticationVerifications()
    {
        return $this->hasMany(AuthenticationVerification::class)->orderBy('created_at', 'desc');
    }

    // PIN Authentication Helpers
    public function isPinEnabled()
    {
        return !is_null($this->pin) && (int)$this->pin_state === 1;
    }

    public function isPinLocked()
    {
        return !is_null($this->pin_locked_until) && $this->pin_locked_until > now();
    }

    public function isTwoFactorEnabled()
    {
        return (bool)$this->two_factor_enabled && !is_null($this->two_factor_secret);
    }

    public function isPasskeyEnabled()
    {
        if ($this->relationLoaded('passkeys')) {
            return $this->passkeys->isNotEmpty();
        }

        return $this->passkeys()->exists();
    }

    public function getPassKeyDisplayName(): string
    {
        return trim($this->fullname) ?: ($this->username ?: $this->email);
    }

    public function hasLockedIdentity(): bool
    {
        return ! is_null($this->identity_locked_at);
    }

}
