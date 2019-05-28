<?php

namespace ShineOnCom\Zoho\Integrations\Laravel;

use ShineOnCom\Zoho\Util;
use Illuminate\Support\ServiceProvider;
use ShineOnCom\Zoho\Zoho;

/**
 * Class ZohoServiceProvider
 */
class ZohoServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../../../config/zoho.php' => config_path('zoho.php')
        ]);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if (Util::isLaravel()) {
            $this->mergeConfigFrom(
                __DIR__ . '/../../../config/zoho.php', 'zoho'
            );
        }

        $token = config('zoho.token');

        if ($token) {
            $this->app->singleton('Zoho', function () use ($token) {
                return new Zoho($token);
            });
        }
    }
}
