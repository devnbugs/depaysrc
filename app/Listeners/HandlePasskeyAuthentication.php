<?php

namespace App\Listeners;

use App\Http\Controllers\Auth\LoginController;
use App\Services\Passkeys\PasskeyStateService;
use Spatie\LaravelPasskeys\Events\PasskeyUsedToAuthenticateEvent;

class HandlePasskeyAuthentication
{
    public function __construct(
        protected PasskeyStateService $passkeyState,
        protected LoginController $loginController,
    ) {
    }

    public function handle(PasskeyUsedToAuthenticateEvent $event): void
    {
        $user = $event->passkey->authenticatable;

        if (! $user) {
            return;
        }

        $event->passkey->forceFill(['last_used_at' => now()])->save();
        $this->passkeyState->syncForUser($user);
        $this->loginController->authenticated($event->request, $user);
    }
}
