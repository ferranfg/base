<?php

namespace Ferranfg\Base\Commands;

use diversen\markdownSplit;
use Ferranfg\Base\Models\Post;
use Ferranfg\Base\Models\Assistance;
use Illuminate\Console\Command;

class UpdateDynamicPost extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    public $signature = 'base:update-dynamic-post';

    /**
     * The console command description.
     *
     * @var string
     */
    public $description = 'Updates an old post';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $post = Post::whereStatus('published')
            ->whereType(['entry', 'dynamic'])
            ->where('updated_at', '<', now()->subDays(30))
            ->inRandomOrder()
            ->first();

        $pieces = collect(
                (new markdownSplit)->splitMarkdownAtLevel((string) $post->content, true, 3)
            )
            ->reject(function($piece)
            {
                if ( ! array_key_exists('header', $piece)) return true;
                if ( ! array_key_exists('body', $piece)) return true;

                // Necesitamos texto medio largo para que pueda ser reescrito
                if (mb_strlen($piece['body']) < 200) return true;

                return $piece['header'] == '';
            })
            ->random(1);

        foreach ($pieces as $piece)
        {
            $words = count(explode(' ', $piece['body'])) * 2;
            $prompt = [
                "Rewrite the following subsection taken from a blog post.",
                "This subsection is titled: \"{$piece['header']}\".",
                "The content you write must keep the same meaning and structure as the original.",
                "The response must be in markdown format, using only paragraphs or lists.",
                "Do not include the subsection title in your response; start writing from the first paragraph.",
                "The response should have around {$words} words long.",
                (string) null,
                "Original content:",
                $piece['body'],
            ];

            $assistance = Assistance::completion(implode("\n", $prompt), [
                'temperature' => 0.5,
                'max_tokens' => $words * 1.5,
            ]);

            $content_updated = $assistance->choices[0]->message->content;

            $post->content = str_replace($piece['body'], $content_updated, $post->content);
            $post->save();
        }
    }
}