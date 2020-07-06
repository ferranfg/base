<?php

namespace Ferranfg\Base\Commands;

use Illuminate\Console\Command;

class BaseCommand extends Command
{
    public $signature = 'base';

    public $description = 'My command';

    public function handle()
    {
        $this->comment('All done');
    }
}
