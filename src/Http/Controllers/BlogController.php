<?php

namespace Ferranfg\Base\Http\Controllers;

use Illuminate\Http\Request;
use Ferranfg\Base\Clients\ImageKit;

class BlogController extends BaseController
{
    /**
     * Lista y pagina las entradas del blog
     *
     * @return Response
     */
    public function list(Request $request)
    {
        if ($request->has('guid'))
        {
            $post = $this->postRepository->findById($request->guid);

            return redirect($post->canonical_url, 302);
        }

        $posts = $this->postRepository
            ->whereType('entry')
            ->whereStatus('published')
            ->orderBy('created_at', 'desc')
            ->simplePaginate(5);

        $posts->setPath('/blog');

        return view('base::blog.list', [
            'posts' => $posts
        ]);
    }

    /**
     * Página para la entrada individual de un post.
     *
     * @return Response
     */
    public function post(Request $request)
    {
        $post = $this->postRepository->findBySlug($request->slug);

        abort_unless($post->type == 'entry' and $post->status == 'published', 404);

        $photo_url = ImageKit::init()->url([
            'path' => $post->photo_url,
            'transformation' => [
                ['width' => 1920, 'height' => 1280]
            ]
        ]);

        return view('base::blog.post', [
            'post' => $post,
            'photo_url' => $photo_url,
            'previous' => $this->postRepository->previousPost($post),
            'next' => $this->postRepository->nextPost($post)
        ]);
    }

    /**
     * Añade un comentario a un post.
     *
     * @return Response
     */
    public function comment(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255',
            'content' => 'required|max:255'
        ]);

        $post = $this->postRepository->findBySlug($request->slug);

        abort_unless($post->type == 'entry' and $post->status == 'published', 404);

        $this->commentRepository->comment('comment', $post, $request);

        return redirect()->back()->with('success', __('Gracias por tu comentario.'));
    }
}