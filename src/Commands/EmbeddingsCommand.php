<?php

namespace Ferranfg\Base\Commands;

use Illuminate\Console\Command;

class InstallCommand extends Command
{
    public $signature = 'base:embeddings';

    public $description = 'Create and upload embeddings to OpenAI';

    public function handle()
    {

        $post->embeddings();

    }
}