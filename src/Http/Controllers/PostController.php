<?php

namespace Ferranfg\Base\Http\Controllers;

use Schema;
use Closure;
use Ferranfg\Base\Base;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Ferranfg\Base\Repositories\PostRepository;

class PostController extends Controller
{
    public $postRepository;

    public function __construct(
        PostRepository $postRepository
    )
    {
        $this->postRepository = $postRepository;

        $this->middleware(function (Request $request, Closure $next)
        {
            abort_unless(Schema::hasTable(Base::post()->getTable()), 404);

            return $next($request);
        });
    }

    public function all(Request $request)
    {
        return $this->postRepository->whereType($request->type)->paginate();
    }

    public function show(Request $request)
    {
        return $this->postRepository->findBySlug($request->slug);
    }

}