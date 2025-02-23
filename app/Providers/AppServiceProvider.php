<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Carbon\Carbon;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Carbon::setLocale('id');
        View::composer('*', function ($view) {
            if (Auth::check()) {
                $unreadNotificationsCount = Auth::user()->unreadNotifications->count();
                $view->with('unreadNotificationsCount', $unreadNotificationsCount);
            }
        });
    }
}
