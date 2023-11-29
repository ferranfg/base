<?php

namespace Ferranfg\Base\Commands;

use Ferranfg\Base\Clients\Unsplash;
use Ferranfg\Base\Models\Post;
use Ferranfg\Base\Models\Product;
use Ferranfg\Base\Models\Assistance;
use Illuminate\Console\Command;

class GenerateDynamicPost extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    public $signature = 'base:generate-dynamic-post {action=generate} {--title=} {--type=} {--topic=}';

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
            "Write a long blog post content for an article titled: \"{$post->name}\".",
            "The article should be about: \"{$post->excerpt}\".",
        ];

        if ($post->content)
        {
            $prompt = array_merge($prompt, [
                (string) null,
                "Outline:",
                "- The content you write must complete the following outline structure.",
                "- Ignore and remove any h1 heading present in the outline.",
                "- Do not remove any h2 or h3 headings from the outline.",
                "- Outline structure: \"{$post->content}\".",
            ]);
        }
        else
        {
            $prompt[] = "The content you write should have a minimum of 6-8 h2, h3 heading sections.";
        }

        $prompt = array_merge($prompt, [
            (string) null,
            "Response:",
            "- Language: \"" . strtoupper(config('app.locale')) . "\".",
            "- Response must be in Markdown format. Use bold, italics, lists, links, etc.",
            "- Response must contain more than 2000 words.",
            (string) null,
            "Conditions:",
            "- If available, use the \"Context sections\" to add links to other articles in the blog.",
            "- Do not include an h1 title; start with text.",
            "- Do not include the word \"Section\" in the title of the sections.",
            "- Produce a reply that doesn't include phrases like 'Certainly,' or 'Here is the your content'.",
            "- Add an introductory paragraph before the first h2 heading.",
            "- Write at least two paragraphs for every h3 subsection.",
        ]);

        if ($post->showcase_product_ids)
        {
            $product_ids = collect($post->showcase_product_ids)->values();
            $products = Product::whereIn('id', $product_ids)->get();

            if ($products->count())
            {
                $prompt = array_merge($prompt, [
                    (string) null,
                    "Product showcase:",
                    "- Include the following products in the article.",
                    "- Add a section for each product, in the order they appear below.",
                    "- Include the following information for each product: name, URL, image, price.",
                    "- List:",
                ]);

                foreach ($products as $product)
                {
                    $prompt[] = "  - " . implode(". ", [
                        "Name: \"{$product->name}\"",
                        "URL: \"{$product->attached_url}\"",
                        "Image: \"" . img_url($product->photo_url) . "\"",
                        "Price: \"{$product->formatAmount()}\"",
                    ]);
                }
            }
        }
        else if ($post->main_keyword and config('services.unsplash.collections'))
        {
            $images = Unsplash::search($post->main_keyword, 1, 15);

            if ($images->count() >= 2)
            {
                $prompt[] = (string) null;
                $prompt[] = 'Here are 2 image URLs related to the topic. Spread them, one at a time, between sections of the article:';

                foreach ($images->pluck('urls.regular')->random(2) as $url)
                {
                    $prompt[] = "- ![]({$url})";
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
        list($title, $type, $topic) = $this->getTitleTypeAndTopic($create_post);

        $prompt = [
            "Imagine a new blog post to write about {$topic}.",
            (string) null,
            'Suggest the following fields for the imagined blog post:',
            '- Title: It should be concise, between 40-60 characters in length. It should incorporate relevant keywords and encourage readers to explore the article further.',
            '- Excerpt: It should be between 150-160 characters in length and should be optimized for search engines. Make sure it provides a clear and compelling preview of what the page offers to encourage clicks from users in search engine results.',
            '- Keywords: Provide a list of 3-5 comma-separated one-word keywords. Aim for a mix of popular, moderately popular, and niche-specific keywords.',
            (string) null,
            'Response must be in JSON format and follow the structure:',
            '{"name": "Replace with post title", "excerpt": "Replace with post excerpt", "keywords": "Replace with post keywords"}',
            (string) null,
            'Conditions:',
            '- Be very specific about the topic.',
            '- Language: "' . strtoupper(config('app.locale')) . '".',
            "- Post type: \"{$type}\".",
            "- The title \"{$title}\".",
        ];

        $archive = (new Post)
            ->whereStatus('published')
            ->whereIn('type', ['entry', 'dynamic'])
            ->orderBy('updated_at', 'desc')
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

    /**
     * Get the title, type and topic for the dynamic post
     *
     * @return array
     */
    public function getTitleTypeAndTopic($create_post = true)
    {
        $title = null;
        $type  = null;
        $topic = null;

        // Get title, type and topic from options
        if ($this->option('title') or $this->option('type') or $this->option('topic'))
        {
            $title = $this->option('title') ?? null;
            $type  = $this->option('type')  ?? null;
            $topic = $this->option('topic') ?? null;
        }
        // Get title, type and topic from config
        else if (config('base.blog_dynamic_posts'))
        {
            $json = file_get_contents(config('base.blog_dynamic_posts'));
            $json = json_decode($json, true);

            $post = collect($json)->whereNull('created_at')->first();

            if ($post and array_key_exists('title', $post)) $title = $post['title'];
            if ($post and array_key_exists('type', $post))  $type = $post['type'];
            if ($post and array_key_exists('topic', $post)) $topic = $post['topic'];
        }

        // Format or random title
        if ( ! is_null($title))
        {
            $title = "will be: \"{$title}\"";
        }
        else
        {
            $letters = ['E', 'A', 'O', 'I', 'N', 'R', 'S', 'D', 'U', 'C', 'L', 'T', 'B', 'P', '3', '5', '7'];
            $title = "must start with \"{$letters[array_rand($letters)]}\"";
        }

        // Random type
        if (is_null($type))
        {
            $types = ['question', 'listicle', 'how-to', 'case-study', 'tutorial', 'checklist', 'statistics', 'faqs', 'glossary', 'comparative analysis'];
            $type = $types[array_rand($types)];
        }

        // Random topic
        if (is_null($topic)) $topic = 'one topic of your system knowledge';

        // Update JSON file
        if ($create_post and config('base.blog_dynamic_posts') and $post)
        {
            $json = file_get_contents(config('base.blog_dynamic_posts'));
            $json = json_decode($json, true);

            $update = collect($json)->flatMap(function ($item) use ($post)
            {
                if ($item['title'] == $post['title'])
                {
                    $item['created_at'] = now()->toDateTimeString();
                }

                return [$item];
            });

            file_put_contents(
                config('base.blog_dynamic_posts'),
                json_encode($update, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
            );
        }

        return ([
            $title,
            $type,
            $topic,
        ]);
    }
}