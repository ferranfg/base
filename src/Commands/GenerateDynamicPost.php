<?php

namespace Ferranfg\Base\Commands;

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
            "The article should be about: \"{$post->excerpt}\".",
            "Include the following keywords: \"{$post->keywords}\".",
            "Language: \"" . strtoupper(config('app.locale')) . "\".",
            "Response must be in Markdown format. Response must contain more than 2000 words.",
            "It should have a minimum of 4 sections.",
            "Do not include an h1 title; start with text.",
            "Do not include the word \"Section\" in the title of the sections.",
            "Produce a reply that doesn't include phrases like 'Certainly,' or 'Here is the your content'.",
        ];

        $assistance = Assistance::completion(implode("\n", $prompt), [
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
        $archive = (new Post)
            ->whereIn('type', ['entry', 'dynamic'])
            ->whereStatus('published')
            ->orderBy('updated_at', 'desc')
            ->take(7);

        $prompt = [
            'Imagine a new blog post idea to write about from one particular area of your knowledge.',
            'Blog post idea must be very specific about the topic and not too broad.',
            'Use different blog types like listicles, how-to guides, case studies, comparison, etc.',
            'Suggest a name, excerpt and keywords for the selected blog post.',
            "Language: \"" . strtoupper(config('app.locale')) . "\".",
            "Response must be in JSON format and follow the structure: ",
            '{"name": "Replace with post title up to 70 chars", "excerpt": "Replace with post excerpt up to 150 chars", "keywords": "Replace with post keywords"}',
        ];

        if ($archive->count())
        {
            $prompt[] = 'Here are the last posts published in the blog as a reference:';

            foreach ($archive->get() as $post)
            {
                $prompt[] = "{\"name\": \"{$post->name}\", \"excerpt\": \"{$post->excerpt}\", \"keywords\": \"{$post->keywords}\"}";
            }
        }

        $assistance = Assistance::completion(implode("\n", $prompt), [
            'temperature' => 1,
            'max_tokens' => 2048,
        ]);

        $content = $assistance->choices[0]->message->content;
        $content = str_replace("\n", ' ', (string) $content);

        $response = json_decode($content);

        // Attempt to get JSON from Markdown
        if (is_null($response))
        {
            preg_match('/\{([^}]+)\}/s', $content, $matches);

            if (array_key_exists(0, $matches))
            {
                $response = json_decode($matches[0]);
            }
        }

        // Not able to get JSON from string
        if ( ! is_object($response)) return $post;

        if (property_exists($response, 'name') and property_exists($response, 'excerpt'))
        {
            $post->author_id = 1;
            $post->name = $response->name;
            $post->excerpt = $response->excerpt;
            $post->content = (string) null;
            $post->type = 'dynamic';
            $post->status = 'draft';
            $post->keywords = $response->keywords;
            $post->save();
        }

        return $post;
    }
}