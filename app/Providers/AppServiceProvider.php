<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use GuzzleHttp\Client;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('api', function ($app) {
            return new Client([
                'base_uri' => env('API_URI'),
                'timeout'  => env('API_TIMEOUT', 2.0),
            ]);
        });
    }
}
