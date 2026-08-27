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
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        // OTP send — strict: 5 per minute per IP and phone number to prevent SMS flooding
        RateLimiter::for('otp', function (Request $request) {
            $phoneKey = $request->input('phone') ? '|' . preg_replace('/\D/', '', $request->input('phone')) : '';
            return Limit::perMinute(5)->by($request->ip() . $phoneKey);
        });

        // OTP verify — 5 attempts per minute per IP and phone number to prevent brute-force
        RateLimiter::for('otp-verify', function (Request $request) {
            $phoneKey = $request->input('phone') ? '|' . preg_replace('/\D/', '', $request->input('phone')) : '';
            return Limit::perMinute(5)->by($request->ip() . $phoneKey);
        });

        // Runner location pings — 30 per minute per authenticated user
        RateLimiter::for('location', function (Request $request) {
            return Limit::perMinute(30)->by($request->user()?->id ?: $request->ip());
        });

        // Delivery PIN verify — 5 attempts per minute per delivery ID
        RateLimiter::for('delivery-pin', function (Request $request) {
            return Limit::perMinute(5)->by(
                ($request->route('delivery') ?? 'unknown') . ':' . ($request->user()?->id ?: $request->ip())
            );
        });

        // IntaSend webhook — 60 per minute per IP
        RateLimiter::for('webhooks', function (Request $request) {
            return Limit::perMinute(60)->by($request->ip());
        });


        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }
}
