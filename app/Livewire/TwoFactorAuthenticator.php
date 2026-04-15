<?php

namespace App\Livewire;

use App\Lib\GoogleAuthenticator;
use App\Models\GeneralSetting;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Throwable;

class TwoFactorAuthenticator extends Component
{
    #[Validate('required|string|max:6')]
    public string $code = '';

    #[Validate('required|string')]
    public string $secret = '';

    public bool $isSetupMode = true;
    public ?string $qrCodeUrl = null;

    public function mount(): void
    {
        $user = $this->currentUser();
        
        if ($user->ts) {
            $this->isSetupMode = false;
        } else {
            $this->generateQRCode();
        }
    }

    public function render(): View
    {
        return view('livewire.two-factor-authenticator', [
            'isEnabled' => $this->currentUser()->ts,
            'qrCodeUrl' => $this->qrCodeUrl,
        ]);
    }

    public function generateQRCode(): void
    {
        $general = GeneralSetting::first();
        $ga = new GoogleAuthenticator();
        $user = $this->currentUser();
        
        $this->secret = $ga->createSecret();
        $this->qrCodeUrl = $ga->getQRCodeGoogleUrl(
            $user->username . '@' . $general->sitename,
            $this->secret
        );
    }

    public function enable(): void
    {
        $this->validate();

        $user = $this->currentUser();
        
        if (!$this->verifyCode($user, $this->code, $this->secret)) {
            throw ValidationException::withMessages([
                'code' => 'The verification code is incorrect.',
            ]);
        }

        $user->tsc = $this->secret;
        $user->ts = 1;
        $user->save();

        // Send notification
        $this->notifyUser($user, '2FA_ENABLE');

        $this->isSetupMode = false;
        $this->code = '';
        
        $this->dispatch('2faEnabled');
        session()->flash('success', 'Two-factor authentication enabled successfully.');
    }

    public function disable(): void
    {
        $this->validate();

        $user = $this->currentUser();
        
        if (!$this->verifyCode($user, $this->code)) {
            throw ValidationException::withMessages([
                'code' => 'The verification code is incorrect.',
            ]);
        }

        $user->tsc = null;
        $user->ts = 0;
        $user->save();

        // Send notification
        $this->notifyUser($user, '2FA_DISABLE');

        $this->isSetupMode = true;
        $this->code = '';
        
        $this->generateQRCode();
        
        $this->dispatch('2faDisabled');
        session()->flash('success', 'Two-factor authentication disabled successfully.');
    }

    protected function verifyCode(Authenticatable $user, string $code, ?string $secret = null): bool
    {
        try {
            $ga = new GoogleAuthenticator();
            $userSecret = $secret ?? $user->tsc;
            
            if (!$userSecret) {
                return false;
            }
            
            // GoogleAuthenticator::verifyCode allows for time discrepancy
            return $ga->verifyCode($userSecret, $code, 1);
        } catch (Throwable $e) {
            return false;
        }
    }

    protected function notifyUser(Authenticatable $user, string $notificationType): void
    {
        $userAgent = getIpInfo();
        $osBrowser = osBrowser();
        
        notify($user, $notificationType, [
            'operating_system' => @$osBrowser['os_platform'],
            'browser' => @$osBrowser['browser'],
            'ip' => @$userAgent['ip'],
            'time' => @$userAgent['time']
        ]);
    }

    protected function currentUser(): Authenticatable
    {
        return auth()->user();
    }
}
