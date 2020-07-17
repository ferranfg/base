<?php

namespace Ferranfg\Base\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class PublishCommand extends Command
{
    public $signature = 'base:publish';

    public $description = 'Publish Base package migrations';

    public function handle()
    {
        Artisan::call('vendor:publish', [
            '--provider' => 'Ferranfg\Base\BaseServiceProvider',
            '--tag' => 'migrations'
        ]);
    }
}
