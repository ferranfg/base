<?php

namespace Ferranfg\Base;

use Blade;
use Illuminate\Support\ServiceProvider;
use Ferranfg\Base\Commands\PublishCommand;
use Ferranfg\Base\Commands\InstallCommand;
use Ferranfg\Base\Commands\EmbeddingsCommand;
use Ferranfg\Base\Commands\SendPostNewsletter;

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
                    __DIR__ . '/../database/migrations/create_comments_table.php.stub' => database_path("migrations/{$time}_create_comments_table.php"),
                    __DIR__ . '/../database/migrations/create_events_table.php.stub' => database_path("migrations/{$time}_create_events_table.php"),
                    __DIR__ . '/../database/migrations/create_metadata_table.php.stub' => database_path("migrations/{$time}_create_metadata_table.php"),
                    __DIR__ . '/../database/migrations/create_notes_table.php.stub' => database_path("migrations/{$time}_create_notes_table.php"),
                    __DIR__ . '/../database/migrations/create_posts_table.php.stub' => database_path("migrations/{$time}_create_posts_table.php"),
                    __DIR__ . '/../database/migrations/create_products_table.php.stub' => database_path("migrations/{$time}_create_products_table.php"),
                    __DIR__ . '/../database/migrations/create_tags_table.php.stub' => database_path("migrations/{$time}_create_tags_table.php"),
                    __DIR__ . '/../database/migrations/update_users_table.php.stub' => database_path("migrations/{$time}_update_users_table.php"),
                    __DIR__ . '/../database/migrations/update_users_connect.php.stub' => database_path("migrations/{$time}_update_users_connect.php"),
                ], 'migrations');
            }

            $this->commands([
                EmbeddingsCommand::class,
                InstallCommand::class,
                PublishCommand::class,
                SendPostNewsletter::class,
            ]);
        }

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'base');
        $this->loadJsonTranslationsFrom(__DIR__.'/../resources/lang', 'base');
        $this->loadRoutesFrom(__DIR__.'/routes.php');

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
    }
}
