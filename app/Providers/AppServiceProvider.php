<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

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
        if ($this->app->environment('production')) {
            $appUrl = rtrim((string) config('app.url'), '/');
            $secureAppUrl = preg_replace('/^http:\/\//i', 'https://', $appUrl);

            URL::forceScheme('https');
            URL::forceRootUrl($secureAppUrl);
            URL::useAssetOrigin($secureAppUrl);
        }
    }
}
