<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

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
        RateLimiter::for('login', fn (Request $request) => [
            Limit::perMinute(5)->by('login-ip:'.$request->ip()),
            Limit::perMinute(5)->by('login-id:'.$request->ip().'|'.strtolower((string) $request->input('identifier'))),
        ]);
        RateLimiter::for('register', fn (Request $request) => Limit::perMinute(5)->by('register:'.$request->ip()));
        RateLimiter::for('otp-verify', fn (Request $request) => Limit::perMinute(10)->by('otp-verify:'.$request->ip()));
        RateLimiter::for('otp-resend', fn (Request $request) => Limit::perMinute(5)->by('otp-resend:'.$request->ip()));
        RateLimiter::for('verification-email', fn (Request $request) => Limit::perMinute(5)->by('verification:'.$request->ip()));
        RateLimiter::for('password-reset', fn (Request $request) => Limit::perMinute(5)->by('password-reset:'.$request->ip()));
    }
}
