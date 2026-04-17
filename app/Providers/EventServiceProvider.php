<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use HenryEjemuta\LaravelMonnify\Events\NewWebHookCallReceived;
use App\Listeners\MonnifyNotificationListener;
use Spatie\LaravelPasskeys\Events\PasskeyUsedToAuthenticateEvent;
use App\Listeners\HandlePasskeyAuthentication;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\Failed;
use App\Listeners\CountlyAuthListener;

class EventServiceProvider extends ServiceProvider
{
    /**
	 * The event listener mappings for the application.
	 *
	 * @var array
	 */
	protected $listen = [
		Registered::class => [
			SendEmailVerificationNotification::class,
			//    ... Other Event Registration
		],

		Login::class => [
			[CountlyAuthListener::class, 'handleLogin'],
		],

		Logout::class => [
			[CountlyAuthListener::class, 'handleLogout'],
		],

		Failed::class => [
			[CountlyAuthListener::class, 'handleLoginFailed'],
		],

		NewWebHookCallReceived::class => [
			MonnifyNotificationListener::class,
			//    ... Other Listeners you wish to also receive the WebHook call event
		],

		\App\Events\MonnifyTokenEvent::class => [
			\App\Listeners\MonnifyTokenListener::class,
		],
        PasskeyUsedToAuthenticateEvent::class => [
            HandlePasskeyAuthentication::class,
        ],
	];


    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     *
     * @return bool
     */
    public function shouldDiscoverEvents()
    {
        return true;
    }
}
