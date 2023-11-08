<?php

namespace Ferranfg\Base\Http\Controllers;

use Schema;
use Closure;
use Ferranfg\Base\Base;
use Ferranfg\Base\Models\Cart;
use Ferranfg\Base\Models\Product;
use Ferranfg\Base\Repositories\CommentRepository;
use Ferranfg\Base\Repositories\ProductRepository;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ShopController extends Controller
{
    use ValidatesRequests;

    public $commentRepository;

    public $productRepository;

    public function __construct(
        CommentRepository $commentRepository,
        ProductRepository $productRepository,
    )
    {
        $this->commentRepository = $commentRepository;
        $this->productRepository = $productRepository;

        $this->middleware(function (Request $request, Closure $next)
        {
            abort_unless(config('base.shop_enabled'), 404);
            abort_unless(Schema::hasTable(Base::product()->getTable()), 404);

            return $next($request);
        });
    }

    /**
     * Show the products home page.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function list(Request $request)
    {
        if ($request->has('guid'))
        {
            $product = $this->productRepository->findById($request->guid);

            return redirect($product->canonical_url, 302);
        }

        $products = $this->productRepository
            ->whereAvailable()
            ->orderByVisits()
            ->simplePaginate(8);

        $products->setPath(config('base.shop_path'));

        $offers = $this->productRepository
            ->whereAvailable()
            ->orderByDiscount()
            ->take(8)
            ->get();
        
        view()->share([
            'meta_title' => meta_title(config('base.shop_title')),
            'meta_description' => config('base.shop_description')
        ]);

        return view('base::shop.list', [
            'products' => $products,
            'brands' => $this->productRepository->getBrands()->take(6)->get(),
            'offers' => $offers,
        ]);
    }

    /**
     * Redirect to a random product page.
     *
     * @return Response
     */
    public function random(Request $request)
    {
        $product = $this->productRepository->randomAvailable();

        return redirect($product->canonical_url, 302);
    }

    /**
     * Display the public page.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Product  $product
     * @return \Illuminate\View\View
     */
    public function product(Request $request)
    {
        $product = $this->productRepository->findBySlug($request->slug);

        abort_unless(in_array($product->type, ['simple', 'affiliate']), 404);

        $product->trackVisit();

        if ($request->header('X-Requested-With') == 'XMLHttpRequest')
        {
            return view('base::shop.modal', [
                'product' => $product
            ]);
        }

        $photo_url = img_url($product->photo_url, [
            ['width' => 1920, 'height' => 1280]
        ]);

        view()->share([
            'meta_title' => meta_title($product->name),
            'meta_description' => $product->excerpt,
            'meta_image' => $photo_url
        ]);

        $related = $this->productRepository
            ->whereAvailable()
            ->where('products.id', '!=', $product->id)
            ->orderByVisits()
            ->take(4)
            ->get();

        return view('base::shop.product', [
            'product' => $product,
            'photo_url' => $photo_url,
            'related' => $related,
            'previous' => $this->productRepository->previousProduct($product),
            'next' => $this->productRepository->nextProduct($product),
        ]);
    }

    /**
     * Añade el producto al carrito de la compra.
     *
     * @return Response
     */
    public function add(Request $request)
    {
        $product = $this->productRepository->findBySlug($request->slug);

        Cart::add('cart', [
            'id' => $product->id,
            'name' => $product->name,
            'price' => $product->amount,
            'quantity' => (int) $request->get('quantity', 1),
            'attributes' => [
                'currency' => $product->currency,
                'canonical_url' => $product->canonical_url,
                'photo_url' => $product->photo_url,
            ],
            'associatedModel' => $product
        ]);

        return redirect()->to('/cart');
    }

    /**
     * Añade una review a un producto.
     *
     * @return Response
     */
    public function review(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255',
            'content' => 'required|max:255',
            'rating' => 'required|numeric'
        ]);

        $product = $this->productRepository->findBySlug($request->slug);

        $this->commentRepository->comment('review', $product, $request);

        return redirect()->back()->with('success', __('Gracias por tu comentario.'));
    }
}
