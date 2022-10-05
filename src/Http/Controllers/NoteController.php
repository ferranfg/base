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
    public function index($slug = null, $base_path = "notes")
    {
        $post = new Note($slug, $base_path);

        abort_unless($post->exists, 404);

        if ($slug == $post->page_id) return redirect($post->canonical_url, 301);

        view()->share([
            'meta_title' => meta_title($post->name),
            'meta_description' => $post->excerpt,
            'meta_image' => $post->photo_url
        ]);

        return view(config('base.notes_view', 'base::blog.post'), [
            'post' => $post,
            'photo_url' => $post->photo_url,
            'previous' => null,
            'next' => null,
            'random' => null,
        ]);
    }
}