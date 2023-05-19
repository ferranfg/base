<?php

namespace Ferranfg\Base\Commands;

use Ferranfg\Base\Models\Assistance;
use Illuminate\Console\Command;

class EmbeddingsCommand extends Command
{
    public $signature = 'base:embeddings';

    public $description = 'Create and upload embeddings to OpenAI';

    public function handle()
    {
        $embeddings_handler = app(config('base.assistance_embeddings_handler'));

        if ( ! method_exists($embeddings_handler, 'handle')) return Command::FAILURE;

        Assistance::truncate();

        foreach ($embeddings_handler->handle() as $input)
        {
            $assistance = Assistance::embeddingFromInput($input);
            $assistance->save();
        }
    }
}