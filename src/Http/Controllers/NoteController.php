<?php

namespace Ferranfg\Base\Http\Controllers;

use Illuminate\Http\Request;
use Ferranfg\Base\Models\Note;
use Illuminate\Routing\Controller;

class NoteController extends Controller
{
    /**
     * Muestra la entrada correspondiente al page_id en Notion
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $post = new Note($request->slug);

        abort_unless($post->exists, 404);

        view()->share([
            'meta_title' => meta_title($post->name),
            'meta_description' => $post->excerpt,
            'meta_image' => $post->photo_url
        ]);

        return view('base::blog.post', [
            'post' => $post,
            'photo_url' => $post->photo_url,
            'previous' => null,
            'next' => null,
            'random' => null,
        ]);
    }
}