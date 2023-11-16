<?php

namespace Ferranfg\Base\Commands;

use diversen\markdownSplit;
use Ferranfg\Base\Base;
use Ferranfg\Base\Models\Assistance;
use Illuminate\Console\Command;

class UpdateDynamicPost extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    public $signature = 'base:update-dynamic-post {post?} {--level=3} {--debug=false}';

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
        if ($this->argument('post'))
        {
            $post = Base::post()->find($this->argument('post'));
        }
        else
        {
            $post = Base::post()
                ->whereStatus('published')
                ->whereType(['entry', 'dynamic'])
                ->where('updated_at', '<', now()->subDays(90)) // 3 months
                ->inRandomOrder()
                ->first();
        }

        if (is_null($post)) return Command::FAILURE;

        $level = $this->option('level');
        $pieces = collect(
                (new markdownSplit)->splitMarkdownAtLevel((string) $post->content, true, $level)
            )
            ->reject(function($piece) use ($level)
            {
                // Wrong piece format
                if ( ! array_key_exists('level', $piece)) return true;
                // We only want level {$level} headings
                if ($piece['level'] != $level) return true;
                // Long text to rewrite
                if (mb_strlen($piece['body']) < 200) return true;

                return false;
            });

        if ( ! $pieces->count()) return Command::SUCCESS;

        // Prueba piloto haciendo el cambio de un solo trozo
        $pieces = [$pieces->random()];

        foreach ($pieces as $piece)
        {
            $tokens = ceil(count(explode(' ', $piece['body']))) * 1.5;
            $prompt = [
                "Rewrite the following subsection taken from a blog post.",
                "This subsection is titled: \"{$piece['header']}\".",
                "The content you write must keep the same meaning and structure as the original.",
                "The response must be in markdown format; using only paragraphs, bold, italics, lists or links.",
                "Keep the original elements such as links, images, code blocks, etc.",
                "Do not include the subsection title in your response; start writing from the first paragraph.",
                "Limit your response to {$tokens} tokens or less.",
                (string) null,
                "Original content:",
                $piece['body'],
            ];

            $assistance = Assistance::completion(implode("\n", $prompt), [
                'temperature' => 0.5
            ]);

            $content_updated = $assistance->choices[0]->message->content;

            if ($this->option('debug') == 'false')
            {
                $post->content = str_replace($piece['body'], $content_updated, $post->content);
                $post->save();
            }

            $this->info("Post Updated ID: {$post->id}");
            $this->info("Old Content: {$piece['body']}");
            $this->info("New Content: {$content_updated}");
            $this->info("Debug Mode: {$this->option('debug')}");
        }
    }
}