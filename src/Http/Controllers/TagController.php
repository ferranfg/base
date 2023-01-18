<?php

namespace Ferranfg\Base\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Ferranfg\Base\Repositories\TagRepository;

class TagController extends Controller
{
    public $tagRepository;

    public function __construct(
        TagRepository $tagRepository
    )
    {
        $this->tagRepository = $tagRepository;
    }

    public function all(Request $request)
    {
        return $this->tagRepository->whereType($request->type)->paginate();
    }

    public function show(Request $request)
    {
        return $this->tagRepository->findBySlug($request->slug);
    }

    public function posts(Request $request)
    {
        return $this->show($request)->posts()->paginate();
    }

}