<?php

namespace SDU\MFA;

use App\Listeners\SaveUserToDatabase;
use Illuminate\Auth\Events\Login;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

class MFAServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/mfa.php' => config_path('sdu-mfa.php')
        ]);

        $this->publishes([
            __DIR__ . '/../migrations' => database_path('migrations')
        ], 'sdu-mfa-migrations');

        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');

        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'SDU\MFA');


        Auth::extend('sdu', function ($app)
        {
            return new SDUGuard($app['request']);
        });
    }
}
