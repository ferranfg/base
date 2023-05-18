<?php

namespace Ferranfg\Base\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use OpenAI;

class EmbeddingsCommand extends Command
{
    public $signature = 'base:embeddings';

    public $description = 'Create and upload embeddings to OpenAI';

    public function handle()
    {
        $embeddings_handler = app(config('base.embeddings_handler'));

        if ( ! method_exists($embeddings_handler, 'handle')) return Command::FAILURE;

        $client = OpenAI::client(
            config('services.openai.secret'),
            config('services.openai.key')
        );

        foreach ($embeddings_handler->handle() as $title => $input)
        {
            $response = $client->embeddings()->create([
                'model' => 'text-embedding-ada-002',
                'input' => $input
            ]);

            DB::connection(config('base.embeddings_connection'))->insert(
                'insert into embeddings (content, embedding) values (?, ?)',
                [$input, $response->embeddings]
            );
        }
    }
}