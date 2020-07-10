<?php

namespace Ferranfg\Base\Http\Controllers;

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
    }

    public function all(Request $request)
    {
        return $this->postRepository->paginate();
    }

    public function show(Request $request)
    {
        return $this->postRepository->findBySlug($request->slug, $request->locale);
    }

}