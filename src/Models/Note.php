<?php

namespace Ferranfg\Base\Models;

use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use FiveamCode\LaravelNotionApi\Notion;

class Note
{
    private $secret;

    public $exists;

    public $name;

    public $canonical_url;

    public $excerpt;

    public $word_count;

    public $reading_time;

    public $photo_url;

    public $created_at;

    public $updated_at;

    public $author;

    public $content;

    public $comments_disabled;

    public $comments;

    public function __construct($slug = null)
    {
        $default_page_id = config('services.notion.page_id');

        $note = is_null($slug) ? null : DB::table('notes')
            ->where('slug', $slug)
            ->orWhere('page_id', $slug)
            ->first();

        $page_id = $note ? $note->page_id : $slug ?? $default_page_id;

        $this->secret = config('services.notion.secret');

        if (is_null($page_id) or is_null($this->secret)) return $this;

        try
        {
            $page = cache()->remember("notion-page-{$page_id}", 60 * 60, function () use ($page_id)
            {
                return (new Notion($this->secret))->pages()->find($page_id);
            });
        }
        catch (Exception $e)
        {
            return $this;
        }

        $blocks = cache()->remember("notion-blocks-{$page_id}", 60 * 10, function () use ($page_id)
        {
            $api_url = 'https://us-central1-ferran-figueredo.cloudfunctions.net/notion';

            $request = (new Client)->get($api_url . '?' . http_build_query([
                'api_key' => $this->secret,
                'page_id' => $page_id
            ]));

            return json_decode((string) $request->getBody());
        });

        foreach ($blocks as $block)
        {
            if ( ! in_array($block->type, ['child_database']))
            {
                $content = $block->parent;

                $content = preg_replace('/]\(([a-zA-Z0-9])(?<![http])/', '](/notes/$1', $content);
                $content = preg_replace('/]\(\/([a-zA-Z0-9])/', '](/notes/$1', $content);

                $this->content .= $content . "\n\n";
            }
        }

        // DYNAMIC URLS
        if (is_null($note) and $page_id != $default_page_id)
        {
            $insert = ['page_id' => $page_id, 'slug' => Str::slug($page->getTitle())];
            $note = (object) $insert;

            DB::table('notes')->insert($insert);
        }

        // SUPPORTED
        $this->exists = true;
        $this->name = $page->getTitle();
        $this->canonical_url = $note ? url("notes/{$note->slug}") : url("notes/{$page_id}");
        $this->excerpt = $page->getProperty('Excerpt') ? $page->getProperty('Excerpt')->getPlainText() : '';
        $this->word_count = str_word_count(strip_tags($this->content));
        $this->reading_time = floor(bcdiv($this->word_count, 200));
        $this->photo_url = $page->getCover();
        $this->created_at = $page->getCreatedTime();
        $this->updated_at = $page->getLastEditedTime();

        $this->author = (object) [
            'name' => 'Ferran Figueredo',
            'photo_url' => ''
        ];

        // UNSUPPORTED
        $this->comments_disabled = true;
        $this->comments = collect();

        return $this;
    }
}