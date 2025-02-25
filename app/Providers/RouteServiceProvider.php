<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * Path default setelah pengguna login.
     *
     * @var string
     */
    public const HOME = '/dashboard';

    /**
     * Konfigurasi route model bindings, pattern filters, dan lainnya.
     */
    public function boot(): void
    {
        $this->configureRateLimiting();
        $this->mapRoutes();
    }

    /**
     * Konfigurasi Rate Limiting untuk API.
     */
    protected function configureRateLimiting(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by(optional($request->user())->id ?: $request->ip());
        });
    }

    /**
     * Memetakan semua rute aplikasi.
     */
    protected function mapRoutes(): void
    {
        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }
}