<?php

namespace Ferranfg\Base;

use Illuminate\Support\ServiceProvider;
use Ferranfg\Base\Commands\BaseCommand;

class BaseServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/base.php' => config_path('base.php'),
            ], 'config');

            $this->publishes([
                __DIR__.'/../resources/views' => base_path('resources/views/vendor/base'),
            ], 'views');

            if (! class_exists('CreatePackageTable')) {
                $this->publishes([
                    __DIR__ . '/../database/migrations/create_base_table.php.stub' => database_path('migrations/' . date('Y_m_d_His', time()) . '_create_base_table.php'),
                ], 'migrations');
            }

            $this->commands([
                BaseCommand::class,
            ]);
        }

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'base');
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/base.php', 'base');
    }
}
