<?php

namespace Ferranfg\Base\Models;

use GuzzleHttp\Client;
use FiveamCode\LaravelNotionApi\Notion;

class NotionPost
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

    public function __construct($page_id = null)
    {
        $page_id = $page_id ?? config('services.notion.page_id');

        $this->secret = config('services.notion.secret');

        if (is_null($page_id) or is_null($this->secret)) return $this;

        $page = cache()->remember("notion-page-{$page_id}", 60 * 60, function () use ($page_id)
        {
            return (new Notion($this->secret))->pages()->find($page_id);
        });

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
            $content = preg_replace('/]\(([a-zA-Z0-9])(?<![http])/', '](/notes/$1', $block->parent);

            $this->content .= $content . "\n\n";
        }

        // SUPPORTED
        $this->exists = true;
        $this->name = $page->getTitle();
        $this->canonical_url = url("notes/{$page_id}");
        $this->excerpt = '';
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