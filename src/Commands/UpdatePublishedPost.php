<?php

namespace Ferranfg\Base\Commands;

use Ferranfg\Base\Base;
use Illuminate\Console\Command;

class UpdatePublishedPost extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'base:update-published-post {--publish_facebook=true} {--publish_instagram=true}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish on Facebook a random post from the site.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $posts = Base::post()
            ->whereStatus('published')
            ->whereIn('type', ['entry', 'dynamic'])
            ->whereFeatured(false)
            ->where('updated_at', '<', now()->subDays(30))
            ->orderBy('updated_at', 'asc')
            ->take(5)
            ->get();

        if ($posts->isEmpty()) return Command::SUCCESS;

        $post = $posts->random();

        $post->updated_at = now();
        $post->save();

        $post->publishMeta(
            $this->option('publish_facebook')  == 'true',
            $this->option('publish_instagram') == 'true'
        );

        return Command::SUCCESS;
    }
}
