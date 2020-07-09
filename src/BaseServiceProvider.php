<?php

namespace Ferranfg\Base;

use Laravel\Cashier\Cashier;
use Illuminate\Support\ServiceProvider;
use Ferranfg\Base\Commands\InstallCommand;

class BaseServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole())
        {
            $this->publishes([
                __DIR__.'/../config/base.php' => config_path('base.php'),
            ], 'config');

            $this->publishes([
                __DIR__.'/../resources/views' => base_path('resources/views/vendor/base'),
            ], 'views');

            if ( ! class_exists('CreatePackageTable'))
            {
                $time = date('Y_m_d_His', time());

                $this->publishes([
                    // Spatie
                    __DIR__ . '/../database/migrations/create_tags_table.php.stub' => database_path("migrations/{$time}_create_tags_table.php"),
                    __DIR__ . '/../database/migrations/create_activity_log_table.php.stub' => database_path("migrations/{$time}_create_activity_log_table.php"),
                    // Base
                    __DIR__ . '/../database/migrations/create_posts_table.php.stub' => database_path("migrations/{$time}_create_posts_table.php"),
                    __DIR__ . '/../database/migrations/create_comments_table.php.stub' => database_path("migrations/{$time}_create_comments_table.php"),
                ], 'migrations');
            }

            $this->commands([
                InstallCommand::class,
            ]);
        }

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'base');
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/base.php', 'base');

        Cashier::ignoreMigrations();
    }
}