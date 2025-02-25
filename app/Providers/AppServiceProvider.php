<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use App\Models\NotificationRecipient;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
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
                $unreadCount = NotificationRecipient::where('user_id', Auth::id())
                    ->whereNull('read_at')
                    ->count();
            } else {
                $unreadCount = 0;
            }
    
            $view->with('unreadNotificationsCount', $unreadCount);
        });
        Paginator::useTailwind();
    }
}
