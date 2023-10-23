<?php

namespace Ferranfg\Base\Models;

use Schema;
use Exception;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use FiveamCode\LaravelNotionApi\Notion;

class Note
{
    private $secret;

    private $table = 'notes';

    public $page_id;

    public $base_path;

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

    public $comments;

    public function __construct($slug = null, $base_path = "notes")
    {
        if (is_null($slug)) $slug = config('services.notion.page_id');

        $note = Schema::hasTable($this->table) ? DB::table($this->table)
            ->where('slug', $slug)
            ->orWhere('page_id', $slug)
            ->first() : null;

        $this->secret = config('services.notion.secret');
        $this->page_id = $note ? $note->page_id : $slug;
        $this->base_path = $base_path;

        if (is_null($this->page_id) or is_null($this->secret)) return $this;

        try
        {
            $page = cache()->remember("notion-page-{$this->page_id}", 60 * 60, function ()
            {
                return (new Notion($this->secret))->pages()->find($this->page_id);
            });
        }
        catch (Exception $e)
        {
            return $this;
        }

        // DYNAMIC URLS
        if (is_null($note)) $note = $this->createDynamicNote($page);

        // SUPPORTED
        $this->exists = true;
        $this->name = $page->getTitle();
        $this->canonical_url = $this->getCanonicalUrl($note);
        $this->excerpt = $page->getProperty('Excerpt') ? $page->getProperty('Excerpt')->getPlainText() : '';
        $this->content = $this->getContent($note);
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
        $this->comments = collect();

        return $this;
    }

    private function createDynamicNote($page)
    {
        $note = [
            'page_id' => str_replace('-', '', (string) $page->getId()),
            'slug' => Str::slug($page->getTitle()),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ];

        if (Schema::hasTable($this->table)) DB::table($this->table)->insert($note);

        return (object) $note;
    }

    private function getContent($note)
    {
        $page_id = $note->page_id;
        $page_content = '';

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

                $page_content .= $content . "\n\n";
            }
        }

        return $page_content;
    }

    private function getCanonicalUrl($note)
    {
        return $note->page_id == config('services.notion.page_id') ?
            url($this->base_path) :
            url($this->base_path . '/' . $note->slug);
    }
}