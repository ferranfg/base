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
    public $signature = 'base:generate-dynamic-post {action=generate} {--topic=}';

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
        if ($this->argument('action') == 'suggest') return $this->suggestDynamicPost(false);

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
            "Response must be in Markdown format. Use bold, italics, lists, etc.",
            "Response must contain more than 2000 words.",
            "If available, use the \"Context sections\" to add links to other articles in the blog.",
            "It should have a minimum of 4 sections.",
            "Do not include an h1 title; start with text.",
            "Do not include the word \"Section\" in the title of the sections.",
            "Produce a reply that doesn't include phrases like 'Certainly,' or 'Here is the your content'.",
        ];

        $assistance = Assistance::completion(implode("\n", $prompt), [
            'temperature' => 0.5,
            'match_count' => 5,
        ]);

        $post->content = $assistance->choices[0]->message->content;
        $post->status = 'published';
        $post->save();

        $post->publishFacebook();

        return Command::SUCCESS;
    }

    /**
     * Create a new dynamic post
     *
     * @return \Ferranfg\Base\Models\Post
     */
    public function suggestDynamicPost($create_post = true)
    {
        $topic = $this->option('topic') ?
            'the topic "' . $this->option('topic') . '"' :
            'one topic of your system knowledge';

        $prompt = [
            "Imagine a new blog post idea to write about {$topic}.",
            'Blog post idea must be very specific about the topic and not too broad.',
            'Use different blog types like listicles, how-to guides, case studies, comparison, etc.',
            'Suggest the following fields for the selected blog post:',
            '- Name: must be up to 70 characters.',
            '- Excerpt: must be up to 150 characters.',
            '- Keywords: must be popular used on social media and search engines.',
            "Language: \"" . strtoupper(config('app.locale')) . "\".",
            "Response must be in JSON format and follow the structure: ",
            '{"name": "Replace with post name", "excerpt": "Replace with post excerpt", "keywords": "Replace with post keywords"}',
        ];

        $archive = (new Post)
            ->whereIn('type', ['entry', 'dynamic'])
            ->whereStatus('published')
            ->orderBy('created_at', 'desc')
            ->take(8);

        if ($archive->count())
        {
            $prompt[] = '---';
            $prompt[] = 'Here are the last posts published in the blog as a reference. Do not repeat the same topic.';

            foreach ($archive->get() as $post)
            {
                $prompt[] = "{\"name\": \"{$post->name}\", \"excerpt\": \"{$post->excerpt}\"}";
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

        // Only return the OpenAI response
        if ( ! $create_post) return $this->info(
            json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );

        // Create new post
        $post = new Post;

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