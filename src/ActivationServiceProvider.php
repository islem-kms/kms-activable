<?php

namespace IslemKms\Activable;

use Illuminate\Support\ServiceProvider;

class ActivationServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/config/activable.php' => config_path('activable.php')
        ], 'config');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {

    }
}