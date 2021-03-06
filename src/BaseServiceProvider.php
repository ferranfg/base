<?php

namespace Ferranfg\Base;

use Blade;
use Ferranfg\Base\Base;
use Laravel\Spark\Spark;
use Laravel\Cashier\Cashier;
use Illuminate\Support\ServiceProvider;
use Spatie\NovaTranslatable\Translatable;
use Ferranfg\Base\Commands\PublishCommand;
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
                $time = '2021_01_01_000000';

                $this->publishes([
                    // Spatie
                    __DIR__ . '/../database/migrations/create_tags_table.php.stub' => database_path("migrations/{$time}_create_tags_table.php"),
                    __DIR__ . '/../database/migrations/create_activity_log_table.php.stub' => database_path("migrations/{$time}_create_activity_log_table.php"),
                    // Revisionable
                    __DIR__ . '/../database/migrations/create_revisionable_table.php.stub' => database_path("migrations/{$time}_create_revisionable_table.php"),
                    // Base
                    __DIR__ . '/../database/migrations/create_products_table.php.stub' => database_path("migrations/{$time}_create_products_table.php"),
                    __DIR__ . '/../database/migrations/create_posts_table.php.stub' => database_path("migrations/{$time}_create_posts_table.php"),
                    __DIR__ . '/../database/migrations/create_comments_table.php.stub' => database_path("migrations/{$time}_create_comments_table.php"),
                    __DIR__ . '/../database/migrations/create_events_table.php.stub' => database_path("migrations/{$time}_create_events_table.php"),
                    __DIR__ . '/../database/migrations/create_metadata_table.php.stub' => database_path("migrations/{$time}_create_metadata_table.php"),
                ], 'migrations');
            }

            $this->commands([
                InstallCommand::class,
                PublishCommand::class,
            ]);
        }

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'base');
        $this->loadRoutesFrom(__DIR__.'/routes.php');

        Translatable::defaultLocales([
            config('app.locale')
        ]);

        Blade::directive('markdown', function ($expression)
        {
            return "<?php echo (new Parsedown)->text($expression); ?>";
        });

        Blade::directive('basedown', function ($expression)
        {
            return "<?php echo (new \Ferranfg\Base\Basedown)->directive($expression); ?>";
        });
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/base.php', 'base');

        Cashier::ignoreMigrations();

        Spark::collectEuropeanVat('ES');

        Spark::useUserModel(Base::$userModel);
        Spark::useTeamModel(Base::$teamModel);
    }
}
