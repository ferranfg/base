<?php

namespace Ferranfg\Base\Http\Controllers;

use Ferranfg\Base\Repositories\ProductRepository;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Vitalybaev\GoogleMerchant\Feed;
use Vitalybaev\GoogleMerchant\Product;

class FeedController extends Controller
{
    protected $productRepository;

    public function __construct(
        ProductRepository $productRepository
    )
    {
        $this->productRepository = $productRepository;
    }

    /**
     * XML con el feed de todos los productos.
     *
     * @return Response
     */
    public function merchant(Request $request)
    {
        return $this->feed($this->productRepository->whereAvailable()->get());
    }

    /**
     * Contruye el XML con el feed de los productos.
     *
     * @return Response
     */
    protected function feed($products)
    {
        $feed = new Feed(
            config('app.name'),
            config('app.url'),
            config('base.meta_description')
        );

        foreach ($products as $product)
        {
            // https://support.google.com/merchants/answer/7052112?visit_id=637592823590150490-2004200117&hl=en&rd=1
            $item = new Product();

            $item->setId((string) $product->slug);
            $item->setTitle((string) $product->name);
            $item->setDescription((string) $product->description);
            $item->setLink((string) $product->canonical_url);
            if ($product->photo_url) $item->setImage((string) $product->photo_url);
            if ($product->attached_url) $item->setAdditionalImage((string) $product->attached_url);
            $item->setAttribute('availability', 'in stock', false);
            $item->setPrice("{$product->amount}{$product->currency}");
            $item->setBrand((string) $product->brand);
            $item->setCondition('new');
            $item->setAttribute('is_bundle', $product->type == 'bundle' ? 'yes' : 'no', false);

            // https://support.google.com/merchants/answer/6324436
            if ($product->google_category) $item->setGoogleCategory($product->google_category);

            $feed->addProduct($item);
        }

        return response($feed->build(), 200, [
            'Content-Type' => 'application/xml'
        ]);
    }
}
