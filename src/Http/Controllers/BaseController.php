<?php

namespace Ferranfg\Base\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Ferranfg\Base\Repositories\PostRepository;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class BaseController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public $postRepository;

    public function __construct(PostRepository $postRepository)
    {
        $this->middleware(function ($request, $next)
        {
            view()->share([
                'meta_title' => config('base.meta_title'),
                'meta_description' => config('base.meta_description'),
                'meta_url' => url()->current(),
                'meta_image' => config('base.meta_image'),
                'og_width' => 1200,
                'og_height' => 628
            ]);

            return $next($request);
        });
        
        $this->postRepository = $postRepository;
    }
}