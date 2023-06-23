<?php

namespace Ferranfg\Base\Commands;

use Ferranfg\Base\Models\Assistance;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class EmbeddingsCommand extends Command
{
    public $signature = 'base:embeddings';

    public $description = 'Create and upload embeddings to OpenAI';

    public function handle()
    {
        [$class, $method] = Str::parseCallback(config('base.assistance_embeddings'));

        $embeddings_handler = app($class);

        if ( ! method_exists($embeddings_handler, $method)) return Command::FAILURE;

        foreach ($embeddings_handler->$method() as $input)
        {
            $assistance = Assistance::embeddingFromInput($input);
            $assistance->save();
        }

        return Command::SUCCESS;
    }
}