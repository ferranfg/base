<?php

namespace Ferranfg\Base\Commands;

use Ferranfg\Base\Base;
use Illuminate\Console\Command;

class InternalLinking extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'base:internal-linking';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create internal links for given post.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $hits = 0;

        foreach (Base::post()->whereStatus('published')->get() as $post)
        {
            if (is_null($post->keywords)) continue;

            $keywords = explode(', ', $post->keywords);

            foreach ($keywords as $keyword)
            {
                Base::post()->where('content', 'LIKE', "% {$keyword} %")
                    ->where('id', '!=', $post->id)
                    ->get()
                    ->each(function ($post) use ($keyword, &$hits)
                    {
                        $hits++;

                        // $post->content = str_replace(" {$keyword} ", " [{$keyword}]({$post->canonical_url}) ", $post->content);
                        // $post->save();
                    });
            }
        }

        logger()->info("Internal linking finished. {$hits} hits.");

        return Command::SUCCESS;
    }
}