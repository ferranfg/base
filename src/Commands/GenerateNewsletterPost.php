<?php

namespace Ferranfg\Base\Commands;

use Ferranfg\Base\Base;
use Illuminate\Console\Command;

class GenerateNewsletterPost extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'base:generate-newsletter-post {--type=all}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a weekly summary of the best publications and send as newsletter.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $posts = Base::post()
            ->where(Base::post()->getTable() . '.created_at', '>=', now()->subWeek())
            ->orderByVisits()
            ->limit(3)
            ->get();

        if ( ! $posts->count()) return Command::SUCCESS;

        // Create new post
        $newsletter = Base::post()->firstOrNew(['slug' => 'newsletter-' . now()->format('Ymd')]);

        $newsletter->author_id = 1;
        $newsletter->name = config('app.name') . ' — ' . now()->format('d M Y');
        $newsletter->excerpt = __('Here is a list of the best publications of the week.');
        $newsletter->content = view('base::newsletter.weekly', compact('posts'))->render();
        $newsletter->type = 'newsletter';
        $newsletter->status = 'private';
        $newsletter->save();

        $newsletter->sendNewsletter($this->option('type'));

        return Command::SUCCESS;
    }
}
