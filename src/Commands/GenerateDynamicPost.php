<?php

namespace Ferranfg\Base\Commands;

use Ferranfg\Base\Models\Post;
use Ferranfg\Base\Models\Assistance;
use Illuminate\Console\Command;

class GenerateDynamicPost extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    public $signature = 'base:generate-dynamic-post';

    /**
     * The console command description.
     *
     * @var string
     */
    public $description = 'Generate the first pending dynamic post';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $post = Post::whereStatus('pending')->whereType('dynamic')->first();

        if (is_null($post)) return Command::FAILURE;

        $prompt = [
            "Write a long post content for an article titled: \"{$post->name}\".",
            "Include the following keywords: \"{$post->keywords}\".",
            "Language: \"" . strtoupper(config('app.locale')) . "\".",
            "Response must be in Markdown format. Response must contain more than 2000 words.",
            "It should have a minimum of 4 sections.",
            "Do not include an h1 title; start with text.",
            "Do not include the word \"Section\" in the title of the sections.",
            "Produce a reply that doesn't include phrases like 'Certainly,' or 'Here is the your content'.",
        ];

        $assistance = Assistance::completion(implode(' ', $prompt), [
            'temperature' => 0.5,
            'max_tokens' => 7168,
        ]);

        $post->content = $assistance->choices[0]->message->content;
        $post->status = 'published';
        $post->save();

        return Command::SUCCESS;
    }
}