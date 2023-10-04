<?php

namespace Ferranfg\Base\Commands;

use Ferranfg\Base\Models\Assistance;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class EmbeddingsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    public $signature = 'base:embeddings';

    /**
     * The console command description.
     *
     * @var string
     */
    public $description = 'Create and upload embeddings to OpenAI';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        [$class, $method] = Str::parseCallback(config('base.assistance_embeddings'));

        $embeddings_handler = app($class);

        if ( ! method_exists($embeddings_handler, $method)) return Command::FAILURE;

        foreach ($embeddings_handler->$method() as $content)
        {
            if (is_array($content)) $content = json_encode($content);

            if (Assistance::whereContent($content)->exists()) continue;

            $assistance = Assistance::embeddingFromInput($content);
            $assistance->save();
        }

        return Command::SUCCESS;
    }
}