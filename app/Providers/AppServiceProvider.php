<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

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
        if (str_starts_with((string) config('app.url'), 'https://')) {
            URL::forceScheme('https');
        }

        Password::defaults(fn (): Password => Password::min(12)->mixedCase()->numbers());

        RateLimiter::for('asesor', fn (Request $request): array => [
            Limit::perMinute(20)->by($request->ip()),
            Limit::perDay(config('tienda.ia.limite_diario'))->by('asesor:global'),
        ]);
    }
}
