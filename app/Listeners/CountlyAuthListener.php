<?php

namespace App\Listeners;

use App\Services\CountlyService;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\Registered;

/**
 * Countly Auth Event Listeners
 * 
 * Listens to Laravel authentication events
 * and tracks them in Countly analytics
 */
class CountlyAuthListener
{
    protected CountlyService $countlyService;

    public function __construct(CountlyService $countlyService)
    {
        $this->countlyService = $countlyService;
    }

    /**
     * Handle successful user login
     */
    public function handleLogin(Login $event): void
    {
        $this->countlyService->trackAuthEvent('login_success', [
            'user_id' => $event->user->id,
            'email' => $event->user->email,
        ]);
    }

    /**
     * Handle failed login attempt
     */
    public function handleLoginFailed(Failed $event): void
    {
        $this->countlyService->trackAuthEvent('login_failed', [
            'credentials' => $event->credentials['email'] ?? 'unknown',
        ]);
    }

    /**
     * Handle user registration
     */
    public function handleRegistered(Registered $event): void
    {
        $this->countlyService->trackAuthEvent('register_success', [
            'user_id' => $event->user->id,
            'email' => $event->user->email,
        ]);
    }

    /**
     * Handle user logout
     */
    public function handleLogout(Logout $event): void
    {
        $this->countlyService->trackAuthEvent('logout', [
            'user_id' => $event->user->id,
        ]);

        $this->countlyService->endSession();
    }
}
