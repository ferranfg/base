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

        if ($post->keywords and config('services.unsplash.collections'))
        {
            $images = Unsplash::search($post->keywords, 1, 30, 'landscape');

            if ($images->count() >= 2)
            {
                $prompt[] = 'Here are some image URLs related to the topic. Add them into the article:';

                foreach ($images->pluck('urls.regular')->random(2) as $url)
                {
                    $prompt[] = "- ![{$keyword}]({$url})";
                }
            }
        }

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
        $topic = $this->option('topic') ? '"' . $this->option('topic') . '"' : 'one topic of your system knowledge';

        $letters = ['e', 'a', 'o', 'i', 'n', 'r', 's', 'd', 'u', 'c', 'l', 't', 'b', 'p', '3', '5', '7'];
        $letter = strtoupper($letters[array_rand($letters)]);

        $post_type = ['question', 'listicle', 'how-to', 'case-study', 'tutorial', 'checklist', 'statistics', 'facts', 'historical', 'faqs', 'glossary', 'comparative analysis', 'inspiration and motivation'];
        $post_type = $post_type[array_rand($post_type)];

        $prompt = [
            "Imagine a new blog post to write about {$topic}.",
            (string) null,
            'Suggest the following fields for the imagined blog post:',
            '- Title: It should be concise, ideally between 40-60 characters in length. It should incorporate relevant keywords and encourage readers to explore the article further.',
            '- Excerpt: It should be between 150-160 characters in length and should be optimized for search engines. Make sure it provides a clear and compelling preview of what the page offers to encourage clicks from users in search engine results.',
            '- Keywords: Provide a list of 5-10 one-word keywords. Aim for a mix of popular, moderately popular, and niche-specific keywords.',
            (string) null,
            'Response must be in JSON format and follow the structure:',
            '{"name": "Replace with post title", "excerpt": "Replace with post excerpt", "keywords": "Replace with post keywords"}',
            (string) null,
            'Conditions:',
            '- Be very specific about the topic.',
            '- Language: "' . strtoupper(config('app.locale')) . '".',
            "- Post type: \"{$post_type}\".",
            "- The title must start with: \"{$letter}\".",
        ];

        $archive = (new Post)
            ->whereIn('type', ['entry', 'dynamic'])
            ->whereStatus('published')
            ->orderBy('created_at', 'desc')
            ->take(8);

        if ($archive->count())
        {
            $prompt[] = (string) null;
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