<?php

namespace Ferranfg\Base\Commands;

use Ferranfg\Base\Clients\Unsplash;
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
    public $description = 'Generate the first draft dynamic post';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $post = Post::whereStatus('draft')->whereType('dynamic')->first();

        if (is_null($post)) $post = $this->suggestDynamicPost();

        if ( ! $post->exists) return Command::FAILURE;

        $post->status = 'pending';
        $post->save();

        $prompt = [
            "Write a long post content for an article titled: \"{$post->name}\".",
            "Include the following keywords: \"{$post->keywords}\".",
            "Language: \"" . strtoupper(config('app.locale')) . "\".",
            "Response must be in Markdown format. Response must contain more than 2000 words.",
            "It should have a minimum of 4 sections.",
            "Do not include an h1 title; start with text.",
            "Do not include the word \"Section\" in the title of the sections.",
            "Produce a reply that doesn't include phrases like 'Certainly,' or 'Here is the your content'.",
        ];

        $assistance = Assistance::completion(implode(' ', $prompt), [
            'temperature' => 0.5,
            'max_tokens' => 7168,
        ]);

        $post->content = $assistance->choices[0]->message->content;
        $post->status = 'published';
        $post->save();

        return Command::SUCCESS;
    }

    /**
     * Create a new dynamic post
     *
     * @return \Ferranfg\Base\Models\Post
     */
    public function suggestDynamicPost()
    {
        $post = new Post;

        $prompt = [
            'Imagine a list of 10 blog post ideas and pick one randomly to write about.',
            'Suggest a name, excerpt and keywords for the selected blog post.',
            "Language: \"" . strtoupper(config('app.locale')) . "\".",
            "Response must follow the JSON structure: ",
            '{"name": "Replace with post title up to 70 chars", "excerpt": "Replace with post excerpt up to 150 chars", "keywords": "Replace with post keywords"}',
        ];

        $assistance = Assistance::completion(implode(' ', $prompt), [
            'temperature' => 1,
            'max_tokens' => 1024,
        ]);

        $response = $assistance->choices[0]->message->content;
        $response = str_replace("\n", '', (string) $response);
        $response = json_decode($response);

        if ( ! is_object($response)) return $post;

        if (property_exists($response, 'name') and property_exists($response, 'excerpt'))
        {
            $post->author_id = 1;
            $post->name = $response->name;
            $post->excerpt = $response->excerpt;
            $post->photo_url = Unsplash::randomFromCollections()->pluck('urls.regular')->random();
            $post->content = (string) null;
            $post->type = 'dynamic';
            $post->status = 'draft';
            $post->keywords = $response->keywords;
            $post->save();
        }

        return $post;
    }
}