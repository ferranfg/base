<?php

namespace Ferranfg\Base\Commands;

use Ferranfg\Base\Base;
use Ferranfg\Base\Jobs\SendPostNewsletter as SendPostNewsletterJob;
use Illuminate\Console\Command;

class SendPostNewsletter extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'base:send-post-newsletter {postId} {--type=test}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends the current post newsletter';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $post = Base::post()->find($this->argument('postId'));

        if (is_null($post)) return Command::FAILURE;

        SendPostNewsletterJob::dispatch($post, $this->option('type'));

        return Command::SUCCESS;
    }
}
