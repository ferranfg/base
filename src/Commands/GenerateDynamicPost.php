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
            "Create a blog post content for an titled: \"{$post->name}\".",
            "Use transition words. Use active voice. Write over 1800 words.",
            "It should have a minimum of 6 sections. Add subtitles for each section.",
            "Include the following keywords: \"{$post->keywords}\".",
            "Language: \"" . strtoupper(config('app.locale')) . "\".",
            "The response must be written in Markdown."
        ];

        $assistance = Assistance::completion(implode(' ', $prompt), [
            'temperature' => 0.2,
            'max_tokens' => 2048
        ]);

        $post->content = $assistance->choices[0]->message->content;
        $post->status = 'published';
        $post->save();

        return Command::SUCCESS;
    }
}