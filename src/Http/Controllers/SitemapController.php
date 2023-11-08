<?php

namespace Ferranfg\Base\Http\Controllers;

use Illuminate\Routing\Controller;
use Ferranfg\Base\Repositories\PostRepository;

abstract class SitemapController extends Controller
{
    protected $urls = [];

    protected $postRepository;

    public function __construct(
        PostRepository $postRepository
    )
    {
        $this->postRepository = $postRepository;
    }

    /**
     * Hydrate the blog urls.
     *
     * @return void
     */
    protected function hydrateBlogUrls($base_path = 'blog', $type = ['entry', 'dynamic', 'newsletter'])
    {
        $posts = $this->postRepository
            ->whereStatus('published')
            ->whereIn('type', $type)
            ->get();

        $tags = [];

        $this->urls[] = (object) [
            'loc' => url($base_path),
            'lastmod' => $posts->last()->updated_at->tz('UTC')->toAtomString(),
        ];

        foreach ($posts as $post)
        {
            $this->urls[] = (object) [
                'loc' => $post->canonical_url,
                'lastmod' => $post->updated_at->tz('UTC')->toAtomString(),
            ];

            foreach (explode(',', $post->keywords) as $keyword)
            {
                $keyword = urlencode(trim($keyword));

                if ($keyword) $tags[$keyword] = $post->updated_at->tz('UTC')->toAtomString();
            }
        }

        foreach ($tags as $tag => $lastmod)
        {
            $this->urls[] = (object) [
                'loc' => url("/tag/{$tag}"),
                'lastmod' => $lastmod,
            ];
        }
    }
}