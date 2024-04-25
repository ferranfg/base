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
    public $signature = 'base:update-dynamic-post {post?} {--level=3} {--index=random} {--debug=false}';

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

        $subsection_title = null;
        $subsection_content = null;

        if ($level == "intro")
        {
            $subsection_title = "Introduction";
            $subsection_content = explode("\r\n", $post->content)[0] . "\n\n";
        }
        else
        {
            $subsections = collect(
                    (new markdownSplit)->splitMarkdownAtLevel((string) $post->content, true, $level)
                )
                ->reject(function($subsection) use ($level)
                {
                    // Wrong subsection format
                    if ( ! array_key_exists('level', $subsection)) return true;
                    // We only want level {$level} headings
                    if ($subsection['level'] != $level) return true;

                    return false;
                })
                // Remove the keys to start from 0
                ->values();

            if ( ! $subsections->count()) return Command::SUCCESS;

            $subsection = $subsections->get((int) $this->option('index'));

            if ($this->option('index') == 'random' or is_null($subsection)) $subsection = $subsections->random();

            $subsection_title = $subsection['header'];
            $subsection_content = $subsection['body'];
        }

        $prompt = [
            "Read the following subsection taken from a blog post.",
            (string) null,
            "Post Title: \"{$post->name}\"",
            "Post Except: \"{$post->excerpt}\"",
            (string) null,
            "Subsection Title: \"{$subsection_title}\".",
            "Subsection Content:",
            $subsection_content,
            (string) null,
            "Now, write a new paragraph to extend this same subsection, written in a similar style and tone as the text above.",
            "The response must be plain text. Do not include any Markdown or HTML formatting.",
        ];

        $assistance = Assistance::completion(implode("\n", $prompt), [
            'temperature' => 0.5
        ]);

        $subsection_updated = $subsection_content . $assistance->choices[0]->message->content . "\n\n";

        if ($this->option('debug') == 'false')
        {
            $post->content = str_replace($subsection_content, $subsection_updated, $post->content);
            $post->save();
        }

        $this->info("Post Updated ID: {$post->id}");
        $this->info("Old Content: {$subsection_content}");
        $this->info("New Content: {$subsection_updated}");
        $this->info("Debug Mode: {$this->option('debug')}");
    }
}