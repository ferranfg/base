<?php

namespace Ferranfg\Base\Commands;

use Ferranfg\Base\Base;
use Illuminate\Console\Command;

class PushFacebookPost extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'base:push-facebook-post';

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
        $post = Base::post()
            ->whereStatus('published')
            ->whereIn('type', ['entry', 'dynamic'])
            ->inRandomOrder()
            ->first();

        $post->publishFacebook();

        return Command::SUCCESS;
    }
}
