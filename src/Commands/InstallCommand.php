<?php

namespace Ferranfg\Base\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class InstallCommand extends Command
{
    public $signature = 'base:install';

    public $description = 'Installs Laravel Spark and Laravel Nova';

    public function handle()
    {
        Artisan::call('spark:install');

        Artisan::call('nova:install');

        Artisan::call('storage:link');
    }
}
