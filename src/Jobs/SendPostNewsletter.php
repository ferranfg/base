<?php

namespace Ferranfg\Base\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendPostNewsletter implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 3600; // 60 minutes

    /**
     * The post instance.
     *
     * @var Post
     */
    private $post;

    /**
     * The type of the newsletter.
     *
     * @var string
     */
    private $type;

    /**
     * Create a new job instance.
     *
     * @param Post $post
     * @param string $type
     * @return void
     */
    public function __construct($post, $type = 'test')
    {
        $this->post = $post;
        $this->type = $type;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->post->sendNewsletter($this->type);
    }
}