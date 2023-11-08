<?php

namespace Ferranfg\Base\Http\Controllers;

use Illuminate\Routing\Controller;
use Ferranfg\Base\Repositories\PostRepository;
use Ferranfg\Base\Repositories\ProductRepository;

abstract class SitemapController extends Controller
{
    protected $urls = [];

    protected $postRepository;

    protected $productRepository;

    public function __construct(
        PostRepository $postRepository,
        ProductRepository $productRepository
    )
    {
        $this->postRepository = $postRepository;
        $this->productRepository = $productRepository;
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

        if ( ! $posts->count()) return;

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

    /**
     * Hydrate the shop urls.
     *
     * @return void
     */
    protected function hydrateShopUrls()
    {
        if ( ! config('base.shop_enabled')) return;

        $products = $products = $this->productRepository
            ->whereAvailable()
            ->get();

        if ( ! $products->count()) return;

        $this->urls[] = (object) [
            'loc' => url('shop'),
            'lastmod' => $products->last()->updated_at->tz('UTC')->toAtomString(),
        ];

        foreach ($products as $product)
        {
            $this->urls[] = (object) [
                'loc' => $product->canonical_url,
                'lastmod' => $product->updated_at->tz('UTC')->toAtomString(),
            ];
        }
    }
}