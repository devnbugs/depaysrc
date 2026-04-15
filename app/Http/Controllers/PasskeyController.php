<?php

namespace App\Http\Controllers;

use App\Services\Passkeys\PasskeyStateService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class PasskeyController extends Controller
{
    public function __construct(protected PasskeyStateService $passkeyState)
    {
    }

    public function index()
    {
        $pageTitle = 'Passkey Settings';
        $user = Auth::user()->load('passkeys');

        return view('user.user.settings.passkey', compact('pageTitle', 'user'));
    }

    public function disable(): RedirectResponse
    {
        $user = Auth::user();

        if (! $user->isTwoFactorEnabled() && ! $user->isPinEnabled()) {
            return back()->withNotify([['error', 'Enable PIN or 2FA before removing every passkey from the account.']]);
        }

        $user->passkeys()->delete();
        $this->passkeyState->syncForUser($user->fresh());

        return back()->withNotify([['success', 'All passkeys removed successfully.']]);
    }
}
