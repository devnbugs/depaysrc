<?php

namespace App\Providers;

use App\Models\AdminNotification;
use App\Models\Deposit;
use App\Models\Frontend;
use App\Models\GeneralSetting;
use App\Models\Language;
use App\Models\Page;
use App\Models\User;
use App\Models\Withdrawal;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Throwable;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::useTailwind();

        if ($this->app->runningInConsole() && ! $this->app->runningUnitTests()) {
            return;
        }

        View::share([
            'general' => (object) [],
            'activeTemplate' => 'user.',
            'activeTemplateTrue' => 'assets/templates/default/',
            'language' => collect(),
            'pages' => collect(),
        ]);

        try {
            $general = Cache::remember('general-setting', 300, fn () => GeneralSetting::first());
            $activeTemplate = activeTemplate();

            View::share([
                'general' => $general,
                'activeTemplate' => $activeTemplate,
                'activeTemplateTrue' => activeTemplate(true),
                'language' => Language::all(),
                'pages' => Page::where('tempname', $activeTemplate)->where('slug', '!=', 'home')->get(),
            ]);

            View::composer('admin.partials.sidenav', function ($view) {
                $view->with([
                    'banned_users_count'           => User::banned()->count(),
                    'email_unverified_users_count' => User::emailUnverified()->count(),
                    'sms_unverified_users_count'   => User::smsUnverified()->count(),
                    'pending_deposits_count'       => Deposit::pending()->count(),
                    'pending_withdraw_count'       => Withdrawal::pending()->count(),
                ]);
            });

            View::composer('admin.partials.topnav', function ($view) {
                $view->with([
                    'adminNotifications' => AdminNotification::where('read_status', 0)->with('user')->orderBy('id', 'desc')->get(),
                ]);
            });

            View::composer('partials.seo', function ($view) {
                $seo = Frontend::where('data_keys', 'seo.data')->first();
                $view->with([
                    'seo' => $seo ? $seo->data_values : $seo,
                ]);
            });

            if (optional($general)->force_ssl && request()->isSecure()) {
                URL::forceScheme('https');
            }
        } catch (Throwable $e) {
            // Keep bootstrap resilient if the database is unavailable during maintenance or package discovery.
        }

    }
}
