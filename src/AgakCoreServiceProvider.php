<?php

namespace Mhafizhasan\AgakCore;

use Illuminate\Support\ServiceProvider;

class AgakCoreServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        // publish on boot
        $this->publishes([
            __DIR__.'/../migrations/create_activity_log_table.php' => database_path('/migrations/create_activity_log_table.php')
        ],'migrations');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app['AgakLogger'] = $this->app->share(function($app) {
            return new AgakLogger;
        });

        $this->app['AgakAPI'] = $this->app->share(function($app) {
            return new AgakAPI;
        });
    }
}
