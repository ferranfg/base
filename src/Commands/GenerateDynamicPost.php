<?php

namespace Ferranfg\Base\Commands;

use Ferranfg\Base\Base;
use Ferranfg\Base\Clients\Unsplash;
use Ferranfg\Base\Models\Product;
use Ferranfg\Base\Models\Assistance;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class GenerateDynamicPost extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    public $signature = 'base:generate-dynamic-post {action=generate} {--title=} {--type=} {--topic=} {--keyword=}';

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

        $post = null;

        if (Schema::hasColumn(Base::post()->getTable(), 'scheduled_at')) $post = Base::post()
            ->whereStatus('scheduled')
            ->whereType('dynamic')
            ->where('scheduled_at', '<=', now())
            ->orderBy('scheduled_at', 'asc')
            ->first();

        if (is_null($post)) $post = Base::post()->whereStatus('draft')->whereType('dynamic')->first();

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
            $prompt[] = "The content you write should have four h2 sections with two h3 sub-sections each.";
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
            "- Produce a reply that doesn't include phrases like 'Certainly,' or 'Here is the your content'.",
            "- Do not include an h1 title; start with text.",
            "- Do not include the word \"Section\" in the title of the sections.",
            "- Add an introductory paragraph before the first h2 heading.",
            "- Write three paragraphs with four sentences each, for every h3 sub-subsection.",
            "- Do not include any h4, h5, or h6 headings.",
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

        $post->publishMeta();

        return Command::SUCCESS;
    }

    /**
     * Create a new dynamic post
     *
     * @return \Ferranfg\Base\Models\Post
     */
    public function suggestDynamicPost($create_post = true)
    {
        list($is_random, $title, $type, $topic, $keyword) = $this->getSuggestion($create_post);

        $prompt = [
            "Imagine a new blog post to write about {$topic}.",
            (string) null,
            'Suggest the following fields for the imagined blog post:',
        ];

        if ($is_random) $prompt = array_merge($prompt, [
            '- Title: It should be concise, between 40-60 characters in length. It should incorporate relevant keywords and encourage readers to explore the article further.',
        ]);

        $prompt = array_merge($prompt, [
            '- Excerpt: It should be between 150-160 characters in length and should be optimized for search engines. Make sure it provides a clear and compelling preview of what the page offers to encourage clicks from users in search engine results.',
            '- Keywords: Provide a list of 3-5 comma-separated one-word keywords. Aim for a mix of popular, moderately popular, and niche-specific keywords.',
            (string) null,
            'Response must be in JSON format and follow the structure:',
            '{"name": "Replace with post title", "excerpt": "Replace with post excerpt", "keywords": "Replace with post keywords"}',
            (string) null,
            'Conditions:',
            '- Language: ' . strtoupper(config('app.locale')) . '.',
            "- Post type: {$type}.",
            "- Post title {$title}.",
        ]);

        if ($is_random)
        {
            $archive = Base::post()
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
        $post = Base::post();

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
            $post->keywords = $keyword ? "{$keyword}, {$response->keywords}" : $response->keywords;
            $post->save();
        }

        return $post;
    }

    /**
     * Get the title, type, topic and keyword for the dynamic post
     *
     * @return array
     */
    public function getSuggestion($create_post = true)
    {
        $is_random = false;

        $title   = null;
        $type    = null;
        $topic   = null;
        $keyword = null;

        // Get title, type and topic from options
        if ($this->option('title') or $this->option('type') or $this->option('topic') or $this->option('keyword'))
        {
            $title   = $this->option('title')   ?? null;
            $type    = $this->option('type')    ?? null;
            $topic   = $this->option('topic')   ?? null;
            $keyword = $this->option('keyword') ?? null;
        }
        // Get title, type and topic from config
        else if (config('base.blog_dynamic_posts'))
        {
            $json = file_get_contents(config('base.blog_dynamic_posts'));
            $json = json_decode($json, true);

            $post = collect($json)->whereNull('created_at')->random();

            if ($post and array_key_exists('title',   $post)) $title   = $post['title'];
            if ($post and array_key_exists('type',    $post)) $type    = $post['type'];
            if ($post and array_key_exists('topic',   $post)) $topic   = $post['topic'];
            if ($post and array_key_exists('keyword', $post)) $keyword = Str::snake((string) $post['keyword'], ' ');
        }

        // Format or random title
        if (is_null($title))
        {
            $is_random = true;

            $letters = ['E', 'A', 'O', 'I', 'N', 'R', 'S', 'D', 'U', 'C', 'L', 'T', 'B', 'P', '3', '5', '7'];
            $title = "must start with \"{$letters[array_rand($letters)]}\"";
        }
        else
        {
            $title = "will be: \"{$title}\"";
        }

        // Random type
        if (is_null($type))
        {
            $types = ['question', 'listicle', 'how-to', 'checklist', 'comparison'];
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
            $is_random,
            $title,
            $type,
            $topic,
            $keyword,
        ]);
    }
}